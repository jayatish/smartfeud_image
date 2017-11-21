<?php   if ( ! defined('BASEPATH')) exit('No direct script access allowed');


class Model_service_customer extends CI_model
{
 
	function __construct()
	{
		parent::__construct();
	}

	function GetUserListofcustomer()
	{

		$data=array();
		//$this->db->order_by("cu_id", "desc"); 


		$q =  $this->db->query("select * from service_customer order by cu_id desc ") ;
		//echo $this->db->last_query();
		//die;
		//$q=$this->db->get('service_customer');
		if($q->num_rows>0)
		{
			foreach($q->result() as $result)
			{
				$data[]=$result;
			}
		}
		return $data;
	}


	function Getactivecustomer()
	{

		$data=array();
		$this->db->where('status','1');
		$q=$this->db->get('service_customer');
		if($q->num_rows>0)
		{
			foreach($q->result() as $result)
			{
				$data[]=$result;
			}
		}
		return $data;
	}






	function GetCustomerDetails($admin_id)
	{
		$data=array();
		$this->db->where('cu_id',$admin_id);
		$q=$this->db->get('service_customer');
		if($q->num_rows>0)
		{
			foreach($q->result() as $result)
			{
				$data[]=$result;
			}
		}
		return $data;
	}
//------------------------- Start function to update status in users --------------------------------//
	function update_status_customer()
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
		$this->db->where('cu_id',$this->input->post('edit_id'));
		$update=$this->db->update('service_customer',$update_status);
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
	function delete_customer()
	{
		
		$edit_id=$this->input->post('edit_id');


		 $sql_del = "select * from service_customer where cu_id = '".$edit_id."' ";
        $query_del = $this->db->query($sql_del);
		
		if($query_del->num_rows()>0)
		{
			$yeart_file = $query_del->result_array();
		}                
		$old_boat_image = $yeart_file[0]['c_image'];

		$old_boat_image_thumb = $yeart_file[0]['c_image_thumb'];
		@unlink($this->config->item('file_upload_absolute_path').$old_boat_image);

		@unlink($this->config->item('file_upload_absolute_path').$old_boat_image_thumb);



		$this->db->where('cu_id',$edit_id);
		$delete=$this->db->delete('service_customer');
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

	function update_customer()
	{

		$id = $this->input->post('edit_hidden');
					  
        $sql_del = "select * from service_customer where cu_id = '".$id."' ";
        $query_del = $this->db->query($sql_del);
		
		if($query_del->num_rows()>0)
		{
			$yeart_file = $query_del->result_array();
		}   

		$old_boat_image = $yeart_file[0]['c_image'];

		$old_boat_image_thumb = $yeart_file[0]['c_image_thumb'];


		if ($_FILES['c_image']['size'] != 0 && $_FILES['c_image']['name'] != "")
        {
			@unlink($this->config->item('file_upload_absolute_path').$old_boat_image);

			@unlink($this->config->item('file_upload_absolute_path').$old_boat_image_thumb);


			 $this->load->library('upload');

			 $this->load->library('image_lib');


			$configUpload['upload_path'] = $this->config->item('file_upload_absolute_path').'uploads/service_image/';
			$configUpload['allowed_types'] = 'gif|jpg|png';
			$configUpload['encrypt_name']	= 'TRUE';
			$this->upload->initialize($configUpload);
	        $this->upload->do_upload('c_image');
			$upload_data = $this->upload->data(); //Returns array of containing all of the data related to the file you uploaded.
			$filepath_image = 'uploads/service_image/'.$upload_data['file_name'];


			$config = array(
    						  'source_image'  => $upload_data['full_path'], //path to the uploaded image
    						  'new_image'     => $this->config->item('file_upload_absolute_path').'uploads/service_image/thumb/', //path to
    							'maintain_ratio'    => true,
							    'create_thumb'       => true,   
   								 'width'             => 100,
    							'height'            => 101
   											 );
 				
							//this is the magic line that enables you generate multiple thumbnails
							//you have to call the initialize() function each time you call the resize()
							//otherwise it will not work and only generate one thumbnail
   						    $this->image_lib->initialize($config);
                            $this->image_lib->resize();

                            	//print_r($upload_data) ;

                            //	die;

                            	$thumbfilename = $upload_data['raw_name'].'_thumb'.$upload_data['file_ext'] ;
                            $filepath_image_thumb = 'uploads/service_image/thumb/'.$thumbfilename;
 







        }
		else
        {
          $filepath_image = $old_boat_image;
          $filepath_image_thumb = $old_boat_image_thumb ;

        }
		
		$data=array(
			'c_name'=>$this->input->post('c_name'),
			'c_subname'=>$this->input->post('c_subname'),
			'c_desc'=>$this->input->post('c_desc'),
			'status'=>$this->input->post('status'),
			'c_image'=>$filepath_image ,
			'c_image_thumb' => $filepath_image_thumb
	    );	
		
		$this->db->where("service_customer.cu_id",$this->input->post('edit_hidden'));
	    $this->db->update('service_customer', $data);

	}
}
?>