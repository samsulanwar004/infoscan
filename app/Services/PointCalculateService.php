<?php 

namespace App\Services;

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

	public function __construct($memberId, $snapType, $modeType, $city = '')
	{
		$this->member_id = $memberId;
		$this->snap_type = $snapType;
		$this->mode_type = $modeType;
		$this->city = $city;
	}

	public function save()
	{
		$pointTask = $this->getPointBySnapType();
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

	private function getPointBySnapType()
	{
		$taskId = $this->getTaskId();
		$levelId = $this->getMemberLvl();
		if ($taskId == false) {
			throw new \Exception('Task not found!');
		}
		$point = \DB::select('select * from tasks as t inner join tasks_level_points as tlp on t.id = tlp.task_id inner join level_points as l on l.id = tlp.level_id where tlp.task_id = '.$taskId.' and tlp.level_id = '.$levelId);
		return $point[0]->point;
	}

	private function getTaskId()
	{
		if ($this->mode_type == 'images') {
			$taskName = $this->snap_type;
		} else {
			$taskName = $this->snap_type.' and '.$this->mode_type;
		}

		$task = \App\Task::where('name', ucfirst($taskName))->first();

		return is_null($task) ? null : $task->id;
	}

	private function getMemberCode()
	{
		$memberId = $this->member_id;

		$member = \App\Member::where('id', $memberId)->first();

		return $member->member_code;
	}
}
