<?php
class LeMAKSoftBSS_Exception extends Exception{
    const NOT_MODIFIED = 304; 
    const BAD_REQUEST = 400; 
    const NOT_FOUND = 404; 
    const NOT_ALOWED = 405; 
    const CONFLICT = 409; 
    const PRECONDITION_FAILED = 412; 
    const INTERNAL_ERROR = 500; 
}


class LeMAKSoftBSS
{
	
    const sendSMSURL = 'https://api.lemak-soft.com/sms/v1.0.0/LeMAKSoftBSS.svc/rest/json/SendSMS';  
    const checkBalanceURL = 'https://api.lemak-soft.com/sms/v1.0.0/LeMAKSoftBSS.svc/rest/json/CheckSMSBalance';  
    const trackSendingURL = 'https://api.lemak-soft.com/sms/v1.0.0/LeMAKSoftBSS.svc/rest/json/TrackSmsSending';  
    const smsHistoryURL = 'https://api.lemak-soft.com/sms/v1.0.0/LeMAKSoftBSS.svc/rest/json/SmsSendingHistory';


    const HTTP_OK = 200;
    //const HTTP_CREATED = 201;	for futher development
	const POST   = 'POST';
    //const GET    = 'GET'; for futher development
	
    private $_appUserName = null;
    private $_appPassword = null;
	
 
    function __construct($appUserName, $appPassword)
    {
        $this->_appUserName = $appUserName;        
        $this->_appPassword = $appPassword;      
    }   
 
    private function _exec($command, $url, $params)
    {
		$data_string = json_encode($params); 		
        $curl = curl_init();    
		
        switch ($command) {
          
            case self::POST:
                curl_setopt($curl, CURLOPT_URL, $url);
                curl_setopt($curl, CURLOPT_POST, true);
                curl_setopt($curl, CURLOPT_POSTFIELDS, $data_string);																				 
				curl_setopt($curl, CURLOPT_POST, true);
                break;
            /*case self::GET:
                curl_setopt($curl, CURLOPT_URL, $url . '?' . http_build_query($data_string));
                break;*/
        }
		curl_setopt($curl, CURLOPT_HTTPHEADER, array(                                                                          
			'Content-Type: application/json',                                                                                
			'Accept: application/json',                                                                                
			'Content-Length: ' . strlen($data_string))                                                                       
		);
		curl_setopt($curl, CURLOPT_CUSTOMREQUEST, $command);
		curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0);
		curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($curl, CURLOPT_CERTINFO, false);		
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);        
        $server_response = curl_exec($curl);
        $curlstatus = curl_getinfo($curl, CURLINFO_HTTP_CODE);
		
		print curl_error($curl);
        curl_close($curl);
        switch ($curlstatus) {
            case self::HTTP_OK:
            //case self::HTTP_CREATED:            
                $response = $server_response;//return a json array
                break;
            default: throw new LeMAKSoftBSS_Exception("Error : {$curlstatus}", $curlstatus);
                
        }
        return $response;
        
		
    } 




	
	
	/**** Functions exposed by the service ****/
	public function SendSMS($senderName,$listOfReceipients,$message)
    {
		$params=array(
			"userName"=>$this->_appUserName,
			"password"=>$this->_appPassword,
			"senderName"=>$senderName,
			"listOfPhoneNumbers"=>$listOfReceipients,
			"message"=>$message
		);
        return $this->_exec(self::POST,self::sendSMSURL, $params);
    }
	
	public function CheckSMSBalance()
    {
		$params=array(
			"userName"=>$this->_appUserName,
			"password"=>$this->_appPassword
		);
        return $this->_exec(self::POST,self::checkBalanceURL, $params);
    }
	public function TrackSmsSending($trackingCode)
    {
		$params=array(
			"userName"=>$this->_appUserName,
			"password"=>$this->_appPassword,
			"sendingTrackingNumber"=>$trackingCode
		);
        return $this->_exec(self::POST,self::trackSendingURL, $params);
    }
	public function SmsSendingHistory($from,$to)
    {
		//check the dates 
		$params=array(
			"userName"=>$this->_appUserName,
			"password"=>$this->_appPassword,
			"from"=>$from,
			"to"=>$to
		);
        return $this->_exec(self::POST,self::smsHistoryURL, $params);
    }
	/**** Functions exposed by the service ****/
	
}
