<?php   if ( ! defined('BASEPATH')) exit('No direct script access allowed');


class Model_service extends CI_model
{
 
function __construct()
	{
		parent::__construct();
	}


public function getallservices()
{
		$this->db->select('*');
		$this->db->from('service_customer');
		
		$this->db->where('status','1'); 
		
		$query = $this->db->get();
		//echo $this->db->last_query();
		//die();
		$result = $query->result_array();
		return $result;



}

public function	getallportfolio()
{

		$this->db->select('customer.*');
		$this->db->from('customer');
		$this->db->from('portfolio');
		$this->db->where('customer.status','1'); 
		$this->db->where('customer.cu_id = portfolio.cus_id');
		$this->db->where('portfolio.status','1');
		$query = $this->db->get();
		//echo $this->db->last_query();
		//die();
		$result = $query->result_array();
		return $result;



}





	
} //end





?>