<?php
class modelprofile extends CI_Model 
{
	
	
	function __construct()
	{
		parent::__construct();
	}
//----------------- Start function to get profile details ---------------------------//
	function GetProfileDetails()
	{
		$data=array();
		$userid=$this->session->userdata('admin_user_data');
		
		$this->db->where('id',$userid);
		$q=$this->db->get('admin');
		
		if($q->num_rows>0)
		{
			foreach ($q->result() as $result) 
			{	
				$data[]=$result;
			}
		}
		return $data;
	}
//------------------ End function to get profile details ----------------------------//

//---------------------- Start function to update profile -------------------------//
	function update()
	{
		$userid=$this->input->post('userid');
		$ctaegory_string = $features_string = '';
		if($this->input->post('categories')!=''){
			$category_string = implode(',',$this->input->post('categories'));
		}if($this->input->post('features')!=''){
			$features_string = implode(',',$this->input->post('features'));
		}
		$update=array(
			'full_name' => $this->input->post('full_name'),
			'username' => $this->input->post('username'),
			'email' => $this->input->post('email'),
			'address' => $this->input->post('address'),
			'phone_no' => $this->input->post('phone'),
			'super_user' => $this->input->post('super_user'),
			'signature' => $this->input->post('signature'),
			'categories' => $category_string,
			'fetures' => $features_string,
		);
		$this->db->where('id',$userid);
		//echo $this->db->last_query();die;
		$update_profile=$this->db->update('admin',$update);

		if($_FILES['image']['name']!='')
		{
			
			$image_name="image";
			$config['upload_path'] = 'uploads/user_image/'; 
			$config['allowed_types'] = 'gif|jpg|jpeg|png'; 
			$config['encrypt_name'] = TRUE; 
			$config['max_size'] = '*'; 
			
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
			
				$config1 = array(
				  'source_image' => 'uploads/user_image/'.$fInfo['file_name'], //get original image
				  'new_image' => 'uploads/user_image/thumb/', //save as new image //need to create thumbs first
				  'maintain_ratio' => true,
				  'width' => 150,
				  'height' => 150
				   
				);
				$this->load->library('image_lib', $config1); //load library
				$this->image_lib->resize(); //generating thumb
				//echo $this->image_lib->display_errors(); die;

				if($this->input->post('existing_image')!='')
				{
					$unlink_main_path='uploads/user_image/'.$this->input->post('existance_image');
					$unlink_thumb_path='uploads/user_image/thumb/'.$this->input->post('existance_image');
					unlink($unlink_main_path);
					unlink($unlink_thumb_path);
				}
				$imagename=$fInfo['file_name'];
				$update_image_value=array('user_image' => $imagename);
				$this->db->where('id',$userid);
				$update_image=$this->db->update('admin',$update_image_value);
			}
		}

		if($update_profile)
		{
			$this->session->set_userdata('success_msg','Your profile has been updated successfully.');
			$return=1;
		}
		else
		{
			$this->session->set_userdata('error_msg','Sorry! There is an error to update profile.');
			$return=0;
		}
		return $return;
	}
//------------------------- End function to update profile ------------------------------//
}
?>