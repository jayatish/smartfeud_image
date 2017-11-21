<?php   if ( ! defined('BASEPATH')) exit('No direct script access allowed');


class Model_api extends CI_model
{
 
	function __construct()
	{
		parent::__construct();

		//echo '<pre>';print_r($login);
		
	}
	function check_authenticate(){
		$return = array('user_id' => $_SESSION['user_id'],'user_type' => $_SESSION['user_type']);
		return $return;
	}
	function checkLogin(){
		$login = check_authenticate();
		//echo '<pre>';print_r($login);
		$user_id = $login['user_id'];
		$user_type = $login['user_type'];
		$data = array('user_id' => $user_id,'user_type' => $user_type);
		return $data;
	}
	function UpdateBanner($details){
		$partner_id = $details['partner_id'];
		$update_banner=array('status' => '0');
		$this->db->where('end_date',date('Y-m-d'));
		$this->db->update('partner_banner',$update_banner);
		//echo $this->CI->db->last_query();

		$this->db->where('status','1');
		$this->db->where('partner_id',$partner_id);
		$q=$this->db->get('partner_package');
		if($q->num_rows>0){
			foreach($q->result() as $result){
				$package_slot=$result->shared_person;
				$this->db->where('end_date >',date('Y-m-d'));
				$this->db->where('package_id',$result->id);
				$this->db->where('status','1');
				$get_value=$this->db->get('partner_banner');
				$total_rows=$get_value->num_rows;
				//echo $total_rows.'@@'.$result->id.'<br/>';
				if($package_slot>$total_rows){
					$update_booked=array('is_booked' => '0');
					$this->db->where('id',$result->id);
					$this->db->update('partner_package',$update_booked);
				}
			}
		}
		$return = array('success' => 'true');
		return $return;
	}
	function GetSiteSettings($data){
		$image_small_url=$image_big_url=''; 
		$details=array();
		$partner_id = $data['partner_id'];
		$this->db->where('id',$partner_id);
		$q=$this->db->get('partner');
		if($q->num_rows>0){
			foreach($q->result() as $result){
				if($result->logo!=''){
					$image_small_path='uploads/partner/thumb/'.$result->logo;
					$image_big_path='uploads/partner/'.$result->logo;
					if(file_exists($image_small_path)){
						$image_small_url=base_url().'uploads/partner/thumb/'.$result->logo;
					}if(file_exists($image_big_path)){
						$image_big_url=base_url().'uploads/partner/'.$result->logo;
					}
				}
				$details[]=array(
					'id' => $result->id,
					'site_name' => $result->site_name,
					'site_url' => $result->site_url,
					'admin_email' => $result->admin_email,
					'phone' => $result->phone,
					'amount_per_girl' => $result->amount_girl,
					'amount_per_club' => $result->amount_club,
					'facebook_link' => $result->facebook,
					'twitter_link' => $result->twitter,
					'small_image' => $image_small_url,
					'big_image' => $image_big_url 
				);
			}
		}
		if(count($details)>0){
			$result=array('success' => 'true','result' => $details);
		}else{
			$result=array('success' => 'false','result' => $details);
		}
		return $result;
	}
	function partner_list(){
		$login = is_login();
		$user_id = $login['user_id'];
		$user_type = $login['user_type'];
		$data=array();
		$this->db->where('status','1');
		$q=$this->db->get('partner');
		if($q->num_rows>0){
			foreach($q->result() as $keys => $result){
				if($result->logo!=''){
					$image_small_path='uploads/partner/thumb/'.$result->logo;
					$image_big_path='uploads/partner/'.$result->logo;
					if(file_exists($image_small_path)){
						$image_small_url=base_url().'uploads/partner/thumb/'.$result->logo;
					}if(file_exists($image_big_path)){
						$image_big_url=base_url().'uploads/partner/'.$result->logo;
					}
				}
				$data[]=array(
					'id' => $result->id,
					'site_name' => $result->site_name,
					'site_url' => $result->site_url,
					'admin_email' => $result->admin_email,
					'phone' => $result->phone,
					'amount_per_girl' => $result->amount_girl,
					'amount_per_club' => $result->amount_club,
					'facebook_link' => $result->facebook,
					'twitter_link' => $result->twitter,
					'small_image' => $image_small_url,
					'big_image' => $image_big_url
				);
				
			}
		}
		//echo '<pre>';print_r($data);
		/*for($i=0;$i<count($data);$i++){
			echo '<pre>';$data[$i];
		}*/
		if(count($data)>0){
			$return=array('succes' => 'true','user_id' => $user_id,'user_type' => $user_type,'result' => $data);
		}else{
			$return=array('succes' => 'false','user_id' => $user_id,'user_type' => $user_type,'result' => $data);
		}
		return $return;
	}

	function banner_list($partner_id){
		$login = is_login();
		$user_id = $login['user_id'];
		$user_type = $login['user_type'];
		$data=$package_array=$banner_array=array();
		$partner_folder_name=$this->model_common->GetPartnerName($partner_id);
		$this->db->where('partner_id',$partner_id);
		$this->db->where('status','1');
		$position=$this->db->get('partner_position');
		if($position->num_rows>0){
			foreach($position->result() as $position_result){

				
				$this->db->where('partner_id',$partner_id);
				$this->db->where('position_id',$position_result->id);
				$this->db->where('status','1');
				$package=$this->db->get('partner_package');
				if($package->num_rows>0){
					foreach($package->result() as $package_result){
						if($package_result->package_image!=''){
							$image_small_path='partner/'.$partner_folder_name.'/package/thumb/'.$package_result->package_image;
							$image_big_path='partner/'.$partner_folder_name.'/package/'.$package_result->package_image;
							if(file_exists($image_small_path)){
								$small_image_url=base_url().'partner/'.$partner_folder_name.'/package/thumb/'.$package_result->package_image;
							}else{
								$small_image_url=base_url().'uploads/Pic_comin_soon.jpg';
							}
							if(file_exists($image_big_path)){
								$big_image_url=base_url().'partner/'.$partner_folder_name.'/package/'.$package_result->package_image;
							}else{
								$big_image_url=base_url().'uploads/Pic_comin_soon.jpg';
							}
						}
						$banner_array=array();
						$this->db->select('partner_banner.*');
						$this->db->from('partner_banner');
						$this->db->from('users');
						$this->db->where('partner_banner.package_id',$package_result->id);
						$this->db->where('partner_banner.partner_id',$partner_id);
						$this->db->where('partner_banner.uid = users.id');
						$this->db->where('users.status','1');
						$this->db->where("(partner_banner.start_date <= '".date('Y-m-d')."' AND partner_banner.end_date > '".date('Y-m-d')."')");
						$this->db->order_by('partner_banner.start_date','ASC');
						$banner_list=$this->db->get();
						if($banner_list->num_rows>0){
							foreach($banner_list->result() as $banner_list__result){
								if($banner_list__result->banner_image!=''){
									$banner_image_small_path='partner/'.$partner_folder_name.'/banner/thumb/'.$banner_list__result->banner_image;
									$banner_image_big_path='partner/'.$partner_folder_name.'/banner/'.$banner_list__result->banner_image;
									if(file_exists($banner_image_small_path)){
										$banner_small_image_url=base_url().'partner/'.$partner_folder_name.'/package/thumb/'.$banner_list__result->banner_image;
									}else{
										$banner_small_image_url=base_url().'uploads/Pic_comin_soon.jpg';
									}
									if(file_exists($banner_image_big_path)){
										$banner_big_image_url=base_url().'partner/'.$partner_folder_name.'/banner/'.$banner_list__result->banner_image;
									}else{
										$banner_big_image_url=base_url().'uploads/Pic_comin_soon.jpg';
									}
								}
								$left_link='http://';
						        if(strpos($banner_list__result->banner_link,$left_link)!=false){
						            $get_left_link=$banner_list__result->banner_link;
						        }else{
						            $get_left_link=$left_link.$banner_list__result->banner_link;
						        }
								$banner_array[]=array(
									'id' => $banner_list__result->id,
									'banner_link' => $get_left_link,
									'start_date' => $banner_list__result->start_date,
									'end_date' => $banner_list__result->end_date,
									'small_image' => $banner_small_image_url,
									'big_image' => $banner_big_image_url
								);
							}
						}

						//echo '<pre>';print_r($banner_array);
						$package_array[]=array(
							'id' => $package_result->id,
							'package_name' => $package_result->package_name,
							'slot_number' => $package_result->shared_person,
							'banner_size' => $package_result->banner_size,
							'amount' => $package_result->amount,
							'duration' => $package_result->duration,
							'small_image' => $small_image_url,
							'big_image' => $big_image_url,
							'banner_list' => $banner_array
						);

						unset($banner_array);
					}
				}

				$data[]=array(
					'id' => $position_result->id,
					'position_name' => $position_result->position,
					'package' => $package_array
				);
				unset($package_array);
			}
		}
		$return=array('user_id' => $user_id,'user_type' => $user_type,'result' => $data);
		return $return;
	}

	function girl_list($partner_id){
		

		$this->db->select('partner_girl.*');
		$this->db->from('partner_girl');
		$this->db->from('users');
		$this->db->from('girl');
		$this->db->where('partner_girl.partner_id',$partner_id);
		$this->db->where('partner_girl.main_girl_id = girl.id');
		$this->db->where("partner_girl.status NOT IN ('3','7')");
		$this->db->where("partner_girl.show_status",'1');
		$this->db->where("partner_girl.uid = users.id");
		$this->db->where('users.status','1');
		$this->db->order_by('partner_girl.partner_position','ASC');
		$count=$this->db->get();
		$total_record=$count->num_rows;
		$result=array('success' => 'true','total_record' => $total_record);
		return $result;
	}

	function girl_record($partner_id,$start_index,$per_page_value,$language_id){
		$data=array();
		$slogan=$zip_city=$region_name='';
		$partner_folder_name=$this->model_common->GetPartnerName($partner_id);

		$this->db->select('partner_girl.*');
		$this->db->from('partner_girl');
		$this->db->from('users');
		$this->db->from('girl');
		$this->db->where('partner_girl.partner_id',$partner_id);
		$this->db->where('partner_girl.main_girl_id = girl.id');
		$this->db->where("partner_girl.status NOT IN ('3','7')");
		$this->db->where("partner_girl.show_status",'1');
		$this->db->where("partner_girl.uid = users.id");
		$this->db->where('users.status','1');
		$this->db->order_by('partner_girl.partner_position','ASC');
		$this->db->limit($per_page_value,$start_index);
		$q=$this->db->get();
		//echo $this->db->last_query();
		if($q->num_rows>0){
			foreach($q->result() as $result){
				$get_girl_image=$this->partner_model->GetPartnerGirlLatestImage($partner_folder_name,$result->id);
				if($result->slogan!=''){
                	$slogan.=substr($result->slogan,0,25);
	                if(strlen($result->slogan)>25){
	                  $slogan.="...";
	                }
              	}else{
                	$slogan.="&nbsp;";
              	}
              	if($result->girl_type==2){
              		if($result->zip_city!=''){
                      $zip_city=$result->zip_city;
                    }else{
                      $zip_city="No information available";
                    }

                    if($result->region!=0){
                      //$region_name=$this->model_common->GetRegionName($result->region);
                      $region_name=$this->model_common->GetSpecificLanguage('region_language','language_text',$result->region,$language_id);
                    }else{
                      $region_name="No Information Available";
                    }
              	}if($result->girl_type==1){
              		//echo $result->main_girl_id.'<br/>';
              		$girl_club_details=$this->model_common->GetParticularClubDetails($result->main_girl_id);
              		//echo '<pre>';print_r($girl_club_details);
              		if($girl_club_details[0]->zip_city!=''){
                      $zip_city=$girl_club_details[0]->zip_city;
                    }else{
                      $zip_city="No information available";
                    }

                    if($girl_club_details[0]->canton!=0){
                      //$region_name=$this->model_common->GetRegionName($girl_club_details[0]->canton);
                      $region_name=$this->model_common->GetSpecificLanguage('region_language','language_text',$girl_club_details[0]->canton,$language_id);
                    }else{
                      $region_name="No Information Available";
                    }
              	}
              	//$status=$this->model_common->GetStatus($result->status);
              	$status = $this->model_common->GetStatusLanguage($result->status,$language_id);
				$data[]=array(
					'type' => $result->girl_type,
					'encoded_id' => base64_encode($result->id),
					'girl_id' => $result->id,
					'girl_name' => $result->name,
					'slogan' => $slogan,
					'zip_city' => $zip_city,
					'region' => $region_name,
					'girl_image' => $get_girl_image,
					'girl_status' => $status,
					'position' => $result->partner_position
				);
				$slogan='';
			}
		}
		if(count($data)>0){
			$result=array('success' => 'true','result' => $data);
		}else{
			$result=array('success' => 'false','result' => $data);
		}
		return $result;
	}

