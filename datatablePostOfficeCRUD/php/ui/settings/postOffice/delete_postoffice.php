<?php
	$base_path = dirname(dirname(dirname(dirname(__FILE__))));
    // include_once($base_path . '/ui/login/check_session.php');


	if($_SERVER['REQUEST_METHOD'] != 'POST'){
			$response['error'] = true;
			$response['message'] = "Invalid request merthod!";
			echo json_encode($response);
			exit();
		}

		if(isset($_POST['postcode']) && strlen($_POST['postcode'])>0 )
		{
			$postcode = (int)$_POST['postcode'];
		}else{
			$response['error'] = true;
			$response['message'] = "Data Must be Selected!";
			echo json_encode($response);
			exit();

		}

		require_once($base_path . "/db/Database.php");
		$db = new Database();
		$dbcon = $db->db_connect();

		if (!$db->is_connected()) {
			$response['error'] = true;
			$response['message'] = "Database is not connected!";
			echo json_encode($response);
			exit();
		}

		$result = delete_postoffice($dbcon,$postcode);
		if($result>0){
				$response['error'] = false;
				$response['message'] = "Deleted";
		}
	else{
		$response['error'] = true;
		$response['message'] = "Delete Failed";
	}
	echo json_encode($response);


	//loc_postoffice (postcode, po, ps, iscity, districtno)
	function delete_postoffice($dbcon,$postcode){
		$sql = "DELETE
				FROM loc_postoffice
				WHERE postcode=?";
		$stmt = $dbcon->prepare($sql);
		$stmt->bind_param("i",$postcode);
		$stmt->execute();
			//var_dump($stmt);
		return $stmt->affected_rows;
	}
