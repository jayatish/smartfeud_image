<?php
class model_portfolio extends CI_Model 
{
	
	
	function __construct()
	{
		parent::__construct();
	}
//------------------------- Start function to get the users list -------------------------------//
	function GetServicesList()
	{
		$data=array();
		
		$q=$this->db->get('portfolio');
		if($q->num_rows>0)
		{
			foreach($q->result() as $result)
			{
				$data[]=$result;
			}
		}
		return $data;
	}




	function GetServiceDetails($admin_id)
	{
		$data=array();
		$this->db->where('sr_id',$admin_id);
		$q=$this->db->get('portfolio');
		if($q->num_rows>0)
		{
			foreach($q->result() as $result)
			{
				$data[]=$result;
			}
		}
		return $data;
	}

//-------------------------- End function to update users details ---------------------------------//
//------------------------- Start function to update status in users --------------------------------//
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
		$this->db->where('sr_id',$this->input->post('edit_id'));
		$update=$this->db->update('portfolio',$update_status);
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
//--------------------------- End function to update status in users ---------------------------------//
//----------------------------- Start function to delete cms details -------------------------------//
	function delete()
	{
		
		$edit_id=$this->input->post('edit_id');

		
		$sql_del = "select * from portfolio_image where cus_id = '".$edit_id."' ";
        $query_del = $this->db->query($sql_del);
		
		if($query_del->num_rows()>0)
		{

			$yeart_file = $query_del->result_array();

			foreach($yeart_file as $eachyeart_file)
			{

				$old_boat_image = $eachyeart_file['img_path'];

				$filepath_thumb = $this->config->item('file_upload_absolute_path').'uploads/fitness_image/thumb/' ;

				 $old_boat_image_thumb = 'thumb_'.substr($eachyeart_file['img_path'] ,22);
				@unlink($this->config->item('file_upload_absolute_path').$old_boat_image);

				@unlink($filepath_thumb.$old_boat_image_thumb);



			}



		}                
		

		//	die;











		$this->db->where('cus_id',$edit_id);

		$delete=$this->db->delete('portfolio_image');



		$this->db->where('sr_id',$edit_id);
		$delete=$this->db->delete('portfolio');
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





//------------------------- Start function to update status in users --------------------------------//


	public function Update()
	{

		$edit_id = $this->input->post('edit_id');
								
		$customer_name  =  $this->input->post('cus_name') ;

		$customer_desc  =  $this->input->post('cus_desc') ;

		$portfolio_name =  $this->input->post('portfolio_name') ;


		$status  =  $this->input->post('status') ;    
	
		   $data=array(
         				'cus_id'=> $customer_name ,
										
						'cus_desc'=>$customer_desc,

						'portfolio_name'=>$portfolio_name,
										
						'status'=>$status
																														
	                     );

							 $this->db->where("portfolio.sr_id",$edit_id);
                              $update  =    $this->db->update('portfolio', $data); 



				   
			//mysql_query($insert) or die(mysql_error());
			
			$fitness_id = $edit_id;
			
			 $number_of_files = sizeof($_FILES['step_image']['tmp_name']);
     
            if($number_of_files >0)
			{

			 		$files = $_FILES['step_image'];
		 
		    		// first make sure that there is no error in uploading the files
		    	   for($i=0;$i<$number_of_files;$i++)
		   			 {
					      if($_FILES['step_image']['error'][$i] != 0)
					      {
					        // save the error message and return false, the validation of uploaded files failed
					        $this->form_validation->set_message('fileupload_check', 'Couldn\'t upload the file(s)');
					        return FALSE;
					      }
		    		}
	
	

					$this->load->library('upload');

					$this->load->library('image_lib');

		    		// next we pass the upload path for the images
		    		$config['upload_path'] = $this->config->item('file_upload_absolute_path').'uploads/fitness_image';
		  		  	// also, we make sure we allow only certain type of images
		  		 	 $config['allowed_types'] = 'gif|jpg|png|jpeg';
		    		 $configUpload['encrypt_name']	= 'TRUE';
		 
		    // now, taking into account that there can be more than one file, for each file we will have to do the upload
					    for ($i = 0; $i < $number_of_files; $i++)
					    {
						      $_FILES['step_image']['name'] = $files['name'][$i];
						      $_FILES['step_image']['type'] = $files['type'][$i];
						      $_FILES['step_image']['tmp_name'] = $files['tmp_name'][$i];
						      $_FILES['step_image']['error'] = $files['error'][$i];
						      $_FILES['step_image']['size'] = $files['size'][$i];
					      
					      	//now we initialize the upload library
					     	 $this->upload->initialize($config);
					     	 if ($this->upload->do_upload('step_image'))
					    	  {
					       		 $upload_data  = $this->upload->data();

					      		  $filepath_image = 'uploads/fitness_image/'.$upload_data['file_name'];

					      		  $filepath_thumb = $this->config->item('file_upload_absolute_path').'uploads/fitness_image/thumb/' ;

		        	 $resize_conf = array(
                  							  // it's something like "/full/path/to/the/image.jpg" maybe
                   							 'source_image'  => $upload_data['full_path'], 
                    						// and it's "/full/path/to/the/" + "thumb_" + "image.jpg
                  						  // or you can use 'create_thumbs' => true option instead
                   							 'new_image'     => $filepath_thumb.'thumb_'.$upload_data['file_name'],
                   							 'width'         => 200,
                   							 'height'        => 200
                   						 );

                				// initializing
                				$this->image_lib->initialize($resize_conf);

                				$this->image_lib->resize();







						        	 $data=array(
				         				'cus_id'=> $fitness_id ,
														
										'img_path'=>$filepath_image
																											
					                     );

					        			$this->db->insert('portfolio_image',$data) ;


					     		 }
					      		else
					     		 {
					       
					      		}


					      }
			   		 }
 
				if($update)
				{
					$this->session->set_userdata('success_msg','Portfolio  updated successfully.');
					$return=1;
				}
				else
				{
					$this->session->set_userdata('error_msg','Failed to update status. Please try again.');
					$return=0;
				}
				return $return;




	}
	
//--------------------------- End function to update status in users ---------------------------------//
//----------------------------- Start function to delete cms details -------------------------------//
	


























}
?>