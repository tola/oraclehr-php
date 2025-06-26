<?php
$host = 'localhost';
$port = '1521';
$sid = ''; // or ORCL depending on your setup
$service_name = 'kulenpdb'; // leave blank if using SID
$username = 'hr';
$password = '*****';

$connectStr = $service_name
    ? "(DESCRIPTION=(ADDRESS_LIST=(ADDRESS=(PROTOCOL=TCP)(HOST=$host)(PORT=$port)))(CONNECT_DATA=(SERVICE_NAME=$service_name)))"
    : "(DESCRIPTION=(ADDRESS_LIST=(ADDRESS=(PROTOCOL=TCP)(HOST=$host)(PORT=$port)))(CONNECT_DATA=(SID=$sid)))";

$conn = oci_connect($username, $password, $connectStr);

if (!$conn) {
    $e = oci_error();
    die("Connection failed: " . htmlentities($e['message']));
}
