<?php
 class Update{
 	protected $CI;
	private $font = 'font/monofont.ttf';
 	public function __construct($rules = array())
	{
		$this->CI =& get_instance();
		if(!$this->CI->session->userdata('user_session'))
		      $this->setSecurity(uniqid());
		$update_banner=array('status' => '0');
		$this->CI->db->where('end_date',date('Y-m-d'));
		$this->CI->db->update('partner_banner',$update_banner);
		//echo $this->CI->db->last_query();

		$this->CI->db->where('status','1');
		$this->CI->db->where('partner_id','4');
		$q=$this->CI->db->get('partner_package');
		if($q->num_rows>0){
			foreach($q->result() as $result){
				$package_slot=$result->shared_person;
				$this->CI->db->where('end_date >',date('Y-m-d'));
				$this->CI->db->where('package_id',$result->id);
				$this->CI->db->where('status','1');
				$get_value=$this->CI->db->get('partner_banner');
				$total_rows=$get_value->num_rows;
				//echo $total_rows.'@@'.$result->id.'<br/>';
				if($package_slot>$total_rows){
					$update_booked=array('is_booked' => '0');
					$this->CI->db->where('id',$result->id);
					$this->CI->db->update('partner_package',$update_booked);
				}
			}
		}
	}
	
}