	function latest_girl($detail){
		$data=array();
		$partner_id=$detail['partner_id'];
		$language_id = $detail['language_id'];
		$slogan=$zip_city=$region_name='';
		$partner_folder_name=$this->model_common->GetPartnerName($partner_id);
		
		$this->db->select('partner_girl.*');
		$this->db->from('partner_girl');
		$this->db->from('girl');
		$this->db->from('users');
		$this->db->where('partner_girl.partner_id',$partner_id);
		$this->db->where('partner_girl.main_girl_id = girl.id');
		$this->db->where("partner_girl.status NOT IN ('3','7')");
		$this->db->where("partner_girl.show_status",'1');
		$this->db->where("partner_girl.uid = users.id");
		$this->db->where("users.status",'1');
		$this->db->order_by('partner_girl.id','DESC');
		$this->db->limit(12,0);
		$q=$this->db->get();
		//echo $this->db->last_query();
		if($q->num_rows>0){
			foreach($q->result() as $result){
				$get_girl_image=$this->partner_model->GetPartnerGirlLatestImage($partner_folder_name,$result->id);
				if($result->slogan!=''){
                	$slogan.=substr($result->slogan,0,25);
	                if(strlen($result->slogan)>25){
	                  $slogan.="...";
	                }
              	}else{
                	$slogan.="&nbsp;";
              	}
              	if($result->girl_type==2){
              		if($result->zip_city!=''){
                      $zip_city=$result->zip_city;
                    }else{
                      $zip_city="No information available";
                    }

                    if($result->region!=0){
                      //$region_name=$this->model_common->GetRegionName($result->region);
                      $region_name=$this->model_common->GetSpecificLanguage('region_language','language_text',$result->region,$language_id);
                    }else{
                      $region_name="No Information Available";
                    }
              	}if($result->girl_type==1){
              		$girl_club_details=$this->model_common->GetParticularClubDetails($result->main_girl_id);
              		if($girl_club_details[0]->zip_city!=''){
                      $zip_city=$girl_club_details[0]->zip_city;
                    }else{
                      $zip_city="No information available";
                    }

                    if($girl_club_details[0]->canton!=0){
                      //$region_name=$this->model_common->GetRegionName($girl_club_details[0]->canton);
                      $region_name=$this->model_common->GetSpecificLanguage('region_language','language_text',$girl_club_details[0]->canton,$language_id);
                    }else{
                      $region_name="No Information Available";
                    }
              	}
              	//$status=$this->model_common->GetStatus($result->status);
              	$status = $this->model_common->GetStatusLanguage($result->status,$language_id);
				$data[]=array(
					'type' => $result->girl_type,
					'encoded_id' => base64_encode($result->id),
					'girl_id' => $result->id,
					'girl_name' => $result->name,
					'slogan' => $slogan,
					'zip_city' => $zip_city,
					'region' => $region_name,
					'girl_image' => $get_girl_image,
					'girl_status' => $status,
					'position' => $result->partner_position
				);
				$slogan='';
			}
		}
		if(count($data)>0){
			$result=array('success' => 'true','result' => $data);
		}else{
			$result=array('success' => 'false','result' => $data);
		}
		return $result;
	}
	function club_list($partner_id,$start_index,$per_page_value,$language_id){
	
		$data=$week_new_array=array();
		$club_type=$canton='';
		$partner_folder_name=$this->model_common->GetPartnerName($partner_id);
		$this->db->select('partner_club.*');
		$this->db->from('partner_club');
		$this->db->from('club');
		$this->db->from('users');
		$this->db->where('partner_club.partner_id',$partner_id);
		$this->db->where('partner_club.main_club_id = club.id');
		$this->db->where("partner_club.status NOT IN ('3','7')");
		$this->db->where('partner_club.uid = users.id');
		$this->db->where("users.status",'1');
		$this->db->order_by('partner_club.partner_position','ASC');
		$q=$this->db->get();
		//echo $this->db->last_query();
		$total_record=$q->num_rows();


		$this->db->select('partner_club.*');
		$this->db->from('partner_club');
		$this->db->from('club');
		$this->db->from('users');
		$this->db->where('partner_club.partner_id',$partner_id);
		$this->db->where('partner_club.main_club_id = club.id');
		$this->db->where("partner_club.status NOT IN ('3','7')");
		$this->db->where('partner_club.uid = users.id');
		$this->db->where("users.status",'1');
		$this->db->order_by('partner_club.partner_position','ASC');
		$this->db->limit($per_page_value,$start_index);
		$q=$this->db->get();
		//echo $this->db->last_query();
		if($q->num_rows>0){
			foreach($q->result() as $result){
				$encoded_id = base64_encode($result->id);
				$get_club_image=$this->partner_model->GetPartnerClubLatestImage($partner_folder_name,$result->id);
				//$club_type=$this->model_common->GetClubType($result->club_type);
				$club_type_language = $this->model_common->GetSpecificLanguage('club_type_language','language_text',$result->club_type,$language_id);
				if($result->canton!=0){
					//$canton=$this->model_common->GetRegionName($result->canton);
					$canton=$this->model_common->GetSpecificLanguage('region_language','language_text',$result->canton,$language_id);
				}
				$full_available=$result->full_available;
				$week_array=array('1' => 'Sun','2' => 'Mon','3' => 'Tue','4' => 'Wed','5' => 'Thu','6' => 'Fri','7' => 'Sat');
                //$get_week=$this->redmap_common_model->GetPartnerWeekAvailable('partner_club_available_time',$main_list->id);
                $week_new_array[]=array('is_available' => $full_available,'day_name' => 'All');
                $get_week=$this->partner_model->GetPartnerWeekAvailable('partner_club_available_time',$result->id);
                foreach($week_array as $week_keys => $week_values){
                	if(count($get_week)>0){
	                    if(in_array($week_keys,$get_week)){
	                      $select_more_week="1";
	                    }else{
	                      $select_more_week="0";
	                    }
	                }else{
	                    $select_more_week="0";
	                }
                	$week_new_array[]=array(
                		'is_available' => $select_more_week,
                		'day_name' => $week_values
                	);
                }
				$data[]=array(
					'id' => $result->id,
					'encoded_id' => $encoded_id,
					'name' => $result->name,
					'club_type' => $club_type_language,
					'region' => $canton,
					'phone' => $result->phone,
					'full_available' => $full_available,
					'week' => $week_new_array,
					'club_image' => $get_club_image,
					'position' => $result->partner_position
				);
				unset($week_new_array);
			}
		}
		if(count($data)>0){
			$result=array('success' => 'true','total_record' => $total_record,'result' => $data);
		}else{
			$result=array('success' => 'false','total_record' => $total_record,'result' => $data);
		}
		return $result;
	}

