<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

error_reporting(E_ALL);
ini_set("display_errors", 1);
class Api extends CI_Controller
{
	
    function __construct()
	{
		parent::__construct();
		$this->load->helper('form');
		$this->obj = new stdClass();
		header("Access-Control-Allow-Origin: *");
		header("Content-Type: application/json; charset=UTF-8");
		header('Access-Control-Allow-Methods: GET, POST, OPTIONS');

	}
	function test_side(){
		echo "test side";
	}
	function ImageUpload(){
		$result = array();
		//echo json_encode($_FILES['profileImage']);
		$auth_token = $_POST['auth_token'];

		$folder = "uploads/smartfued_image/";

		$path_parts = pathinfo($_FILES["profileImage"]["name"]);
		
		$upload_image = $path_parts['filename']."_".time().".".$path_parts['extension'];
		$target_dir   = $folder;
		$target_file  = $target_dir ."/". basename($upload_image);
		if(move_uploaded_file($_FILES["profileImage"]["tmp_name"], $target_file)){
			chmod($target_file, 0777);

			$result['status'] = "success";
			$result['upload_image'] = $upload_image;
			$image_upload = curl_init();
	        $image_upload_url = "http://173.249.8.195:3001/apidemo/updtprofileimage"; // where you want to post data
	        curl_setopt($image_upload, CURLOPT_URL,$image_upload_url);
	        curl_setopt($image_upload, CURLOPT_POST, true);  // tell curl you want to post something
	        curl_setopt($image_upload, CURLOPT_POSTFIELDS, "uploaded_image=".$upload_image."&auth_token=".$auth_token); // define what you want to post
	        curl_setopt($image_upload, CURLOPT_RETURNTRANSFER, true); // return the output in string format
	        $image_output = curl_exec ($image_upload); // execute
	        curl_close ($image_upload); // close curl handle
	        $image_list = json_decode($image_output);
	        echo json_encode($image_list);
		}else{
			$result['status'] = "error";
			$result['message'] = "Error to upload image";
			echo json_encode($result);
		}
		
	}
}
?>