<?php
class model_other_admin extends CI_Model 
{
	
	
	function __construct()
	{
		parent::__construct();
	}
//------------------------- Start function to get the other admin details -------------------------------//
	function GetOtherAdmin()
	{
		$data=array();
		$admin_id=$this->session->userdata('admin_user_data');
		$this->db->where('id !=',$admin_id);
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

//--------------------------- End function to get the other admin details -----------------------------------//
//------------------------ Start function to insert details in Admin --------------------------//
	function Insert()
	{
		$insert=array(
			'full_name' => $this->input->post('full_name'),
			'username' => $this->input->post('username'),
			'email' => $this->input->post('email'),
			'password' => md5($this->input->post('password')),
			'phone_no' => $this->input->post('phone_no'),
			'description' => $this->input->post('description'),
			'reg_date' => date('Y-m-d H:i:s'),
			'status' => $this->input->post('status'),
		);
		$insert_details=$this->db->insert('admin',$insert);
		$last_insert_id=$this->db->insert_id();
		if($insert_details)
		{
			if($_FILES['logo']['name']!='')
			{
				
				$image_name="logo";
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

					
					
					$imagename=$fInfo['file_name'];
					$update_image_value=array('user_image' => $imagename);
					$this->db->where('id',$last_insert_id);
					$update_image=$this->db->update('admin',$update_image_value);
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
//-------------------------- End function to insert details in Admin --------------------------//
//--------------------------- Start function to get the Admin Details ----------------------------//
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
//------------------------------ End function to get the Admin Details -----------------------------//
//------------------------ Start function to update admin details -------------------------------//
	function Update()
	{
		$edit_id=$this->input->post('edit_id');
		$update=array(
			'full_name' => $this->input->post('full_name'),
			'username' => $this->input->post('username'),
			'email' => $this->input->post('email'),
			'phone_no' => $this->input->post('phone_no'),
			'description' => $this->input->post('description'),
			'status' => $this->input->post('status'),
		);
		$this->db->where('id',$edit_id);
		$update_details=$this->db->update('admin',$update);
		//$last_insert_id=$this->db->insert_id();
		if($update_details)
		{
			if($this->input->post('password')!='')
			{
				$update_pwd=array('password' => md5($this->input->post('password')));
				$this->db->where('id',$edit_id);
				$this->db->update('admin',$update_pwd);
			}
			if($_FILES['logo']['name']!='')
			{
				
				$image_name="logo";
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

					if($this->input->post('exist_image')!='')
					{
						$main_image_path='uploads/user_image/'.$this->input->post('exist_image');
						$thumb_image_path='uploads/user_image/thumb/'.$this->input->post('exist_image');
						if(file_exists($main_image_path))
						{
							unlink($main_image_path);
						}
						if(file_exists($thumb_image_path))
						{
							unlink($thumb_image_path);
						}
					}
					
					$imagename=$fInfo['file_name'];
					$update_image_value=array('user_image' => $imagename);
					$this->db->where('id',$update_details);
					$update_image=$this->db->update('admin',$update_image_value);
				}
			}
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
//-------------------------- End function to update admin details ---------------------------------//
//------------------------- Start function to update status in Admin --------------------------------//
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
//--------------------------- End function to update status in Admin ---------------------------------//
//----------------------------- Start function to delete admin details -------------------------------//
	function delete()
	{
		$user_image='';
		$edit_id=$this->input->post('edit_id');
		$this->db->where('id',$edit_id);
		$q=$this->db->get('admin');
		if($q->num_rows>0)
		{
			foreach($q->result() as $result)
			{
				$user_image=$result->user_image;
			}
		}
		if($user_image!='')
		{
			$unlink_main_path='uploads/user_image/'.$user_image;
			$unlink_thumb_path='uploads/user_image/thumb/'.$user_image;
			if(file_exists($unlink_main_path))
			{
				unlink($unlink_main_path);
			}
			if(file_exists($unlink_thumb_path))
			{
				unlink($unlink_thumb_path);
			}
		}

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
//-------------------------------- End function to delete admin details --------------------------------//
}
?>