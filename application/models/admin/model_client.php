<?php
class model_client extends CI_Model 
{
	
	
	function __construct()
	{
		parent::__construct();
	}
//------------------------- Start function to get the users list -------------------------------//
	function GetClientList()
	{
		$data=array();
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
//--------------------------- End function to get the users list -----------------------------------//

//--------------------------- Start function to get the Users Details ----------------------------//
	function GetDetails($admin_id)
	{
		$data=array();
		$this->db->where('id',$admin_id);
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
//------------------------------ End function to get the Users Details -----------------------------//


//------------------------ Start function to update users details -------------------------------//
	function Update()
	{
		$edit_id = $this->input->post('edit_id');
		$update_array = array(
			'company_name' => $this->input->post('company_name'),
			'phone_no' => $this->input->post('phone_no'),
			'address' => $this->input->post('address')
		);
		$this->db->where('id',$edit_id);
		$update_details = $this->db->update('users',$update_array);
		if($update_details)
		{

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

//----------------------------- Start function to delete cms details -------------------------------//
	function delete()
	{
		
		$edit_id=$this->input->post('edit_id');
		$this->db->where('id',$edit_id);
		$delete=$this->db->delete('users');
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