<?php

namespace App\Services;

use Illuminate\Http\Request;
use App\LuckyDraw;
use Auth;

class LuckyDrawService
{
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
}