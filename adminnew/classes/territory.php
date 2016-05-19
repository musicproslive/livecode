<?php
/**************************************************************************************
Created By 	:Arun
Created On	:10-07-2011
Purpose		:territory (country and states)	
**************************************************************************************/

class territory extends siteclass
	{
		
		public function getAllCountries($args = "1")
			{	
				$sql		=	"select * from tblcountries where is_active = 1 order by country_name asc"; 
				$result		=	$this->getdbcontents_sql($sql, 0);
				return $result;
			}
		public function getAllStates($args="1")
			{
			    $sql		=	"select * from tblstates where $args order by state_name asc"; 
				$result		=	$this->getdbcontents_sql($sql);
				return $result;
				
			}
		public function getAllCities($args="1")
			{
				$sql		=	"select * from tblcities  where $args"; 
				$result		=	$this->getdbcontents_sql($sql);
				return $result;
				
			}	
			/*************************************************************/
			
			
			/**************************************************************/
		public function getCountryName($args="1")
			{
				$countryArry	=	$this->getdbcontents_cond('tblcountries',$args);
				return $countryArry[0]['country'];
			}
		public function getStateName($args="1")
			{
				$stateArry	=	$this->getdbcontents_cond('tblcountry_state',$args);
				return $stateArry[0]['state'];
			}
			
		public function insertCountry($dataArray)
			{
				$country_id		=	$this->db_insert('tblcountries',$dataArray);
				if(!$country_id) 
					{
						$this->dbRollBack;
						$this->setPageError($this->getDbErrors());
						return false;
					}
					
				else return $country_id;
				
			}
		public function insertState($dataArray)
			{
				$state_id		=	$this->db_insert('tblstates',$dataArray);
				if(!$state_id) 
					{
						$this->dbRollBack;
						$this->setPageError($this->getDbErrors());
						return false;
					}
				else return $country_id;
			}
		public function updateCountry($dataArray,$Id)
			{
				$data		=	$this->db_update('tblcountries',$dataArray,'id='.$Id);
				if(!$data) 	
					{
						$this->setPageError($this->getDbErrors());
						return false;
					}		
					else return true;	
			}
		public function updateState($dataArray,$stateId)
			{
				$data		=	$this->db_update('tblcountry_state',$dataArray,"id=".$stateId);
				if(!$data) 	
					{
						$this->setPageError($this->getDbErrors());
						return false;
					}		
				else return true;	
			}	
	}
?>