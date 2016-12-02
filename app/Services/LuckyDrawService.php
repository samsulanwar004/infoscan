<?php

namespace App\Services;

use Illuminate\Http\Request;
use App\LuckyDraw;
use Auth;

class LuckyDrawService
{
	/**
     * @param $request
     * @param null $id
     * @return bool
     */
	public function persistLuckyDraw(Request $request, $id = null)
	{
		$date = $request->input('start_at');
		$dateArray = explode(' - ', $date);
		$LuckyDrawCode = strtolower(str_random(10));

		$l = is_null($id) ? new LuckyDraw : $this->getLuckyDrawById($id);
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
}