<?php
class Model_cron extends CI_Model{
	function __construct(){
		parent::__construct();
	}
	function UpdateTicket(){
		$get_start_date = date('Y-m-d H:i:s',mktime(date('H')-24,date('i'),date('s'),date('m'),date('d'),date('Y')));
		$get_end_date = date('Y-m-d H:i:s',mktime(date('H')-12,date('i'),date('s'),date('m'),date('d'),date('Y')));
		//echo $get_start_date."==".$get_end_date;
		$this->db->where("(`add_date` >= '".$get_start_date."' AND `add_date` <= '".$get_end_date."')");
		$q = $this->db->get('osc_ticket');
		//echo $this->db->last_query();die;
		//echo $q->num_rows;die;
		if($q->num_rows>0){
			//echo "TEST";die;
			foreach($q->result() as $result){
				$ticket_id = $result->id;
				//echo $ticket_id.'<br/>';
				$this->db->where('ticket_id',$ticket_id);
				$this->db->where("(`add_date` >= '".$get_start_date."' AND `add_date` <= '".$get_end_date."')");
				$notes_list = $this->db->get('osc_ticket_notes');
				//echo $this->db->last_query();
				if($notes_list->num_rows==0){
					//echo "TEST";
					$update_status = array('status_id' => '2');
					$this->db->where('id',$ticket_id);
					$this->db->update('osc_ticket',$update_status);
					//echo $this->db->last_query();
				}
			}
		}
		//die;
		return true;
	}
}
?>