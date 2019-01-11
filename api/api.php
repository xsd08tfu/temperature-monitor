<?php

if($_GET['action']=="reading"){
  if($_SERVER['REQUEST_METHOD']=="PUT"){
    $data = json_decode(file_get_contents("php://input"),$assoc=TRUE);
  } else {
    if (file_get_contents("php://input")!=NULL){
      $data = json_decode(file_get_contents("php://input"),$assoc=TRUE);
    } else {
      $data = json_decode($_POST['data'],$assoc=TRUE);
    }
  };
  print_r($data);
} else {
  echo $_GET['action'];
};
?>