	function girl_details($data){
		$login = is_login();
		$user_id = $login['user_id'];
		$user_type = $login['user_type'];
		$language_id = $data['language_id'];
		$rubrik_name = $gender_name = $club_available_type = $pre_encoded_id = $pre_girl_name = $next_encoded_id = $next_girl_name = $weight = $size ='';
		$week_array=array('Sunday' => '1','Monday' => '2','Tuesday' => '3','Wednesday' => '4','Thursday' => '5','Friday' => '6','Saturday' => '7');
		$details=$girl_bio=$contact=$text=$service_list_array=$payment_list_array=$available_date=$club_date=$girl_image_array=$comment=$week_new_array=array();
		$partner_id=$data['partner_id'];
		$girl_id=$data['girl_id'];
		$partner_folder_name=$this->model_common->GetPartnerName($partner_id);
		$this->db->where('id',$girl_id);
		$this->db->where('partner_id',$partner_id);
		$q=$this->db->get('partner_girl');
		if($q->num_rows>0){
			foreach($q->result() as $result){
				//echo $result->type;
				/*$pev_girl_id = $this->partner_model->isExistPrevId('girl',$result->id);
				$nxt_girl_id = $this->partner_model->isExistNextId('girl',$result->id);
				if($pev_girl_id!=''){
					$pre_encoded_id=base64_encode($pev_girl_id);
					$pre_girl_name = $this->partner_model->getGirlName($pev_girl_id);
				}
				if($nxt_girl_id!=''){
					$next_encoded_id=base64_encode($nxt_girl_id);
					$next_girl_name = $this->partner_model->getGirlName($nxt_girl_id);
				}*/
				$this->model_common->CountViews('1',$result->main_girl_id);

				if($result->partner_position!=1){
					$lower_position=$result->partner_position-1;
					$this->db->select('partner_girl.*');
					$this->db->from('partner_girl');
					$this->db->from('users');
					$this->db->where('partner_girl.partner_position <',$result->partner_position);
					$this->db->where("(partner_girl.status NOT IN ('3','7'))");
					$this->db->where('partner_girl.partner_id',$partner_id);
					$this->db->where('partner_girl.uid = users.id');
					$this->db->order_by('partner_girl.partner_position','DESC');
					$this->db->limit(1,0);
					$pre_value=$this->db->get();
					if($pre_value->num_rows>0){
						foreach($pre_value->result() as $pre_value_result){
							$pre_encoded_id=base64_encode($pre_value_result->id);
							$pre_girl_name = $this->partner_model->getGirlName($pre_value_result->id);
						}
					}
				}
				
				$upper_position=$result->partner_position+1;
				$this->db->select('partner_girl.*');
				$this->db->from('partner_girl');
				$this->db->from('users');
				$this->db->where('partner_girl.partner_position >',$result->partner_position);
				$this->db->where("(partner_girl.status NOT IN ('3','7'))");
				$this->db->where('partner_girl.partner_id',$partner_id);
				$this->db->where('partner_girl.uid = users.id');
				$this->db->order_by('partner_girl.partner_position','ASC');
				$this->db->limit(1,0);
				$nxt_value=$this->db->get();
				//$res = $nxt_value->row();	
				//echo $this->db->last_query();
				//echo '<pre>';print_r($res);
				if($nxt_value->num_rows>0){
					foreach($nxt_value->result() as $nxt_value_result){
						
						$next_encoded_id=base64_encode($nxt_value_result->id);
						//$next_girl_name = $this->partner_model->getGirlName($nxt_value_result->id);
						$next_girl_name=$nxt_value_result->name;
					}
					
				}

				if($result->rubrik!=0){
					$rubrik_en=$this->model_common->GetFieldName('girl_rubrik','rubrik_name_en',$result->rubrik);
					$rubrik_ge=$this->model_common->GetFieldName('girl_rubrik','rubrik_name_ge',$result->rubrik);
				}if($result->gender!=0){
					$gender_name_en=$this->model_common->GetFieldName('gender_list','gender_name_en',$result->gender);
					$gender_name_ge=$this->model_common->GetFieldName('gender_list','gender_name_ge',$result->gender);
				}
				
				$get_girl_image=$this->partner_model->GetPartnerGirlLatestImage($partner_folder_name,$result->id);

				if($result->weight!='0'){
					$weight = $result->weight.' Kg';
				}if($result->size!='0'){
					$size = $result->size.' cm';
				}
				$girl_bio[]=array(
					'Sprachen' => $this->model_common->GetLanguageName($result->language,$language_id),
					'Alter' => $result->age,
					'weight' => $weight,
					'size' => $size,
					'Haare' => $this->model_common->GetSpecificLanguage('girl_hair_language','language_text',$result->hair,$language_id),
					'Haarfarbe' => $this->model_common->GetSpecificLanguage('girl_hair_color_language','language_text',$result->hair_color,$language_id),
					'add_date' => date('d.m.Y',strtotime($result->add_date))
				);
				
				$text[]=array(
					'title' => $result->text_ads_title,
					'text' => $result->text_ads_text
				);

				if($result->services!=''){
					//$service_array=$this->model_common->GetServiceList($result->services);
					$service_array = $this->model_common->GetServiceListLanguage($result->services,$language_id);
					//echo '<pre>';print_r($service_array);
					if(count($service_array)>0){
						foreach($service_array as $services){
							$service_list_array[]=array(
								'name'=> $services
							);
						}
					}
				}

				if($result->payment_option!=''){
					$payment_array=$this->model_common->GetPayementList($result->payment_option,$language_id);

					foreach ($payment_array as $payment) {
						$payment_list_array[]=array('name' => $payment);
					}
				}
				if($result->request_require==0)
				{
					$request_require="NO";
				}else
				{
					$request_require="YES";
				}
				$payment_list=array(
					'payment_option' => $payment_list_array,
					'hand_msg' => $result->hand_msg,
					'quickly' => $result->quickly,
					'half_hour' => $result->half_hour,
					'one_hour' => $result->one_hour,
					'house_hotel_visit' => $result->house_hotel_visit,
					'request_require' =>$request_require
				);
				$girl_images=$this->partner_model->GetGirlImages($result->id);
				$girl_comment=$this->model_common->GetAllComment('1',$result->main_girl_id);
				if(count($girl_images)>0){
					foreach($girl_images as $images){
						$Upload_path='partner/'.$partner_folder_name.'/girl/'.$images->girl_images;
                    	if(file_exists($Upload_path)){
                    		$girl_image_array[]=array(
                    			'image_id' => $images->id,
                    			'image_url' => base_url().'partner/'.$partner_folder_name.'/girl/'.$images->girl_images,
                    			'image_name' => $images->girl_images
                    		);
                    	}
					}
				}

				if(count($girl_comment)>0){
					foreach($girl_comment as $comment_result){
						$comment[]=array(
							'id' => $comment_result->id,
							'username' => $comment_result->username,
							'post_date' => date('d.m.Y',strtotime($comment_result->add_date)),
							'comment' => $comment_result->comment
						);
					}
				}

				
				if($result->girl_type==1){
					
					$girl_club_details=$this->model_common->GetPartnerClubDetails($result->main_girl_id);
					//echo '<pre>';print_r($girl_club_details);
					if($girl_club_details){
						foreach ($girl_club_details as $club_details) {
							//echo '<pre>';print_r($club_details);
							$club_type_language = $this->model_common->GetSpecificLanguage('club_type_language','language_text',$club_details->club_type,$language_id);
							$region_name=$this->model_common->GetSpecificLanguage('region_language','language_text',$club_details->canton,$language_id);
							$contact[]=array(
								'club_encode_id' => base64_encode($club_details->id),
								'club_name' => $club_details->name,
								'club_type' => $club_type_language,
								'strasse' => $club_details->road,
								'zip_city' => $club_details->zip_city,
								'address' => $club_details->additional_address,
								'region_land' => $region_name,
								'phone' => $club_details->phone,
								'email' => $club_details->email,
								'website' => $club_details->website,
								'latitude' => $club_details->latitude,
								'longitude' => $club_details->longitude
							);
						}
					}

					if($result->full_available==1){
						$available_date[]='24/7';
					}else{
						$available=$this->partner_model->GetPartnerTimeAvailabilty('partner_girl_available_time',$result->id);
						if(count($available)>0){
							foreach ($available as $avalabilty) {
								$day_available=array_search($avalabilty->week_id,$week_array);
								$available_date[]=array(
									'day_name' => array_search($avalabilty->week_id,$week_array),
						            'time_details' => date('H:i',strtotime($avalabilty->start_time)).' : '.date('H:i',strtotime($avalabilty->end_time))
								);
							}
						}else{
							//$club_time_details=$this->model_common->GetClubDetails($result->main_girl_id);
							//echo '<pre>';print_r($girl_club_details);
							if(count($girl_club_details)>0){
								$club_available_type = 'YES';
								
								foreach ($girl_club_details as $club_time) {
									$club_full_available=$club_time->full_available;
									if($club_time->full_available==1){

										$club_date[]='24/7';
									}else{
										/*$club_available=$this->partner_model->GetPartnerTimeAvailabilty('partner_club_available_time',$club_time->id);
                						if(count($club_available)>0){
                							foreach($club_available as $club_avalabilty){
                								$club_day_available=array_search($club_avalabilty->week_id,$week_array);
                								$club_date[]=array(
                									$club_day_available => date('H:i',strtotime($club_avalabilty->start_time)).' : '.date('H:i',strtotime($club_avalabilty->end_time))
                								);
                							}
                						}else{
                							$club_date[]="No time available yet.";
                						}*/
                						$week_new_array=array();
                						$available=$this->partner_model->GetPartnerTimeAvailabilty('partner_club_available_time',$club_time->id);
                						//echo '<pre>';print_r($available);
						                if(count($available)>0){
						                	foreach($available as $avalabilty){
						                		$week_new_array[]=array(
						                			'day_name' => array_search($avalabilty->week_id,$week_array),
						                			'time_details' => date('H:i',strtotime($avalabilty->start_time)).' : '.date('H:i',strtotime($avalabilty->end_time))
						                		);
						                	}
						                	$available_time=date('H:i',strtotime($avalabilty->start_time)).' : '.date('H:i',strtotime($avalabilty->end_time));
						                }

									}

									$available_date[]=array(
										'club_name' => $club_time->name,
										'club_full_available' => $club_full_available,
										'available' => $week_new_array
									);
									unset($week_new_array);
								}
							}
							
						}
					}
					/*$details[]=array(
						'name' => $result->name,
						'slogan' => $result->slogan,
						'bio' => $girl_bio,
						'contact' => $contact,
						'text' => $text,
						'service' => $service_array,
						'payment' => $payment_list,
						'available' => $available_date,
						'image' => $girl_image_array,
						'comment' => $comment
					);*/
				}if($result->girl_type==2){
					//$rubrik_name=$this->model_common->GetFieldName('girl_rubrik','rubrik_name_en',$result->rubrik);
					$rubrik_name=$this->model_common->GetSpecificLanguage('girl_rubrik_language','language_text',$result->rubrik,$language_id);
					$gender_name=$this->model_common->GetSpecificLanguage('gender_list_language','language_text',$result->gender,$language_id);
					$region_name=$this->model_common->GetSpecificLanguage('region_language','language_text',$result->region,$language_id);
					$contact[]=array(
						
						'strasse' => $result->road,
						'zip_city' => $result->zip_city,
						'address' => $result->additional_address,
						'region_land' => $region_name,
						'phone' => $result->phone,
						'email' => $result->email,
						'website' => $result->website,
						'latitude' => $result->latitude,
						'longitude' => $result->longitude
					);

					

					if($result->full_available==1){
						$available_date[]='24/7';
					}else{
						$available=$this->partner_model->GetPartnerTimeAvailabilty('partner_girl_available_time',$result->id);
        				if(count($available)>0){
        					foreach($available as $avalabilty){
        						$day_available=array_search($avalabilty->week_id,$week_array);
								/*$available_date[]=array(
									$day_available => date('H:i',strtotime($avalabilty->start_time)).' : '.date('H:i',strtotime($avalabilty->end_time))
								);*/
								$available_date[]=array(
		                			'day_name' => array_search($avalabilty->week_id,$week_array),
		                			'time_details' => date('H:i',strtotime($avalabilty->start_time)).' : '.date('H:i',strtotime($avalabilty->end_time))
		                		);
        					}
        				}/*else{
        					$available_date[]="No time available yet.";
        				}*/
					}


				}

				$details[]=array(
					'pre_encoded_id' => $pre_encoded_id,
					'pre_girl_name' => $pre_girl_name,
					'next_encoded_id' => $next_encoded_id,
					'next_girl_name' => $next_girl_name,
					'girl_id' => $result->id,
					'main_girl_id' => $result->main_girl_id,
					'name' => $result->name,
					'last_update' => date('d.m.Y',strtotime($result->update_time)),
					'slogan' => $result->slogan,
					'girl_type' => $result->girl_type,
					'rubrik' => $rubrik_name,
					'gender' => $gender_name,
					'latest_image' => $get_girl_image,
					'bio' => $girl_bio,
					'contact' => $contact,
					'text' => $text,
					'service' => $service_list_array,
					'payment' => $payment_list,
					'available_type' => $club_available_type,
					'prvt_girl_full_available' => $result->full_available,
					'available' => $available_date,
					'image' => $girl_image_array,
					'comment' => $comment
				);
			}
		}
		$result=array('success' => 'true','user_id' => $user_id,'user_type' => $user_type,'result' => $details);
		return $result;
	}

