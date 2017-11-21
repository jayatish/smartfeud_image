<?php
class model_client extends CI_Model 
{
	
	
	function __construct()
	{
		parent::__construct();
	}

    function GetTicketList($client_id,$ticket_id){
        $data = array();
        $this->db->select('status_list.status_name,status_list.status_color_codes,osc_ticket.*');
        $this->db->from('status_list');
        $this->db->from('osc_ticket');
        if($ticket_id!=''){
            $this->db->like('osc_ticket.ticket_no',$ticket_id);
        }
        $this->db->where('osc_ticket.user_id',$client_id);
        $this->db->where('osc_ticket.status_id = status_list.id');
        $this->db->order_by('osc_ticket.add_date','DESC');
        $q = $this->db->get();
        if($q->num_rows>0){
            foreach($q->result() as $result){
                $data[] = $result;
            }
        }
        return $data;
    }

    function InsertTicket(){
        $image_array = array();
        $project_unique_number = 0;
        if($this->input->post('project_id')!=''){
            $explode_project = explode('##',$this->input->post('project_id'));
            $project_unique_number = $explode_project[0];
            $this->db->select('osc_ticket.*');
            $this->db->from('osc_ticket');
            $this->db->from('osc_project');
            $this->db->where('osc_project.project_id',$explode_project[1]);
            $this->db->where('osc_project.id = osc_ticket.project_id');
            $q = $this->db->get();
            
            $total_number = $q->num_rows+1;
            $ticket_number = $explode_project[1].'-'.$total_number;
        }else{
            $ticket_number = $this->model_ticket->generate_random_password();
        }
        
        $userid = $this->session->userdata['loged_user']['login_id'] ;

        for($count=1;$count<8;$count++){
            if($_FILES['attachment_'.$count]['name']!='')
            {
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
    //echo $ticket_number;die;
        $insert_array = array(
            'ticket_no' => $ticket_number,
            'project_id' => $project_unique_number,
            'name' => $this->input->post('booker_name'),
            'email' => $this->input->post('booker_conf_email'),
            'company_id' => $this->input->post('category'),
            'priority_id' => $this->input->post('priority'),
            'subject' => $this->input->post('booking_subject'),
            'message' => $this->input->post('booking_message'),
            'problem_id' => $this->input->post('problem'),
            'status_id' => '1',
          
            'user_id'=>$userid ,
            'add_date' => date('Y-m-d H:i:s'),
            'updated_date' => date('Y-m-d H:i:s')
        );
        $insert = $this->db->insert('osc_ticket',$insert_array);
        $last_insert_id = $this->db->insert_id();
        $insert_assign_id = array(
            'ticket_id' => $last_insert_id,
            'assign_id' => '0',
            'assign_date' => date('Y-m-d H:i:s')
        );
        $this->db->insert('osc_assign_list',$insert_assign_id);
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
            $this->session->set_userdata('success_msg','Your Ticket Added Successfully.');
            $return=1;
        }
        else
        {   
            $this->session->set_userdata('error_msg','Failed to insert data. Please try again.');
            $return=0;
        }
        return $return;
    }
//----------------------------------- Start function to insert the ticket reply -----------------------------------//
  function InsertTicketreply(){
        $message = $this->input->post('reply_message') ; 
        $ticket_number_id = $this->input->post('ticket_id') ; 
        $userid = $this->session->userdata['loged_user']['login_id'] ;
        if($this->input->post('project_id')!=''){
            if($this->input->post('project_id')!=$this->input->post('pre_project_id')){
                $explode_project = explode('##',$this->input->post('project_id'));
                $project_unique_number = $explode_project[0];
                $this->db->select('osc_ticket.*');
                $this->db->from('osc_ticket');
                $this->db->from('osc_project');
                $this->db->where('osc_project.project_id',$explode_project[1]);
                $this->db->where('osc_project.id = osc_ticket.project_id');
                $q = $this->db->get();
                
                $total_number = $q->num_rows+1;
                $ticket_number = $explode_project[1].'-'.$total_number;
            }else{
                $ticket_number = $this->input->post('tracking_id');
            }
            
        }else{
            if($this->input->post('pre_project_id')!=0){
                //echo "TEST SECTION";die;
                $ticket_number = $this->model_ticket->generate_random_password();
            }else{
                $ticket_number = $this->input->post('tracking_id');
            }
            
        }
        //echo $ticket_number;die;
        for($count=1;$count<5;$count++){
            if($_FILES['attachment_'.$count]['name']!='')
            {
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
                    $fInfo = $this->upload->data(); //uploading
            //echo '<pre>';print_r($fInfo);echo '<pre>';
          
                    $image_array[] = $fInfo['file_name'];
                }
            }
        }
        $update_ticket_array['ticket_no'] = $ticket_number;
        $update_ticket_array['project_id'] = $this->input->post('project_id');
        if($message!=''){
            $update_ticket_array['status_id'] = '3';
        }

        /*$update_status = array(
            'status_id' => '3',
        );*/
        $this->db->where('id',$ticket_number_id);
        $update = $this->db->update('osc_ticket',$update_ticket_array);
        //echo $this->db->last_query();die;
        if($message!=''){
            $insert_array = array(
                'ticket_id' => $ticket_number_id,
                'sender_id' => $userid,
                'notes' => $message,
                'type' => '1',
                'add_date' => date('Y-m-d H:i:s')
            );
            $insert = $this->db->insert('osc_ticket_notes',$insert_array);
            $last_insert_id = $this->db->insert_id();
            if(count($image_array)>0){
                for($image_count=0;$image_count<count($image_array);$image_count++){
                    $image_name = $image_array[$image_count];
                    $insert_document = array(
                        'notes_id' => $last_insert_id,
                        'notes_documents' => $image_name
                    );
                    $this->db->insert('osc_ticket_notes_documents',$insert_document);
                }
            }
        }
        if($update)
        {
            $this->session->set_userdata('success_msg','Ticket update Successfully.');
            $return=1;
        }
        else
        {
            $this->session->set_userdata('error_msg','Failed to insert data. Please try again.');
            $return=0;
        }
        return $return;
    }
//------------------------------------ End function to insert the ticket reply -----------------------------------//
//------------------------------------ Start function to insert the projects -------------------------------------//
    function insert_project(){
        $project_id = $this->model_common->GetUniqueProjectId();
        $userid = $this->session->userdata['loged_user']['login_id'] ;
        $insert_array = array(
            'project_id' => $project_id,
            'client_id' => $userid,
            'project_name' => $this->input->post('project_name'),
            'project_description' => $this->input->post('description'),
            'start_date' => $this->input->post('start_date'),
            'end_date' => $this->input->post('end_date'),
            'add_date' => date('Y-m-d H:i:s'),
            'status' => '1'
        );
        $insert = $this->db->insert('osc_project',$insert_array);
        if($insert){
            $this->session->set_userdata('success_msg','Project added successfully.');
            $return=1;
        }else{
            $this->session->set_userdata('error_msg','Failed to add project. Please try again.');
            $return=0;
        }
        return $return;
    }
//--------------------------------------- End function to insert the projects ------------------------------------//
//--------------------------------- Start function to get the list of projects ------------------------------------//
    function ProjectList($client_id){
        $data = array();
        $this->db->where('client_id',$client_id);
        $this->db->where('status','1');
        $this->db->order_by('add_date','DESC');
        $q = $this->db->get('osc_project');
        if($q->num_rows>0){
            foreach($q->result() as $result){
                $data[] = $result;
            }
        }
        return $data;
    }
//---------------------------------- End function to get the list of projects -------------------------------------//
//---------------------------------- Start function to get the project details ----------------------------------------//
    function ProjectDetails($project_id){
        $data = array();
        $this->db->where('id',$project_id);
        $q = $this->db->get('osc_project');
        if($q->num_rows>0){
            foreach($q->result() as $result){
                $data[] = $result;
            }
        }
        return $data;
    }
//------------------------------------ End function to get the project details ----------------------------------------//
//-------------------------------- Start function to get the list of all assignements ------------------------------------//
    function AssignmentList($project_id){
        $data  = array();
        $this->db->select('status_list.status_name,status_list.status_color_codes,osc_ticket.*');
        $this->db->from('status_list');
        $this->db->from('osc_ticket');
        $this->db->where('osc_ticket.project_id',$project_id);
        $this->db->where('osc_ticket.status_id = status_list.id');
        $this->db->order_by('osc_ticket.ticket_no','ASC');
        $details = $this->db->get();
        if($details->num_rows>0){
            foreach($details->result() as $result){
                $data[] = $result;
            }
        }
        return $data;
    }
//--------------------------------- End function to get the list of all assignments -------------------------------------//
//------------------------------------ Start function to insert new assignment ----------------------------------------//
    function update_assignment(){
        $project_edit_id = $this->input->post('project_edit_id');
        $project_id = $this->input->post('project_id');
        $this->db->where('project_id',$project_edit_id);
        $q = $this->db->get('osc_ticket');
        $total_number = $q->num_rows+1;
        $generate_assignment_id = $project_id.'-'.$total_number;
        $userid = $this->session->userdata['loged_user']['login_id'] ;

        for($count=1;$count<8;$count++){
            if($_FILES['attachment_'.$count]['name']!='')
            {
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
    //echo $ticket_number;die;
        $insert_array = array(
            'ticket_no' => $generate_assignment_id,
            'project_id' => $project_edit_id,
            'name' => $this->input->post('booker_name'),
            'email' => $this->input->post('booker_conf_email'),
            'company_id' => $this->input->post('category'),
            'priority_id' => $this->input->post('priority'),
            'subject' => $this->input->post('booking_subject'),
            'message' => $this->input->post('booking_message'),
            'problem_id' => $this->input->post('problem'),
            'status_id' => '1',
            'user_id'=>$userid ,
            'add_date' => date('Y-m-d H:i:s'),
            'updated_date' => date('Y-m-d H:i:s')
        );
        $insert = $this->db->insert('osc_ticket',$insert_array);
        $last_insert_id = $this->db->insert_id();
        $insert_assign_id = array(
            'ticket_id' => $last_insert_id,
            'assign_id' => '0',
            'assign_date' => date('Y-m-d H:i:s')
        );
        $this->db->insert('osc_assign_list',$insert_assign_id);
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
            $this->session->set_userdata('success_msg','Your Assignment Submitted Successfully.');
            $return=1;
        }
        else
        {   
            $this->session->set_userdata('error_msg','Failed to submit assignment. Please try again.');
            $return=0;
        }
    }
//-------------------------------------- End function to insert new assignment -----------------------------------------//
}
?>