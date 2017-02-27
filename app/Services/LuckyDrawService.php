<?php

namespace App\Services;

use Illuminate\Http\Request;
use App\LuckyDraw;
use Carbon\Carbon;
use Cache;
use App\Transformers\LuckyDrawTransformer;
use App\Services\TransactionService;
use Auth;
use App\MemberLuckyDraw;

class LuckyDrawService
{

	/**
     * @var string
     */
	protected $date;

	public function __construct()
	{
		$this->date = Carbon::now('Asia/Jakarta');
	}

	/**
     * @param $request
     * @return bool
     */
	public function createLuckyDraw(Request $request)
	{
		$date = $request->input('start_at');
		$dateArray = explode(' - ', $date);
		$LuckyDrawCode = strtolower(str_random(10));

		$l = new LuckyDraw;
		$l->luckydraw_code = $LuckyDrawCode;
		$l->title = $request->input('title');
		$l->start_at = $dateArray[0];
		$l->end_at = $dateArray[1];
		$l->description = $request->input('description');
		$l->point = $request->input('point');
		$l->created_by = Auth::user()->id;
		$l->is_multiple = $request->has('is_multiple') ? 1 : 0;

        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $filename = sprintf(
                "%s-%s.%s",
                $LuckyDrawCode,
                date('Ymdhis'),
                $file->getClientOriginalExtension()
            );

            $l->image = $filename;
            $file->storeAs('luckydraws', $filename, 'public');
        }

		$l->save();

		return $l;
	}

	public function updateLuckyDraw(Request $request, $id)
	{
		/**
	     * @param $request
	     * @param null $id
	     * @return bool
	     */

		$date = $request->input('start_at');
		$dateArray = explode(' - ', $date);

		$l = $this->getLuckyDrawById($id);
		$l->title = $request->input('title');
		$l->start_at = $dateArray[0];
		$l->end_at = $dateArray[1];
		$l->description = $request->input('description');
		$l->point = $request->input('point');
		$l->is_multiple = $request->has('is_multiple') ? 1 : 0;

		if ($request->hasFile('image') != null && $l->image == true) {
            \Storage::delete('public/luckydraws/' . $l->image);
        }

        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $filename = sprintf(
                "%s-%s.%s",
                $l->luckydraw_code,
                date('Ymdhis'),
                $file->getClientOriginalExtension()
            );

            $l->image = $filename;
            $file->storeAs('luckydraws', $filename, 'public');
        }

		$l->update();

		return $l;

	}

	/**
     * @return mixed
     */

	public function getAllLuckyDraw()
	{
		$l = LuckyDraw::orderBy('id', 'DESC')
			->paginate(50);
		return $l;
	}

	/**
	 * @param null id
     * @return mixed
     */
	public function getLuckyDrawById($id)
	{
		return LuckyDraw::where('id', '=', $id)->first();
	}

	/**
     * @return mixed
     */
	public function getApiAllLuckyDraw()
	{
		if (Cache::has('lucky')) {
			return Cache::get('lucky');
		} else {

			$l = LuckyDraw::where('start_at', '<=', $this->date)
				->where('end_at', '>=', $this->date)
				->get();

			$transform = fractal()
				->collection($l)
				->transformWith(new LuckyDrawTransformer)
				->toArray();
			// save in lucky cache	
			//Cache::put('lucky', $transform, $this->date->addMinutes(10));

			return $transform;
		}
	}

	public function getRandomNumber($request)
	{
		$code = $request->input('code');
		$point = $request->input('point');
		$lucky = $this->getLuckyDrawByCode($code);
		$member = auth('api')->user();

		if ($lucky == false)
		{
			throw new \Exception("Lucky Draw Code not found!", 1);			
		}

		if ($lucky->is_multiple == 0)
		{
			$checkMultiple = $this->isMultipleLuckyDraw($member->id, $lucky->id);

			if ($checkMultiple == true) {
				throw new \Exception("Lucky Draw is not multiple", 1);				
			}
		}

		$currentMemberPoint = (new TransactionService)->getCreditMember($member->member_code);

		if ($currentMemberPoint < $point)
		{
			throw new \Exception("Credit not enough!", 1);			
		}

		$transaction = [
			'member_code' => $member->member_code,
			'point' => $point,
		];

		//credit point to member
		$this->transactionCredit($transaction);

		$randomNumber = pseudo_random_text('numeric', 7);

		$mld = new MemberLuckyDraw;
		$mld->random_number = $randomNumber;
		$mld->member()->associate($member);
		$mld->luckydraw()->associate($lucky);

		$mld->save();

		return $randomNumber;		
	}

	public function getLuckyDrawByCode($code)
	{
		return LuckyDraw::where('luckydraw_code', '=', $code)
			->first();
	}

	public function transactionCredit($transaction)
	{
		$kasir = config('common.transaction.member.cashier');
        $member = config('common.transaction.member.user');
        $data = [
            'detail_transaction' => [
                '0' => [
                    'member_code_from' => $kasir,
                    'member_code_to' => $member,
                    'amount' => $transaction['point'],
                    'detail_type' => 'cr'
                ],
                '1' => [
                    'member_code_from' => $member,
                    'member_code_to' => $kasir,
                    'amount' => $transaction['point'],
                    'detail_type' => 'db'
                ],
            ],
        ];

        (new TransactionService())->redeemPointToLuckyDraw($transaction, $data);
	}

	public function isMultipleLuckyDraw($memberId, $luckydrawId)
	{
		return MemberLuckyDraw::where('member_id', '=', $memberId)
			->where('luckydraw_id', '=', $luckydrawId)
			->first();
	}

}