	function club_details($data){
		$login = is_login();
		$language_id = $data['language_id'];
		$user_id = $login['user_id'];
		$user_type = $login['user_type'];
		//echo $user_id;die;
		$contact_array=$details=$text_array=$avaliable_array=$establish_list=$establish_array=$payment_list=$girl_array=$image_array=$comment_array=$week_new_array=array();
		$pre_encoded_id=$pre_club_name=$next_encoded_id=$next_club_name=$available_time='';
		$week_array=array('Sunday' => '1','Monday' => '2','Tuesday' => '3','Wednesday' => '4','Thursday' => '5','Friday' => '6','Saturday' => '7');
		//echo '<pre>';print_r($data);
		$partner_id=$data['partner_id'];
		$club_id=$data['club_id'];

		$partner_folder_name=$this->model_common->GetPartnerName($partner_id);

		$this->db->where('partner_id',$partner_id);
		$this->db->where('id',$club_id);
		$q=$this->db->get('partner_club');
		//echo $this->db->last_query();
		if($q->num_rows>0){
			foreach($q->result() as $result){
				$get_club_image=$this->partner_model->GetPartnerClubLatestImage($partner_folder_name,$result->id);
				$club_comment=$this->model_common->GetAllComment('2',$result->main_club_id);
				$club_images=$this->partner_model->GetClubImages($result->id);

				/*$pev_club_id = $this->partner_model->isExistPrevId('club',$result->id);
				$nxt_club_id = $this->partner_model->isExistNextId('club',$result->id);
				if($pev_club_id!=''){
					$pre_encoded_id=base64_encode($pev_club_id);
					$pre_club_name = $this->partner_model->getClubName($pev_club_id);
				}
				if($nxt_club_id!=''){
					$next_encoded_id=base64_encode($nxt_club_id);
					$next_club_name = $this->partner_model->getClubName($nxt_club_id);
				}*/
				$this->model_common->CountViews('2',$result->main_club_id);

				if($result->partner_position!=1){
					$lower_position=$result->partner_position-1;
					$this->db->select('partner_club.*');
					$this->db->from('partner_club');
					$this->db->from('users');
					$this->db->where('partner_club.partner_position <',$result->partner_position);
					$this->db->where("partner_club.status",'1');
					$this->db->where('partner_club.partner_id',$partner_id);
					$this->db->where('partner_club.uid = users.id');
					$this->db->where('users.status','1');
					$this->db->order_by('partner_club.partner_position','DESC');
					$this->db->limit(1,0);
					$pre_value=$this->db->get();
					//echo $this->db->last_query();
					if($pre_value->num_rows>0){
						foreach($pre_value->result() as $pre_value_result){
							$pre_encoded_id=base64_encode($pre_value_result->id);
							$pre_club_name = $this->partner_model->getClubName($pre_value_result->id);
						}
					}
				}
				//echo $result->partner_position;
				$upper_position=$result->partner_position+1;
				$this->db->select('partner_club.*');
				$this->db->from('partner_club');
				$this->db->from('users');
				$this->db->where('partner_club.partner_position >',$result->partner_position);
				$this->db->where("partner_club.status",'1');
				$this->db->where('partner_club.partner_id',$partner_id);
				$this->db->where('partner_club.uid = users.id');
				$this->db->where('users.status','1');
				$this->db->order_by('partner_club.partner_position','ASC');
				$this->db->limit(1,0);
				$nxt_value=$this->db->get();
				//$res = $nxt_value->row();	
				//echo $this->db->last_query();
				//echo '<pre>';print_r($res);
				if($nxt_value->num_rows>0){
					foreach($nxt_value->result() as $nxt_value_result){
						
						$next_encoded_id=base64_encode($nxt_value_result->id);
						$next_club_name=$nxt_value_result->name;
					}
					
				}

				if($result->canton!=0){
					//$canton=$this->model_common->GetRegionName($result->canton);
					$canton=$this->model_common->GetSpecificLanguage('region_language','language_text',$result->canton,$language_id);
				}else{
					$canton = '';
				}
				//$club_type=$this->model_common->GetClubType($result->club_type);
				$club_type = $this->model_common->GetSpecificLanguage('club_type_language','language_text',$result->club_type,$language_id);
				$contact_array[]=array(
					'strasse' => $result->road,
					'plz_ort' => $result->zip_city,
					'address' => $result->additional_address,
					'region' => $canton,
					'telephone' => $result->phone,
					'email' => $result->email,
					'website' => $result->website,
					'latitude' => $result->latitude,
					'longitude' => $result->longitude
				);
				$text_array[]=array(
					'title' => $result->service_title,
					'text' => $result->service_test
				);


				$full_available=$result->full_available;
				
                $available=$this->partner_model->GetPartnerTimeAvailabilty('partner_club_available_time',$result->id);
                if(count($available)>0){
                	foreach($available as $avalabilty){
                		$week_new_array[]=array(
                			'day_name' => array_search($avalabilty->week_id,$week_array),
                			'time_details' => date('H:i',strtotime($avalabilty->start_time)).' : '.date('H:i',strtotime($avalabilty->end_time))
                		);
                	}
                	$available_time=date('H:i',strtotime($avalabilty->start_time)).' : '.date('H:i',strtotime($avalabilty->end_time));
                }

				

				if($result->establish!=''){
					$club_establish_array=$this->partner_model->GetEstablish($result->establish,$language_id);
					foreach ($club_establish_array as $establish) {
						$establish_list[]=array(
							'name' => $establish
						);
					}
				}

				$establish_array[]=array(
					'no_of_room' => $result->no_of_room,
					'no_of_suite' => $result->no_of_suite,
					'establish' => $establish_list
				);

				if($result->payment!=''){
					$payment_array=$this->model_common->GetPayementList($result->payment,$language_id);
					foreach ($payment_array as $payment) {
						$payment_list[]=array(
							'payment_name' => $payment
						);
					}
				}

				$girl_list=$this->partner_model->GetClubGirls($result->main_club_id);
				//echo "JAYATISH";
				if(count($girl_list)>0){
					//echo count($girl_list);
					foreach($girl_list as $girl){
						$get_girl_link=$this->partner_model->GetPartnerLink('girl',$partner_id,$girl->id);
	                    $get_latest_image=$this->partner_model->GetPartnerGirlLatestImage($partner_folder_name,$get_girl_link);
						$girl_array[]=array(
							'girl_id' => $get_girl_link,
							'girl_image' => $get_latest_image,
							'encoded_id' => base64_encode($get_girl_link),
						);
					}
				}

				if(count($club_images)>0){
					foreach($club_images as $image){
						$Upload_path='partner/'.$partner_folder_name.'/club/'.$image->club_image;
                		
                        if(file_exists($Upload_path))
                        {
                            $image_path=base_url().'partner/'.$partner_folder_name.'/club/'.$image->club_image;
                            $image_array[]=array(
                            	'image_id' => $image->id,
                            	'image_url' => $image_path,
                            	'image_name' => $image->club_image
                            );
                        }
					}
				}
				if(count($club_comment)>0){
					foreach ($club_comment as $comment) {
						$comment_array[]=array(
							'id' => $comment->id,
							'username' => $comment->username,
							'post_date' => date('d.m.Y',strtotime($comment->add_date)),
							'comment' => $comment->comment
						);
					}
				}

				$details[]=array(
					'pre_encoded_id' => $pre_encoded_id,
					'pre_club_name' => $pre_club_name,
					'next_encoded_id' => $next_encoded_id,
					'next_club_name' => $next_club_name,
					'club_id' => $result->id,
					'main_club_id' => $result->main_club_id,
					'club_name' => $result->name,
					'club_type' => $club_type,
					'slogan' => $result->slogan,
					'latest_image' => $get_club_image,
					'contact' => $contact_array,
					'text' => $text_array,
					'full_available' => $full_available,
					'week_array' => $week_new_array,
					'establish' => $establish_array,
					'payment' => $payment_list,
					'girls' => $girl_array,
					'images' => $image_array,
					'comment' => $comment_array
				);
			}
		}
		$return=array('success' => 'true','user_id' => $user_id,'user_type' => $user_type,'result' => $details);
		return $return;
	}

	function comments_count($data){
		$count=array();
		$partner_id = $data['partner_id'];
		$this->db->select('users.username,comment.*');
		$this->db->from('users');
		$this->db->from('comment');
		//$this->db->where('comment.partner_id',$partner_id);
		$this->db->where('comment.uid = users.id');
		$this->db->where('users.status','1');
		$this->db->order_by('comment.add_date','DESC');
		$q=$this->db->get();
		//$count = $q->num_rows;
		if($q->num_rows>0){
			foreach($q->result() as $result){
				if($result->type==1){
					$this->db->where('main_girl_id',$result->foreign_id);
					$this->db->where('partner_id',$partner_id);
					$this->db->where("`status` NOT IN ('3','7')");
					$partner_girl = $this->db->get('partner_girl');
					if($partner_girl->num_rows>0){
						foreach($partner_girl->result() as $partner_girl_result){
							$count[]=$partner_girl_result;
						}
					}
				}
				if($result->type==2){
					$this->db->where('main_club_id',$result->foreign_id);
					$this->db->where('partner_id',$partner_id);
					$this->db->where("status",'1');
					$partner_club = $this->db->get('partner_club');
					if($partner_club->num_rows>0){
						foreach($partner_club->result() as $partner_club_result){
							$count[]=$partner_club_result;
						}
					}
				}
			}
		}
		$return=array('success' => 'true','total_record' => count($count));
		return $return;
	}

	function comments_result($data){
		$details=array();
		$partner_id = $data['partner_id'];
		$start_index = $data['start_index'];
		$per_page_value = $data['per_page_value'];
		$partner_folder_name=$this->model_common->GetPartnerName($partner_id);

		$this->db->select('users.username,comment.*');
		$this->db->from('users');
		$this->db->from('comment');
		//$this->db->where('comment.partner_id',$partner_id);
		$this->db->where('comment.uid = users.id');
		$this->db->where('users.status','1');
		$this->db->order_by('comment.add_date','DESC');
		$this->db->limit($per_page_value,$start_index);
		$q=$this->db->get();
		if($q->num_rows>0){
			foreach($q->result() as $result){
				if($result->type==1){
					$this->db->where('main_girl_id',$result->foreign_id);
					$this->db->where('partner_id',$partner_id);
					$this->db->where("`status` NOT IN ('3','7')");
					$partner_girl = $this->db->get('partner_girl');
					if($partner_girl->num_rows>0){
						foreach($partner_girl->result() as $partner_girl_result){
							$get_image=$this->partner_model->GetPartnerGirlLatestImage($partner_folder_name,$partner_girl_result->id);
							$encrypt_id=base64_encode($partner_girl_result->id);
							$add_comment_date = date('d.m.Y h:i A',strtotime($result->add_date));
							$details[]=array(
								'comment_type' => $result->type,
								'comment_id' => $result->id,
								'encrypt_id' => $encrypt_id,
								'image_name' => $get_image,
								'username' => $result->username,
								'add_date' => $add_comment_date,
								'comment' => $result->comment
							);
						}
					}
					

				}if($result->type==2){
					//$get_image=$this->partner_model->GetPartnerClubLatestImage($partner_folder_name,$result->partner_girl_club_id);
					$this->db->where('main_club_id',$result->foreign_id);
					$this->db->where('partner_id',$partner_id);
					$this->db->where("status",'1');
					$partner_club = $this->db->get('partner_club');
					if($partner_club->num_rows>0){
						foreach($partner_club->result() as $partner_club_result){
							$get_image=$this->partner_model->GetPartnerClubLatestImage($partner_folder_name,$partner_club_result->id);
							$encrypt_id=base64_encode($partner_club_result->id);
							$add_comment_date = date('d.m.Y h:i A',strtotime($result->add_date));
							$details[]=array(
								'comment_type' => $result->type,
								'comment_id' => $result->id,
								'encrypt_id' => $encrypt_id,
								'image_name' => $get_image,
								'username' => $result->username,
								'add_date' => $add_comment_date,
								'comment' => $result->comment
							);
						}
					}
				}
			}
		}
		if(count($details)>0){
			$return=array('success' => 'true','result' => $details);
		}else{
			$return=array('success' => 'false','result' => $details);
		}
		return $return;
	}

	

