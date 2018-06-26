<?php
include 'config.php';
require_once $config['autoloadpath'];

$q = 'SELECT 
        t.merchant_name as "Merchant Name", 
        t.merchant_contact as "Merchant Contact", 
        t.merchant_email as "Merchant Email", 
        t.merchant_address as "Merchant Address", 
        t.merchant_state as "Merchant State", 
        t.merchant_lga as "Merchant LGA", 
        t.transaction_status as "Transaction Status", 
        t.transaction_id as "Transaction ID", 
        t.transaction_time as "Transaction Time" 
      FROM transactions t
        WHERE t.transaction_time > (NOW() - INTERVAL 2 DAY)';

$conn = mysqli_connect($config['db']['hostname'], 
          $config['db']['user'],
          $config['db']['password'],
          $config['db']['database'],
          $config['db']['port']);

  if (mysqli_connect_errno()) {
         die("Failed to connect to MySQL: " . mysqli_connect_error());
  }

$tempdir = $config['tempdir'];

$csv_filename = 'transactions_report_'.date('Y-m-d').'.csv';
$csv_export = '';

$query = mysqli_query($conn, $q);
$field = mysqli_field_count($conn);

for($i = 0; $i < $field; $i++) {
  $csv_export.= mysqli_fetch_field_direct($query, $i)->name.',';
}

$csv_export.=PHP_EOL;

while($row = mysqli_fetch_array($query)) {
  for($i = 0; $i < $field; $i++) {
    $csv_export.= '"'.$row[mysqli_fetch_field_direct($query, $i)->name].'",';
  }
  $csv_export.=PHP_EOL;
}

if(!file_exists($tempdir))
  mkdir($tempdir,0774);

if(!is_dir($tempdir))
  die('temp file is not a directory');

$attachmentPath = $tempdir.$csv_filename;
$fh = fopen($attachmentPath, 'w') or die("Failed to create file");

fwrite($fh, $csv_export) or die("Could not write to file");
fclose($fh);

$message = (new Swift_Message())
              ->setSubject('2 day Send Package Transactions Report '.date('jS, F, Y'))
              ->setFrom($config['smtp']['from'])
              ->setTo($config['smtp']['to'])
              ->setBody('Please find attached the 2 day Send Package transactions report -'.date('jS, F, Y'))
              ->addPart('<q>Please find attached the 2 day Send Package transactions report - '.date('jS, F, Y').'</q>','text/html')
              ->attach(Swift_Attachment::fromPath($attachmentPath));

$transport = (new Swift_SmtpTransport($config['smtp']['server'],$config['smtp']['port']))
                ->setUsername($config['smtp']['user'])
                ->setPassword($config['smtp']['password']);

$mailer = new Swift_Mailer($transport);

$result = $mailer->send($message);

if($result) {
  echo 'successfully sent transaction csv file'.PHP_EOL;
} else {
  echo 'unable to send message'.PHP_EOL;
}

?>