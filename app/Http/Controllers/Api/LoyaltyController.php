<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use GuzzleHttp\Client;
use Carbon\Carbon;
use Validator;
use App\MemberReward;
use App\Mail\OrderLoyalty;
use Illuminate\Support\Facades\Mail;
use App\Services\TransactionService;

class LoyaltyController extends BaseApiController
{

    private $member;

    private $status;

    const CACHE_EXPIRED_TOKEN = 'expired.token.egift';

    public function __construct()
    {
        $this->member = auth('api')->user();
    }

    public function index()
    {
        try {

            if (cache(self::CACHE_EXPIRED_TOKEN)) {
                $accessToken = cache(self::CACHE_EXPIRED_TOKEN);
            } else {
                $accessToken = $this->getAccessToken();
            }

            //request list egift
            $method = 'POST';
            $url = config('common.egift.url_list');
            $key = 'Bearer '.$accessToken;
            $type = 'form_params';
            $data = [];

            $result = $this->requestToClient($method, $url, $key, $data, $type);

            return $this->success($result, 200, true);

        } catch (\Exception $e) {
            return $this->error($e, 400, true);
        }
    }

    public function show($id)
    {
        try {
            if (cache(self::CACHE_EXPIRED_TOKEN)) {
                $accessToken = cache(self::CACHE_EXPIRED_TOKEN);
            } else {
                $accessToken = $this->getAccessToken();
            }

            //request show egift
            $method = 'POST';
            $url = config('common.egift.url_list');
            $key = 'Bearer '.$accessToken;
            $type = 'form_params';
            $data = [
                'program_ids' => array($id),
            ];

            $result = $this->requestToClient($method, $url, $key, $data, $type);

            return $this->success($result, 200, true);

        } catch (\Exception $e) {
            return $this->error($e, 400, true);
        }
    }

    public function store(Request $request)
    {
        try {
            $validation = $this->validRequest($request);
            if ($validation->fails()) {
                return $this->error($validation->errors(), 400, true);
            }

            if (cache(self::CACHE_EXPIRED_TOKEN)) {
                $accessToken = cache(self::CACHE_EXPIRED_TOKEN);
            } else {
                $accessToken = $this->getAccessToken();
            }

            $programId = $request->input('program_id');
            $image = $request->input('image');

            //request purchase egift
            $method = 'POST';
            $url = config('common.egift.url_purchase');
            $key = 'Bearer '.$accessToken;
            $type = 'json';

            $data = [
                'purchases' => [[
                    'program_id' => $programId,
                    'quantity' => 1,
                    'expired_value' => config('common.egift.expired'),
                ]],
            ];

            $result = $this->requestToClient($method, $url, $key, $data, $type);

            if ($this->getStatusCode() == '200') {
                //credit point and cash
                $this->creditCash($result->purchases[0]->value);

                $order = $this->saveMemberReward($result, $image);

                $this->sendOrderLoyaltyEmail($this->member, $order);
            } else {
                throw new Exception("Invalid Response from client!", 1);
            }

            return $this->success();

        } catch (\Exception $e) {
            return $this->error($e, 400, true);
        }
    }

    public function reward()
    {
        try {
            $reward = $this->getMemberReward();
            $reward = collect($reward)->toArray();

            return $this->success($reward, 200, true);
        } catch (\Exception $e) {
            return $this->error($e, 400, true);
        }
    }

    public function detailReward($id)
    {
        try {
            $reward = $this->getMemberReward($id);
            $reward = collect($reward)->toArray();

            return $this->success($reward, 200, true);
        } catch (\Exception $e) {
            return $this->error($e, 400, true);
        }
    }

    private function saveMemberReward($result, $image)
    {
        $mr = new MemberReward;
        $mr->trx_no = $result->trx_no;
        $mr->trx_time = $this->formatDate($result->trx_time);
        $mr->order_number = $result->order_number;
        $mr->brand = $result->purchases[0]->brand;
        $mr->egift_code = $result->purchases[0]->egift_code;
        $mr->program_name = $result->purchases[0]->program_name;
        $mr->item_name = $result->purchases[0]->item_name;
        $mr->value = $result->purchases[0]->value;
        $mr->expired_date = $this->formatDate($result->purchases[0]->expired_date);
        $mr->url = $result->purchases[0]->url;
        $mr->image = $image;
        $mr->member()->associate($this->member);

        $mr->save();

        return $mr;
    }

    private function getMemberReward($trxNo = null)
    {
        return is_null($trxNo) ?
            MemberReward::where('member_id', $this->member->id)
                ->get() :
            MemberReward::where('member_id', $this->member->id)
                ->where('trx_no', $trxNo)
                ->first();
    }

