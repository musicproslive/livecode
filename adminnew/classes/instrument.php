<?php
/**************************************************************************************
Created By 	:Lijesh
Created On	:05-07-2011
Purpose		:Instrument Management
**************************************************************************************/

class instrument extends siteclass
	{
		
		public function getAllInstruments($args="1")
			{	
				$sql		=	"select instrument_id, name, instrument_image from tblinstrument_master where is_deleted = 0 order by name asc"; 
				$result		=	$this->getdbcontents_sql($sql);
				return $result;
			}
			
		public function getUserInstruments($userID)
			{	
				$sql		=	"SELECT 
									userInstrument.user_id, userInstrument.instrument_id, instrumentMaster.name, instrumentMaster.instrument_image
								 FROM 
								 	tbluser_instruments AS userInstrument
								LEFT JOIN tblinstrument_master AS instrumentMaster ON userInstrument.instrument_id = instrumentMaster.instrument_id AND instrumentMaster.is_deleted = 0
								 WHERE userInstrument.user_id = $userID AND userInstrument.is_deleted = 0"; 
				$result		=	$this->getdbcontents_sql($sql, false);
				return $result;
			}
		public function getInstrumentImage($instId)
			{
				$sql		=	"SELECT instrument_image FROM tblinstrument_master where instrument_id =".$instId; 
				$result		=	end($this->getdbcontents_sql($sql, false));
				return $result['instrument_image'];
			}
	}
?>
