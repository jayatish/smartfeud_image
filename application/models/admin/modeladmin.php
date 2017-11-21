<?php
class modeladmin extends CI_Model 
{
	
	
	function __construct()
	{
		parent::__construct();
	}
	
//-------------------- Start function to get all site settings details ------------------------------//
	function AllSiteSettings()
	{
		$q=$this->db->get('site_settings');
		if($q->num_rows>0)
		{
			foreach($q->result() as $result)
			{
				define(strtoupper($result->field_name), $result->field_value);
			}
		}
	}
//---------------------- End function to get all site settings details ------------------------------//
//------------------ Start function to authenticate with admin login credentials ----------------------------//
	function UserAuthenticate()
	{
		$admin_user_data='';
		$admin_user_name='';
		
		
		$this->db->where('username',$this->input->post('username'));
		$this->db->where('password',md5($this->input->post('password')));
		$this->db->where('status','1');
		$q=$this->db->get('admin');
		//echo $this->db->last_query();die;
		
		if($q->num_rows>0)
		{
			foreach ($q->result() as $result) 
			{
				$admin_user_data=$result->id;
				$admin_user_name=$result->username;
			}
			//echo $this->db->last_query();die;
			//echo $school_id.'<br/>'.$school_type;die;
			$message=array(
				'admin_user_data' => $admin_user_data,
				'admin_user_name' => $admin_user_name,
				
			);
			//echo '<pre>';print_r($message);die;
			$this->session->set_userdata($message);
			$return=1;
		}
		else
		{
			$return=0;
		}
		return $return;
	}
//------------------ End function to authenticate with admin login credentials ----------------------------//

//------------------ Start function to get user details -----------------------------------//
	function User_Details()
	{
		$userid=$this->session->userdata('admin_user_data');
		$usertype=$this->session->userdata('admin_user_type');
		//echo $usertype;die;

		$this->db->where('id',$userid);
		$q=$this->db->get('admin');
		
		//echo $this->db->last_query();die;
		if($q->num_rows>0)
		{
			foreach($q->result() as $result)
			{
				//if(($usertype=='Super_Admin') || ($usertype=='Admin'))
				//{
					if($result->user_image!='')
					{
						$image_path='uploads/user_image/thumb/'.$result->user_image;
						if(file_exists($image_path))
						{
							$user_image=base_url().'uploads/user_image/thumb/'.$result->user_image;
						}
						else
						{
							$user_image=base_url().'assets/images/no-user.png';
						}
					}
					else
					{
						$user_image=base_url().'assets/images/no-user.png';
					}
					$username=$result->full_name;
				//}
				
				$data=array(
					'admin_image' => $user_image,
					'admin_name' => $username
				);
			}
		}
		return $data;
	}
//-------------------- End function to get user details ------------------------------------//
//-------------------- Start function to update password -------------------------------//
	function update_password()
	{
		
		$userid=$this->input->post('userid');
		$new_password=$this->input->post('new_password');
		$update_array=array('password' => md5($new_password));
		$this->db->where('id',$userid);
		$q=$this->db->update('admin',$update_array);
		if($q)
		{
			$this->session->set_userdata('success_msg','Your password update successfully.');
			$return=1;
		}
		else
		{
			$this->session->set_userdata('error_msg','There is an error to update password. Please try again.');
			$return=0;
		}
		return $return;
	}
//----------------------- End function to update password ---------------------------------//


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
//--------------------------- End function to get latitude and longitude from an address ---------------------------------//
//---------------------------------- Start function to get User Details -------------------------------------//
	function GetUserDetails($user_id)
	{
		$data=array();
		$this->db->where('id',$user_id);
		$q=$this->db->get('users');
		if($q->num_rows>0)
		{
			foreach($q->result() as $result)
			{
				$data[]=$result;
			}
		}
		return $data;
	}
//------------------------------------ End function to get User Details --------------------------------------//
//--------------------------------- Start function to get All club type ----------------------------------------//
	function AllClubType()
	{
		$data=array();
		$this->db->where('status','1');
		$q=$this->db->get('club_type');
		if($q->num_rows>0)
		{
			foreach($q->result() as $result)
			{
				$data[]=$result;
			}
		}
		return $data;
	}
//------------------------------------ End function to get All club Type ------------------------------------------//
//--------------------------------- Start function to get All region ----------------------------------------//
	function AllRegion()
	{
		$data=array();
		$this->db->where('status','1');
		$q=$this->db->get('region');
		if($q->num_rows>0)
		{
			foreach($q->result() as $result)
			{
				$data[]=$result;
			}
		}
		return $data;
	}
//------------------------------------ End function to get All region ------------------------------------------//
//------------------------------------ Start function to get Club Other Details ---------------------------------------//
	function GetClubOtherDetails($table)
	{
		$data=array();
		$this->db->where('status','1');
		$q=$this->db->get($table);
		if($q->num_rows>0)
		{
			foreach($q->result() as $result)
			{
				$data[]=$result;
			}
		}
		return $data;
	}
//-------------------------------------- End function to get Club Other Details ---------------------------------------//
//------------------------------- Start function to get all parent service list -------------------------------------//
	function GetAllParentServiceList()
	{
		$data=array();
		$this->db->where('status','1');
		$this->db->where('parent_id','0');
		$q=$this->db->get('girl_service');
		if($q->num_rows>0)
		{
			foreach($q->result() as $result)
			{
				$data[]=$result;
			}
		}
		return $data;
	}
//---------------------------------- End function to get all parent service list ----------------------------------------//
//---------------------------------- Start function to get girl details --------------------------------------------//
	function GetGirlDetails($table_name)
	{
		$data=array();
		$this->db->where('status','1');
		$q=$this->db->get($table_name);
		if($q->num_rows>0)
		{
			foreach ($q->result() as $result) {
				$data[]=$result;
			}
		}
		return $data;
	}
//------------------------------------ End function to get girl details --------------------------------------------//
//--------------------------------- Start function to get girl List --------------------------------//
	function GetClubList($uid)
	{
		$data=array();
		$this->db->where('status','1');
		$this->db->where('uid',$uid);
		$q=$this->db->get('club');
		if($q->num_rows>0)
		{
			foreach($q->result() as $result)
			{
				$data[]=$result;
			}
		}
		return $data;
	}
//---------------------------------- End function to get girl List -----------------------------------//
//-------------------------------------- Start function to get the service list --------------------------------------//
	function GetService($type)
	{
		$data=array();
		$this->db->where('parent_id',$type);
		$this->db->where('status','1');
		$q=$this->db->get('girl_service');
		if($q->num_rows>0)
		{
			foreach ($q->result() as $result) {
				$data[]=$result;
			}
		}
		return $data;
	}
//---------------------------------------- End function to get the service list ---------------------------------------//
//----------------------------------- Start function to get the gender list -------------------------------------//
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
//-------------------------------------- End function to get the gender list ------------------------------------//
//---------------------------------- Start function to get Girl Status Name ----------------------------------//
	function GetGirlStatus($status_id){
		$data='';
		$this->db->where('id',$status_id);
		$this->db->where('status','1');
		$get=$this->db->get('girl_status');
		if($get->num_rows>0)
		{
			foreach($get->result() as $result){
				$data=$result->status_name_en;
			}
		}
		return $data;
	}
//----------------------------------- End function to get Girl Status Name ----------------------------------//
//------------------------------------ Start function to get the partner name ---------------------------------------//
	function GetPartnerName($partner_id){
		$partner_name='';
		$this->db->where('id',$partner_id);
		$q=$this->db->get('partner');
		if($q->num_rows>0){
			foreach($q->result() as $result){
				$partner_name='partner_folder_'.$result->id;
			}
		}
		return $partner_name;
	}
//-------------------------------------- End function to get the partner name ---------------------------------------//
	function GetFieldName($table_name,$column_name,$id){
		$data='';
		$this->db->where('id',$id);
		$rows=$this->db->get($table_name);
		if($rows->num_rows>0){
			foreach ($rows->result() as $row_result) {
				$data=$row_result->$column_name;
			}
		}
		return $data;
	}
	function GetTableDetails($table_name='',$status=''){

		$data=array();
		if($status!=''){
			$this->db->where('status',$status);
		}
		$q=$this->db->get($table_name);
		if($q->num_rows>0){
			foreach($q->result() as $result){
				$data[]=$result;
			}
		}
		return $data;
	}

