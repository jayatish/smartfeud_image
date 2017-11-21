<?php
class model_comment extends CI_Model 
{
	
	
	function __construct()
	{
		parent::__construct();
	}
//------------------------- Start function to get the rubrik list -------------------------------//
	function GetList($type){
		$data=array();
		if($type==1){
			$table_name="girl";
		}if($type==2){
			$table_name="club";
		}
		$this->db->select('users.username,'.$table_name.'.name,comment.*');
		$this->db->from('users');
		$this->db->from($table_name);
		$this->db->from('comment');
		$this->db->where('comment.type',$type);
		$this->db->where('comment.foreign_id = '.$table_name.'.id');
		$this->db->where('comment.uid = users.id');
		$this->db->where('users.status','1');
		$this->db->order_by('add_date','DESC');
		$q=$this->db->get();
		if($q->num_rows>0){
			foreach($q->result() as $result){
				$data[]=$result;
			}
		}
		return $data;
	}

//--------------------------- End function to get the rubrik list -----------------------------------//
//--------------------------- Start function to get Details for edit ----------------------------//
	function GetDetails($type,$edit_id)
	{
		$data=array();
		if($type==1){
			$table_name="girl";
		}if($type==2){
			$table_name="club";
		}
		$this->db->select('users.username,'.$table_name.'.name,comment.*');
		$this->db->from('users');
		$this->db->from($table_name);
		$this->db->from('comment');
		$this->db->where('comment.id',$edit_id);
		$this->db->where('comment.foreign_id = '.$table_name.'.id');
		$this->db->where('comment.uid = users.id');
		$q=$this->db->get();
		if($q->num_rows>0){
			foreach($q->result() as $result){
				$data[]=$result;
			}
		}
		return $data;
	}
//------------------------------ End function to get Details for edit -----------------------------//
//----------------------------- Start function to update  details -------------------------------------//
	function update_comment()
	{
		$edit_id=$this->input->post('comment_id');
		$update_array=array(
			'comment' => $this->input->post('comment')
		);
		$this->db->where('id',$edit_id);
		$update=$this->db->update('comment',$update_array);
		if($update)
		{
			
			$this->session->set_userdata('success_msg','Comment update successfully.');
			$return=1;
		}
		else
		{
			$this->session->set_userdata('error_msg','Failed to update comment. Try again.');
			$return=0;
		}
		return $return;
	}
//-------------------------------- ENd function to update  details -------------------------------------//
//------------------------- Start function to update status --------------------------------//
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
		$update=$this->db->update('partner',$update_status);
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
//--------------------------- End function to update status ---------------------------------//
//----------------------------- Start function to delete details -------------------------------//
	function comment_delete()
	{
		$edit_id=$this->input->post('edit_id');
		$this->db->where('id',$edit_id);
		$delete=$this->db->delete('comment');
		if($delete)
		{
			$this->session->set_userdata('success_msg','Comment delete successfully.');
			$return=1;
		}
		else
		{
			$this->session->set_userdata('error_msg','Fail to delete comment. Try again.');
			$return=0;
		}
		return $return;
	}
//-------------------------------- End function to delete details --------------------------------//

}
?>