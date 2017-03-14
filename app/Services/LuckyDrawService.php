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
use App\Libraries\ImageFile;
use Image;
use Storage;
use App\Jobs\MemberActionJob;

class LuckyDrawService
{

	const DEFAULT_FILE_DRIVER = 's3';
	const RESIZE_IMAGE = [240,null];

	/**
     * @var string
     */
	protected $date;

	/**
     * @var string
     */
	protected $s3Url;

	public function __construct()
	{
		$this->date = Carbon::now('Asia/Jakarta');
		$this->s3Url = env('S3_URL', '');
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
		$l->is_active = $request->has('is_active') ? 1 : 0;

        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $filename = sprintf(
                "%s-%s.%s",
                $LuckyDrawCode,
                date('Ymdhis'),
                $file->getClientOriginalExtension()
            );

            //tmp file in storage local
            $path = storage_path('app/public')."/luckydraws/".$filename;
            //resize image
            $image = new ImageFile(Image::make($file->path())
            	->resize(self::RESIZE_IMAGE[0], self::RESIZE_IMAGE[1])
            	->save($path));

            Storage::disk(self::DEFAULT_FILE_DRIVER)
            	->putFileAs('luckydraws', $image, $filename, 'public');

            //delete file tmp
            Storage::delete('public/luckydraws/' . $filename);

            $l->image = $this->completeImageLink('luckydraws/'.$filename);
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
		$LuckyDrawCode = strtolower(str_random(10));

		$l = $this->getLuckyDrawById($id);
		$l->title = $request->input('title');
		$l->start_at = $dateArray[0];
		$l->end_at = $dateArray[1];
		$l->description = $request->input('description');
		$l->point = $request->input('point');
		$l->is_multiple = $request->has('is_multiple') ? 1 : 0;
		$l->is_active = $request->has('is_active') ? 1 : 0;

		if ($request->hasFile('image') != null && $l->image == true) {
            \Storage::delete('public/luckydraws/' . $l->image);
        }

        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $filename = sprintf(
                "%s-%s.%s",
                $LuckyDrawCode,
                date('Ymdhis'),
                $file->getClientOriginalExtension()
            );

            //tmp file in storage local
            $path = storage_path('app/public')."/luckydraws/".$filename;
            //resize image
            $image = new ImageFile(Image::make($file->path())
            	->resize(self::RESIZE_IMAGE[0], self::RESIZE_IMAGE[1], function ($constraint) {
            		$constraint->aspectRatio();
            	})->save($path));

            Storage::disk(self::DEFAULT_FILE_DRIVER)
            	->putFileAs('luckydraws', $image, $filename, 'public');

            //delete file tmp
            Storage::delete('public/luckydraws/' . $filename);

            $l->image = $this->completeImageLink('luckydraws/'.$filename);
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

		//build data for member history
        $content = [
            'type' => 'luckydraw',
            'title' => 'Lucky Draw',
            'description' => 'Kamu telah menukarkan poinmu untuk ikut undian. Semoga beruntung!',
            'flag' => 1,
        ];

        $config = config('common.queue_list.member_action_log');
        $job = (new MemberActionJob($member->id, 'luckydraw', $content))->onQueue($config)->onConnection(env('INFOSCAN_QUEUE'));
        dispatch($job); 

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

	/**
     * @param $filename
     * @return string
     */
    protected function completeImageLink($filename)
    {
        return $this->s3Url . '' . $filename;
    }

    public function getEndDateLuckyDraw($date)
    {
    	return LuckyDraw::with('memberLuckyDraws')
    		->where('end_at', '<=', $date)
    		->where('is_active', '=', 1)
    		->get();
    }

}