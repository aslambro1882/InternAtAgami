<?php
$base_path = dirname(dirname(dirname(__FILE__)));
// include_once($base_path."/login/check_session.php");


$response = array();
if ($_SERVER['REQUEST_METHOD'] != 'POST') {
    $response['error'] = true;
    $response['message'] = "Invalid Request method";
    echo json_encode($response);
    exit();
}

try {

    $base_path = dirname(dirname(dirname(dirname(__FILE__))));
    require_once($base_path . "/db/Database.php");

    $db = new Database();
    $dbcon = $db->db_connect();
    if (!$db->is_connected()) {
        throw new \Exception("Database is not connected!", 1);
    }

    // 2 index
    // 1 mandatory
    // 0 optional

    $schemaKeys = array(
        "postcode" => 2,
        "po" => 0,
        "ps" => 0,
        "iscity" => 0,
        "districtno" => 0
    );

    $postoffices = json_decode($_POST['data'], true);

    $new = 0;
    $update = 0;
    $multiple = 0;

    $logs_array = array();

    for ($i = 0; $i < count($postoffices); $i++) {
        $postoffice = $postoffices[$i];
        $postcode = -1;
        if (isset($postoffice['postcode'])) {
            $postcode = (int) $postoffice['postcode'];
        }

        $postcode_res = get_postoffice($dbcon, $postcode);

        if ($postcode_res->num_rows > 0) {
            $nos = update_postoffice($dbcon, $postoffice, $schemaKeys, $postcode);

            if (!$nos['error']) {
                $resp = array();
                $resp['error'] = false;
                $resp['postcode'] = $postcode;
                $resp['message'] = "Postofice is Updated Successfully.";
                $logs_array[] = $resp;
                $update++;
            } else {
                throw new \Exception($nod['msg'], 1);
            }
        } else {
            $new_postcode = add_postoffice($dbcon, $postoffice, $schemaKeys);
            if (!$new_postcode['error']) {
                $resp = array();
                $resp['error'] = false;
                $resp['postcode'] = $new_postcode['insert_id'];
                $resp['message'] = "Postofice is Created Successfully.";
                $logs_array[] = $resp;
                $new++;
            } else {
                throw new \Exception($new_postcode['msg'], 1);
            }
        }
    }

    $response['error'] = false;
    $response['message'] = "Created: $new; Updated: $update; Multiple Ignored: $multiple";
} catch (Exception $e) {
    $response['error'] = true;
    $response['message'] = $e->getMessage();
}

echo json_encode($response);

$dbcon->close();

/**
 * Local Function
 */

//bir_workstatus (postcode,wstatustitle,controllevel,polarity,colorcode)
function update_postoffice($dbcon, $postoffice, $schemaKeys, $postcode)
{

    $ignore_list = array('dbcon', 'postcode');
    $update_param_type = array();
    $update_params_list = array();
    $update_params_vars = array();
    $sk = array_keys($schemaKeys);

    foreach ($sk as $parameter) {
        $name = $parameter;
        if (in_array($name, $ignore_list)) {
            continue;
        }

        if (!isset($postoffice[$name])) {
            if ($schemaKeys[$name] === 0) {
                continue;
            } else {
                // throw exception or something
                $val = "";
            }
        } else {
            $val = $postoffice[$name];
        }

        // if (is_null($val)) {
        //     if ($schemaKeys[$name] !== 0) {
        //         continue;
        //     }
        // }

        $update_params_list[] = $name . '=?';
        $update_params_vars[] = $val;
        $update_param_type[] = 's';
    }

    // add postcode
    $update_params_vars[] = &$postcode;
    $update_param_type[] = 'i';


    $sql = "UPDATE loc_postoffice
            SET " . implode(',', $update_params_list) . "
            WHERE postcode=?";
    //echo $sql;
    $stmt = $dbcon->prepare($sql);
    if (!$stmt) {
        echo $dbcon->error;
    }
    $stmt->bind_param(implode('', $update_param_type), ...$update_params_vars);
    $stmt->execute();
    if ($stmt->error) {
        return array("error" => true, "msg" => $stmt->error . " " . implode(',', $update_params_list) . " " . implode('', $update_param_type) . " " . implode(',', $update_params_vars));
    }

    return array("error" => false, "affected_rows" => $stmt->affected_rows, "msg" => "postoffice $postcode was updated");
}

