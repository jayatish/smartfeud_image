<?php
class model_report extends CI_Model 
{
	
	
	function __construct()
	{
		parent::__construct();
	}
	
//------------------------- Start function to get the cms list -------------------------------//
	function GetReportList($report_type_result,$date_range_result,$date_range_part1_result)
	{
		$add_date_array = array();
		$data = array();

		if(($report_type_result==1) || ($report_type_result==2)){
			
			/*$this->db->order_by('add_date','DESC');
			$q = $this->db->get('osc_ticket');
			echo $this->db->last_query();
			if($q->num_rows>0){
				foreach($q->result() as $result){
					if($report_type_result==1){
						$register_date = date('Y-m-d',strtotime($result->add_date));
						if(!in_array($register_date, $add_date_array)){
							$add_date_array[] = $register_date;
						}
					}if($report_type_result==2){
						$register_date = date('Y-m',strtotime($result->add_date));
						if(!in_array($register_date, $add_date_array)){
							$add_date_array[] = $register_date;
						}
					}
					
				}
			}*/

			if($report_type_result==1){
				if($date_range_result==1){
					$monday = date( 'Y-m-d', strtotime( 'monday this week' ) );
					$friday = date( 'Y-m-d', strtotime( 'friday this week' ) );
					if($date_range_part1_result==1){
						$today_date = date('Y-m-d');
						$this->db->like('add_date',$today_date,'after');
					}if($date_range_part1_result==2){
						$previous_day = date('Y-m-d',mktime(date('H'),date('i'),date('s'),date('m'),date('d')-1,date('Y')));
						$this->db->like('add_date',$previous_day,'after');
					}if($date_range_part1_result==3){
						$first_day_this_month = date('Y-m-01');
						$last_day_this_month  = date('Y-m-t');
						$this->db->where("(add_date >= '".$first_day_this_month." 00:00:00' and add_date <= '".$last_day_this_month." 23:59:59')");
					}if($date_range_part1_result==4){
						$first_day_last_month = date('Y-m-d', strtotime('first day of last month'));
						$last_day_last_month  = date('Y-m-d', strtotime('last day of last month'));
						$this->db->where("(add_date >= '".$first_day_last_month." 00:00:00' and add_date <= '".$last_day_last_month." 23:59:59')");
					}if($date_range_part1_result==5){
						$first_day = date('Y-m-d');
						$last_day  = date('Y-m-d', mktime(date('H'),date('i'),date('s'),date('m'),date('d')-30,date('Y')));
						$this->db->where("(add_date >= '".$first_day." 00:00:00' and add_date <= '".$last_day." 23:59:59')");
					}if($date_range_part1_result==6){
						$monday = date( 'Y-m-d', strtotime( 'monday this week' ) );
						$sunday = date( 'Y-m-d', strtotime( 'sunday this week' ) );
						$this->db->where("(add_date >= '".$monday." 00:00:00' and add_date <= '".$sunday." 23:59:59')");
					}if($date_range_part1_result==7){
						$monday = date( 'Y-m-d', strtotime( 'monday last week' ) );
						$sunday = date( 'Y-m-d', strtotime( 'sunday last week' ) );
						$this->db->where("(add_date >= '".$monday." 00:00:00' and add_date <= '".$sunday." 23:59:59')");
					}if($date_range_part1_result==8){
						$monday = date( 'Y-m-d', strtotime( 'monday this week' ) );
						$friday = date( 'Y-m-d', strtotime( 'friday this week' ) );
						$this->db->where("(add_date >= '".$monday." 00:00:00' and add_date <= '".$friday." 23:59:59')");
					}if($date_range_part1_result==9){
						$monday = date( 'Y-m-d', strtotime( 'monday last week' ) );
						$friday = date( 'Y-m-d', strtotime( 'friday last week' ) );
						$this->db->where("(add_date >= '".$monday." 00:00:00' and add_date <= '".$friday." 23:59:59')");
					}if($date_range_part1_result==10){
						$current_year = date( 'Y');
						$this->db->like('add_date',$current_year,'after');
					}if($date_range_part1_result==11){
						$last_year = date( 'Y',mktime(date('H'),date('i'),date('s'),date('m'),date('d'),date('Y')-1));
						$this->db->like('add_date',$last_year,'after');
					}
				}
				$this->db->order_by('add_date','DESC');
				$q = $this->db->get('osc_ticket');
				echo $this->db->last_query();
				if($q->num_rows>0){
					foreach($q->result() as $result){
						$register_date = date('Y-m-d',strtotime($result->add_date));
						if(!in_array($register_date, $add_date_array)){
							$add_date_array[] = $register_date;
						}
					}
				}
			}if($report_type_result==2){
				$this->db->order_by('add_date','DESC');
				$q = $this->db->get('osc_ticket');
				if($q->num_rows>0){
					foreach($q->result() as $result){
						$register_date = date('Y-m',strtotime($result->add_date));
						if(!in_array($register_date, $add_date_array)){
							$add_date_array[] = $register_date;
						}
					}
				}
			}
			return $add_date_array;
		}else if($report_type_result==3){
			$this->db->where('status','1');
			$q = $this->db->get('admin');
			if($q->num_rows>0){
				foreach($q->result() as $result){
					$data[] = $result;
				}
			}
			return $data;
		}else{
			$this->db->where('status','1');
			$q = $this->db->get('categories');
			if($q->num_rows>0){
				foreach($q->result() as $result){
					$data[] = $result;
				}
			}
			return $data;
		}
		
	}

//--------------------------- End function to get the cms list -----------------------------------//
//-------------------------- Start function to get the count of particular ticket --------------------------------//
	function TicketCount($date,$status_id){
		$data = 0;
		$this->db->like('add_date',$date,'after');
		$this->db->where('status_id',$status_id);
		$q = $this->db->get('osc_ticket');
		//echo $this->db->last_query();
		return $q->num_rows;
	}
//---------------------------- End function to get the count of Particuler Ticket --------------------------------//
//---------------------------- Start function to get the count of Assigned Tickets --------------------------------//
	function AssignedTicketCount($field_name,$user_id){
		$data = 0;
		$this->db->where($field_name,$user_id);
		$q = $this->db->get('osc_ticket');
		return $q->num_rows;
	}
//------------------------------ End function to get the count of Assigned Tickets --------------------------------//

//---------------------------- Start function to get the table details ---------------------------------//
	function GetTableDetails($table_name,$status_name){
		$data = array();
		if($status_name==1){
			$this->db->where('status','1');
		}
		$this->db->order_by('id','ASC');
		$q = $this->db->get($table_name);
		//echo $this->db->last_query();
		if($q->num_rows>0){
			foreach($q->result() as $result){
				$data[] = $result;
			}
		}
		//echo '<pre>';print_r($data);
		return $data;
	}
//------------------------------ End function to get the table details ---------------------------------//
//--------------------------- Start function to get the list of users -------------------------------//
  function GetUserList($type){
    $data = array();
    if($type==0){
    	$this->db->where('type',$type);
	    $this->db->where('status','1');
	    $q = $this->db->get('admin');
	    if($q->num_rows>0){
	      foreach($q->result() as $result){
	        $data[] = $result;
	      }
	    }
    }else{
    	$this->db->where('type',$type);
	    $this->db->where('status','1');
	    $q = $this->db->get('users');
	    if($q->num_rows>0){
	      foreach($q->result() as $result){
	        $data[] = $result;
	      }
	    }
    }
    
    return $data;
  }
//----------------------------- End function to get the list of users -------------------------------//
//------------------------ Start function to insert details in cms --------------------------//
	function Insert()
	{
		$image_array = array();
		$ticket_number = $this->model_ticket->generate_random_password();

		for($count=0;$count<5;$count++){
			if($_FILES['attachment_'.$count]['name']!='')
		    {
		      
		      $image_name='attachment_'.$count;
		      $config['upload_path'] = 'uploads/ticket_document/'; 
		      $config['allowed_types'] = 'gif|jpg|png|zip|rar|csv|doc|docx|jpeg|xls|xlsx|txt|pdf'; 
		      $config['encrypt_name'] = TRUE; 
		      $config['max_size'] = '50000'; 
		      
		      $this->load->library('upload', $config); 
		      if(!$this->upload->do_upload($image_name)) 
		      {
		        
		        $this->session->set_userdata('error_msg',$this->upload->display_errors());
		        $return=1;
		        return $return;
		      }
		      else 
		      { 
		        $fInfo = $this->upload->data(); //uploading
		        //echo '<pre>';print_r($fInfo);echo '<pre>';
		      
		        $image_array[] = $fInfo['file_name'];
		      }
		    }
		}
		//echo $ticket_number;die;
		$insert_array = array(
			'ticket_no' => $ticket_number,
			'name' => $this->input->post('name'),
			'email' => $this->input->post('email'),
			'company_id' => $this->input->post('company_name'),
			'priority_id' => $this->input->post('priority'),
			'subject' => $this->input->post('subject'),
			'message' => $this->input->post('message'),
			'problem_id' => $this->input->post('problem_id'),
			'status_id' => '1',
			'assign_id' => $this->input->post('assign_id'),
			'add_date' => date('Y-m-d H:i:s'),
			'updated_date' => date('Y-m-d H:i:s')
		);
		$insert = $this->db->insert('osc_ticket',$insert_array);
		$last_insert_id = $this->db->insert_id();
		//echo $this->db->last_query();die;
		if($insert)
		{
			if(count($image_array)>0){
				for($image_count=0;$image_count<count($image_array);$image_count++){
					$image_name = $image_array[$image_count];
					$insert_document = array(
						'ticket_id' => $last_insert_id,
						'document_name' => $image_name
					);
					$this->db->insert('osc_ticket_document',$insert_document);
				}
			}
			$this->session->set_userdata('success_msg','Data added successfully.');
			$return=1;
		}
		else
		{
			$this->session->set_userdata('error_msg','Failed to insert data. Please try again.');
			$return=0;
		}
		return $return;
	}
//-------------------------- End function to insert details in cms --------------------------//
//--------------------------- Start function to generate the ticket ------------------------------//
	function generate_random_password() {
		$an = "0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ";
	    $su = strlen($an) - 1;
	    $ticket_number = substr($an, rand(0, $su), 1) .substr($an, rand(0, $su), 1).substr($an, rand(0, $su), 1)."-".substr($an, rand(0, $su), 1).substr($an, rand(0, $su), 1).substr($an, rand(0, $su), 1)."-".substr($an, rand(0, $su), 1) .substr($an, rand(0, $su), 1).substr($an, rand(0, $su), 1);

	    $this->db->where('ticket_no',$ticket_number);
	    $q = $this->db->get('osc_ticket');
	    if($q->num_rows>0){
	    	$this->model_ticket->generate_random_password();
	    }else{
	    	return $ticket_number;
	    }
	    //return $password;
	  }
//----------------------------- ENd function to generate the ticket ------------------------------//
//--------------------------- Start function to get the CMS Details ----------------------------//
	function GetDetails($ticket_id)
	{
		$data=array();
		$this->db->where('id',$ticket_id);
		$q=$this->db->get('osc_ticket');
		if($q->num_rows>0)
		{
			foreach($q->result() as $result)
			{
				$data[]=$result;
			}
		}
		return $data;
	}
//------------------------------ End function to get the CMS Details -----------------------------//
//--------------------------- Start function to get the list of uploaded documents --------------------------------//
	function UploadedDocument($ticket_id){
		$data = array();
		$this->db->where('ticket_id',$ticket_id);
		$q = $this->db->get('osc_ticket_document');
		if($q->num_rows>0){
			foreach($q->result() as $result){
				$data[]=$result;
			}
		}
		return $data;
	}
//----------------------------- End function to get the list of uploaded documents --------------------------------//
//------------------------ Start function to update cms details -------------------------------//
	function Update()
	{
		$edit_id=$this->input->post('edit_id');
		for($count=0;$count<5;$count++){
			if($_FILES['attachment_'.$count]['name']!='')
		    {
		      
		      $image_name='attachment_'.$count;
		      $config['upload_path'] = 'uploads/ticket_document/'; 
		      $config['allowed_types'] = 'gif|jpg|png|zip|rar|csv|doc|docx|jpeg|xls|xlsx|txt|pdf'; 
		      $config['encrypt_name'] = TRUE; 
		      $config['max_size'] = '50000'; 
		      
		      $this->load->library('upload', $config); 
		      if(!$this->upload->do_upload($image_name)) 
		      {
		        
		        $this->session->set_userdata('error_msg',$this->upload->display_errors());
		        $return=1;
		        return $return;
		      }
		      else 
		      { 
		        $fInfo = $this->upload->data(); //uploading
		        //echo '<pre>';print_r($fInfo);echo '<pre>';
		      
		        $image_array[] = $fInfo['file_name'];
		      }
		    }
		}
		$update_array = array(
			
			'name' => $this->input->post('name'),
			'email' => $this->input->post('email'),
			'company_id' => $this->input->post('company_name'),
			'priority_id' => $this->input->post('priority'),
			'subject' => $this->input->post('subject'),
			'message' => $this->input->post('message'),
			'problem_id' => $this->input->post('problem_id'),
			'status_id' => $this->input->post('status_id'),
			'assign_id' => $this->input->post('assign_id'),
			'updated_date' => date('Y-m-d H:i:s')
		);
		$this->db->where('id',$edit_id);
		$update=$this->db->update('osc_ticket',$update_array);
		if($update){
			if(count($image_array)>0){
				for($image_count=0;$image_count<count($image_array);$image_count++){
					$image_name = $image_array[$image_count];
					$insert_document = array(
						'ticket_id' => $edit_id,
						'document_name' => $image_name
					);
					$this->db->insert('osc_ticket_document',$insert_document);
				}
			}
			$this->session->set_userdata('success_msg','Data update successfully.');
			$return=1;
		}else{
			$this->session->set_userdata('error_msg','Failed to update data.');
			$return=1;
		}
		
		return $return;
	}
//-------------------------- End function to update cms details ---------------------------------//
//------------------------- Start function to update status in cms --------------------------------//
	function update_status()
	{
		if($this->input->post('status')==0)
		{
			$updated_status='1';
		}
		if($this->input->post('status')==1)
		{
			$updated_status='0';
		}
		$update_status=array('status' => $updated_status);
		$this->db->where('id',$this->input->post('edit_id'));
		$update=$this->db->update('cms',$update_status);
		if($update)
		{
			$this->session->set_userdata('success_msg','Status updated successfully.');
			$return=1;
		}
		else
		{
			$this->session->set_userdata('error_msg','Failed to update status. Please try again.');
			$return=0;
		}
		return $return;
	}
//--------------------------- End function to update status in cms ---------------------------------//
//----------------------------- Start function to delete cms details -------------------------------//
	function delete()
	{
		
		$edit_id=$this->input->post('edit_id');
		$this->db->where('id',$edit_id);
		$delete=$this->db->delete('cms');
		if($delete)
		{
			$this->db->where('static_text_id',$edit_id);
			$this->db->delete('cms_language');
			$this->session->set_userdata('success_msg','Details delete successfully.');
			$return=1;
		}
		else
		{
			$this->session->set_userdata('error_msg','Failed to delete details. Please try again.');
			$return=0;
		}
		return $return;
	}
//-------------------------------- End function to delete testimonials details --------------------------------//
	function GetSpecificLanguage($type,$language_id,$status_id){
		$data='';
		$this->db->where('static_text_id',$status_id);
		$this->db->where('language_id',$language_id);
		$q=$this->db->get('cms_language');
		if($q->num_rows>0){
			foreach($q->result() as $result){
				$data=$result->$type;
			}
		}
		return $data;
	}



