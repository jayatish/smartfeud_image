<?php
class model_user extends CI_Model 
{
	
	
	function __construct()
	{
		parent::__construct();
	}
//------------------------- Start function to get the users list -------------------------------//
	function GetUserList()
	{
		$data=array();
		$this->db->where('type','0');
		//$this->db->where('super_user','0');
		$q=$this->db->get('admin');
		if($q->num_rows>0)
		{
			foreach($q->result() as $result)
			{
				$data[]=$result;
			}
		}
		return $data;
	}



	

//--------------------------- End function to get the users list -----------------------------------//
//------------------------ Start function to insert details in cms --------------------------//
	function Insert()
	{
		$insert=array(
			'title_en' => $this->input->post('title_en'),
			'title_ge' => $this->input->post('title_ge'),
			'description_en' => $this->input->post('description_en'),
			'description_ge' => $this->input->post('description_ge'),
			'added_date' => date('Y-m-d'),
			'status' => $this->input->post('status'),
		);
		$insert_details=$this->db->insert('cms',$insert);
		$last_insert_id=$this->db->insert_id();
		if($insert_details)
		{
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
//--------------------------- Start function to get the Users Details ----------------------------//
	function GetDetails($admin_id)
	{
		$data=array();
		$this->db->where('id',$admin_id);
		$q=$this->db->get('admin');
		if($q->num_rows>0)
		{
			foreach($q->result() as $result)
			{
				$data[]=$result;
			}
		}
		return $data;
	}
//------------------------------ End function to get the Users Details -----------------------------//
//------------------------------- Start function to get Canton Name -------------------------------//
	function GetCity($city_id)
	{
		$data='';
		$this->db->where('id',$city_id);
		$q=$this->db->get('cities');
		if($q->num_rows>0)
		{
			foreach($q->result() as $result)
			{
				$data=$result->city_name;
			}
		}
		return $data;
	}
//----------------------------------- End function to get Canton Name ------------------------------//
//------------------------------- Start function to send gender details -------------------------------------//
	function GetGender($gender_id)
	{
		$gender_name='';
		$this->db->where('id',$gender_id);
		$q=$this->db->get('gender_list');
		if($q->num_rows>0)
		{
			foreach($q->result() as $result)
			{
				$gender_name=$result->gender_name_en;
			}
		}
		return $gender_name;
	}
//---------------------------------- End function to send gender details -------------------------------------//


function isEmailExist($email)
{
      $this->db->select('id');
    $this->db->where('email', $email);
    $query = $this->db->get('admin');

    if ($query->num_rows() > 0) {
        return true;
    } else {
        return false;
    }
}





function isUserNameExist($username)
{
      $this->db->select('id');
    $this->db->where('username', $username);
    $query = $this->db->get('admin');

    if ($query->num_rows() > 0) {
        return true;
    } else {
        return false;
    }
}








//------------------------ Start function to update users details -------------------------------//
	function Update()
	{
		 $edit_id  = $this->input->post('edit_id');
		 $user_email =  $this->input->post('user_email');
		
		$password = md5($this->input->post('password'));

		 $super_user =  $this->input->post('super_user');
	
		$categories =  $this->input->post('categories');

		$features =  $this->input->post('features');

		 $address =  $this->input->post('address');
         $phone_no =  $this->input->post('phone_no');

					foreach($categories as $checkcategories) 
					{
           					

						$checkcategorieselements[] =$checkcategories ;

    				}
    					 $allcats = implode(',', $checkcategorieselements);
    				
					

   				 foreach($features as $checkfeatures)
   					  {
         				 

   					  	$checkfeatureselements[] =$checkfeatures ;
   					 }

   					 	 $allfeatures = implode(',', $checkfeatureselements);



					if(!empty($password))
       					 {

       					 	if($super_user=='1')
                   				{

                   					$update=array(
												'full_name'  => $this->input->post('full_name'),
												'username'   => $this->input->post('username'),
												'email'		=> $user_email ,
												'address'    => $address ,
                   									'phone_no'   => $phone_no ,
												'password'   => md5($this->input->post('password')), 
												'fetures'	 => "" ,
								                'categories' => "" ,
												'super_user' => $this->input->post('super_user'),
												'signature'  => $this->input->post('signature'),			
												'status'     => $this->input->post('status')
											);
                   				}
                   				else
                   				{
                   					$update=array(
												'full_name'  => $this->input->post('full_name'),
												'username'   => $this->input->post('username'),
												'email'		=> $user_email ,
												'address'    => $address ,
                   								'phone_no'   => $phone_no ,
												'password'   => md5($this->input->post('password')),
												'fp'   => $this->input->post('password'), 
												'fetures'	 => $allfeatures ,
                								'categories' => $allcats ,
												'super_user' => $this->input->post('super_user'),
												'signature'  => $this->input->post('signature'),			
												'status'     => $this->input->post('status')
											);
                   				}	

       					 }
       					 else
       					 {


       					 	if($super_user=='1')
                   				{

                   					$update=array(
												'full_name'  => $this->input->post('full_name'),
												'username'   => $this->input->post('username'),
												 'email'		=> $user_email ,
												 'address'    => $address ,
                   									'phone_no'   => $phone_no ,
												'fetures'	 => "" ,
								                'categories' => "" ,
												'super_user' => $this->input->post('super_user'),
												'signature'  => $this->input->post('signature'),			
												'status'     => $this->input->post('status')
											);
                   				}
                   				else
                   				{

                   					$update=array(
												'full_name'  => $this->input->post('full_name'),
												'username'   => $this->input->post('username'),
												 'email'		=> $user_email ,
												 'address'    => $address ,
                   									'phone_no'   => $phone_no ,
												'fetures'	 => $allfeatures ,
                								'categories' => $allcats ,
												'super_user' => $this->input->post('super_user'),
												'signature'  => $this->input->post('signature'),			
												'status'     => $this->input->post('status')
											);

                   					

                   				}	




       					 }	

       					 	$this->db->where('id',$edit_id);
		$update_details=$this->db->update('admin',$update);
		//$last_insert_id=$this->db->insert_id();
		if($update_details)
		{
			
			/*if($this->input->post('password')!='')
			{
				$update_pwd=array('password' => md5($this->input->post('password')));
				$this->db->where('id',$edit_id);
				$update=$this->db->update('users',$update_pwd);
			}*/


			$this->session->set_userdata('success_msg','Data updated successfully.');
			$return=1;
		}
		else
		{
			$this->session->set_userdata('error_msg','Failed to update data. Please try again.');
			$return=0;
		}
		return $return;







   					 
	}
//-------------------------- End function to update users details ---------------------------------//
//------------------------- Start function to update status in users --------------------------------//
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
		$update=$this->db->update('admin',$update_status);
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
//--------------------------- End function to update status in users ---------------------------------//
//----------------------------- Start function to delete cms details -------------------------------//
	function delete()
	{
		
		$edit_id=$this->input->post('edit_id');
		$this->db->where('id',$edit_id);
		$delete=$this->db->delete('admin');
		if($delete)
		{
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

}
?>