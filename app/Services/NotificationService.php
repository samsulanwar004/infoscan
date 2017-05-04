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

		return \OneSignal::sendNotificationToUser($this->message, $to, null, $this->data);
	}

	public function updateMemberDeviceToken($token)
	{
		$user = auth('api')->user();
		$member = DB::select('select count(`id`) as total from `member_push_tokens` where `token` = :token and `member_id` = :member_id;', [
				'member_id' => $user->id,
				'token' => $token
			]);

		if(0 == $member[0]->total && $user) {
			DB::insert('insert into member_push_tokens (member_id, token, created_at) values (?,?,?)', [
				auth('api')->user()->id, $token, date('Y-m-d H:i:s')
			]);
		}
	}

	public function getLastUserDeviceToken()
	{
		$user = null !== $this->user ? $this->user : auth('api')->user()->id;
		//$activeUser = auth('api')->user()->id;
		$member = DB::select('select token from `member_push_tokens` where `member_id`= :memberId order by id desc limit 1', ['memberId' => $user]);

		if($member) {
			return $member[0]->token;
		}

		return false;
	}

	public function setUser($userId)
	{
		$this->user = $userId;

		return $this;
	}

	public function setMessage($message)
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