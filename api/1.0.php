<?php
$path = $_SERVER['DOCUMENT_ROOT'];
include ($path."/mysqli_connect.php");
if ($stmt = mysqli_prepare($link, "SELECT `api_key_id`,`api_key` FROM $db_name.`api_keys` WHERE api_key = ?")) {
/* bind parameters for markers */
  mysqli_stmt_bind_param($stmt, "s", $_GET['key']);
/* execute query */
  mysqli_stmt_execute($stmt) or die(mysqli_error($link));
/* bind result variables */
  mysqli_stmt_bind_result($stmt, $api_key_id, $api_key);//Bind to variables.
/* fetch value */
  mysqli_stmt_fetch($stmt);
  mysqli_stmt_close($stmt);
};
$now = time();
function returnJson ($query) {
  global $link;
  $result = mysqli_query($link,$query) or die(mysqli_error($link));
  $array = mysqli_fetch_all($result, MYSQLI_ASSOC);
  foreach ($array as $key => $value) {
    $array[$key]['date'] = date("d-m-Y H:i", $value['timestamp']);
  }
  header('Content-Type: application/json');
  echo json_encode($array);
}
if(isset($api_key_id)){
  $query = "SELECT * FROM $db_name.`sensors` WHERE `api_key` = '$api_key'"; //Get sensor array
  $result = mysqli_query($link,$query) or die(mysqli_error($link));
  $sensor_array = mysqli_fetch_all($result, MYSQLI_ASSOC);
  $sensor_array = array_column($sensor_array,NULL,'sensor_id');
  if($sensor_array[$_GET['sensor']]['api_key']==$api_key){
    if($_GET['action']=="reading"){
      $request = json_decode(file_get_contents("php://input"),$assoc=TRUE);
      $now = time();
      if ($stmt = mysqli_prepare($link, "INSERT INTO `readings` (`timestamp`, `sensor_id`, `lux`, `temperature`, `pressure`) VALUES (?,?,?,?,?)")) {
      /* bind parameters for markers */
        mysqli_stmt_bind_param($stmt, "issss", $now, $_GET['sensor'], $request['lux'], $request['temp'], $request['pressure']);
      /* execute query */
        mysqli_stmt_execute($stmt) or die(mysqli_error($link));
      /* close */
        mysqli_stmt_close($stmt);
      };
    } elseif($_GET['action']=="temperature"){
      $start = strtotime($_GET['period']." ago");
      $query = "SELECT `timestamp`,`temperature` FROM $db_name.`readings` WHERE `sensor_id` = '".$_GET['sensor']."' AND `timestamp` BETWEEN $start AND $now"; //Get sensor array
      returnJson($query);
    } elseif($_GET['action']=="pressure"){
      $start = strtotime($_GET['period']." ago");
      $query = "SELECT `timestamp`,`pressure` FROM $db_name.`readings` WHERE `sensor_id` = '".$_GET['sensor']."' AND `timestamp` BETWEEN $start AND $now"; //Get sensor array
      returnJson($query);
    } elseif($_GET['action']=="light"){
      $start = strtotime($_GET['period']." ago");
      $query = "SELECT `timestamp`,`lux` FROM $db_name.`readings` WHERE `sensor_id` = '".$_GET['sensor']."' AND `timestamp` BETWEEN $start AND $now"; //Get sensor array
      returnJson($query);
    } else {
      http_response_code(400);
      echo "Unknown API Method";
    };
  } else {
    http_response_code(401);
    echo "Sensor not associated with this API Key";
  };
} else {
  http_response_code(401);
  echo "Unauthorised API Key";
}

mysqli_close($link);
?>
