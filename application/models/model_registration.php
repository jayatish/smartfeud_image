<?php   if ( ! defined('BASEPATH')) exit('No direct script access allowed');


class Model_registration extends CI_model
{
 
	function __construct()
	{
		parent::__construct();
	}
	
	function InsertDetails()
	{
		$getRandomNumber=$this->model_common->GetRandomNumber();
		$insert_user=array(
			'type' => '0',
			'username' => $this->input->post('username'),
			'email' => $this->input->post('email'),
			'password' => md5($this->input->post('password')),
			'phone' => $this->input->post('phone'),
			'city' => $this->input->post('city'),
			'random_id' => $getRandomNumber,
			'super_user' => '0',
			'register_date' => date('Y-m-d H:i:s'),
			'status' => '0'
		);
		$insert=$this->db->insert('users',$insert_user);
		if($insert)
		{
			$this->session->set_userdata('front_success_msg',$this->lang->line('reg_success_msg'));
			$return=1;
		}
		else
		{
			$this->session->set_userdata('front_error_msg',$this->lang->line('reg_error_msg'));
			$return=0;
		}
		return $return;
	}
//------------------------------- Start function to user authenticate ---------------------------------------//
	function check_login()
	{
		$username=$this->input->post('login_username');
		$password=md5($this->input->post('login_password'));
		$this->db->where('username',$username);
		$this->db->where('password',$password);
		$this->db->where('type','0');
		$q=$this->db->get('users');
		//echo $this->db->last_query();die;
		if($q->num_rows>0)
		{
			foreach($q->result() as $result)
			{
				if($result->status==0)
				{
					$this->session->set_userdata('front_error_msg',$this->lang->line('login_inactive_msg'));
					$return=0;
				}
				else
				{
					$user_id=$result->id;
					$user_type=$result->type;
					$session_userdata=array(
						'user_id' => $user_id,
						'user_type' => $user_type
					);
					$this->session->set_userdata($session_userdata);
					$return=1;
				}
			}
		}
		else
		{
			$this->session->set_userdata('front_error_msg',$this->lang->line('login_error_msg'));
			$return=0;
		}
		return $return;
	}
//---------------------------------- End function to user authenticate ---------------------------------------//
//--------------------------------- Start function to insert client details -------------------------------------//
	function InsertClientDetails()
	{
		$get_birthday=date('Y-m-d', strtotime($this->input->post('birthday')));
		$getRandomNumber=$this->model_common->GetRandomNumber();
		//echo $this->input->post('gender');die;
		$insert_array=array(
			'type' => '1',
			'username' => $this->input->post('username'),
			'password' => md5($this->input->post('password')),
			'email' => $this->input->post('email'),
			'birthday' => $get_birthday,
			'gender' => $this->input->post('gender'),
			'register_date' => date('Y-m-d H:i:s'),
			'random_id' => $getRandomNumber,
			'status' => '0'
		);
		$insert=$this->db->insert('users',$insert_array);
		if($insert)
		{
			$this->session->set_userdata('front_success_msg',$this->lang->line('reg_success_msg'));
			$return=1;
		}
		else
		{
			$this->session->set_userdata('front_error_msg',$this->lang->line('reg_error_msg'));
			$return=0;
		}
		return $return;
	}
//----------------------------------- End function to insert client details -------------------------------------//
//--------------------------------- Start function to check client authentication -----------------------------------//
	function CheckClientLogin($username,$password)
	{
		$this->db->where('username',$username);
		$this->db->where('password',md5($password));
		$this->db->where('type','1');
		$q=$this->db->get('users');
		//echo $this->db->last_query();die;
		if($q->num_rows>0)
		{
			foreach($q->result() as $result)
			{
				$status=$result->status;
				if($status==0)
				{
					$this->session->set_userdata('front_header_error_msg','Profile deactivate');
					$return=0;
				}
				else
				{
					$user_id=$result->id;
					$user_type=$result->type;
					$session_userdata=array(
						'user_id' => $user_id,
						'user_type' => $user_type
					);
					$this->session->set_userdata($session_userdata);
					$return=2;
				}
			}
		}
		else
		{
			$this->session->set_userdata('front_error_msg','Invalid username / password');
			$return=1;
		}
		return $return;
	}
//----------------------------------- End function to check client authentication -----------------------------------//

	
} //end