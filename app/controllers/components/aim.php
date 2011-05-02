<?php
class AimComponent extends Object {
	// API login information
	var $g_loginname = "6q5xSF3KU"; // Keep this secure.
	var $g_transactionkey = "2X52Yt6SxN65gs95"; // Keep this secure.
	var $g_apihost = "api.authorize.net";
	var $g_apipath = "/xml/v1/request.api";
	
	function startup(&$controller) {
	}
	
	function getReturnDefaults() {
		//defaults
		$result['success'] = false;
		$result['profile_id'] = false;
		$result['payment_id'] = false;
		$result['system_message'] = false;
		$result['user_message'] = false;
		$result['error_type'] = 'user'; // if anything else, report error and don't tell user anything
		$result['response_code'] = false;
		$result['transaction_id'] = false; // needed for later refunds
		$result['raw'] = false;
		
		return $result;
	}
	
	// read auth.net messages and make it nice and simple for me
	function prepare($xml) {
		$result = array(); // what I'' return
		$response = $this->xml2array($xml);
		
		// if we get any other code, we show serious issue
		$user_errors = array('E00029','E00027');
		
		$result = $this->getReturnDefaults();
		
		// set values for all of these variables
		$result['response_code'] = $response[0]['elements'][1]['elements'][0]['text'];
		
		if(isset($response[0]['elements'][0]['text']) && $response[0]['elements'][0]['text'] != 'Error')
			$result['success'] = true;
		else
			$result['system_message'] = $response[0]['elements'][1]['elements'][1]['text'];
		
		if(!in_array($result['response_code'],$user_errors) && !$result['success']) {
			$result['error_type'] = 'system';
			$result['user_message'] = 
				'Sorry, there was a billing error we can\'t figure out. I just emailed tech support. '.
				'I would reccomend creating a <a href="/signup/">free</a> account in the meantime.'
				;
		}
		else if(!$result['success'])
			$result['user_message'] = 'That credit card didn\'t go through. Kindly double check everything or try a new card.';
		
		if(isset($response[1]['name']) && $response[1]['name'] == 'customerProfileId')
			$result['profile_id'] = $response[1]['text'];
			
		if(isset($response[1]['name']) && $response[1]['name'] == 'customerPaymentProfileId')
			$result['payment_id'] = $response[1]['text'];

		// get transaction id
		if(isset($response[1]['text'])) {
			$directresponse = explode(',',$response[1]['text']);
			if(isset($directresponse[6]) && $directresponse[6] > 0)
				$result['transaction_id'] = $directresponse[6];
		}
		
		return $result;
	}
	
	function setupProfile($email) {
		$xml = $this->getXmlHeader()."<createCustomerProfileRequest xmlns=\"AnetApi/xml/v1/schema/AnetApiSchema.xsd\">" .
			$this->MerchantAuthenticationBlock().
			'<profile>'.
			"<email>$email</email>".
			'</profile>'.
			'</createCustomerProfileRequest>';
		
		$request = $this->send_xml_request($xml);		
		return $this->prepare($request);
	}
	
	function setupPaymentProfile($profile_id,$credit_card,$expiration_year,$expiration_month,$email) {
		$xml = $this->getXmlHeader()."<createCustomerPaymentProfileRequest xmlns=\"AnetApi/xml/v1/schema/AnetApiSchema.xsd\">" .
			$this->MerchantAuthenticationBlock().
			"<customerProfileId>$profile_id</customerProfileId>".
			"<paymentProfile>".
			"<billTo><firstName>$email</firstName></billTo>".			
			"<payment>".
			 "<creditCard>".
			  "<cardNumber>$credit_card</cardNumber>".
			  "<expirationDate>$expiration_year-$expiration_month</expirationDate>". // required format for API is YYYY-MM
			 "</creditCard>".
			"</payment>".
			"</paymentProfile>".
			"<validationMode>liveMode</validationMode>". // or testMode
			"</createCustomerPaymentProfileRequest>";

		$request = $this->send_xml_request($xml);		
		return $this->prepare($request);	
	}
	
