<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;

class NotificationService
{
	private $message;

	private $data = [];

	private $user = null;

	public function __construct($message = null)
	{
		$this->message = $message;
	}

	public function send()
	{
		$to = $this->getLastUserDeviceToken();
		if(false === $to) {
			logger('There is member device token!');

			return false;
		}

		if(! $this->message) {
			logger('no message setted');
			return false;
		}
		logger($to);
		logger($this->message);
		return \OneSignal::sendNotificationToUser($this->message, $to, null, $this->data);
	}

	public function updateMemberDeviceToken($token)
	{
		$member = DB::select('select count(`id`) as total from `member_push_tokens` where `token` = :token;', ['token' => $token]);

		if(0 == $member[0]->total && auth('api')->user()) {
			DB::insert('insert into member_push_tokens (member_id, token, created_at) values (?,?,?)', [
				auth('api')->user()->id, $token, date('Y-m-d H:i:s')
			]);
		}
	}

	public function getLastUserDeviceToken()
	{
		if(! $this->user) {
			$activeUser = auth('api')->user()->id;
			$member = DB::select('select token from `member_push_tokens` where `member_id`= :memberId order by id desc limit 1', ['memberId' => $activeUser]);
		} else {

		}

		if($member) {
			return $member[0]->token;
		}

		return false;
	}

	public function stMessage($message)
	{
		$this->message = $message;

		return $this;
	}

	public function setData($data = [])
	{
		$this->data = $data;
		return $this;
	}
}