function add_postoffice($dbcon, $postoffice, $schemaKeys)
{
    $ignore_list = array('dbcon');
    $insert_param_type = array();
    $insert_params_list = array();
    $insert_params_keys_list = array();
    $insert_params_vars = array();
    $sk = array_keys($schemaKeys);

    foreach ($sk as $parameter) {
        $name = $parameter;
        if (in_array($name, $ignore_list)) {
            continue;
        }

        if (!isset($postoffice[$name])) {
            if ($schemaKeys[$name] === 0) {
                continue;
            } else {
                // throw exception or something
                $val = "";
            }
        } else {
            $val = $postoffice[$name];
        }

        $insert_params_list[] = $name . '';
        $insert_params_keys_list[] = '?';
        $insert_params_vars[] = $val;
        $insert_param_type[] = 's';
    }

    // // add postcode
    // $update_params_vars[] = &$postcode;
    // $update_param_type[] = 'i';

    // //add districtno
    // if (!in_array('districtno', $insert_params_list)) {
    //     //var_dump($insert_params_list);
    //     $insert_params_list[] = 'districtno';
    //     $insert_params_keys_list[] = '?';
    //     $insert_params_vars[] = get_districtno($dbcon, $postoffice['district_bn']);
    //     $insert_param_type[] = 's';
    // }


    $sql = "INSERT INTO loc_postoffice
            (" . implode(',', $insert_params_list) . ")
            VALUES
            (" . implode(',', $insert_params_keys_list) . ")";
    $stmt = $dbcon->prepare($sql);
    $stmt->bind_param(implode('', $insert_param_type), ...$insert_params_vars);
    $stmt->execute();
    if ($stmt->error) {
        return array("error" => true, "msg" => $stmt->error . " " . implode(',', $insert_params_list) . " " . implode('', $insert_param_type) . " " . implode(',', $insert_params_vars));
    }

    return array("error" => false, "insert_id" => $stmt->insert_id, "msg" => "new postoffice with code $stmt->insert_id is inserted for " . implode(',', $postoffice));
}

function get_postoffice($dbcon, $postcode)
{
    $sql = "SELECT postcode
            FROM loc_postoffice
            WHERE postcode=?";

    $stmt = $dbcon->prepare($sql);
    $stmt->bind_param("i", $postcode);
    $stmt->execute();
    $res = $stmt->get_result();
    $stmt->close();

    return $res;
}

// function get_districtno($dbcon, $districtname)
// {

//     $escape_districtname = unicodeString($districtname); //html_entity_decode($districtname, 0, 'UTF-8');//urldecode($districtname); //$dbcon->real_escape_string($districtname);

//     $str = "%" . $escape_districtname . "%";

//     $sql = "SELECT * FROM emc_district WHERE bndistrictname LIKE ? ORDER BY LOCATE('$escape_districtname', bndistrictname) LIMIT 1";
//     $stmt = $dbcon->prepare($sql);
//     if (!$stmt) {
//         // echo $str;
//         // echo $sql;
//     }
//     $stmt->bind_param("s", $str);
//     $stmt->execute();
//     $res = $stmt->get_result();

//     if ($res->num_rows == 1) {
//         return $res->fetch_array(MYSQLI_ASSOC)['districtno'];
//     } else {
//         //echo $res->num_rows;
//         // echo $str;
//         // echo $sql;
//         return -1;
//     }
// }

function unicodeString($str, $encoding = null)
{
    if (is_null($encoding)) $encoding = ini_get('mbstring.internal_encoding');
    return preg_replace_callback('/\\\\u([0-9a-fA-F]{4})/u', function ($match) use ($encoding) {
        return mb_convert_encoding(pack('H*', $match[1]), $encoding, 'UTF-16BE');
    }, $str);
}
