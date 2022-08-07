<?php



$debug = true;
$host = isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : "localhost";
$isSecure = false;
if (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on') {
    $isSecure = true;
} elseif (!empty($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] == 'https' || !empty($_SERVER['HTTP_X_FORWARDED_SSL']) && $_SERVER['HTTP_X_FORWARDED_SSL'] == 'on') {
    $isSecure = true;
}
$REQUEST_PROTOCOL = $isSecure ? 'https' : 'http';

// $debughost = array('localhost', '192.168.1.8');
// $debug = in_array($host, $debughost);
// $debug = strcasecmp($host, "localhost")==0 || strcasecmp($host, "192.168.0.103")==0;

$debughost = array('localhost', '192.168.1');

for ($i = 0; $i < count($debughost); $i++) {
    // code...
    $adebughost = $debughost[$i];
    // echo $adebughost."<br/>";
    if (strpos($host, $adebughost) === 0) {
        $debug = true;
        break;
    } else {
        $debug = false;
    }
}

// echo $debug."CONFIG HOST: ".$host;

if ($debug) {
    $projectName = "cuaa";

    #MySQL Database name:
    define('DB_NAME', 'agami_alumni');
    #MySQL Database User Name:
    define('DB_USER', 'root');
    #MySQL Database Password:
    define('DB_PASSWORD', '11135984');
    #MySQL Hostname:
    define('DB_HOST', 'localhost');

    define('PREFIX', 'alm_');

    // echo "New HOST: ".(strcasecmp($host, "localhost")==0?$host:($host.":3306"));

    $publicAccessUrl = $REQUEST_PROTOCOL . "://$host/" . $projectName . "/";
    $projectPath = DIRECTORY_SEPARATOR . $projectName . DIRECTORY_SEPARATOR;
    $authUrl = $REQUEST_PROTOCOL . "://$host/" . $projectName . "/auth/?redirect=";
} elseif (strcasecmp($host, "cu-alumni.org") === 0 || strcasecmp($host, "www.cu-alumni.org") === 0) {
    $projectName = "public_html";

    #MySQL Database name:
    define('DB_NAME', 'agami_alumni_cuaa');
    #MySQL Database User Name:
    define('DB_USER', 'cuaa_admin');
    #MySQL Database Password:
    define('DB_PASSWORD', '1Er{*fiugOq(');
    #MySQL Hostname:
    define('DB_HOST', 'localhost');

    define('PREFIX', 'alm_');

    $publicAccessUrl = $REQUEST_PROTOCOL . "://$host/";
    $projectPath = DIRECTORY_SEPARATOR . $projectName . DIRECTORY_SEPARATOR;
    $authUrl = $REQUEST_PROTOCOL . "://$host/auth/?redirect=";
} elseif (strcasecmp($host, "cuaa.agamilabs.com") === 0 || strcasecmp($host, "www.cuaa.agamilabs.com") === 0) {
    $projectName = "public_html";

    #MySQL Database name:
    define('DB_NAME', 'agami_alumni_cuaa');
    #MySQL Database User Name:
    define('DB_USER', 'cuaa_admin_test');
    #MySQL Database Password:
    define('DB_PASSWORD', '1Er{*fiugOq(');
    #MySQL Hostname:
    define('DB_HOST', 'localhost');

    define('PREFIX', 'alm_');

    $publicAccessUrl = $REQUEST_PROTOCOL . "://$host/";
    $projectPath = DIRECTORY_SEPARATOR . $projectName . DIRECTORY_SEPARATOR;
    $authUrl = $REQUEST_PROTOCOL . "://$host/auth/?redirect=";
} else {
    $err_msg =  "$host is not allowed in Production Mode. Please contact AGAMiLabs Ltd.";
    if ($_SERVER['REQUEST_METHOD'] != 'POST') {
        echo $err_msg;
    } else {
        $response = array();
        $response['error'] = true;
        $response['message'] = $err_msg;
    }

    exit();
}