	function deleteProfile($profile_id) {
		$xml = $this->getXmlHeader().
			"<deleteCustomerProfileRequest xmlns=\"AnetApi/xml/v1/schema/AnetApiSchema.xsd\">".
			$this->MerchantAuthenticationBlock().
			"<customerProfileId>$profile_id</customerProfileId>".
			"</deleteCustomerProfileRequest>"
		;
		
		$request = $this->send_xml_request($xml);		
		return $this->prepare($request);	
	}
	
	function updatePaymentProfile($profile_id,$credit_card,$expiration_year,$expiration_month,$email,$payment_profile_id) {
		// we delete and then add, b/c the auth.net update payment command doesn't validate
		$xml = $this->getXmlHeader()."<deleteCustomerPaymentProfileRequest xmlns=\"AnetApi/xml/v1/schema/AnetApiSchema.xsd\">" .
			$this->MerchantAuthenticationBlock().
			"<customerProfileId>$profile_id</customerProfileId>".
			"<customerPaymentProfileId>$payment_profile_id</customerPaymentProfileId>".
			"</deleteCustomerPaymentProfileRequest>";

		$request = $this->send_xml_request($xml);
		$response = $this->prepare($request);
		
		if($response['success'])
			return $this->setupPaymentProfile($profile_id,$credit_card,$expiration_year,$expiration_month,$email);
		else
			return $response;
	}
	
	function charge($profile_id, $payment_profile_id, $amount, $description) {
		$xml = $this->getXmlHeader().
			"<createCustomerProfileTransactionRequest xmlns=\"AnetApi/xml/v1/schema/AnetApiSchema.xsd\">" .
			$this->MerchantAuthenticationBlock().
			"<transaction>".
			"<profileTransAuthCapture>".
			"<amount>$amount</amount>". // should include tax, shipping, and everything.
			"<customerProfileId>$profile_id</customerProfileId>".
			"<customerPaymentProfileId>$payment_profile_id</customerPaymentProfileId>".
			"<order>".
			"<description>$description</description>".
			"</order>".
			"</profileTransAuthCapture>".
			"</transaction>".
			"</createCustomerProfileTransactionRequest>";
			
		$request = $this->send_xml_request($xml);		
		return $this->prepare($request);
	}
	
	// we have to process refunds via the AIM gateway, not CMI. lame!
	function refund($transaction_id,$amount,$cc) {
		/*x_type=CREDIT
		x_trans_id=Transaction ID here Last revised: 8/12/2008
		x_card_num=Full credit card number or last four digits only here*/
		$submission	= array
		(			
			'x_type' => 'CREDIT',
			'x_trans_id'=>$transaction_id,
			'x_card_num'=>$cc,
			'x_amount'=>$amount		
		);
		
		return $this->makeAimRequest($submission);
	}
	