	function side_bar_section($data){
		$first_cunter_array=$second_cunter_array=$third_cunter_array=$fourth_cunter_array=$firth_cunter_array=$sixth_cunter_array=$first_banner=$second_banner=$third_banner=$fourth_banner=$fifth_banner=$sixth_banner=array();
	    $partner_id = $data['partner_id'];
	    if($partner_id==4){
	    	$increment_number=6;
	    }if($partner_id==2){
	    	$increment_number=4;
	    }
	    $position_id = $data['position_id'];
	    $partner_folder_name=$this->model_common->GetPartnerName($partner_id);

	    $left_side=$this->partner_model->GetPartnerBannerList($partner_id,$position_id);
	    if(count($left_side)>0){
	      $main_counter=1;$first_counter=1;$second_counter=2;$third_counter=3;$fourth_counter=4;$fifth_counter=5;$sixth_counter=6;
	      for($i=0;$i<count($left_side);$i++) {
	        $upload_left_path='partner/'.$partner_folder_name.'/banner/'.$left_side[$i]->banner_image;
	        if(file_exists($upload_left_path)){ 
	          if(($main_counter==1) || ($main_counter==$first_counter)){
	            $first_cunter_array[]=$i;
	            $first_counter=$first_counter+$increment_number;
	          }
	          if(($main_counter==2) || ($main_counter==$second_counter)){
	            $second_cunter_array[]=$i;
	            $second_counter=$second_counter+$increment_number;
	          }
	          if(($main_counter==3) || ($main_counter==$third_counter)){
	            $third_cunter_array[]=$i;
	            $third_counter=$third_counter+$increment_number;
	          }
	          if(($main_counter==4) || ($main_counter==$fourth_counter)){
	            $fourth_cunter_array[]=$i;
	            $fourth_counter=$fourth_counter+$increment_number;
	          }
	          if(($main_counter==5) || ($main_counter==$fifth_counter)){
	            	$firth_cunter_array[]=$i;
	            	$fifth_counter=$fifth_counter+$increment_number;
	          	}
	          	if(($main_counter==6) || ($main_counter==$sixth_counter)){
	            	$sixth_cunter_array[]=$i;
	            	$sixth_counter=$sixth_counter+$increment_number;
	          	}
	          
	          $main_counter++;
	        }
	      }

	      if(count($first_cunter_array)>0){
	        for($a=0;$a<count($first_cunter_array);$a++){
	          $first_counter_key=$first_cunter_array[$a];
	          //echo $first_counter_key;
	          $first_side_link='http://';
	          if(strpos($left_side[$first_counter_key]->banner_link,$first_side_link)!=false){
	            $get_first_side_link=$left_side[$first_counter_key]->banner_link;
	          }else{
	            $get_first_side_link=$first_side_link.$left_side[$first_counter_key]->banner_link;
	          }
	          $first_banner[]=array(
	            'banner_link' => $get_first_side_link,
	            'small_image' => base_url().'partner/'.$partner_folder_name.'/banner/thumb/'.$left_side[$first_counter_key]->banner_image,
	            'big_image' => base_url().'partner/'.$partner_folder_name.'/banner/'.$left_side[$first_counter_key]->banner_image,
	          );
	        }
	      }

	      if(count($second_cunter_array)>0){
	        for($b=0;$b<count($second_cunter_array);$b++){
	          $second_counter_key=$second_cunter_array[$b];
	          $second_side_link='http://';
	          if(strpos($left_side[$second_counter_key]->banner_link,$second_side_link)!=false){
	            $get_second_side_link=$left_side[$second_counter_key]->banner_link;
	          }else{
	            $get_second_side_link=$second_side_link.$left_side[$second_counter_key]->banner_link;
	          }
	          $second_banner[]=array(
	            'banner_link' => $get_second_side_link,
	            'small_image' => base_url().'partner/'.$partner_folder_name.'/banner/thumb/'.$left_side[$second_counter_key]->banner_image,
	            'big_image' => base_url().'partner/'.$partner_folder_name.'/banner/'.$left_side[$second_counter_key]->banner_image,
	          );
	        }
	      }

	      	if(count($third_cunter_array)>0){
		        for($c=0;$c<count($third_cunter_array);$c++){
		          $third_counter_key=$third_cunter_array[$c];
		          $third_side_link='http://';
		          if(strpos($left_side[$third_counter_key]->banner_link,$third_side_link)!=false){
		            $get_third_side_link=$left_side[$third_counter_key]->banner_link;
		          }else{
		            $get_third_side_link=$third_side_link.$left_side[$third_counter_key]->banner_link;
		          }
		          $third_banner[]=array(
		            'banner_link' => $get_third_side_link,
		            'small_image' => base_url().'partner/'.$partner_folder_name.'/banner/thumb/'.$left_side[$third_counter_key]->banner_image,
		            'big_image' => base_url().'partner/'.$partner_folder_name.'/banner/'.$left_side[$third_counter_key]->banner_image,
		          );
		        }
		    }

		    if(count($fourth_cunter_array)>0){
		        for($d=0;$d<count($fourth_cunter_array);$d++){
		          $fourth_counter_key=$fourth_cunter_array[$d];
		          $fourth_side_link='http://';
		          if(strpos($left_side[$fourth_counter_key]->banner_link,$fourth_side_link)!=false){
		            $get_fourth_side_link=$left_side[$fourth_counter_key]->banner_link;
		          }else{
		            $get_fourth_side_link=$fourth_side_link.$left_side[$fourth_counter_key]->banner_link;
		          }

		          $fourth_banner[]=array(
		            'banner_link' => $get_fourth_side_link,
		            'small_image' => base_url().'partner/'.$partner_folder_name.'/banner/thumb/'.$left_side[$fourth_counter_key]->banner_image,
		            'big_image' => base_url().'partner/'.$partner_folder_name.'/banner/'.$left_side[$fourth_counter_key]->banner_image,
		          );
		        }
		    }

		    if(count($firth_cunter_array)>0){
		        for($fifth=0;$fifth<count($firth_cunter_array);$fifth++){
			        $fifth_counter_key=$firth_cunter_array[$fifth];
			        $fifth_side_link='http://';
			        if(strpos($left_side[$fifth_counter_key]->banner_link,$fifth_side_link)!=false){
			          $get_fifth_side_link=$left_side[$fifth_counter_key]->banner_link;
			        }else{
			          $get_fifth_side_link=$fifth_side_link.$left_side[$fifth_counter_key]->banner_link;
			        }

			        $fifth_banner[]=array(
			          'banner_link' => $get_fifth_side_link,
			          'small_image' => base_url().'partner/'.$partner_folder_name.'/banner/thumb/'.$left_side[$fifth_counter_key]->banner_image,
			          'big_image' => base_url().'partner/'.$partner_folder_name.'/banner/'.$left_side[$fifth_counter_key]->banner_image,
			        );
				}
		    }

		    if(count($sixth_cunter_array)>0){
		        for($f=0;$f<count($sixth_cunter_array);$f++){
		          $sixth_counter_key=$sixth_cunter_array[$f];
		          //echo $sixth_counter_key;
		          $sixth_side_link='http://';
		          if(strpos($left_side[$sixth_counter_key]->banner_link,$sixth_side_link)!=false){
		            $get_sixth_side_link=$left_side[$sixth_counter_key]->banner_link;
		          }else{
		            $get_sixth_side_link=$sixth_side_link.$left_side[$sixth_counter_key]->banner_link;
		          }
		          $sixth_banner[]=array(
		            'banner_link' => $get_sixth_side_link,
		            'small_image' => base_url().'partner/'.$partner_folder_name.'/banner/thumb/'.$left_side[$sixth_counter_key]->banner_image,
		            'big_image' => base_url().'partner/'.$partner_folder_name.'/banner/'.$left_side[$sixth_counter_key]->banner_image,
		          );
		        }
		    }
	      //echo '<pre>';print_r($third_banner);
	    }

		$return=array('success' => 'true','first_row' => $first_banner,'second_row' => $second_banner,'third_row' => $third_banner,'fourth_row' => $fourth_banner,'fifith_row' => $fifth_banner,'sixth_row' => $sixth_banner);
		return $return;
	}

	function testimonial($data){
		$details=array();
		//echo '<pre>';print_r($data);
		$partner_id = $data['partner_id'];
		$this->db->where('status','1');
		$this->db->where('partner_id',$partner_id);
		$this->db->order_by('id','DESC');
		$q=$this->db->get('testimonials');
		if($q->num_rows>0)
		{
			foreach($q->result() as $result)
			{
				$testimonial_image=$this->model_common->TestimonialImage($result->testimonials_image);
				$details[]=array(
					'testimonial_id' => $result->id,
					'testimonial_image' => $testimonial_image,
					'description_en' => $result->description_en,
					'description_ge' => $result->description_ge,
					'username' => $result->name
				);
			}
		}
		if(count($details)>0){
			$return=array('success' => 'true','result' => $details);
		}else{
			$return=array('success' => 'false','result' => $details);
		}
		return $return;
	}

	function cms_details($data){
		$language_id = $data['language_id'];
		$partner_id = $data['partner_id'];
		$cms_id = $data['cms_id'];
		$details = array();
		$this->db->select('cms_language.*');
		$this->db->from('cms_language');
		$this->db->from('cms');
		$this->db->where('cms.partner_id',$partner_id);
		$this->db->where('cms.id',$cms_id);
		$this->db->where('cms.status','1');
		$this->db->where('cms.id = cms_language.static_text_id');
		$this->db->where('cms_language.language_id',$language_id);
		$q=$this->db->get();
		//echo $this->db->last_query();
		if($q->num_rows>0){
			foreach($q->result() as $result){
				$details[]=array(
					'cms_id' => $result->id,
					'title' => $result->language_text,
					'description' => $result->language_description,
					//'added_date' => $result->added_date 
				);
			}
		}
		$result=array('success' => 'true','result' => $details);
		return $result;
	}

	function AllSelectedRegion($details){
		$partner_id = $details['partner_id'];
		$language_id = $details['language_id'];
		$girl_data=$club_data=$main_data=$partner_club_data=array();
		$this->db->where('partner_id',$partner_id);
		$this->db->where('region !=','0');
		$this->db->where("status NOT IN ('3','7')");
		$girl=$this->db->get('partner_girl');
		//echo $this->db->last_query();
		if($girl->num_rows>0){
			foreach($girl->result() as $girl_result){
				$girl_data[]='"'.$girl_result->region.'"';
			}
		}
		
		$this->db->select('club.canton');
		$this->db->from('girl_position');
		$this->db->from('partner_girl');
		$this->db->from('girl');
		$this->db->from('club');
		$this->db->where('girl.domain_id','0');
		$this->db->where('girl.region','0');
		$this->db->where('girl.girl_type','1');
		$this->db->where("girl.status NOT IN ('3','7')");
		$this->db->where('girl.id = girl_position.girl_id');
		$this->db->where('girl.id = partner_girl.main_girl_id');
		$this->db->where('partner_girl.partner_id',$partner_id);
		$this->db->where("girl_position.club_id = club.id");
		$this->db->where('club.canton !=','0');
		$this->db->where('club.status','1');
		$club=$this->db->get();
		if($club->num_rows>0){
			foreach($club->result() as $club_result){
				$club_data[]='"'.$club_result->canton.'"';
			}
		}

		$this->db->select('partner_club.canton');
		$this->db->from('partner_club');
		$this->db->from('users');
		$this->db->where('partner_club.canton !=','0');
		$this->db->where('partner_club.partner_id',$partner_id);
		$this->db->where('partner_club.uid = users.id');
		$this->db->where('users.status','1');
		$partner_club=$this->db->get();
		if($partner_club->num_rows>0){
			foreach($partner_club->result() as $partner_club_result){
				$partner_club_data[]=$partner_club_result->canton;
			}
		}
		//echo '<pre>';print_r($partner_club_data);
		$final_data=array_values(array_unique(array_merge($club_data,$girl_data,$partner_club_data)));
		$this->db->select('region_language.*');
		$this->db->from('region');
		$this->db->from('region_language');
		if(count($final_data)>0){
			$region_id=implode(',',$final_data);
			$this->db->where("region.id IN (".$region_id.")");
		}
		$this->db->where('region.id = region_language.static_text_id');
		$this->db->where('region_language.language_id',$language_id);
		$this->db->where('region.status','1');
		$this->db->order_by('region_language.language_text','ASC');
		$q=$this->db->get();
		if($q->num_rows>0)
		{
			foreach($q->result() as $result)
			{
				//$region_language = $this->model_common->GetSpecificLanguage('region_language','language_text',$result->id,$language_id);
				$main_data[]=array(
					'region_id' => $result->static_text_id,
					'region_name' => $result->language_text,
				);
			}
		}
		if(count($main_data)>0){
			$return=array('success' => 'true','result' => $main_data);
		}else{
			$return=array('success' => 'false','result' => $main_data);
		}
		return $return;
	}

	function SelectedStatus($details){
		$language_id = $details['language_id'];
		$data=array();
		$this->db->select('girl_status_language.*');
		$this->db->from('girl_status');
		$this->db->from('girl_status_language');
		$this->db->where('girl_status.status','1');
		$this->db->where("girl_status.id NOT IN ('3','7')");
		$this->db->where('girl_status.id = girl_status_language.static_text_id');
		$this->db->where('girl_status_language.language_id',$language_id);
		$this->db->order_by('girl_status_language.language_text','ASC');
		$q=$this->db->get();
		if($q->num_rows>0)
		{
			foreach ($q->result() as $result) {
				/*$this->db->where('language_id',$language_id);
				$this->db->where('static_text_id',$result->id);
				$status_language = $this->db->get('girl_status_language');
				if($status_language->num_rows>0){
					foreach($status_language->result() as $status_language_result){
						$status_language = $status_language_result->language_text;
					}
				}*/
				$data[]=array(
					'status_id' => $result->static_text_id,
					'status_name' => $result->language_text,
				);
			}
		}
		if(count($data)>0){
			$return=array('success' => 'true','result' => $data);
		}else{
			$return=array('success' => 'true','result' => $data);
		}
		return $return;
	}

