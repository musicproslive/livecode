<?php
/**
 * Orbital class
 * 
 * @author Lijesh
 * @package Classes
 * @since  Nov-05-2012
 * 
 */

/**
 * 
 * This is the class file which communicates to the payment gateway
 * @package Classes
 *  
 */
class orbital extends siteclass
	{
		private	$orbitalKey			=	"";
		private	$consumerId			=	"";
		private	$orprofileId		=	"";
		public 	$statusMessage		=	"";
		public	$currentResponse	=	"";
		public	$txRefNum			=	"";
		
		function __construct()
			{
				if(ORBITAL_LIVE	==	1)
					{
						define(ORBITAL_CONNECTION_USERNAME,ORBITAL_CONNECTION_USERNAME_LIVE);
						define(ORBITAL_CONNECTION_PASSWORD,ORBITAL_CONNECTION_PASSWORD_LIVE);
						define(ORBITAL_CUSTOMER_BIN,ORBITAL_CUSTOMER_BIN_LIVE);
						define(ORBITAL_CUSTOMER_MERCHANTID,ORBITAL_CUSTOMER_MERCHANTID_LIVE);
						define(ORBITAL_TERMINALID,ORBITAL_TERMINALID_LIVE);
						define(ORBITAL_CONNECTING_URL,ORBITAL_CONNECTING_URL_LIVE);
					}
				else
					{
						define(ORBITAL_CONNECTION_USERNAME,ORBITAL_CONNECTION_USERNAME_TEST);
						define(ORBITAL_CONNECTION_PASSWORD,ORBITAL_CONNECTION_PASSWORD_TEST);
						define(ORBITAL_CUSTOMER_BIN,ORBITAL_CUSTOMER_BIN_TEST);
						define(ORBITAL_CUSTOMER_MERCHANTID,ORBITAL_CUSTOMER_MERCHANTID_TEST);
						define(ORBITAL_TERMINALID,ORBITAL_TERMINALID_TEST);
						define(ORBITAL_CONNECTING_URL,ORBITAL_CONNECTING_URL_TEST);
					}
			}
			
		public function setcallIds($consumerId,$orprofId)
			{
				if($consumerId)	$this->consumerId				=	$consumerId;
				if($orprofId)	$this->orprofileId				=	$orprofId;
			}
		
		public function setStatusMessage($msg)
			{
				$this->statusMessage	=	$msg;
			}
			
		public function getStatusMessage()
			{
				return $this->statusMessage;
			}
			
		public function getCurrentResponse()
			{
				return $this->currentResponse;
			}

		public function createPaymentAccount($userArry)
			{				
				$xml		=	
							'<?xml version="1.0" encoding="UTF-8"?>
								<Request>
									<Profile>
										<OrbitalConnectionUsername>'.ORBITAL_CONNECTION_USERNAME.'</OrbitalConnectionUsername>
										<OrbitalConnectionPassword>'.ORBITAL_CONNECTION_PASSWORD.'</OrbitalConnectionPassword>
										<CustomerBin>'.ORBITAL_CUSTOMER_BIN.'</CustomerBin>
										<CustomerMerchantID>'.ORBITAL_CUSTOMER_MERCHANTID.'</CustomerMerchantID>
										
										<CustomerName>'.$userArry["name"].'</CustomerName>
										<CustomerAddress1>'.$userArry["address1"].'</CustomerAddress1>
										<CustomerAddress2>'.$userArry["address2"].'</CustomerAddress2>
										<CustomerCity>'.$userArry["city"].'</CustomerCity>
										<CustomerState>'.$userArry["state"].'</CustomerState>
										<CustomerZIP>'.$userArry["zipcode"].'</CustomerZIP>
										<CustomerEmail>'.$userArry["email"].'</CustomerEmail>
										<CustomerProfileAction>C</CustomerProfileAction>
										<CustomerProfileOrderOverrideInd>NO</CustomerProfileOrderOverrideInd>
										<CustomerProfileFromOrderInd>A</CustomerProfileFromOrderInd>
										<CustomerAccountType>CC</CustomerAccountType>
										<Status>A</Status>
										<CCAccountNum>'.$userArry["card_no"].'</CCAccountNum>
										<CCExpireDate>'.$userArry["expiry_date"].'</CCExpireDate>
									</Profile>
								</Request>';
									
				
				$this->setcallIds($userArry["user_id"]);
				$response	=	$this->sendRequest($xml);
				
				$this->setStatusMessage($response[$this->orbitalKey]["CustomerProfileMessage"]);
				echo $response[$this->orbitalKey]["ProfileProcStatus"]." ".isset($response[$this->orbitalKey]["CustomerRefNum"]);
				
				if($response[$this->orbitalKey]["ProfileProcStatus"]	==	0 && isset($response[$this->orbitalKey]["CustomerRefNum"]))
					{	
						$dataIns["user_id"]			=	$userArry["user_id"];
						$dataIns["orbital_profile_id"]	=	$response[$this->orbitalKey]["CustomerRefNum"];
						
						$dataIns["card_no"]				=	'************'.substr($userArry["card_no"] ,(strlen($userArry["card_no"]) - 4 ),4); 
						$dataIns["ip"]					=	$_SERVER["REMOTE_ADDR"];
						$dataIns['expiry_date'] 		= 	$response[$this->orbitalKey]["CCExpireDate"];
						$dataIns['created_date'] 		= 	date('Y-m-d H:i:s');
						
						if($this->isAnyActiveCreditCard($dataIns["user_id"]) == 0)		$dataIns["is_active"] = 1;
						
						$this->db_insert('tblusers_ccs', $dataIns, 0);
						return true;
					}
				else	
					{
						if(!trim($this->getStatusMessage()))
							{
								$this->setStatusMessage("Invalid inputs");
							}
						return false;
					}
			}
		public function isAnyActiveCreditCard($userID)
			{
				$count		=	$this->getdbcount_cond("tblusers_ccs","user_id=$userID AND is_active =1 AND is_deleted=0");
				return $count;
			}
		public function updatePaymentAccount($userArry)
			{
					$xml		=	
								'<?xml version="1.0" encoding="UTF-8"?>
									<Request>
										<Profile>
											<OrbitalConnectionUsername>'.ORBITAL_CONNECTION_USERNAME.'</OrbitalConnectionUsername>
											<OrbitalConnectionPassword>'.ORBITAL_CONNECTION_PASSWORD.'</OrbitalConnectionPassword>
											<CustomerBin>'.ORBITAL_CUSTOMER_BIN.'</CustomerBin>
											<CustomerMerchantID>'.ORBITAL_CUSTOMER_MERCHANTID.'</CustomerMerchantID>
											
											<CustomerRefNum>'.$userArry["orbital_profile_id"].'</CustomerRefNum>
					
											<CustomerAddress1>'.$userArry["address1"].'</CustomerAddress1>
											<CustomerAddress2>'.$userArry["address2"].'</CustomerAddress2>
											<CustomerCity>'.$userArry["city"].'</CustomerCity>
											<CustomerState>'.$userArry["state"].'</CustomerState>
											<CustomerZIP>'.$userArry["zip_code"].'</CustomerZIP>
											<CustomerEmail>'.$userArry["email"].'</CustomerEmail>
											<CustomerProfileAction>U</CustomerProfileAction>
											<CustomerProfileOrderOverrideInd>NO</CustomerProfileOrderOverrideInd>
											<CustomerProfileFromOrderInd>A</CustomerProfileFromOrderInd>
											<CustomerAccountType>CC</CustomerAccountType>
											<Status>A</Status>
											<CCAccountNum>'.$userArry["card_no"].'</CCAccountNum>
											<CCExpireDate>'.$userArry["expiry_date"].'</CCExpireDate>
										</Profile>
									</Request>';
				
				$this->setcallIds($userArry["consumer_id"],$userArry["orbital_profile_id"]);
				$response		=	$this->sendRequest($xml);
				
				$this->setStatusMessage($response[$this->orbitalKey]["CustomerProfileMessage"]);
				
				if($response[$this->orbitalKey]["ProfileProcStatus"]	==	0)
					{
						$this->db_update("paf_consumers_ccs",array("updated_date"=>"escape now() escape"),"orbital_profile_id=".$response["ProfileResp"]["CustomerRefNum"]);
						return true;
					}
				else	return false;
			}
		public function retrieveOrbitalProfileDetails($orbital_profile_id,$consumer_id)
			{
				$xml		=	
							'<?xml version="1.0" encoding="UTF-8"?>
								<Request>
									<Profile>
										<OrbitalConnectionUsername>'.ORBITAL_CONNECTION_USERNAME.'</OrbitalConnectionUsername>
										<OrbitalConnectionPassword>'.ORBITAL_CONNECTION_PASSWORD.'</OrbitalConnectionPassword>
										<CustomerBin>'.ORBITAL_CUSTOMER_BIN.'</CustomerBin>
										<CustomerMerchantID>'.ORBITAL_CUSTOMER_MERCHANTID.'</CustomerMerchantID>
										
										<CustomerRefNum>'.$orbital_profile_id.'</CustomerRefNum>
										
										<CustomerProfileAction>R</CustomerProfileAction>
									
									</Profile>
								</Request>';
								
				$this->setcallIds($consumer_id,$orbital_profile_id);
				$response	=	$this->sendRequest($xml);
				
				$this->setStatusMessage($response[$this->orbitalKey]["CustomerProfileMessage"]);
				if($response[$this->orbitalKey]["ProfileProcStatus"]	==	0)	return $response;
				else														return false;
			}
		
		public function consumerPaymentProcessing($dataArry)
			{
				$xml		=	
					'<?xml version="1.0" encoding="UTF-8"?>
					<Request>
						<NewOrder>
							<OrbitalConnectionUsername>'.ORBITAL_CONNECTION_USERNAME.'</OrbitalConnectionUsername>
							<OrbitalConnectionPassword>'.ORBITAL_CONNECTION_PASSWORD.'</OrbitalConnectionPassword>
							<IndustryType>EC</IndustryType>
							<MessageType>AC</MessageType>
							<BIN>'.ORBITAL_CUSTOMER_BIN.'</BIN>
							<MerchantID>'.ORBITAL_CUSTOMER_MERCHANTID.'</MerchantID>
							<TerminalID>'.ORBITAL_TERMINALID.'</TerminalID>
										
							
							<CurrencyCode>840</CurrencyCode>
							<CurrencyExponent>2</CurrencyExponent>
							
							<CustomerRefNum>'.$dataArry["orbital_profile_id"].'</CustomerRefNum>
							
							<OrderID>'.$dataArry["order_id"].'</OrderID>
							<Amount>'.$dataArry["amount"].'</Amount>
							<Comments> <![CDATA[ '.$dataArry["comments"].']]> </Comments>
						</NewOrder>
				</Request>';	
						
				$this->setcallIds($dataArry["user_id"],$dataArry["orbital_profile_id"]);
				$response	=	$this->sendRequest($xml);
				$this->setStatusMessage($response[$this->orbitalKey]["StatusMsg"]);
				if($response[$this->orbitalKey]["ApprovalStatus"]	==	1)		return true;
				else															return false;
			}
		public function reversalPaymentProcessing($dataArry)
			{
				$xml		=	
					'<?xml version="1.0" encoding="UTF-8"?>
						<Request>
							<Reversal>
								<OrbitalConnectionUsername>'.ORBITAL_CONNECTION_USERNAME.'</OrbitalConnectionUsername>
								<OrbitalConnectionPassword>'.ORBITAL_CONNECTION_PASSWORD.'</OrbitalConnectionPassword>
								<TxRefNum>'.$dataArry["tx_ref_num"].'</TxRefNum>';
				if($dataArry["amount"])
					{
						$xml		.=	'<adjustedAmount>'.$dataArry["amount"].'</adjustedAmount>';
					}
				$xml		.=	'<OrderID>'.$dataArry["order_id"].'</OrderID>
								<BIN>'.ORBITAL_CUSTOMER_BIN.'</BIN>
								<MerchantID>'.ORBITAL_CUSTOMER_MERCHANTID.'</MerchantID>
								<TerminalID>'.ORBITAL_TERMINALID.'</TerminalID>
							</Reversal>
					</Request>';
				
				$response	=	$this->sendRequest($xml);
				if($response[$this->orbitalKey]["ProcStatus"]	==	0)		return true;
				else														return false;
			}
		public function refundConsumerPayment($dataArry)
			{
				$xml		=	
					'<?xml version="1.0" encoding="UTF-8"?>
					<Request>
						<NewOrder>
							<OrbitalConnectionUsername>'.ORBITAL_CONNECTION_USERNAME.'</OrbitalConnectionUsername>
							<OrbitalConnectionPassword>'.ORBITAL_CONNECTION_PASSWORD.'</OrbitalConnectionPassword>
							<IndustryType>EC</IndustryType>
							<MessageType>R</MessageType>
							<BIN>'.ORBITAL_CUSTOMER_BIN.'</BIN>
							<MerchantID>'.ORBITAL_CUSTOMER_MERCHANTID.'</MerchantID>
							<TerminalID>'.ORBITAL_TERMINALID.'</TerminalID>
							
							<CurrencyCode>840</CurrencyCode>
							<CurrencyExponent>2</CurrencyExponent>
				
							<CustomerRefNum>'.$dataArry["orbital_profile_id"].'</CustomerRefNum>
							
							<OrderID>'.$dataArry["order_id"].'</OrderID>
							<Amount>'.$dataArry["amount"].'</Amount>
						</NewOrder>
				</Request>';
				
				$this->setcallIds($dataArry["user_id"],$dataArry["orbital_profile_id"]);
				$response	=	$this->sendRequest($xml);
				if($response[$this->orbitalKey]["ApprovalStatus"]	==	1)		return true;
				else	return false;
			}

		public function deletePaymentAccount($orbital_profile_id,$consumer_id)
			{
				$xml		=	
				'<?xml version="1.0" encoding="UTF-8"?>
					<Request>
						<Profile>
							<OrbitalConnectionUsername>'.ORBITAL_CONNECTION_USERNAME.'</OrbitalConnectionUsername>
							<OrbitalConnectionPassword>'.ORBITAL_CONNECTION_PASSWORD.'</OrbitalConnectionPassword>
							<CustomerBin>'.ORBITAL_CUSTOMER_BIN.'</CustomerBin>
							<CustomerMerchantID>'.ORBITAL_CUSTOMER_MERCHANTID.'</CustomerMerchantID>
							
							
							<CustomerRefNum>'.$orbital_profile_id.'</CustomerRefNum>
							
							<CustomerProfileAction>D</CustomerProfileAction>
						
						</Profile>
					</Request>';
				
				$this->setcallIds($consumer_id,$orbital_profile_id);
				$response	=	$this->sendRequest($xml);
				$this->setStatusMessage($response[$this->orbitalKey]["CustomerProfileMessage"]);
				if($response[$this->orbitalKey]["ProfileProcStatus"]	==	0)	return true;
				else														return false;
			}
				
		public function sendRequest($xml, $consumerId)
			{
				$prevfn		=	debug_backtrace();
				$headers	=	array(
									"MIME-Version: 1.1",
									"Content-type: application/PTI54",
									"Content-length: ".	strlen($xml),
									"Content-transfer-encoding: text",
									"Request-number: 1",
									"Document-type: Request"
								);
				$ch = curl_init();	
				
				//echo ORBITAL_CONNECTING_URL;exit;
							
				curl_setopt($ch, CURLOPT_URL,ORBITAL_CONNECTING_URL);
				curl_setopt($ch,CURLOPT_USERAGENT, "Mozilla/4.0 (compatible; MSIE 5.01; Windows NT 5.0)");
				curl_setopt($ch,CURLOPT_HTTPHEADER, $headers);
				curl_setopt($ch,CURLOPT_POST,true); 
				curl_setopt($ch,CURLOPT_POSTFIELDS,$xml); 
				curl_setopt($ch,CURLOPT_RETURNTRANSFER, true); 
				
				curl_setopt ($ch, CURLOPT_SSL_VERIFYHOST, 0);
				curl_setopt ($ch, CURLOPT_SSL_VERIFYPEER, 0);
				
				ob_clean();//execute post
				$result 	= curl_exec($ch);
				$response						=	$this->XML2Array($result);
				
				$this->currentResponse			=	$response;
				
				$dataIns["user_id"]			=	$this->consumerId;
				if($this->orprofileId)				$dataIns["orbital_profile_id"]	=	$this->orprofileId;
				else								$dataIns["orbital_profile_id"]	=	$response[$this->orbitalKey]["CustomerRefNum"];
				$dataIns["call_name"]			=	$prevfn[1]["function"];
				$dataIns["request"]				=	$xml;
				$dataIns["response"]			=	$result;
				$dataIns["ip"]					=	$_SERVER['REMOTE_ADDR'];
				$dataIns["created_date"]		=	date('Y-m-d H:i:s');

				if(!is_array($response[$this->orbitalKey]["TxRefNum"]))
					{
						$this->txRefNum			=	trim($response[$this->orbitalKey]["TxRefNum"]);
						$dataIns["tx_ref_num"]	=	trim($response[$this->orbitalKey]["TxRefNum"]);
					}
					
				//new modification on 10/06/2012
				if(trim($dataIns["call_name"])	==	"createPaymentAccount"	||	trim($dataIns["call_name"])	==	"updatePaymentAccount")
					{
						//$dataIns["request"]				=	"***credit card details***";
						//$dataIns["response"]			=	"***credit card details***";
					}	
				$this->db_insert('tblusers_ccs_log', $dataIns, 0);
				//------------------------------------------------------//
				
				return $response;
				
			}
		
		public function XML2Array ( $xml , $recursive = false )
			{
				if ( ! $recursive )	$array 	= 	simplexml_load_string ( $xml ) ;
				else				$array 	= 	$xml ;
				
				$newArray 		= 	array () ;
				$array 			= 	( array ) $array ;
				foreach ( $array as $key => $value )
					{
						$value 	= ( array ) $value ;
						if ( isset ( $value [ 0 ] ) )	$newArray [ $key ] = trim ( $value [ 0 ] ) ;
						else							$newArray [ $key ] = $this->XML2Array ( $value , true ) ;
					}
				foreach($newArray as $keys=>$vals)	
					{
						$this->orbitalKey	=	$keys;
						foreach($vals as $k=>$v)	if(is_array($v)) if(count($v)==	0)	$newArray[$keys][$k]	=	"";
					}
				return $newArray ;
			}
	}
?>
