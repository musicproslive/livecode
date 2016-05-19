<?php
/* ***************************************************************************************
Created by	:	Lijesh
Created on	:	11-07-2011
Purpose		:	Model class for course configuration
*************** ************************************************************************ */

class configureCourse extends modelclass
	{
		public function insertCourse($data)
		{
			do//create random code.
			{
			 	$randCode				=	$this->createRandom(LMT_RANDOM_CODE_LIMIT);
			}			
			while($this->getdbcount_sql("SELECT course_code FROM tblcourse where course_code = $random_code") > 0);
			$data['course_code']	=	$randCode;
			$data['created_on']		=	date("Y-m-d H:i:s");
			//insert price in tblcourse_prices update the course code there. then get and insert the price code in tblcourse table
			$price_data['instructor_level']=$data['course_type_level'];
			$price_data['duration']=$data['duration'];
			$price_data['course_type']=3;
			$price_data['currency_type']=1;
			$price_data['cost']=$data['price_code'];
			$price_data['status']=1;
			$price_data['course_code_master']=$data['course_code'];
			$price_details= $this->populateDbArray("tblcourse_prices",$price_data);
			$price_id=$this->db_insert("tblcourse_prices",$price_details);
			$data['price_code']=$price_id;
			$courseDetails = $this->populateDbArray("tblcourses",$data);
				if($this->db_insert("tblcourses",$courseDetails))
				{
					return "Your Meeting configured successfully.";
				}
				else
				{
					return "Error on while meeting Lesson.";
				}
			
		}
		public function udateCourse($data)
		{
			$price_data['cost']=$data['price_code'];
			$price_id=$data['priceid'];
			$this->db_update("tblcourse_prices",$price_data,"id = ".$price_id);
			
			$data_course['title']=$data['instuname'];
			$data_course['instructor_id']=$data['ins_id_hid'];
			$data_course['instrument_id']=$data['instrument'];
			$data_course['course_type_id']=3;
			$data_course['start_date']=$data['start_date'];
			$data_course['start_time']=$data['start_time'];
			$data_course['duration']=$data['duration'];
	        $data_course['channel_link']=$data['channel_link'];
			$data_course['price_code']=$price_id;
			$data_course['max_students']=$data['max_stu'];
			$data_course['min_required']=$data['min_stu'];
			$data_course['course_status_id']=1;
			$data_course['course_type_level']=$data['level'];
		    
			if($this->db_update("tblcourses",array('title'=>$data['title'],
			                                       'instructor_id'=>$data['instructor_id'],
												   'instrument_id'=>$data['instrument_id'],
												   'start_date'=>$data['start_date'],
												   'start_time'=>$data['start_time'],
												   'duration'=>$data['duration'],
												   'price_code'=>$price_id,
												   'channel_link'=>$data_course['channel_link'],
												   'max_students'=>$data['max_students'],
												   'min_required'=>$data['min_required'],
												   'course_type_level'=>$data['course_type_level']),"course_id=".$data['course_id']))
			{
				return "Your Meeting updated successfully.";
			}
			else
			{
				return "Error on while update Meeting.";
			}
		}
		function endCourse($course_id,$ins_id)
		{
			$update_course['course_status_id']	 = LMT_COURSE_STATUS_TAUGHT;
			$update_course['status_reason_id']	 = LMT_CS_REASON_TAUGHT;
			$update_course['status_changed_by']	 = $ins_id;
			$update_course['end_time']	 		 = date("Y-m-d H:i:s");
			$this->db_update('tblcourses', $update_course,"course_id='".$course_id."'", 0);
			
			$update_enroll['enrolled_status_id'] = LMT_CS_ENR_COMPLETED;
			$update_enroll['status_changed_by']	 = $ins_id;
			$update_enroll['status_reason_id']	 = LMT_CS_ENR_REASON_TAUGHT;
			$update_enroll['end_time']	 		 = date("Y-m-d H:i:s");
			$this->db_update('tblcourse_enrollments',$update_enroll,"course_id ='".$course_id."'",0);
		}
		
		
		public function __construct()
			{
				$this->setClassName();
			}	
			
			
		public function redirectAction($errMessage,$action,$url="")	
			{
				$this->setPageError($errMessage);
				$this->executeAction(false,$action,$url);	
			}	
			
		public function executeAction($loadData=true,$action="",$ufURL="",$navigate=false,$sameParams=false,$newParams="",$excludParams="",$page="")
			{
				if(trim($action))	$this->setAction($action);//forced action
				$methodName	=		(method_exists($this,$this->getMethodName()))	? $this->getMethodName($default=false):$this->getMethodName($default=true);
				$this->actionToBeExecuted($loadData,$methodName,$action,$navigate,$sameParams,$newParams,$excludParams,$page,$ufURL);
				$this->actionReturn		=	call_user_func(array($this, $methodName));				
				$this->actionExecuted($methodName);
				return $this->actionReturn;
			}
		public function __destruct() 
			{
				parent::childKilled($this);
			}
	}