<?php
//set the transaction status - must be "success", "cancelled", or "error"
$status = "success";
//set data for PUT request
$bodyparams = array(
			"client_id" => "5z7pr7rxJHUVOs50mWsjtZO9KBfvt4RZgVkxQzyz", // your APP ID goes here!!!
			"client_secret" => "CWMh1cdxigvxzVfujmBl6olfhmgTCbftaiCoCBIb", // your APP SECRET goes here!!!
			"status" => $status);
//sends the PUT request to the Paydunk API
function CallAPI($method, $url, $data = false){
		$curl = curl_init();
		curl_setopt($curl, CURLOPT_PUT, 1);		
		$update_json = json_encode($data);	
		curl_setopt($curl, CURLOPT_URL, $url . "?" . http_build_query($data));
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($curl, CURLOPT_SSLVERSION, 4);
		$result = curl_exec($curl);  
		$api_response_info = curl_getinfo($curl);
		curl_close($curl);
		return $result;
}
//get the transaction_uuid from Paydunk & call the the Paydunk API
$transaction_uuid = $_POST['transaction_uuid'];
if (isset($transaction_uuid)) {
	$url = "https://api.paydunk.com/api/v1/transactions/".$transaction_uuid;
	CallAPI("PUT", $url, $bodyparams);	
}
?>