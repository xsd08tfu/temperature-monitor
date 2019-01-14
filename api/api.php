<?php
$path = $_SERVER['DOCUMENT_ROOT'];
include ($path."/mysqli_connect.php");
if($_GET['action']=="reading"){
  $data = json_decode(file_get_contents("php://input"),$assoc=TRUE);
  print_r($data);

} else {
  echo $_GET['action'];
};
mysqli_close($link);
?>
