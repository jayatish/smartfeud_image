<?php   if ( ! defined('BASEPATH')) exit('No direct script access allowed');


class Model_common extends CI_model
{
 
function __construct()
	{
		parent::__construct();
	}


	function SiteSettingsDetails()
	{
		$query = $this->db->get('site_settings');
        $query2=$query->num_rows();
        if($query->num_rows>0)
        {
            foreach($query->result() as $result_row)
            {
                define(strtoupper($result_row->field_name),$result_row->field_value);
            }
        }
	}
//--------------------------- End function to get Site Settings details ------------------------//	
//------------------------ Start function to get latitude and longitude from an address ----------------------------------//
	function GetLatLong($address)
	{
		$result=file_get_contents("http://maps.googleapis.com/maps/api/geocode/json?address=".urlencode($address)."&sensor=false");
		$json_value=json_decode($result);
		// echo '<pre>';print_r($json_value);
		// echo "------------------------------<br/>";
		if($json_value->status!='OK')
		{
			$result="WRONG ADDRESS";
		}
		else
		{
			/*echo "Latitude : ".$json_value->results[0]->geometry->location->lat.'<br/>';
			echo "Longitude : ".$json_value->results[0]->geometry->location->lng.'<br/>';*/
			$result=$json_value->results[0]->geometry->location->lat.'[DIGITAL_APTECH]'.$json_value->results[0]->geometry->location->lng;
		}
		//die;
		return $result;
	}

	function GetCMS($cms_id)
	{
		$data=array();
		$this->db->where('id',$cms_id);
		//$this->db->where('status','1');
		$q=$this->db->get('cms');
		//echo $this->db->last_query();die;
		if($q->num_rows>0)
		{
			foreach($q->result() as $result)
			{
				$data[]=$result;
			}
		}
		//echo '<pre>';print_r($data);die;
		return $data;
	}
	function GetCMSLanguage($cms_id,$language_id)
	{
		$data=array();
		$this->db->where('static_text_id',$cms_id);
		$this->db->where('language_id',$language_id);
		//$this->db->where('status','1');
		$q=$this->db->get('cms_language');
		//echo $this->db->last_query();die;
		if($q->num_rows>0)
		{
			foreach($q->result() as $result)
			{
				$data[]=$result;
			}
		}
		//echo '<pre>';print_r($data);die;
		return $data;
	}
//--------------------------- End function to get cms page details --------------------------------//
//-------------------------- Start function to send email ----------------------------------------------//
	function send_email($from,$type,$to,$subject,$message_body)
	{
		$banner=$this->get_active_banner();
		$about_mortgage=$this->about_details('5','49');
		$data['body']=$message_body;
		$data['about_mortgage']=$about_mortgage;
		$data['banner_image']=$banner;
		//print_r($data);die;
		$view_page=$this->load->view('email_template',$data,TRUE);
		/*echo $from.'<br/>';
		echo $to.'<br/>';
		echo $subject.'<br/>';*/
		//echo $view_page;die;
		$this->load->library('email');

		$this->email->from($from,$type);
		$this->email->to($to); 
		
		$this->email->subject($subject);
		$this->email->message($view_page);
		if(!$this->email->send())
		{
			$return=0;
		}
		else
		{
			$return=1;
		}
		return $return;
	}
//----------------------------- End function to send email -----------------------------------------------//

	function GetGenderList()
	{
		$data=array();
		$this->db->where('status','1');
		$q=$this->db->get('gender_list');
		if($q->num_rows>0)
		{
			foreach($q->result() as $result)
			{
				$data[]=$result;
			}
		}
		return $data;
	}

	

public function chk_login($data)
    { 
	     $query = $this->db->query("SELECT * FROM `users`  where `username` = '".$data['username_login']."' AND `password` = '".md5($data['password_login'])."' AND `status` = '1' ");
		 $result['num_query'] = $query->num_rows();
		$result['result_log'] = $query->row();
		
		return $result;
	    
		  
	}

public function chk_login_technician($data)	
{

$query = $this->db->query("SELECT * FROM `admin`  where `username` = '".$data['username_login']."' AND `password` = '".md5($data['password_login'])."' AND `status` = '1'  AND type='0' ");
		 $result['num_query'] = $query->num_rows();
		$result['result_log'] = $query->row();
		
		return $result;
	    

}

public function useralldata($user_id,$table_type)
	{

		if($table_type=='technician')
		{
			$query = $this->db->query("select * from `admin` where `id` = '".$user_id."'");
		$result['num_query'] = $query->num_rows();
		$result['result_log'] = $query->row();
		return $result;
		}
	    
		if($table_type=='client')
		{
			$query = $this->db->query("select * from `users` where `id` = '".$user_id."'");


		$result['num_query'] = $query->num_rows();
		$result['result_log'] = $query->row();
		return $result;
		}
	}

public function useralldata_technician($user_id)
	{
	    
	}

