<?php

use Illuminate\Database\Eloquent\Model;

use paragraph1\phpFCM\Client;
use paragraph1\phpFCM\Message;
use paragraph1\phpFCM\Recipient\Device;
use paragraph1\phpFCM\Notification;


class UserNotification extends Model 
{
	public function send()
	{
		$apiKey = 'AAAA9syQkEU:APA91bFiWgmFa9XmWUFvVeEaHYhAfJYEdq0yjwuYNEtIrlovpTLrfenOOJzll4ymedjT7T34_0zQypHV2ZQti3CDYW1RbcQd9AIrfCljoEh-8fcRvEqrDmcR_5Nl-4xqjMtlIn3Kd0GeUr5IqYbNcqxwD2cwBptvBA';
		$client = new Client();
		$client->setApiKey($apiKey);
		$client->injectHttpClient(new \GuzzleHttp\Client());

		$note = new Notification('test title', 'testing body');
		$note->setIcon('notification_icon_resource_name')
		    ->setColor('#ffffff')
		    ->setBadge(1);

		$message = new Message();
		$message->addRecipient(new Device('your-device-token'));
		$message->setNotification($note)
		    ->setData(array('someId' => 111));

		$response = $client->send($message);
		var_dump($response->getStatusCode());
	}

	public function doPostDevice($request)
	{
		UserNotification::insert(['device_id' => $request['device_id'], 'role_id' => $request['role_id'], 'role' => $request['role']]);
		return api(['data' => 'Device Id is added successfully']);
	}
}