	function GetLanguageText($language_code,$static_text_id){
		$language_name='';
		$this->db->where('language_code',$language_code);
		$q=$this->db->get('language_list');
		if($q->num_rows>0){
			foreach($q->result() as $result){
				$language_id = $result->id;
			}
		}
		$this->db->where('language_id',$language_id);
		$this->db->where('static_text_id',$static_text_id);
		$language=$this->db->get('cms_language');
		if($language->num_rows>0){
			foreach($language->result() as $language_result){
				$language_name = $language_result->language_text;
			}
		}
		return $language_name;
	}


    function GetLanguageDesc($language_code,$static_text_id){
		$language_name='';
		$this->db->where('language_code',$language_code);
		$q=$this->db->get('language_list');
		if($q->num_rows>0){
			foreach($q->result() as $result){
				$language_id = $result->id;
			}
		}
		$this->db->where('language_id',$language_id);
		$this->db->where('static_text_id',$static_text_id);
		$language=$this->db->get('cms_language');
		if($language->num_rows>0){
			foreach($language->result() as $language_result){
				$language_name = $language_result->language_description;
			}
		}
		return $language_name;
	}

//---------------------------- Start function to get the Client Name ------------------------------------//
	function GetUserName($user_id){
		$data = '';
		$this->db->where('id',$user_id);
		$q = $this->db->get('users');
		if($q->num_rows>0){
			foreach($q->result() as $result){
				$data = $result->full_name;
			}
		}
		return $data;
	}
//------------------------------ End function to get the Client Name ------------------------------------//
}
?>