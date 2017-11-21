<?php

class modelsite_settings extends CI_Model {
	function __construct()
	{
		parent::__construct();
	}
	
    
	
//--------------------- Start function to get all site settings details ---------------------------//	
	public function GetAllSettings()
	{
		$query = $this->db->get('site_settings');
		$data=array();
        if($query->num_rows() > 0)
		{
			foreach ($query->result() as $row)
			{
				$data[$row->field_name] = $row->field_value;
			}
			
			return $data;
		}
		else
		{
			return $data;
		}
	}
//---------------------- End function to get all site settings details ----------------------------//
	public function update_settings()
	{
		$site_settings=$this->input->post('site_settings');
		$allbox=count($site_settings);
		//echo '<pre>';print_r($site_settings);die;
		if($allbox>0)
		{
			
			foreach($site_settings as $key=>$value)
			{
				if($value!='')
				{	
					if($key=='Address'){
						$return_lat_lng = $this->modeladmin->GetLatLong($value);
						//echo $return_lat_lng;die;
						$explode_return = explode('[DIGITAL_APTECH]',$return_lat_lng);
						//echo '<pre>';print_r($explode_return);die;
						$update_lat = array('field_value' => $explode_return[0]);
						$this->db->where('field_name','latitude');
						$this->db->update('site_settings',$update_lat);
						//echo $this->db->last_query();die;
						$update_lng = array('field_value' => $explode_return[1]);
						$this->db->where('field_name','longitude');
						$this->db->update('site_settings',$update_lng);
						
					}
					$this->db->where('field_name',$key);
					$q=$this->db->get('site_settings');
					if($q->num_rows>0)
					{
						$update_settings=array('field_value' => $value);
						$this->db->where('field_name', $key);
						$query = $this->db->update('site_settings',$update_settings);

						//print_r($this->db->last_query())


							
					}
					else
					{
						$insert_settings=array(
							'field_name' => $key,
							'field_value' => $value
						);
						$query = $this->db->insert('site_settings',$insert_settings);
					}
				}
				
			}

			if($_FILES['logo']['name']!='')
			{
				//print_r($_FILES);die;
				$existing_image_path='uploads/logo/'.$this->input->post('existing_image');
				$existing_image_thumbs_path='uploads/logo/thumbs/'.$this->input->post('existing_image');
				$image_name="logo";
				$config['upload_path'] = 'uploads/logo/'; 
				$config['allowed_types'] = 'gif|jpg|jpeg|png'; 
				$config['max_size'] = '1000'; 
				
				$this->load->library('upload', $config); 
				if(!$this->upload->do_upload($image_name)) 
				{
				 	echo $this->upload->display_errors();die; 
				}
				else 
				{ 
					$fInfo = $this->upload->data();
				
					$config1 = array(
						  'source_image' => 'uploads/logo/'.$fInfo['file_name'], //get original image
						  'new_image' => 'uploads/logo/thumbs/', //save as new image //need to create thumbs first
						  'maintain_ratio' => true,
						  'width' => 150,
						  'height' => 150
						);
					$this->load->library('image_lib', $config1); //load library
					$this->image_lib->resize(); //generating thumb
					//echo $this->image_lib->display_errors(); die;
					
					unlink($existing_image_path);
					unlink($existing_image_thumbs_path);
						
					$imagename=$fInfo['file_name'];
					if($this->input->post('existing_image')!='')
					{
						$update_image_value=array('field_value' => $imagename);
						$this->db->where('field_name','logo');
						$update_image=$this->db->update('site_settings',$update_image_value);
					}
					else
					{
						$update_image_value=array('field_value' => $imagename,'field_name' => 'logo');
						
						$update_image=$this->db->insert('site_settings',$update_image_value);
					}
				}
				
				
			}
			if($query)
			{
				$this->session->set_userdata('success_msg','Site settings updated successfully');
				$return=1;
			}
			else
			{
				$this->session->set_userdata('error_msg','Fail to update site settings');
				$return=0;
			}
			
			
		}
		
		return $return;
	}
	
}
//-------------------------- Satrt function to get Admin Email -----------------------------------//
function GetAdminEmail($admin_id)
{
	$this->db->where('id',$admin_id);
	$q=$this->db->get('admin');
	if($q->num_rows>0)
	{
		foreach($q->result() as $result)
		{
			$admin_email=$result->email;
		}
	}
	return $admin_email;
}