	public function GetTableDetails($table_name,$status_list){
		$data = array();
		if($status_list==1){
			$this->db->where('status','1');
		}
		$q = $this->db->get($table_name);
		if($q->num_rows>0){
			foreach($q->result() as $result){
				$data[] = $result;
			}
		}
		return $data;
	}
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
//------------------------------- Start function to get the table details --------------------------------------//
	function GetTabledDetails($table_name, $column_name, $id){
	  	$data = array();
	  	$this->db->where($column_name,$id);
	  	$q = $this->db->get($table_name);
	  	//echo $this->db->last_query();
	  	if($q->num_rows>0){
	  		foreach($q->result() as $result){
	  			$data[] = $result;
	  		}
	  	}
	  	return $data;
	}
//--------------------------------- End function to get the table details --------------------------------------//
//------------------------------- Start function to count the total reply ----------------------------------------//
	function TotalReply($ticket_id){
		$this->db->where('ticket_id',$ticket_id);
		$this->db->order_by('add_date','DESC');
		$result = $this->db->get('osc_ticket_notes');
		return $result->num_rows;
	}
//--------------------------------- End function to count the total reply ----------------------------------------//
//------------------------------- Start function to get the list of all previous notes ------------------------------------//
	function PreviousNotes($table_name,$field_name,$id){
		$data = array();
		$this->db->where($field_name,$id);
		$this->db->order_by('add_date','DESC');
		$q = $this->db->get($table_name);
		//echo $this->db->last_query();
		if($q->num_rows>0){
			foreach($q->result() as $result){
				$data[] = $result;
			}
		}
		return $data;
	}
//--------------------------------- End function to get the list of all previous notes ------------------------------------//
//-------------------------------- Start function to get the user info ------------------------------------------------//
	function GetUserInfo($userid){
		$data = array();
		$this->db->where('id',$userid);
		$q = $this->db->get('users');
		if($q->num_rows>0){
			foreach($q->result() as $result){
				$data[] = $result;
			}
		}
		return $data;
	}
//---------------------------------- End function to get the user info ------------------------------------------------//
//----------------------------------- Start function to generate unique project number ---------------------------------------//
	function GetUniqueProjectId() {
		$an = "0123456789";
	    $su = strlen($an) - 1;
	    $random_number = substr($an, rand(0, $su), 1).substr($an, rand(0, $su), 1).substr($an, rand(0, $su), 1).substr($an, rand(0, $su), 1).substr($an, rand(0, $su), 1).substr($an, rand(0, $su), 1);
	    $generate_project_number = "P-".$random_number;
	    //echo $generate_project_number;die;
	    $this->db->where('project_id',$generate_project_number);
	    $q = $this->db->get('osc_project');
	    if($q->num_rows>0){
	    	$this->model_common->GetUniqueProjectId();
	    }else{
	    	return $generate_project_number;
	    }
	    //return $password;
	}
//------------------------------------- End function to generate unique project number ---------------------------------------//
//------------------------------- Strt function to get the list of projects -----------------------------------------//
	function GetClientProjectList($userid){
		$data = array();
		$this->db->where('client_id',$userid);
		$this->db->where('status','1');
		$this->db->order_by('add_date','DESC');
		$q = $this->db->get('osc_project');
		if($q->num_rows>0){
			foreach($q->result() as $result){
				$data[] = $result;
			}
		}
		return $data;
	}
//-------------------------------- End function to get the list of projects -----------------------------------------//
} //end