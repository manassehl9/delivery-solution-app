<?php
// database record to be exported
$db_record = 'transactions';
$where = 'WHERE `transaction_time` > (NOW() - INTERVAL 2 DAY)';
// filename for export
$csv_filename = 'db_export_'.$db_record.'_'.date('Y-m-d').'.csv';

// database variables
$hostname = "localhost";
$user = "root";
$password = "";
$database = "sendpackage";
$port = 3306;

$conn = mysqli_connect($hostname, $user, $password, $database, $port);
if (mysqli_connect_errno()) {
    die("Failed to connect to MySQL: " . mysqli_connect_error());
}

$csv_export = '';

// query to get data from database
$query = mysqli_query($conn, "SELECT * FROM ".$db_record." ".$where);
$field = mysqli_field_count($conn);

// create line with field names
for($i = 0; $i < $field; $i++) {
    $csv_export.= mysqli_fetch_field_direct($query, $i)->name.',';
}


$csv_export.= '
';


while($row = mysqli_fetch_array($query)) {
    for($i = 0; $i < $field; $i++) {
        $csv_export.= '"'.$row[mysqli_fetch_field_direct($query, $i)->name].'",';
    }
    $csv_export.= '
    ';
}

header("Content-type: text/x-csv");
header("Content-Disposition: attachment; filename=".$csv_filename."");

die;?>