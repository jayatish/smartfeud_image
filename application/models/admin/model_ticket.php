<?php
class model_ticket extends CI_Model 
{
	function __construct()
	{
		parent::__construct();
	}
	
//------------------------- Start function to get the cms list -------------------------------//
	function GetTicketList()
	{
		$status_value = $priority_value = '';
		$admin_id = $this->session->userdata('admin_user_data');
		$show_type_counter = '';
		if($this->input->post('status')!=''){
			for($status_count=0;$status_count<count($this->input->post('status'));$status_count++){
				$status_array[]='"'.$this->input->post('status')[$status_count].'"';
			}
			$status_value = implode(',',$status_array);
		}
		if($this->input->post('priority')!=''){
			for($priority_count=0;$priority_count<count($this->input->post('priority'));$priority_count++){
				$priority_array[]='"'.$this->input->post('priority')[$priority_count].'"';
			}
			$priority_value = implode(',',$priority_array);
		}
		if($this->input->post('assign_id')!=''){
			if(count($this->input->post('assign_id'))==1){
				if(in_array('1',$this->input->post('assign_id'))){
					$show_type_counter = 1;
				}if(in_array('2',$this->input->post('assign_id'))){
					$show_type_counter = 2;
				}if(in_array('3',$this->input->post('assign_id'))){
					$show_type_counter = 3;
				}
			}
			if(count($this->input->post('assign_id'))==2){
				if((in_array('1',$this->input->post('assign_id'))) && (in_array('2',$this->input->post('assign_id')))){
					$show_type_counter = 12;
				}if((in_array('1',$this->input->post('assign_id'))) && (in_array('3',$this->input->post('assign_id')))){
					$show_type_counter = 13;
				}if((in_array('2',$this->input->post('assign_id'))) && (in_array('3',$this->input->post('assign_id')))){
					$show_type_counter = 23;
				}
			}
		}
		$data=array();
		$this->db->select('status_list.status_name,status_list.status_color_codes,priority.type_name,priority.type_color_code,osc_ticket.*');
		$this->db->from('status_list');
		$this->db->from('priority');
		$this->db->from('osc_ticket');
		if($status_value!=''){
			$this->db->where("osc_ticket.status_id IN (".$status_value.")");
		}if($priority_value!=''){
			$this->db->where("osc_ticket.priority_id IN (".$priority_value.")");
		}if($this->input->post('company_name')!=''){
			$this->db->where("osc_ticket.company_id",$this->input->post('company_name'));
		}
		if($show_type_counter==1){
			$this->db->where('osc_ticket.assign_id',$admin_id);
		}else if($show_type_counter==2){
			$this->db->where("osc_ticket.assign_id NOT IN ('".$admin_id."','0')");
		}else if($show_type_counter==3){
			$this->db->where('osc_ticket.assign_id','0');
		}else if($show_type_counter==12){
			$this->db->where("osc_ticket.assign_id NOT IN ('0')");
		}else if($show_type_counter==13){
			$this->db->where("osc_ticket.assign_id IN ('".$admin_id."','0')");
		}else if($show_type_counter==23){
			$this->db->where("osc_ticket.assign_id NOT IN ('".$admin_id."')");
		}
		$this->db->where('osc_ticket.priority_id = priority.id');
		$this->db->where('osc_ticket.status_id = status_list.id');
		$q=$this->db->get();
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
//---------------------------- Start function to get the table details ---------------------------------//
	function GetTableDetails($table_name,$status_name){
		$data = array();
		if($status_name==1){
			$this->db->where('status','1');
		}
		$this->db->order_by('id','ASC');
		$q = $this->db->get($table_name);
		if($q->num_rows>0){
			foreach($q->result() as $result){
				$data[] = $result;
			}
		}
		return $data;
	}
//------------------------------ End function to get the table details ---------------------------------//
//--------------------------- Start function to get the list of users -------------------------------//
  function GetUserList($type){
    $data = array();
    if($type==0){
	    $this->db->where('status','1');
	    $q = $this->db->get('admin');
	    if($q->num_rows>0){
	      foreach($q->result() as $result){
	        $data[] = $result;
	      }
	    }
    }else{
    	$this->db->where('type',$type);
	    $this->db->where('status','1');
	    $q = $this->db->get('users');
	    if($q->num_rows>0){
	      foreach($q->result() as $result){
	        $data[] = $result;
	      }
	    }
    }
    return $data;
  }
//----------------------------- End function to get the list of users -------------------------------//
//------------------------ Start function to insert details in cms --------------------------//
	function Insert()
	{
		$image_array = array();
		$ticket_number = $this->model_ticket->generate_random_password();
		for($count=0;$count<5;$count++){
			if($_FILES['attachment_'.$count]['name']!=''){
		      	$image_name='attachment_'.$count;
		      	$config['upload_path'] = 'uploads/ticket_document/'; 
		      	$config['allowed_types'] = 'gif|jpg|png|zip|rar|csv|doc|docx|jpeg|xls|xlsx|txt|pdf'; 
		      	$config['encrypt_name'] = TRUE; 
		      	$config['max_size'] = '50000'; 
		      
		      	$this->load->library('upload', $config); 
		      	if(!$this->upload->do_upload($image_name)) 
		      	{
		        
		        	$this->session->set_userdata('error_msg',$this->upload->display_errors());
		        	$return=1;
		        	return $return;
		      	}
		      	else 
		      	{ 
		        	$fInfo = $this->upload->data();
		        	$image_array[] = $fInfo['file_name'];
		      	}
		    }
		}
		$this->db->where('id',$this->input->post('name'));
		$users = $this->db->get('users');
		if($users->num_rows>0){
			foreach($users->result() as $result){
				$username = $result->full_name;
				$useremail = $result->email;
				$usercompany = $result->company_name;
			}
		}
		$insert_array = array(
			'ticket_no' => $ticket_number,
			'user_id' => $this->input->post('name'),
			'name' => $username,
			'email' => $useremail,
			'company_id' => $usercompany,
			'priority_id' => $this->input->post('priority'),
			'subject' => $this->input->post('subject'),
			'message' => $this->input->post('message'),
			'problem_id' => $this->input->post('problem_id'),
			'status_id' => '1',
			'assign_id' => $this->input->post('assign_id'),
			'add_date' => date('Y-m-d H:i:s'),
			'updated_date' => date('Y-m-d H:i:s')
		);
		$insert = $this->db->insert('osc_ticket',$insert_array);
		$last_insert_id = $this->db->insert_id();
		if($insert)
		{
			if(count($image_array)>0){
				for($image_count=0;$image_count<count($image_array);$image_count++){
					$image_name = $image_array[$image_count];
					$insert_document = array(
						'ticket_id' => $last_insert_id,
						'document_name' => $image_name
					);
					$this->db->insert('osc_ticket_document',$insert_document);
				}
			}
			$insert_assign_id = array(
				'ticket_id' => $last_insert_id,
				'assign_id' => $this->input->post('assign_id'),
				'assign_date' => date('Y-m-d H:i:s')
			);
			$this->db->insert('osc_assign_list',$insert_assign_id);
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
	  }
//----------------------------- ENd function to generate the ticket ------------------------------//
//--------------------------- Start function to get the CMS Details ----------------------------//
	function GetDetails($ticket_id)
	{
		$data=array();
		$this->db->select('users.full_name,users.email as user_email,users.company_name,osc_ticket.*');
		$this->db->from('users');
		$this->db->from('osc_ticket');
		$this->db->where('osc_ticket.id',$ticket_id);
		$this->db->where('osc_ticket.user_id = users.id');
		$q=$this->db->get();
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
//--------------------------- Start function to get the list of uploaded documents --------------------------------//
	function UploadedDocument($ticket_id){
		$data = array();
		$this->db->where('ticket_id',$ticket_id);
		$q = $this->db->get('osc_ticket_document');
		if($q->num_rows>0){
			foreach($q->result() as $result){
				$data[]=$result;
			}
		}
		return $data;
	}
//----------------------------- End function to get the list of uploaded documents --------------------------------//
//------------------------ Start function to update cms details -------------------------------//
	function Update()
	{
		$edit_id=$this->input->post('edit_id');
		$update_other_part = $image_array = $update_self_owner_part = array();
		$this->db->where('id',$this->input->post('userid'));
		$users = $this->db->get('users');
		if($users->num_rows>0){
			foreach($users->result() as $result){
				$username = $result->full_name;
				$useremail = $result->email;
				$usercompany = $result->company_name;
			}
		}
		$update_array = array(
			'name' => $username,
			'email' => $useremail,
			'company_id' => $usercompany,
			'priority_id' => $this->input->post('priority'),
			'subject' => $this->input->post('subject'),
			'message' => $this->input->post('message'),
			'problem_id' => $this->input->post('problem_id'),
			//'status_id' => 6,
			//'assign_id' => $this->input->post('assign_id'),
			'updated_date' => date('Y-m-d H:i:s')
		);
		$this->db->where('id',$edit_id);
		$update=$this->db->update('osc_ticket',$update_array);
		if($update){
			$update_assign_id = array(
				'ticket_id' => $edit_id,
				'assign_id' => $this->input->post('assign_id'),
				'assign_date' => date('Y-m-d H:i:s')
			);
			$this->db->where('ticket_id',$edit_id);
			$this->db->update('osc_assign_list',$update_assign_id);

			if($this->input->post('exist_assign_id')==0){
				$update_ticket_status = array('status_id' => 6);
				$this->db->where('id',$edit_id);
				$this->db->update('osc_ticket',$update_ticket_status);
			}
			$this->session->set_userdata('success_msg','Data update successfully.');
			$return=1;
		}else{
			$this->session->set_userdata('error_msg','Failed to update data.');
			$return=1;
		}
		
		return $return;
	}
//-------------------------- End function to update cms details ---------------------------------//
//--------------------- Start function to update notes ----------------------------------------//
	function update_notes(){
		$update_other_part = $update_self_owner_part = array();
		$edit_id = $this->input->post('edit_id');
		$insert_notes = array(
			'type' => '0',
			'ticket_id' => $edit_id,
			'sender_id' => $this->session->userdata('admin_user_data'),
			'notes' => $this->input->post('notes_message'),
			'add_date' => date('Y-m-d H:i:s')
		);
		$insert_note = $this->db->insert('osc_ticket_notes',$insert_notes);
		$notes_id = $this->db->insert_id();
		for($note_attachment_count=0;$note_attachment_count<5;$note_attachment_count++){
			if($_FILES['note_attachment_'.$note_attachment_count]['name']!='')
		    {
		      
			    $note_image_name='note_attachment_'.$note_attachment_count;
			    $config1['upload_path'] = 'uploads/ticket_document/'; 
			    $config1['allowed_types'] = 'gif|jpg|png|zip|rar|csv|doc|docx|jpeg|xls|xlsx|txt|pdf'; 
			    $config1['encrypt_name'] = TRUE; 
			    $config1['max_size'] = '50000'; 
			      
			    $this->load->library('upload', $config1); 
			    if(!$this->upload->do_upload($note_image_name)) 
			    {
			        
			        $this->session->set_userdata('error_msg',$this->upload->display_errors());
			        $return=1;
			        return $return;
			    }
			    else 
			    { 
			        $note_fInfo = $this->upload->data(); 
			        $insert_note_document = array(
			        	'notes_id' => $notes_id,
			        	'notes_documents' => $note_fInfo['file_name']
			        );
			        $this->db->insert('osc_ticket_notes_documents',$insert_note_document);
			    }
		    }
		}

		if($this->input->post('notes_priority')!=''){
			$update_other_part['priority_id'] = $this->input->post('notes_priority');
		}
		if($this->input->post('close_ticket')!=''){
			$update_other_part['status_id'] = $this->input->post('close_ticket');
		}else{
			$update_other_part['status_id'] = 3;
		}
		if(count($update_other_part)>0){
			$update_other_part['updated_date'] = date('Y-m-d H:i:s');
			$this->db->where('id',$edit_id);
			$this->db->update('osc_ticket',$update_other_part);
		}
		
		if($this->input->post('self_owner')!=''){
			$update_self_owner_part['assign_id'] = $this->input->post('self_owner');
			$update_self_owner_part['assign_date'] = date('Y-m-d H:i:s');
			$this->db->where('ticket_id',$edit_id);
			$this->db->update('osc_assign_list',$update_self_owner_part);
		}

		if($insert_note){
			$this->session->set_userdata('success_msg','Notes added successfully.');
			$return=1;
		}else{
			$this->session->set_userdata('error_msg','Failed to add notes.');
			$return=0;
		}
		return $return;
	}
//----------------------- End function to update notes ----------------------------------------//
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
		$update=$this->db->update('cms',$update_status);
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
		$delete=$this->db->delete('cms');
		if($delete)
		{
			$this->db->where('static_text_id',$edit_id);
			$this->db->delete('cms_language');
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
//---------------------------- Start function to get the Client Name ------------------------------------//
	function GetUserName($user_id){
		$data = '';
		$this->db->where('id',$user_id);
		$q = $this->db->get('users');
		if($q->num_rows>0){
			foreach($q->result() as $result){
				$data = $result->full_name;
			}
		}
		return $data;
	}
//------------------------------ End function to get the Client Name ------------------------------------//
//------------------------ Start function to get the list of all reports ---------------------------------------//
	function AllNotesList($ticket_id){
		$data = array();
		$this->db->where('ticket_id',$ticket_id);
		$this->db->order_by('add_date','DESC');
		$q = $this->db->get('osc_ticket_notes');
		if($q->num_rows>0){
			foreach ($q->result() as $result) {
				$data[]=$result;
			}
		}
		return $data;
	}
//-------------------------- End function to get the list of all reports ---------------------------------------//
//----------------------------- Start function to get the user details ------------------------------------------//
	function GetSenderDetails($sender_id,$type){
		$data = array();
		if($type==0){
			$this->db->where('id',$sender_id);
			$this->db->where('status','1');
			$q = $this->db->get('admin');
		}if($type==1){
			$this->db->where('id',$sender_id);
			$this->db->where('status','1');
			$q = $this->db->get('users');
		}
		if($q->num_rows>0){
			foreach($q->result() as $result){
				$data[] = $result;
			}
		}
		return $data;
	}
//------------------------------- End function to get the user details ------------------------------------------//
//------------------------------ Start function to get the list of notes documents -------------------------------------//
	function NotesDocumentList($notes_id){
		$data = array();
		$this->db->where('notes_id',$notes_id);
		$q = $this->db->get('osc_ticket_notes_documents');
		if($q->num_rows>0){
			foreach ($q->result() as $result) {
				$data[] = $result;
			}
		}
		return $data;
	}
//-------------------------------- ENd function to get the list of notes documents -------------------------------------//
//--------------------------------- Start function to get the Assign Id -----------------------------------------//
	function GetAssingID($ticket_id){
		$assign_id = 0;
		$this->db->where('ticket_id',$ticket_id);
		$q = $this->db->get('osc_assign_list');
		if($q->num_rows>0){
			foreach($q->result() as $result){
				$assign_id = $result->assign_id;
			}
		}
		return $assign_id;
	}
//---------------------------------- End function to get the Assign Id --------------------------------------------//
//--------------------------------- Start function to get the status result --------------------------------------------//
	function StatusResult($status_id){
		$data = array();
		$this->db->where('id',$status_id);
		$q = $this->db->get('status_list');
		if($q->num_rows>0){
			foreach ($q->result() as $result) {
				$data[] = $result;
			}
		}
		return $data;
	}
//----------------------------------- End function to get the status result --------------------------------------------//
}
?>