	function makeAimRequest($submission) {
		// add in the stuff you always need
		$submission['x_login'] = $this->g_loginname;
		$submission['x_version'] = '3.1';
		$submission['x_delim_char'] = ',';
		$submission['x_delim_data'] = 'TRUE';
		$submission['x_tran_key'] = $this->g_transactionkey;
		
		$fields = "";
		foreach( $submission as $key => $value ) $fields .= "$key=" . urlencode( $value ) . "&";	
		
		$ch = curl_init("https://secure.authorize.net/gateway/transact.dll"); 
		curl_setopt($ch, CURLOPT_HEADER, 0); // set to 0 to eliminate header info from response
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); // Returns response data instead of TRUE(1)
		curl_setopt($ch, CURLOPT_POSTFIELDS, rtrim( $fields, "& " )); // use HTTP POST to send form data
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE); // uncomment this line if you get no gateway response. ###
		$response = curl_exec($ch); //execute post and get results
		curl_close ($ch);
		
		return $this->prepareAimResponse($response);
	}
	
	// used to format when we have to make classic AIM calls, not CIM calls; mostly for refunds
	function prepareAimResponse($response) {
		
		$formatted = explode(',',$response);
		
		$result = $this->getReturnDefaults();
		
		if($formatted[0] == 1) // success
			$result['success'] = true;
		else {
			$result['success'] = false;
			$result['user_message'] = 'Sorry, we can\'t seem to issue a refund right now. Kindly contact support.';
			$result['system_message'] = $formatted[3];
			$result['error_type'] = 'system';
			$result['raw'] = $response;
		}
		
		return $result;
	}
	
	function getXmlHeader() {
		return "<?xml version=\"1.0\" encoding=\"utf-8\"?>";
	}
	
	function getUserError($error) {
		$errors = explode(',',$error[1]['text']);
		return isset($errors[3]) ? $errors[3] : false;
	}
	
	function isUserError($error) {
		return isset($error[0]['elements'][1]['elements'][0]['text']) && $error[0]['elements'][1]['elements'][0]['text'] = 'E00027';
	}
	
	/* 
		Authorize.NET
		functions from auth.net --http://developer.authorize.net/samplecode/ 	
	*/
	function send_xml_request($content)
	{
		return $this->send_request_via_curl($this->g_apihost,$this->g_apipath,$content);
	}
	
	//function to send xml request via curl
	function send_request_via_curl($host,$path,$content)
	{
		$posturl = "https://" . $host . $path;
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $posturl);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_HTTPHEADER, Array("Content-Type: text/xml"));
		curl_setopt($ch, CURLOPT_HEADER, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $content);
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
		$response = curl_exec($ch);
		return $response;
	}
	
	
	//function to parse the api response
	//The code uses SimpleXML. http://us.php.net/manual/en/book.simplexml.php 
	//There are also other ways to parse xml in PHP depending on the version and what is installed.
	function parse_api_response($content)
	{
		$parsedresponse = simplexml_load_string($content, "SimpleXMLElement", LIBXML_NOWARNING);
		if ("Ok" != $parsedresponse->messages->resultCode) {
			echo "The operation failed with the following errors:<br>";
			foreach ($parsedresponse->messages->message as $msg) {
				echo "[" . htmlspecialchars($msg->code) . "] " . htmlspecialchars($msg->text) . "<br>";
			}
			echo "<br>";
		}
		return $parsedresponse;
	}
	
	function MerchantAuthenticationBlock() {
		return
			"<merchantAuthentication>".
			"<name>" . $this->g_loginname . "</name>".
			"<transactionKey>" . $this->g_transactionkey . "</transactionKey>".
			"</merchantAuthentication>";
	}
	
	function xml2array($xml) {
        $xmlary = array();
               
        $reels = '/<(\w+)\s*([^\/>]*)\s*(?:\/>|>(.*)<\/\s*\\1\s*>)/s';
        $reattrs = '/(\w+)=(?:"|\')([^"\']*)(:?"|\')/';

        preg_match_all($reels, $xml, $elements);

        foreach ($elements[1] as $ie => $xx) {
                $xmlary[$ie]["name"] = $elements[1][$ie];
               
                if ($attributes = trim($elements[2][$ie])) {
                        preg_match_all($reattrs, $attributes, $att);
                        foreach ($att[1] as $ia => $xx)
                                $xmlary[$ie]["attributes"][$att[1][$ia]] = $att[2][$ia];
                }

                $cdend = strpos($elements[3][$ie], "<");
                if ($cdend > 0) {
                        $xmlary[$ie]["text"] = substr($elements[3][$ie], 0, $cdend - 1);
                }

                if (preg_match($reels, $elements[3][$ie]))
                        $xmlary[$ie]["elements"] = $this->xml2array($elements[3][$ie]);
                else if ($elements[3][$ie]) {
                        $xmlary[$ie]["text"] = $elements[3][$ie];
                }
        }

        return $xmlary;
}

}
?>