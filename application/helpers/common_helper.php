<?php
function pr($array=array())
{
	echo '<pre>';
	print_r($array);
	die;
}
function status($stat='')
{
    if($stat!=''){
        return ($stat=='0')?'<img src="'.base_url().'img/in.png" title="Block"/>':'<img src="'.base_url().'img/act.png" title="Active"/>';
    }
}
function is_login(){
    $CI =& get_instance();
    $data['user_id'] = $CI->session->userdata('user_id');
    $data['user_type'] = '';
    if($data['user_id']!=''){
        $data['user_type'] = $CI->session->userdata('user_type');
        //return $data;
    }/*else{
        return $data[''];
    }*/
    return $data;

}
function show_msg()
{
    $CI =& get_instance();
    if($CI->session->flashdata('successmsg'))
    {
        return '<div class="successmsg">'.$CI->session->flashdata('successmsg').'</div>';
    }
    if($CI->session->flashdata('errormsg'))
    {
        return '<div class="errormsg">'.$CI->session->flashdata('errormsg').'</div>';
    }    
    
}
function string_genaretor( $length = 8, $chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!@#$%&?*' ) 
{
    return substr( str_shuffle( $chars ), 0, $length );
}

function race($id=''){
    $arr=array('1'=>'White',
        '2'=>'Hispanic or Latino',
        '3'=>'Black or African American',
        '4'=>'Native American or American Indian',
        '5'=>'Asian',
        '6'=>'Pacific Islander',
        '7'=>'East Indian',
        '8'=>'Other');
    
    $opt='';
    
    foreach ($arr as $key=>$val):
    $sel=($id!='' && $id==$key)?'selected':'';    
    $opt.='<option '.$sel.' value="'.$key.'">'.$val.'</option>';    
    endforeach;
    return $opt;
    
}

function treatment_orientation($id=''){
    $arr=array('1'=>'Science',
        '2'=>'Alternative',
        '3'=>'Both');
    
    $opt='';
    
    foreach ($arr as $key=>$val):
    $sel=($id!='' && $id==$key)?'selected':'';    
    $opt.='<option '.$sel.' value="'.$key.'">'.$val.'</option>';    
    endforeach;
    return $opt;
}

function severity($id=''){
    $arr=array('1'=>'Mild',
        '2'=>'Moderate',
        '3'=>'Severe');
    
    $opt='';
    
    foreach ($arr as $key=>$val):
    $sel=($id!='' && $id==$key)?'selected':'';    
    $opt.='<option '.$sel.' value="'.$key.'">'.$val.'</option>';    
    endforeach;
    return $opt;  
}
function When_it_occur($id=''){
    $arr=array('1'=>'Always present',
        '2'=>'Comes and Goes');
    
    $opt='';
    
    foreach ($arr as $key=>$val):
    $sel=($id!='' && $id==$key)?'selected':'';    
    $opt.='<option '.$sel.' value="'.$key.'">'.$val.'</option>';    
    endforeach;
    return $opt;
    
}

function Showseverity($id=''){
    $txt='';
    if($id=='1'){
        $txt='Mild';
    }elseif($id=='2'){
        $txt='Moderate';
    }elseif($id=='3'){
        $txt='Severe';
    }
    return $txt;
}
function ShowWhen_it_occur($id=''){
    $txt='';
    if($id=='1'){
        $txt='Always present';
    }elseif($id=='2'){
        $txt='Comes and Goes';
    }
    return $txt;
}

function treatment_effectivenes($id=''){
    $arr=array('-3'=>'Much Worse',
        '-2'=>'Moderately Worse',
        '-1'=>'Slightly Worse',
        '0'=>'No Change',
        '1'=>'Slightly Improved',
        '2'=>'Moderately Improved',
        '3'=>'Much Improved'
        );
    
    $opt='';
    
    foreach ($arr as $key=>$val):
    $sel=($id!='' && $id==$key)?'selected':'';    
    $opt.='<option '.$sel.' value="'.$key.'">'.$val.'</option>';    
    endforeach;
    return $opt;
    
}

function treatment_rating($type,$treatment_id,$id,$rating_type='behavior')
{
    if($rating_type=='behavior'){
        $rat_type='BehaviorType!=1';
    }elseif ($rating_type=='symptoms') {
        $rat_type='BehaviorType=1';
    }
        
    $CI =& get_instance();
    $res=$CI->db->query('select * from users_to_behavior where  '.$rat_type.' and Behavior='.$treatment_id);
    $tot=$res->num_rows();
    
    if($type=='BehaviorDosage'){
        $con='BehaviorDosage=\''.$id.'\'';
    }elseif ($type=='BehaviorDailySchedule') {
        $con='BehaviorDailySchedule='.$id;
    }elseif ($type=='BehaviorCalendarSchedule') {
        $con='BehaviorCalendarSchedule='.$id;
    }elseif ($type=='BehaviorMeals') {
        $con='BehaviorMeals='.$id;
    }
    
    $avrg=$CI->db->query('select * from users_to_behavior where '.$rat_type.' and Behavior='.$treatment_id.' and '.$con);
    $CI->db->last_query();
    $tot_avrg=$avrg->num_rows();        
    
    return $cal=$tot_avrg*100/$tot;
}

function treatment_symptoms_rating($tot,$type,$treatment_id,$diagnosis_id)
{
    $CI =& get_instance();
    //$res=$CI->db->query('select * from users_to_behavior where diagnosis_id='.$diagnosis_id);
    //$tot=$res->num_rows();
    if($type=='postitive'){
        $con=' and TreatmentEffectiveness1 !=""';
    }elseif ($type=='negative'){
        $con=' and TreatmentEffectiveness2 !=""';
    }
    $avrg=$CI->db->query('select * from users_to_behavior where  Behavior='.$treatment_id.$con);
    //return $CI->db->last_query();
    $tot_avrg=$avrg->num_rows(); 
    return $cal=$tot_avrg*100/$tot;
}

function symptoms_rating($tot,$type,$symptoms_id,$diagnosis_id)
{
    $CI =& get_instance();
    //$res=$CI->db->query('select * from users_to_problem where diagnosis_id='.$diagnosis_id);
    //$tot=$res->num_rows();
    if($type=='postitive'){
        $con=' and p_TreatmentEffectiveness1 !=""';
    }elseif ($type=='negative'){
        $con=' and p_TreatmentEffectiveness2 !=""';
    }
    $avrg=$CI->db->query('select * from users_to_problem where Problem='.$symptoms_id.$con);
    //return $CI->db->last_query();
    $tot_avrg=$avrg->num_rows(); 
    return $cal=$tot_avrg*100/$tot;
}

function show_country($id)
{
    $CI =& get_instance();
    $CI->db->select('*');
    $CI->db->from('country');
    $CI->db->where('country_id',$id);
    $res=$CI->db->get();
    $r=$res->row_array();
    echo $r['country_name'];
    //$row=$res->row_array();
    //return $row['country_name'];
}

function show_state($id)
{
    $CI =& get_instance();
    $CI->db->select('*');
    $CI->db->from('state');
    $CI->db->where('state_id',$id);
    $res=$CI->db->get();
    $r=$res->row_array();
    echo $r['state_name'];
    //$row=$res->row_array();
    //return $row['country_name'];
}

function show_city($id)
{
    $CI =& get_instance();
    $CI->db->select('*');
    $CI->db->from('city');
    $CI->db->where('city_id',$id);
    $res=$CI->db->get();
    $r=$res->row_array();
    echo $r['city_name'];
    //$row=$res->row_array();
    //return $row['country_name'];
}
function pic_resize($source_path,$thumb_path,$name,$width,$height)
{
    $CI =& get_instance();
    $config['image_library'] = 'gd2';
    $config['source_image']  = $source_path.$name['orig_name'];
    $config['new_image']  =  $thumb_path.$name['orig_name'];
    $config['width']	 = $width;
    $config['height'] = $height;  
	$config['maintain_ratio'] = TRUE;
    $CI->image_lib->initialize($config);      
    $CI->image_lib->resize();
}

function  socail_share_link($id)
{
    $CI =& get_instance();
    $CI->db->select('*');
    $CI->db->from('site_settings');
    $CI->db->where('settings_id',$id);
    $res=$CI->db->get();
    $r=$res->row_array();
    echo $r['site_url'];
}
function  adds_banner($id)
{
    $CI =& get_instance();
    $CI->db->select('*');
    $CI->db->from('banner_manage');
    $CI->db->where('banner_id',$id);
    $res=$CI->db->get();
    $r=$res->row_array();
    echo $r['banner'];
}
?>
