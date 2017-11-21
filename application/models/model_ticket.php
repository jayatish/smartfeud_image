<?php
class model_ticket extends CI_Model 
{
	
	
	function __construct()
	{
		parent::__construct();
	}
	

	function Insert()
	{
		$image_array = array();
    $ticket_number = $this->model_ticket->generate_random_password();
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
            $fInfo = $this->upload->data(); //uploading
            //echo '<pre>';print_r($fInfo);echo '<pre>';
          
            $image_array[] = $fInfo['file_name'];
          }
        }
    }
    //echo $ticket_number;die;
    $insert_array = array(
      'ticket_no' => $ticket_number,
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






    //echo $this->db->last_query();die;
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
	    //return $password;
	  }
//----------------------------- ENd function to generate the ticket ------------------------------//


    function insert_ticketreply()
    {

         $message = $this->input->post('reply_message') ; 
         $email = $this->input->post('token') ; 
           $ticket_number = $this->input->post('orig_track') ; 

          

        $userid = $this->session->userdata['loged_user']['login_id'] ;

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



$query_ticket ="UPDATE osc_ticket SET `status_id` = '3' WHERE `osc_ticket`.`id` ='".$ticket_number."'";

   $this->db->query($query_ticket);




   $insert_array = array(
      'ticket_id' => $ticket_number,
      
      'sender_id' => $userid,
      
      'notes' => $message,
      
      'type' => '1',
      
      'add_date' => date('Y-m-d H:i:s')
      
    );

   //print_r($insert_array);
   //die;
    $insert = $this->db->insert('osc_ticket_notes',$insert_array);
    $last_insert_id = $this->db->insert_id();
    //echo $this->db->last_query();die;
    if($insert)
    {
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
      $this->session->set_userdata('success_msg','Your Reply Send Successfully.');
      $return=1;
    }
    else
    {
      $this->session->set_userdata('error_msg','Failed to insert data. Please try again.');
      $return=0;
    }
    return $return;









    }

    public function LastReplier($ticket_id){
      $data_name = '';
      $this->db->where('ticket_id',$ticket_id);
      $this->db->order_by('add_date','DESC');
      $this->db->limit(1,0);
      $q = $this->db->get('osc_ticket_notes');
      if($q->num_rows>0){
        foreach($q->result() as $result){
          if($result->type==0){
            $this->db->where('id',$result->sender_id);
            $users = $this->db->get('admin');
            if($users->num_rows>0){
              foreach($users->result() as $user_result){
                $data_name = $user_result->full_name;
              }
            }
          }
          if($result->type==1){
            $this->db->where('id',$result->sender_id);
            $users = $this->db->get('users');
            if($users->num_rows>0){
              foreach($users->result() as $user_result){
                $data_name = $user_result->full_name;
              }
            }
          }
        }
      }
      return $data_name;
    }

    public function submit_ticketreply()
    {

        $message = $this->input->post('reply_message') ; 
         
           $ticket_number = $this->input->post('orig_track') ; 

          

        $userid = $this->session->userdata['loged_user']['login_id'] ;

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








   $insert_array = array(
      'ticket_id' => $ticket_number,
      
      'sender_id' => $userid,
      
      'notes' => $message,
      
      'type' => '0',
      
      'add_date' => date('Y-m-d H:i:s')
      
    );

   //print_r($insert_array);
   //die;
    $insert = $this->db->insert('osc_ticket_notes',$insert_array);
    $last_insert_id = $this->db->insert_id();
    //echo $this->db->last_query();die;
    if($insert)
    {
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
      $this->session->set_userdata('success_msg','Your Reply Send Successfully.');
      $return=1;
    }
    else
    {
      $this->session->set_userdata('error_msg','Failed to insert data. Please try again.');
      $return=0;
    }
    return $return;




    }


    public function insert_rfp()
    {

      $company_name = $this->input->post('company_name') ; 
      $company_address = $this->input->post('company_address') ;
      $company_contact = $this->input->post('company_contact') ;
      $company_phone_number = $this->input->post('company_phone_number') ;
      $contact_email = $this->input->post('contact_email') ;

      $work_title = $this->input->post('work_title') ; 
      $rfp_deadline = $this->input->post('rfp_deadline') ; 

      $work_scope = $this->input->post('work_scope') ; 
      $special_consideration = $this->input->post('special_consideration') ;    

      $userid = $this->session->userdata['loged_user']['login_id'] ;


  for($count=9;$count<13;$count++){
      if($_FILES['attachment_'.$count]['name']!='')
        {
          
          $image_name='attachment_'.$count;
          $config['upload_path'] = 'uploads/quote_document/'; 
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


   $insert_array = array(
      'company_name' => $company_name,
      
      'company_address' => $company_address,
      
      'company_contact' => $company_contact,

      'company_phone_number' => $company_phone_number,
      
      'contact_email' => $contact_email,
      
      'work_title' => $work_title,
      
      'rfp_deadline' => $rfp_deadline,
      
      'work_scope' => $work_scope,
      
      'special_consideration' => $special_consideration,
      
      'user_id' => $userid ,
      
      'add_date' => date('Y-m-d H:i:s')
      
    );

   //print_r($insert_array);
   //die;
    $insert = $this->db->insert('osc_quote',$insert_array);
    $last_insert_id = $this->db->insert_id();
    //echo $this->db->last_query();die;
    if($insert)
    {
      if(count($image_array)>0){
        for($image_count=0;$image_count<count($image_array);$image_count++){
          $image_name = $image_array[$image_count];
          $insert_document = array(
            'quote_id' => $last_insert_id,
            'document_name' => $image_name
          );
          $this->db->insert('osc_quote_document',$insert_document);
        }
      }
      $this->session->set_userdata('success_msg','Your Quotation Saved Successfully.');
      $return=1;
    }
    else
    {
      $this->session->set_userdata('error_msg','Failed to insert data. Please try again.');
      $return=0;
    }
    return $return;












    }



public function insert_rfp_responsive()
{

    $company_name = $this->input->post('company_name_responsive') ; 
      $company_address = $this->input->post('company_address_responsive') ;
      $company_contact = $this->input->post('company_contact_responsive') ;
      $company_phone_number = $this->input->post('company_phone_number_responsive') ;
      $contact_email = $this->input->post('contact_email_responsive') ;

      $work_title = $this->input->post('work_title_responsive') ; 
      $rfp_deadline = $this->input->post('rfp_deadline_responsive') ; 

      $work_scope = $this->input->post('work_scope_responsive') ; 
      $special_consideration = $this->input->post('special_consideration_responsive') ;    

      $userid = $this->session->userdata['loged_user']['login_id'] ;


  for($count=9;$count<13;$count++){
      if($_FILES['attachment_responsive_'.$count]['name']!='')
        {
          
          $image_name='attachment_responsive_'.$count;
          $config['upload_path'] = 'uploads/quote_document/'; 
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


   $insert_array = array(
      'company_name' => $company_name,
      
      'company_address' => $company_address,
      
      'company_contact' => $company_contact,

      'company_phone_number' => $company_phone_number,
      
      'contact_email' => $contact_email,
      
      'work_title' => $work_title,
      
      'rfp_deadline' => $rfp_deadline,
      
      'work_scope' => $work_scope,
      
      'special_consideration' => $special_consideration,
      
      'user_id' => $userid ,
      
      'add_date' => date('Y-m-d H:i:s')
      
    );

   //print_r($insert_array);
   //die;
    $insert = $this->db->insert('osc_quote',$insert_array);
    $last_insert_id = $this->db->insert_id();
    //echo $this->db->last_query();die;
    if($insert)
    {
      if(count($image_array)>0){
        for($image_count=0;$image_count<count($image_array);$image_count++){
          $image_name = $image_array[$image_count];
          $insert_document = array(
            'quote_id' => $last_insert_id,
            'document_name' => $image_name
          );
          $this->db->insert('osc_quote_document',$insert_document);
        }
      }
      $this->session->set_userdata('success_msg','Your Quotation Saved Successfully.');
      $return=1;
    }
    else
    {
      $this->session->set_userdata('error_msg','Failed to insert data. Please try again.');
      $return=0;
    }
    return $return;



}



public function GetTicket($ticketid)
{

$t_id = $ticketid ;

$query_ticket =  $this->db->query("select * from osc_ticket where id='".$t_id."'  ");
                                                 

$row = $query_ticket->result_array() ;

 
 return $row ;

}

  public function GetTicketNotes($ticket_id){
    $data = array();
    $this->db->where('ticket_id',$ticket_id);
    $this->db->order_by('add_date','DESC');
    $q = $this->db->get('osc_ticket_notes');
    //echo $this->db->last_query();
    if($q->num_rows>0){
      foreach($q->result() as $result){
        $data[]= $result;
      }
    }
    return $data;
  }

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
//-------------------------------- Start function to get the list of CMS ------------------------------------------//
  function CMSDetails($cms_id){
    $data = array();
    $this->db->where('id',$cms_id);
    $q=$this->db->get('cms');
    if($q->num_rows>0){
      foreach($q->result() as $result){
        $data[] = $result;
      }
    }
    return $data;
  }
//---------------------------------- End function to get the list of CMS ------------------------------------------//

}
?>