	function GirlRubrik($details){
		$language_id = $details['language_id'];
		$data=array();
		$this->db->select('girl_rubrik_language.*');
		$this->db->from('girl_rubrik');
		$this->db->from('girl_rubrik_language');
		$this->db->where('girl_rubrik.status','1');
		$this->db->where('girl_rubrik.id = girl_rubrik_language.static_text_id');
		$this->db->where('girl_rubrik_language.language_id',$language_id);
		$this->db->order_by('girl_rubrik_language.language_text','ASC');
		$q=$this->db->get();
		if($q->num_rows>0){
			foreach ($q->result() as $result) {
				/*$this->db->where('language_id',$language_id);
				$this->db->where('static_text_id',$result->id);
				$status_language = $this->db->get('girl_rubrik_language');
				if($status_language->num_rows>0){
					foreach($status_language->result() as $status_language_result){
						$rubrik_language = $status_language_result->language_text;
					}
				}*/
				$data[]=array(
					'rubrik_id' => $result->static_text_id,
					'rubrik_name' => $result->language_text,
					/*'rubrik_en' => $result->rubrik_name_en,
					'rubrik_ge' => $result->rubrik_name_ge*/
				);
			}
		}
		if(count($data)>0){
			$return=array('success' => 'true','result' => $data);
		}else{
			$return=array('success' => 'true','result' => $data);
		}
		return $return;
	}

	function SearchCount($details){
		$partner_id = $details['partner_id'];
		$region = $details['region'];
		$name = $details['name'];
		$rubrik = $details['rubrik'];
		$status = $details['status'];

     	$service_query='';
     	$club_count_number=0;
		$prvt_girl_plz_data=$club_girl_plz_data=$main_girl_plz_id=$main_girl_region_id=$prvt_girl_data=$club_girl_data=$main_girl_id=$data=$payment_array=$gender_array=$service_array=$service_id=array();
     	if($region!=''){
     		$this->db->where('region',$region);
     		$this->db->where("status NOT IN ('3','7')");
     		$this->db->where('partner_id',$partner_id);
     		$q=$this->db->get('partner_girl');
     		if($q->num_rows>0){
     			foreach($q->result() as $result){
     				$main_girl_region_id[]='"'.$result->id.'"';
     			}
     		}
     		$this->db->select('partner_girl.id');
     		$this->db->from('girl_position');
     		$this->db->from('partner_girl');
     		$this->db->from('club');
     		$this->db->where('club.canton',$region);
     		$this->db->where('club.status','1');
     		$this->db->where('club.id = girl_position.club_id');
     		$this->db->where('girl_position.girl_id = partner_girl.main_girl_id');
     		$this->db->where('partner_girl.region','0');
     		$this->db->where("partner_girl.status NOT IN ('3','7')");
     		$this->db->where("partner_girl.show_status",'1');
     		$this->db->where('partner_girl.partner_id',$partner_id);
     		$this->db->order_by('club.name','ASC');
			$club=$this->db->get();
			if($club->num_rows>0){
				foreach($club->result() as $result_club){
					$main_girl_region_id[]='"'.$result_club->id.'"';
				}
			}
			$main_girl_region_id=array_unique($main_girl_region_id);
     	}

     	if($name!=''){
			$serarch_name=$name;

			$this->db->like('zip_city',$name);
     		$this->db->where("status NOT IN ('3','7')");
     		$this->db->where('partner_id',$partner_id);
     		$plz_city=$this->db->get('partner_girl');
     		if($plz_city->num_rows>0){
     			foreach($plz_city->result() as $plz_city_result){
     				$prvt_girl_plz_data[]='"'.$plz_city_result->id.'"';
     			}
     		}
     		$this->db->select('partner_girl.id');
     		$this->db->from('partner_girl');
     		$this->db->from('girl_position');
     		$this->db->from('club');
     		$this->db->like('club.zip_city',$name);
     		$this->db->where('club.status','1');
     		$this->db->where('club.id = girl_position.club_id');
     		$this->db->where('girl_position.girl_id = partner_girl.main_girl_id');
     		$this->db->where("partner_girl.status NOT IN ('3','7')");
     		$this->db->where("partner_girl.show_status",'1');
     		$this->db->where('partner_girl.partner_id',$partner_id);
     		$this->db->where('partner_girl.zip_city','');
     		$this->db->order_by('club.name','ASC');
			
			$club=$this->db->get();
			if($club->num_rows>0){
				foreach($club->result() as $result_club){
					$club_girl_plz_data[]='"'.$result_club->id.'"';
				}
			}
			$main_girl_plz_id=array_unique(array_merge($prvt_girl_plz_data,$club_girl_plz_data));

			if(count($main_girl_plz_id)==0){
			/*$this->db->like('service_title_en',$serarch_name);
			$service=$this->db->get('girl_service');*/
			$this->db->like('language_text',$serarch_name);
			$service=$this->db->get('girl_service_language');
			if($service->num_rows>0){
				foreach($service->result() as $service_result){
					//$service_id[]=$service_result->id;
					$service_id[] = $service_result->language_text;
				}
			}
			if(count($service_id)>0){
				for($i=0;$i<count($service_id);$i++){
					$service=$service_id[$i];
					$service_array[]="services REGEXP '".$service."'";
				}
				$implode_service=implode(' OR ',$service_array);
				$service_query='OR ('.$implode_service.')';
			}
			$this->db->select('partner_girl.*');
			$this->db->from('partner_girl');
			$this->db->from('users');
			$this->db->where("(`partner_girl`.`name` like '%".$serarch_name."%' ".$service_query." OR `partner_girl`.`address` like '%".$serarch_name."%')");
			}
		}else{
			$this->db->select('partner_girl.*');
			$this->db->from('partner_girl');
			$this->db->from('users');
		}
		if((count($main_girl_plz_id)>0) && (count($main_girl_region_id)>0)){
		$main_girl_id=array_intersect($main_girl_plz_id,$main_girl_region_id);
		}else{
			if((count($main_girl_plz_id)>0) && (count($main_girl_region_id)==0)){
				$main_girl_id=$main_girl_plz_id;
			}if((count($main_girl_plz_id)==0) && (count($main_girl_region_id)>0)){
				$main_girl_id=$main_girl_region_id;
			}
		}
		if($rubrik!=''){
			$this->db->where('partner_girl.rubrik',$rubrik);
		}
		
		if($status!=''){
			$this->db->where('partner_girl.status',$status);
		}else{
			$this->db->where("partner_girl.status NOT IN ('3','7')");
		}
		if(count($main_girl_id)>0){
			$implode_girl_id=implode(',',$main_girl_id);
			$this->db->where("(`partner_girl`.`id` IN (".$implode_girl_id."))");

		}
		$this->db->where('partner_girl.partner_id',$partner_id);
		$this->db->where('partner_girl.uid = users.id');
		$this->db->where("users.status",'1');
		$this->db->order_by('partner_girl.partner_position','ASC');
		$q=$this->db->get();
		$girl_count_number=$q->num_rows();

		if(($region!='') || ($name!='') || ($status=='2')){
			$this->db->select('partner_club.*');
			$this->db->from('partner_club');
			$this->db->from('users');
			$this->db->where('partner_club.partner_id',$partner_id);
			if($this->input->post('region')!=''){
				$this->db->where('partner_club.canton',$region);
			}
			if($this->input->post('girl_name')!=''){	
				$this->db->where("(`partner_club`.`name` like '%".$name."%' OR `partner_club`.`zip_city` like '%".$name."%')");		
			}
			$this->db->where('partner_club.status','1');
			$this->db->where('partner_club.uid = users.id');
			$this->db->where("users.status",'1');
			$club=$this->db->get();
			$club_count_number=$club->num_rows();
		}
		$total_number=$girl_count_number+$club_count_number;
		$return = array('success' => 'true','total_record' => $total_number);
		return $return;
    }