	function GetImageLogo($partner_id){
		$image_path='';
		$this->db->where('id',$partner_id);
		$q=$this->db->get('partner');
		if($q->num_rows>0){
			foreach($q->result() as $result){
				if($result->logo!=''){
					$file_path='uploads/partner/thumb/'.$result->logo;
					if(file_exists($file_path)){
						$image_path=base_url().'uploads/partner/thumb/'.$result->logo;
					}
				}
			}
		}
		return $image_path;
	}

	function GetPartnerList(){
		$data=array();
		$this->db->where('status','1');
		$q=$this->db->get('partner');
		if($q->num_rows>0){
			foreach($q->result() as $result){
				$data[]=$result;
			}
		}
		return $data;
	}

	function GetLanguageList(){
		$data=array();
		$this->db->where('status','1');
		$q=$this->db->get('language_list');
		if($q->num_rows>0){
			foreach($q->result() as $result){
				$data[]=$result;
			}
		}
		return $data;
	}

	function GetDomainName($domain_id){
		$domain_name='';
		$this->db->where('id',$domain_id);
		$q=$this->db->get('club');
		if($q->num_rows>0){
			foreach($q->result() as $result){
				$domain_name=str_replace(' ','_',$result->name);
			}
		}
		return $domain_name;
	}
}
?>