    private function validRequest(Request $request)
    {
        $rules = [
            'program_id' => 'required',
            'image' => 'required',
        ];

        return Validator::make($request->all(), $rules);
    }

    private function requestToClient($method, $url, $key, $data = null, $type)
    {
        $client = new Client();
        $res = $client->request(
            $method,
            $url,
            [
                'headers' => [
                    'Authorization' => $key,
                ],
                $type => $data
            ]
        );

        $this->setStatusCode($res->getStatusCode());

        return json_decode($res->getBody());
    }

    private function getAccessToken()
    {
        $apiKey = config('common.egift.api_key');
        $apiSecret = config('common.egift.api_secret');

        $encodedString = base64_encode($apiKey.':'.$apiSecret);
        $key = 'Basic '.$encodedString;
        $method = 'POST';
        $url = config('common.egift.url_token');
        $type = 'form_params';
        $data = [
            'grant_type' => 'client_credentials',
            'scope' => 'offline_access',
        ];

        $r = $this->requestToClient($method, $url, $key, $data, $type);

        $accessToken = $r->access_token;
        $expired = $r->expires_in;

        $expiresAt = Carbon::now()->addSeconds($expired);

        cache()->put(self::CACHE_EXPIRED_TOKEN, $accessToken, $expiresAt);

        return $accessToken;
    }

    private function formatDate($date)
    {
        $dateTimeArray = explode(' ', $date);
        $dateArray = explode('/', $dateTimeArray[0]);
        $newDate = $dateArray[2].'-'.$dateArray[1].'-'.$dateArray[0];

        $newDateTime = $newDate.' '.$dateTimeArray[1];

        return $newDateTime;
    }

    private function sendOrderLoyaltyEmail($member, $order)
    {
        $message = (new OrderLoyalty($member, $order))
            ->onConnection(env('INFOSCAN_QUEUE', 'sync'))
            ->onQueue(config('common.queue_list.member_order_loyalty'));

        Mail::to($member->email)->queue($message);
    }

    private function creditCash($cash)
    {
        $memberCode = $this->member->member_code;

        $point = $this->getDeductPoint($memberCode, $cash);

        $transaction = [
            'member_code' => $memberCode,
            'point' => $point,
            'cashout' => $cash,
        ];

        //credit point to member
        $this->transactionCredit($transaction);

        $currentPoint = (new TransactionService)->getCreditMember($memberCode);
        $currentCash = (new TransactionService)->getCashCreditMember($memberCode);

        $this->member->temporary_point = $currentPoint;
        $this->member->temporary_cash = $currentCash;
        $this->member->update();

        return true;
    }

    private function getDeductPoint($memberCode, $cash)
    {
        $currentMemberPoint = (new TransactionService)->getCreditMember($memberCode);
        $currentMemberCash = (new TransactionService)->getCashCreditMember($memberCode);

        $exitRate = $currentMemberCash / $currentMemberPoint;

        $point = $cash / $exitRate;

        if ($currentMemberCash < $cash) {
            throw new \Exception("Credit Cash not enough!", 1);
        }

        if ($currentMemberPoint < $point) {
            throw new \Exception("Credit Point not enough!", 1);
        }

        return round($point);
    }

    private function transactionCredit($transaction)
    {
        $cashier = config('common.transaction.member.cashier');
        $cashierMoney = config('common.transaction.member.cashier_money');
        $data = [
            'detail_transaction' => [
                '0' => [
                    'member_code_from' => $transaction['member_code'],
                    'member_code_to' => $cashier,
                    'amount' => $transaction['point'],
                    'detail_type' => 'db',
                ],
                '1' => [
                    'member_code_from' => $transaction['member_code'],
                    'member_code_to' => $cashier,
                    'amount' => $transaction['point'],
                    'detail_type' => 'cr',
                ],
                '2' => [
                    'member_code_from' => $transaction['member_code'],
                    'member_code_to' => $cashierMoney,
                    'amount' => $transaction['cashout'],
                    'detail_type' => 'db',
                ],
                '3' => [
                    'member_code_from' => $transaction['member_code'],
                    'member_code_to' => $cashierMoney,
                    'amount' => $transaction['cashout'],
                    'detail_type' => 'cr',
                ],
            ],
        ];

        (new TransactionService())->redeemPointToLoyalty($transaction, $data);
    }

    private function setStatusCode($value)
    {
        $this->status = $value;
        return $this;
    }

    private function getStatusCode()
    {
        return $this->status;
    }
}
