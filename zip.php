<?php

add_action("wp_ajax_ck_image_doc_to_zip", "ck_image_doc_to_zip");

function ck_image_doc_to_zip(){
	if( !empty($_POST['project_id']) ){
		$ck_project_id = $_POST['project_id'];
		$ck_files = array();
		$ck_3d_image_data = get_post_meta($ck_project_id,'ck_3d_image_data',true);
		if(!empty($ck_3d_image_data)){
			$ck_3d_image_data_encode = stripslashes($ck_3d_image_data);
			$ck_image_data = json_decode($ck_3d_image_data_encode, true);
			$ck_counter = 0;

			/* Data Get From Main Project Image */

			foreach ($ck_image_data as $key => $value) {
				$ck_counter++;
				if(!empty($value['rename'])){
					$ck_files[] = $value['rename'];
				}
			}
			
			foreach($ck_image_data as $key => $add_files){
				foreach($add_files['add_files'] as $sub_key => $sub_value){
		   			$ck_counter++;
		   			if(!empty($sub_value['path'])){
		   				$ck_files[] = basename($sub_value['path']);
					}
				}
			}
		}

		if(!empty($ck_files)){
			if(extension_loaded('zip')){ 
				$zip = new ZipArchive(); // Load zip library 
				$zip_name = time().".zip"; // Zip name

				$path = get_home_path();

				$srcDir = $path."wp-content/uploads/p3d/";

				if ($zip->open($_SERVER['DOCUMENT_ROOT']."/ckzip/".$zip_name, ZIPARCHIVE::CREATE) != TRUE) {
		        	wp_send_json(array('result'=>false, 'message'=>__('Could not open archive')));
				}

				//$_SERVER['DOCUMENT_ROOT']."/ckzip/".$zip_name;				
				foreach($ck_files as $file){
					$zip->addFromString(basename($file),  file_get_contents($srcDir.$file));
				}

				$zip->close();
			    wp_send_json(array('result'=>true, 'message'=>site_url()."/ckzip/".$zip_name));
			}else{
				wp_send_json(array('result'=>false, 'message'=>__('zip extension Not Loaded.')));
			}
		}
	}
	die;
}

?>
