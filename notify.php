<?php
include('PHP/src/GlobeApi.php');

$globe = new GlobeApi();
$sms = $globe->sms(7625);

// $test = array(1,2,3,4,5);
// print_r(array_splice($test, 1));

$json = file_get_contents('php://input');
$json = stripslashes($json);
$message = json_decode($json, true);

print_r($message);

//if we received a message
if($message) {
	if(!isset($message['inboundSMSMessageList']['inboundSMSMessage'])) {
		return 'Not Set inboundSMSMessage';
	}

	foreach($message['inboundSMSMessageList']['inboundSMSMessage'] as $item) {
		if(!isset($item['message'], $item['senderAddress'])) {
			continue;	
		}

		if(strpos(strtoupper($item['message']), 'SEARCH') === 0) {
			$name = split(" ", strtoupper($item['message']));
			$name = implode(" ", array_splice($name, 1));
			//if searching
			$sms->sendMessage(
				$user['access_token'],
				$user['subscriber_number'],
				'You will be receiving the list containing '.$name
			);

			//logic for pull here
		}

		if(strpos(strtoupper($item['message']), 'SUBSCRIBE SEARCH') === 0) {
			$name = split(" ", strtoupper($item['message']));
			$name = implode(" ", array_splice($name, 2));
			//if subscribing to search
			$sms->sendMessage(
				$user['access_token'],
				$user['subscriber_number'],
				'You will be receiving the list containing your '.$name.' every <time interval here>'
			);

			//logic for adding to cron job here
		}

		if(strpos(strtoupper($item['message']), 'END SUBSCRIBE SEARCH') === 0) {
			$name = split(" ", strtoupper($item['message']));
			$name = implode(" ", array_splice($name, 3));
			//if ending subscription
			$sms->sendMessage(
				$user['access_token'],
				$user['subscriber_number'],
				'You have successfully ended your subscription for updates about '.$name
			);
		}
	}
}
?>