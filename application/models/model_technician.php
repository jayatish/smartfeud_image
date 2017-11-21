<?php
class model_technician extends CI_Model 
{
	
	
	function __construct()
	{
		parent::__construct();
	}

  function GetAssignList($technician_id){
    //echo $technician_id;
    $data = array();
    $this->db->select('osc_ticket.*,osc_assign_list.assign_date,status_list.status_name,status_list.status_color_codes');
    $this->db->from('osc_ticket');
    $this->db->from('osc_assign_list');
    $this->db->from('status_list');
    $this->db->where('osc_assign_list.assign_id',$technician_id);
    $this->db->where('osc_assign_list.is_accepted','1');
    $this->db->where('osc_assign_list.ticket_id = osc_ticket.id');
    $this->db->where('osc_ticket.status_id = status_list.id');
    $q = $this->db->get();
    //echo $this->db->last_query(); 
    if($q->num_rows>0){
      foreach ($q->result() as $result) {
        $data[] = $result;
      }
    }
    return $data;
  }
}
?>