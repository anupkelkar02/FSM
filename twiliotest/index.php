<?php
require "test/Services/Twilio.php";
// set your AccountSid and AuthToken from www.twilio.com/user/account
$AccountSid = "ACa3d8767c9b899aa92540f4a1717df0f5";
$AuthToken = "2d65c9adf59e0901ea492b51bf98024f";

$client = new Services_Twilio($AccountSid, $AuthToken);

$message = $client->account->messages->create(array(
"From" => "6754704050",
"To" => "POLIS04050",
"Body" => "Twilio Test message by Malayan G!",
));
// Display a confirmation message on the screen
echo "Sent message {$message->sid}";

die;

$client = new Services_Twilio($AccountSid, $AuthToken);
$call = $client->account->calls->create("+14158675309", "+14155551212", "http://demo.twilio.com/docs/voice.xml", array());
echo $call->sid;
?>