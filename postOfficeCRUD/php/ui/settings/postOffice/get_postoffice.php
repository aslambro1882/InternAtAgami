<?php
$base_path = dirname(dirname(dirname(dirname(__FILE__))));
// include_once($base_path . '/ui/login/check_session.php');


if ($_SERVER['REQUEST_METHOD'] != 'POST') {
	$response['error'] = true;
	$response['message'] = "Invalid request merthod!";
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

$pageno = 1;
if (isset($_POST['pageno'])) {
    $pageno = (int) $_POST['pageno'];
}

$limit = 25;
if (isset($_POST['limit'])) {
    $limit = (int) $_POST['limit'];
}

$result = get_all_postoffice($dbcon,$pageno,$limit);

if ($result->num_rows > 0) {
	$postoffice_array = array();
	while ($row = $result->fetch_array(MYSQLI_ASSOC)) {
		$postoffice_array[] = $row;
	}

	$response['error'] = false;
	$response['data'] = $postoffice_array;
} else {
	$response['error'] = true;
	$response['message'] = "No Degree found! ";
	echo json_encode($response);
	exit();
}
echo json_encode($response);

//loc_postoffice (postcode, po, ps, iscity, districtno)
function get_all_postoffice($dbcon,$pageno,$limit)
{
	$startindex=($pageno-1)*$limit;

	$sql = "SELECT postcode,po, ps, iscity,
					districtno,(SELECT districtname FROM loc_district WHERE districtno=p.districtno) as districtname
			FROM loc_postoffice as p
			LIMIT ?,?";
	$stmt = $dbcon->prepare($sql);
	if (!$stmt) {
		echo $dbcon->error;
	}
	$stmt->bind_param("ii",$startindex,$limit);
	$stmt->execute();
	$result = $stmt->get_result();;
	$stmt->close();
	return $result;
}
