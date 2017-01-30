<?php 

namespace App\Services;

use App\SnapTag;

class PointCalculateService 
{
	/**
     * @var integer
     */
	private $memberId;
	/**
     * @var string
     */
	private $snapType;
	/**
     * @var string
     */
	private $modeType;
	/**
     * @var string
     */
	private $city;

	public function __construct($memberId = null, $snapType = null, $modeType = null, $city = null)
	{
		$this->member_id = $memberId;
		$this->snap_type = $snapType;
		$this->mode_type = $modeType;
		$this->city = $city;
	}

	public function save()
	{
		//$pointTask = $this->getPointBySnapType();
		$memberCode = $this->getMemberCode();

		$transaction = [
            'member_code' => $memberCode,
            'transaction_type' => 101,
            'transaction_detail' =>
            [
                '0' => array (
                'member_code_from' => 'kasir',
                'member_code_to' => 'member',
                'amount' => $pointTask,
                'detail_type' => 'db'
                ),
                '1' => array (
                    'member_code_from' => 'member',
                    'member_code_to' => 'kasir',
                    'amount' => $pointTask,
                    'detail_type' => 'cr'
                ),
            ]
        ];  
        (new TransactionService($transaction))->create();
	}

	private function getMemberLvl()
	{
		$memberId = $this->member_id;
		//dummy get level member
		$memberId = '1';
		return $memberId;
	}

	private function getMemberCode()
	{
		$memberId = $this->member_id;

		$member = \App\Member::where('id', $memberId)->first();

		return $member->member_code;
	}

	public function getPoint($fileId)
	{
		$type = $this->snap_type;
		$mode = $this->mode_type;
		$type = $this->getTypeId($type);

		$tag = $this->checkTags($fileId);

		$mode = $this->getModeId($mode, $tag);		

		$status = ($tag == true) ? '1' : '0';

		$code = $type.$mode.$status;

		$levelId = $this->getMemberLvl();

		$point = \DB::table('tasks')
            ->join('tasks_level_points', 'tasks.id', '=', 'tasks_level_points.task_id')
            ->join('level_points', 'level_points.id', '=', 'tasks_level_points.level_id')
            ->select('tasks_level_points.point')
            ->where('tasks.code', $code)
            ->where('tasks_level_points.level_id', $levelId)
            ->first();

		return $point;
	}

	protected function checkTags($fileId)
	{
		return SnapTag::where('snap_file_id', $fileId)->first();
	}

	protected function getTypeId($type)
	{
		if ($type == 'receipt') {
			$type = 'a';
		} elseif ($type == 'handwritten') {
			$type = 'b';
		} elseif ($type == 'generaltrade') {
			$type = 'c';
		}

		return $type;
	}

	protected function getModeId($mode, $tag)
	{
		switch ($mode) {
			case 'audio':
				$with = ($tag == true) ? '1' : '2';
				$mode = '1'.$with;
				break;

			case 'tags':
				$with = ($tag == true) ? '1' : '2';
				$mode = '2'.$with;
				break;

			case 'input':
				$with = ($tag == true) ? '1' : '2';
				$mode = '3'.$with;
				break;
			
			default:
				$with = ($tag == true) ? '1' : '2';
				$mode = '4'.$with;
				break;
		}

		return $mode;
	}
}
