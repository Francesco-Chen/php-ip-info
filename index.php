<?php

require_once "statistic.php";

header('Content-Type: text/json; charset=utf-8');
if (isset($_SERVER["HTTP_CF_CONNECTING_IP"])) {
  $_SERVER['REMOTE_ADDR'] = $_SERVER["HTTP_CF_CONNECTING_IP"];
}
$ip = $_SERVER["REMOTE_ADDR"];
//Read GET data from URL
if(isset($_GET['ip'])){
  $ip=$_GET['ip'];
}
$ip_num = gmp_strval(gmp_init(bin2hex(inet_pton($ip)), 16), 10);

//Check valid IP
if (filter_var($ip, FILTER_VALIDATE_IP) === false) {
  $data = array(
    'status' => 404,
    'message' => "Wrong ip"
  );
  $json = json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
  echo $json;
  exit();
}

$fp = fopen('ip2asn-combined.tsv', 'r');

$start_pos = 0;
$end_pos = filesize('ip2asn-combined.tsv');
$foundit = false;

while ($start_pos <= $end_pos) {
  $mid_pos = $start_pos + ($end_pos - $start_pos) / 2;
  fseek($fp, $mid_pos);
  fgets($fp); // Discard partial line

  if (ftell($fp) == 0) {
    $line = fgets($fp);
  } else {
    fgets($fp); // Discard partial line
    $line = fgets($fp);
  }

  if (!$line) {
    break;
  }

  list($start, $end, $asn, $country, $org) = explode("\t", $line);

  $start_num = gmp_strval(gmp_init(bin2hex(inet_pton($start)), 16), 10);
  $end_num = gmp_strval(gmp_init(bin2hex(inet_pton($end)), 16), 10);

  if ($ip_num >= $start_num && $ip_num <= $end_num) {

  // NOT routeble IP
    // if($asn == 0){
    //   $data = array(
    //     'ip' => $ip,
    //     'bogon' => true
    //   );
    //   $json = json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
    //     echo $json;
    //     exit();
    // }

    require_once('country_db.php');

    $data = array(
      'ip' => $ip,
      'country' => $countries[$country],
      'country_code' => $country,
      'org' => "AS" . $asn . " " . rtrim($org, "\n"),
      'asn' => $asn,
      'isp' => rtrim($org, "\n"),
      'range_start' => $start,
      'range_end' => $end
    );

    $json = json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
    echo $json;
    $foundit = true;
    break;
  } elseif ($ip_num < $start_num) {
    $end_pos = $mid_pos - 1;
  } else {
    $start_pos = $mid_pos + 1;
  }
}

if ($foundit === false) {
  $data = array(
    'ip' => $ip
  );
  $json = json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
  echo $json;
}

fclose($fp);
?>