    function SearchResult($details){
    	$region_name=$static_region_name='';

    	$language_id = $details['language_id'];
    	$partner_id = $details['partner_id'];
    	$per_page_value = $details['per_page_value'];
    	$page = $details['start_index'];
    	$region = $details['region'];
    	$name = $details['name'];
    	$status = $details['status'];
    	$rubrik = $details['rubrik'];
    	$this->db->where('static_text_id',$region);
    	$this->db->where('language_id',$language_id);
    	$region_details=$this->db->get('region_language');
    	if($region_details->num_rows>0){
    		foreach($region_details->result() as $region_language){
    			$static_region_name=$region_language->language_text;
    		}
    	}
    	//echo $region_name;
    	$partner_folder_name=$this->model_common->GetPartnerName($partner_id);
     	$service_query='';
     	$girl_data=$club_data=$main_whole_data=$data=array();
		$prvt_girl_plz_data=$club_girl_plz_data=$main_girl_plz_id=$main_girl_region_id=$prvt_girl_data=$club_girl_data=$main_girl_id=$data=$payment_array=$gender_array=$service_array=$service_id=array();
     	if($region!=''){
     		$this->db->where('region',$region);
     		$this->db->where("status NOT IN ('3','7')");
     		$this->db->where('partner_id',$partner_id);
     		$q=$this->db->get('partner_girl');
     		if($q->num_rows>0){
     			foreach($q->result() as $result){
     				$main_girl_region_id[]='"'.$result->id.'"';
     			}
     		}
     		$this->db->select('partner_girl.id');
     		$this->db->from('girl_position');
     		$this->db->from('partner_girl');
     		$this->db->from('club');
     		$this->db->where('club.canton',$region);
     		$this->db->where('club.status','1');
     		$this->db->where('club.id = girl_position.club_id');
     		$this->db->where('girl_position.girl_id = partner_girl.main_girl_id');
     		$this->db->where('partner_girl.region','0');
     		$this->db->where("partner_girl.status NOT IN ('3','7')");
     		$this->db->where("partner_girl.show_status",'1');
     		$this->db->where('partner_girl.partner_id',$partner_id);
     		$this->db->order_by('club.name','ASC');
			$club=$this->db->get();
			if($club->num_rows>0){
				foreach($club->result() as $result_club){
					$main_girl_region_id[]='"'.$result_club->id.'"';
				}
			}
			$main_girl_region_id=array_unique($main_girl_region_id);
     	}

     	if($name!=''){
			$serarch_name=$name;
			$this->db->like('zip_city',$name);
     		$this->db->where("status NOT IN ('3','7')");
     		$this->db->where('partner_id',$partner_id);
     		$plz_city=$this->db->get('partner_girl');
     		if($plz_city->num_rows>0){
     			foreach($plz_city->result() as $plz_city_result){
     				$prvt_girl_plz_data[]='"'.$plz_city_result->id.'"';
     			}
     		}
     		//echo "123";die;
     		$this->db->select('partner_girl.id');
     		$this->db->from('partner_girl');
     		$this->db->from('girl_position');
     		$this->db->from('club');
     		$this->db->like('club.zip_city',$name);
     		$this->db->where('club.status','1');
     		$this->db->where('club.id = girl_position.club_id');
     		$this->db->where('girl_position.girl_id = partner_girl.main_girl_id');
     		$this->db->where("partner_girl.status NOT IN ('3','7')");
     		$this->db->where("partner_girl.show_status",'1');
     		$this->db->where('partner_girl.partner_id',$partner_id);
     		$this->db->where('partner_girl.zip_city','');
     		$this->db->order_by('club.name','ASC');
			
			$club=$this->db->get();
			//echo "45464546";die;
			if($club->num_rows>0){
				foreach($club->result() as $result_club){
					$club_girl_plz_data[]='"'.$result_club->id.'"';
				}
			}
			$main_girl_plz_id=array_unique(array_merge($prvt_girl_plz_data,$club_girl_plz_data));

			if(count($main_girl_plz_id)==0){
			/*$this->db->like('service_title_en',$serarch_name);
			$service=$this->db->get('girl_service');*/
			$this->db->like('language_text',$serarch_name);
			$service=$this->db->get('girl_service_language');
			if($service->num_rows>0){
				foreach($service->result() as $service_result){
					//$service_id[]=$service_result->id;
					$service_id[] = $service_result->static_text_id;
				}
			}
			if(count($service_id)>0){
				for($i=0;$i<count($service_id);$i++){
					$service=$service_id[$i];
					$service_array[]="services REGEXP '".$service."'";
				}
				$implode_service=implode(' OR ',$service_array);
				$service_query='OR ('.$implode_service.')';
			}
			//echo "123";die;
			
			$this->db->where("(`name` like '%".$serarch_name."%' ".$service_query." OR `address` like '%".$serarch_name."%')");
			//echo $this->db->last_query();
			}
		}
		if((count($main_girl_plz_id)>0) && (count($main_girl_region_id)>0)){
		$main_girl_id=array_intersect($main_girl_plz_id,$main_girl_region_id);
		}else{
			if((count($main_girl_plz_id)>0) && (count($main_girl_region_id)==0)){
				$main_girl_id=$main_girl_plz_id;
			}if((count($main_girl_plz_id)==0) && (count($main_girl_region_id)>0)){
				$main_girl_id=$main_girl_region_id;
			}
		}

		if($rubrik!=''){
			$this->db->where('rubrik',$rubrik);
		}
		
		if($status!=''){
			$this->db->where('status',$status);
		}else{
			$this->db->where("status NOT IN ('3','7')");
		}
		if(count($main_girl_id)>0){
			$implode_girl_id=implode(',',$main_girl_id);
			$this->db->where("(`id` IN (".$implode_girl_id."))");

		}
		$this->db->where('partner_girl.partner_id',$partner_id);
		$this->db->order_by('partner_position','ASC');
		$q=$this->db->get('partner_girl');
		//echo $this->db->last_query();die;
		if($q->num_rows>0){
			$res=$q->result();
			$arr_type = array('type' => 'girl');
		    foreach ($q->result() as  $result) {
		    	/*$this->db->where('id',$result->uid);
		    	$exist_girl_user=$this->db->get('users');
		    	if($exist_girl_user->num_rows>0){*/
		    	$this->db->where('id',$result->uid);
		    	$this->db->where('status','1');
		    	$exist_user=$this->db->get('users');
		    	if($exist_user->num_rows>0)
		    	{
		    		$slogan='';
			    	$get_girl_image=$this->partner_model->GetPartnerGirlLatestImage($partner_folder_name,$result->id);
					if($result->slogan!=''){
	                	$slogan.=substr($result->slogan,0,25);
		                if(strlen($result->slogan)>25){
		                  $slogan.="...";
		                }
	              	}else{
	                	$slogan.="&nbsp;";
	              	}
	              	if($result->girl_type==2){
	              		if($result->zip_city!=''){
	                      $zip_city=$result->zip_city;
	                    }else{
	                      $zip_city="No information available";
	                    }

	                    if($result->region!=0){
	                      //$region_name=$this->model_common->GetRegionName($result->region);
	                      $region_name=$this->model_common->GetSpecificLanguage('region_language','language_text',$result->region,$language_id);

	                    }else{
	                      $region_name="No Information Available";
	                    }
	              	}if($result->girl_type==1){
	              		$girl_club_details=$this->model_common->GetParticularClubDetails($result->main_girl_id);
	              		if($girl_club_details[0]->zip_city!=''){
	                      $zip_city=$girl_club_details[0]->zip_city;
	                    }else{
	                      $zip_city="No information available";
	                    }

	                    if($girl_club_details[0]->canton!=0){
	                      //$region_name=$this->model_common->GetRegionName($girl_club_details[0]->canton);
	                      $region_name=$this->model_common->GetSpecificLanguage('region_language','language_text',$girl_club_details[0]->canton,$language_id);
	                    }else{
	                      $region_name="No Information Available";
	                    }
	              	}
	              	//$girl_status=$this->model_common->GetStatus($result->status);
	              	$girl_status = $this->model_common->GetStatusLanguage($result->status,$language_id);
	              	if($region!=''){
	              		if($static_region_name==$region_name){
	              			$data[]=array(
								'type' => 'girl',
								'girl_type' => $result->girl_type,
								'encoded_id' => base64_encode($result->id),
								'girl_id' => $result->id,
								'girl_name' => $result->name,
								'slogan' => $slogan,
								'zip_city' => $zip_city,
								'region' => $region_name,
								'girl_image' => $get_girl_image,
								'girl_status' => $girl_status,
								'position' => $result->partner_position
							);
	              		}
	              	}else{
	              		$data[]=array(
							'type' => 'girl',
							'girl_type' => $result->girl_type,
							'encoded_id' => base64_encode($result->id),
							'girl_id' => $result->id,
							'girl_name' => $result->name,
							'slogan' => $slogan,
							'zip_city' => $zip_city,
							'region' => $region_name,
							'girl_image' => $get_girl_image,
							'girl_status' => $girl_status,
							'position' => $result->partner_position
						);
	              	}
					
					$slogan='';	
		    	//}
		    	}
		    	//$data[]=$result;
		    }
		}

		if(($region!='') || ($name!='') || ($status=='2')){
			$this->db->select('partner_club.*');
			$this->db->from('partner_club');
			$this->db->from('users');
			$this->db->where('partner_club.partner_id',$partner_id);
			if($region!=''){
				$this->db->where('partner_club.canton',$region);
			}
			if($name!=''){
				$this->db->where("(`partner_club`.`name` like '%".$name."%' OR `partner_club`.`zip_city` like '%".$name."%')");		
			}
			
			$this->db->where('partner_club.status','1');
			$this->db->where('partner_club.uid = users.id');
			$this->db->where("users.status",'1');
			$this->db->order_by('partner_club.partner_position','ASC');
			$club=$this->db->get();
			//echo $this->db->last_query();
			if($club->num_rows>0){
				foreach($club->result() as $result){
					/*$this->db->where('id',$result->uid);
			    	$exist_club_user=$this->db->get('users');
			    	if($exist_club_user->num_rows>0){*/
						$get_club_image=$this->partner_model->GetPartnerClubLatestImage($partner_folder_name,$result->id);
						$data[]=array(
							'type' => 'club',
							'club_name' => $result->name,
							'slogan' => $result->slogan,
							'encoded_id' => base64_encode($result->id),
							'club_image' => $get_club_image,
							'position' => $result->partner_position
						);
					//}
				}
			}
		}
		$counter = $page+$per_page_value;
		
		if($counter<=count($data)){
			$new_counter=$counter;
		}if($counter>count($data)){
			$new_counter=count($data);
		}
		for($whole_count=$page;$whole_count<$new_counter;$whole_count++){	
				$main_whole_data[]=$data[$whole_count];
		}
		$total_number = count($data);
		if(count($main_whole_data)>0){
			$return = array('success' => 'true','total_record' => $total_number,'result' => $main_whole_data);
		}else{
			$return = array('success' => 'false','total_record' => $total_number,'result' => $main_whole_data);
		}
		
		return $return;
    }

    function submit_comment($data){
    	$partner_id = $data['partner_id'];
    	$type = $data['type'];
    	$girl_id = $data['girl_id'];
    	$main_id = $data['main_id'];
    	$user_id = $data['user_id'];
    	$comment = $data['comment'];
    	$insert_array=array(
            'type' => $type,
            'foreign_id' => $girl_id,
            'partner_girl_club_id' => $main_id,
            'partner_id' => $partner_id,
            'uid' => $user_id,
            'comment' => $comment,
            'add_date' => date('Y-m-d H:i:s')
        );
        $insert=$this->db->insert('comment',$insert_array);
        if($insert){
        	$success = 'true';
        }else{
        	$success = 'false';
        }
        $return = array('success' => $success);
        return $return;
    }

    function check_login($data){
		$username = $data['username'];
		$password = $data['password'];
		$this->db->where('username',$username);
		$this->db->where('password',md5($password));
		$q=$this->db->get('users');
		if($q->num_rows>0){
			foreach($q->result() as $result){
				if($result->status==0){
					$success = 'false';
					$return_msg="Your profile is inactive";
				}else{
					$user_id=$result->id;
					$user_type=$result->type;
					$user_balance=$result->balance;
					$session_userdata=array(
						'user_id' => $user_id,
						'user_type' => $user_type,
						'user_balance' => $user_balance
					);
					$this->session->set_userdata($session_userdata);
					$update_user=array(
						'last_login' => date('Y-m-d H:i:s'),
						'login_ip' => $_SERVER['REMOTE_ADDR']
					);
					$this->db->where('id',$user_id);
					$this->db->update('users',$update_user);
					$success = 'true';
					$return_msg="";
				}
			}
		}else{
			$success = 'false';
			$return_msg="Invalid username or password.";
		}
		$return = array('success' => $success,'msg' => $return_msg);
		return $return;
	}

	function language_list(){
		$data=array();
		$this->db->where('status','1');
		$this->db->where('show_status','1');
		$this->db->order_by('id','DESC');
		$q=$this->db->get('language_list');
		if($q->num_rows>0){
			foreach($q->result() as $result){
				if($result->language_image!=''){
					$language_image=base_url().'uploads/site_language/'.$result->language_image;
					$data[]=array(
						'language_id' => $result->id,
						'language_name' => $result->language_name,
						'language_code' => $result->language_code,
						'language_image' => $language_image
					);
				}
				
			}
		}
		if(count($data)>0){
			$result=array('success' => 'true','result' => $data);
		}else{
			$result = array('success' => 'false','result' => $data);
		}
		return $result;
	}

	function static_text($data){
		$details=array();
		$partner_id = $data['partner_id'];
		$language_id = $data['language_id'];
		$this->db->where('id',$language_id);
		$lang=$this->db->get('language_list');
		if($lang->num_rows>0){
			foreach($lang->result() as $lang_result){
				$language_name=$lang_result->language_name;
				$language_image = $lang_result->language_image;
				$language_image_url=base_url().'uploads/site_language/'.$language_image;
			}
		}
		$this->db->where('partner_id',$partner_id);
		$this->db->where('language_id',$language_id);
		$q=$this->db->get('static_language');
		if($q->num_rows>0){
			foreach ($q->result() as $result) {
				$details[]=array(
					'text_name' => $result->text_name,
					'language_text' => $result->language_text
				);
			}
		}
		if(count($details)>0){
			$result=array('success' => 'true','language_name' => $language_name,'language_img' => $language_image_url,'result' => $details);
		}
		else{
			$result=array('success' => 'false','language_name' => $language_name,'language_img' => $language_image_url,'result' => $details);
		}
		return $result;
	}

	function GetExistance($details){
		//echo '<pre>';print_r($details);
		$this->db->where('id',$details['id']);
		if($details['type']=='girl'){
			$q=$this->db->get('partner_girl');
		}if($details['type']=='club'){
			$q=$this->db->get('partner_club');
		}
		//echo $this->db->last_query();
		if($q->num_rows>0){
			$return = array('success' => 'true');
		}else{
			$return = array('success' => 'false');
		}
		return $return;
	}

	function ClubGirlsList($details){
		$data=array();
		$club_id = $details['club_id'];
		$this->db->select('club_girl.*,club_girl_position.position');
		$this->db->from('club_girl');
		$this->db->from('club_girl_position');
		$this->db->where('club_girl_position.club_id',$club_id);
		$this->db->where('club_girl_position.girl_id = club_girl.id');
		$this->db->where('club_girl_position.position !=','0');
		$this->db->order_by('club_girl_position.position','ASC');
		$q=$this->db->get();
		if($q->num_rows>0){
			foreach($q->result() as $result){
				$girl_image = $this->model_common->ClubGirlLatestImage('girl',$club_id,$result->id);
				$data[]=array(
					'girl_id' => $result->id,
					'girl_image' => $girl_image,
					'girl_name' => $result->name,
					'position' => $result->position
				);
			}
		}
		if(count($data)>0){
			$return = array('success' => 'true','result' => $data);
		}else{
			$return = array('success' => 'false','result' => $data);
		}
		return $return;
	}

