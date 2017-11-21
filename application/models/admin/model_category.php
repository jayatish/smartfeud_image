<?php
class model_category extends CI_Model 
{
	
	
	function __construct()
	{
		parent::__construct();
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
//------------------------- Start function to get the category list -------------------------------//
	
function GetCategoryList()
	{
		$data=array();
		$q=$this->db->get('categories');
		if($q->num_rows>0)
		{
			foreach($q->result() as $result)
			{
				$data[]=$result;
			}
		}
		return $data;
	}


//--------------------------- End function to get the cms list -----------------------------------//
//------------------------ Start function to insert details in cms --------------------------//
	function Insert()
	{
		
		$insert_array=array('category_name' => $this->input->post('category_name'),
			'status' => $this->input->post('status')
		);

		$insert=$this->db->insert('categories',$insert_array);
		
		$last_insert_id=$this->db->insert_id();
		
		if($last_insert_id)
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
//--------------------------- Start function to get the CMS Details ----------------------------//
	function GetDetails($admin_id)
	{
		$data=array();
		$this->db->where('id',$admin_id);
		$q=$this->db->get('categories');
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
//------------------------ Start function to update cms details -------------------------------//
	function Update()
	{
		
        $edit_id       = $this->input->post('edit_id');
		$category_name = $this->input->post('category_name');
		
		if(!empty(category_name)){
			
			$update_array=array('category_name' => $category_name);
                    
			$this->db->where('id',$edit_id);
			$this->db->update('categories',$update_array);
		}

		$this->session->set_userdata('success_msg','Data update successfully.');
		$return=1;
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
		
		$update=$this->db->update('categories',$update_status);
		
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
		$delete=$this->db->delete('categories');
		
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

	// get feture list - tushar -01-06-2016

	function  GetFeaturesList()
	{
		$data=array();
		$q=$this->db->get('features');
		if($q->num_rows>0)
		{
			foreach($q->result() as $result)
			{
				$data[]=$result;
			}
		}
		return $data;
	}


}
?>