	function ClubGirlDetails($details){
		$data=$girl_bio=$service_list_array=$available_date=$other_img=$text=array();
		$language_id=3;
		$week_array=array('Sonntag' => '1','Montag' => '2','Dienstag' => '3','Mittwoch' => '4','Donnerstag' => '5','Freitag' => '6','Samstag' => '7');
		$girl_id = $details['girl_id'];
		$this->db->where('id',$girl_id);
		$q=$this->db->get('club_girl');
		if($q->num_rows>0){
			foreach($q->result() as $result){
				//$girl_image = $this->model_common->ClubGirlLatestImage('girl',$club_id,$result->id);
				$this->db->where('girl_id',$girl_id);
				$club = $this->db->get('club_girl_position');
				if($club->num_rows>0){
					foreach($club->result() as $club_result){
						$club_id = $club_result->club_id;
					}
				}
				$girl_image = $this->model_common->ClubGirlLatestImage('girl',$club_id,$result->id);
				$get_domain_name=$this->model_common->GetDomainName($club_id);
				$this->db->where('girl_id',$girl_id);
				$this->db->order_by('position','ASC');
				$girl_other_img = $this->db->get('club_girl_images');
				//echo $this->db->last_query();
				if($girl_other_img->num_rows>0){
					foreach($girl_other_img->result() as $club_girl_img_result){
						$Upload_path='uploads/'.$get_domain_name.'/girl/'.$club_girl_img_result->girl_images;
						if(file_exists($Upload_path)){
							$image_path=base_url().'uploads/'.$get_domain_name.'/girl/'.$club_girl_img_result->girl_images;
							$other_img[]=array(
								'images' => $image_path,
							);
						}
					}
				}
				if($result->weight!='0'){
					$weight = $result->weight.' Kg';
				}if($result->size!='0'){
					$size = $result->size.' cm';
				}
				$girl_bio[]=array(
					'Sprachen' => $this->model_common->GetLanguageName($result->language,$language_id),
					'Alter' => $result->age,
					'weight' => $weight,
					'size' => $size
				);
				$text[]=array(
					'title' => $result->text_ads_title,
					'text' => $result->text_ads_text
				);
				if($result->services!=''){
					//$service_array=$this->model_common->GetServiceList($result->services);
					$service_array = $this->model_common->GetServiceListLanguage($result->services,$language_id);
					//echo '<pre>';print_r($service_array);
					if(count($service_array)>0){
						foreach($service_array as $services){
							$service_list_array[]=array(
								'name'=> $services
							);
						}
					}
				}

				if($result->full_available==1){
					$available_date[]=array('day_name'=>'24/7 Available','time_details' => '');
				}else{
					$this->db->where('girl_id',$result->id);
					$club_girl_time = $this->db->get('club_girl_available_time');
					if($club_girl_time->num_rows>0){
						foreach($club_girl_time->result() as $club_girl_time_result){
							$available_date[]=array(
								'day_name' => array_search($club_girl_time_result->week_id,$week_array),
					            'time_details' => date('H:i',strtotime($club_girl_time_result->start_time)).' : '.date('H:i',strtotime($club_girl_time_result->end_time))
							);
						}
					}else{
						$this->db->where('id',$club_id);
						$club_details = $this->db->get('club');
						if($club_details->num_rows>0){
							foreach($club_details->result() as $club_details_result){
								$club_full_available = $club_details_result->full_available;
							}
						}
						if($club_full_available==1){

							$available_date[]=array('day_name'=>'24/7 Available','time_details' => '');;
						}else{
							$this->db->where('club_id',$club_id);
							$club_time = $this->db->get('club_available_time');
							if($club_time->num_rows>0){
								foreach($club_time->result() as $club_time_details){
									$available_date[]=array(
										'day_name' => array_search($club_time_details->week_id,$week_array),
							            'time_details' => date('H:i',strtotime($club_time_details->start_time)).' : '.date('H:i',strtotime($club_time_details->end_time))
									);
								}
							}
						}
					}
				}

				$details=array(
					'girl_name' => $result->name,
					'girl_main_image' => $girl_image,
					'girl_other_img' => $other_img,
					'girl_bio' => $girl_bio,
					'girl_text' => $text,
					'girl_service' => $service_list_array,
					'girl_available_time' => $available_date,
				);
			}
		}
		$return = array('success' => 'true','result' => $details);
		return $return;
	}

	function UpdatePartnerGirlPosition($details){
		$partner_id = $details['partner_id'];
		$this->db->select('partner_girl.*');
		$this->db->from('partner_girl');
		$this->db->from('users');
		$this->db->where('partner_girl.partner_id',$partner_id);
		$this->db->where('partner_girl.uid = users.id');
		$this->db->where("partner_girl.status NOT IN ('3','7')");
		$this->db->where('users.status','1');
		$this->db->order_by('partner_position','ASC');
		$q=$this->db->get();
		//echo $this->db->last_query();die;
		if($q->num_rows>0){
			$girl_counter=1;
			foreach($q->result() as $result){
				//echo $girl_counter."----".$result->partner_position.'<br/>';
				if($girl_counter!=$result->partner_position){
					$update_new_position=array('partner_position' => $girl_counter);
					$this->db->where('id',$result->id);
					$this->db->update('partner_girl',$update_new_position);
				}
				$girl_counter++;
			}
		}

		$update_new_status_pos = array('partner_position' => '0');
		$this->db->where('partner_id',$partner_id);
		$this->db->where("status IN ('3','7')");
		$update = $this->db->update('partner_girl',$update_new_status_pos);
		if($update){
			$return = array('success' => 'true');
		}else{
			$return = array('success' => 'false');
		}
		return $return;
	}

	function UpdatePartnerClubPosition($details){
		$partner_id = $details['partner_id'];
		$this->db->select('partner_club.*');
		$this->db->from('partner_club');
		$this->db->from('users');
		$this->db->where('partner_club.partner_id',$partner_id);
		$this->db->where('partner_club.uid = users.id');
		$this->db->where("partner_club.status NOT IN ('0')");
		$this->db->where('users.status','1');
		$this->db->order_by('partner_position','ASC');
		$q=$this->db->get();
		//echo $this->db->last_query();die;
		if($q->num_rows>0){
			$club_counter=1;
			foreach($q->result() as $result){
				//echo $club_counter."----".$result->partner_position.'<br/>';
				if($club_counter!=$result->partner_position){
					$update_new_position=array('partner_position' => $club_counter);
					$this->db->where('id',$result->id);
					$this->db->update('partner_club',$update_new_position);
				}
				$club_counter++;
			}
		}
		//die;
		$update_new_status_pos = array('partner_position' => '0');
		$this->db->where('partner_id',$partner_id);
		$this->db->where("status IN ('0')");
		$update = $this->db->update('partner_club',$update_new_status_pos);
		if($update){
			$return = array('success' => 'true');
		}else{
			$return = array('success' => 'false');
		}
		return $return;
	}

	function SubmitContact($details){
		//echo '<pre>';print_r($details);die;
		$insert_array = array(
			'partner_id' => $details['partner_id'],
			'name' => $details['name'],
			'email' => $details['email'],
			'contact_type' => $details['contact_type'],
			'message' => $details['message'],
			'added_date' => date('Y-m-d H:i:s')
		);
		$insert = $this->db->insert('contact_us',$insert_array);
		if($insert){
			$return = array('success' => 'true');
		}else{
			$return = array('success' => 'false');
		}
		return $return;
	}

	function WebsiteDetails($details){
		$club_id = $details['club_id'];

		$contact_array=$details=$text_array=$avaliable_array=$establish_list=$establish_array=$payment_list=$image_array=$week_new_array=array();
		$available_time='';
		$week_array=array('Sonntag' => '1','Montag' => '2','Dienstag' => '3','Mittwoch' => '4','Donnerstag' => '5','Freitag' => '6','Samstag' => '7');
		

		

		
		$this->db->where('id',$club_id);
		$q=$this->db->get('club');
		//echo $this->db->last_query();
		if($q->num_rows>0){
			foreach($q->result() as $result){
				//$get_club_image=$this->partner_model->GetPartnerClubLatestImage($partner_folder_name,$result->id);
				$club_images=$this->club_model->GetClubImages($result->id);

				
				
				

				if($result->canton!=0){
					//$canton=$this->model_common->GetRegionName($result->canton);
					$canton=$this->model_common->GetSpecificLanguage('region_language','language_text',$result->canton,'3');
				}else{
					$canton = '';
				}
				//$club_type=$this->model_common->GetClubType($result->club_type);
				$club_type = $this->model_common->GetSpecificLanguage('club_type_language','language_text',$result->club_type,'3');
				$contact_array[]=array(
					'strasse' => $result->road,
					'plz_ort' => $result->zip_city,
					'address' => $result->additional_address,
					'region' => $canton,
					'telephone' => $result->phone,
					'email' => $result->email,
					'website' => $result->website,
					'latitude' => $result->latitude,
					'longitude' => $result->longitude
				);
				$text_array[]=array(
					'title' => $result->service_title,
					'text' => $result->service_test
				);


				$full_available=$result->full_available;
				
                $available=$this->club_model->GetClubTimeAvailabilty('club_available_time',$result->id);
                if(count($available)>0){
                	foreach($available as $avalabilty){
                		$week_new_array[]=array(
                			'day_name' => array_search($avalabilty->week_id,$week_array),
                			'time_details' => date('H:i',strtotime($avalabilty->start_time)).' : '.date('H:i',strtotime($avalabilty->end_time))
                		);
                	}
                	$available_time=date('H:i',strtotime($avalabilty->start_time)).' : '.date('H:i',strtotime($avalabilty->end_time));
                }

				

				if($result->establish!=''){
					$club_establish_array=$this->club_model->GetEstablish($result->establish,'3');
					foreach ($club_establish_array as $establish) {
						$establish_list[]=array(
							'name' => $establish
						);
					}
				}

				$establish_array[]=array(
					'no_of_room' => $result->no_of_room,
					'no_of_suite' => $result->no_of_suite,
					'establish' => $establish_list
				);

				if($result->payment!=''){
					$payment_array=$this->model_common->GetPayementList($result->payment,'3');
					foreach ($payment_array as $payment) {
						$payment_list[]=array(
							'payment_name' => $payment
						);
					}
				}

				

				if(count($club_images)>0){
					foreach($club_images as $image){
						$Upload_path='uploads/club/thumb/'.$image->club_image;
                		
                        if(file_exists($Upload_path))
                        {
                            $image_path=base_url().'uploads/club/thumb/'.$image->club_image;
                            $image_big_path = base_url().'uploads/club/'.$image->club_image;
                            $image_array[]=array(
                            	'image_id' => $image->id,
                            	'image_url' => $image_path,
                            	'big_image_url' => $image_big_path,
                            	'image_name' => $image->club_image
                            );
                        }
					}
				}
				

				$details[]=array(
					
					'club_id' => $result->id,
					'club_name' => $result->name,
					'club_type' => $club_type,
					'slogan' => $result->slogan,
					/*'latest_image' => $get_club_image,*/
					'contact' => $contact_array,
					'text' => $text_array,
					'full_available' => $full_available,
					'week_array' => $week_new_array,
					'establish' => $establish_array,
					'payment' => $payment_list,
					'images' => $image_array
				);
			}
		}
		$return=array('success' => 'true','result' => $details);
		return $return;
	}
}
?>