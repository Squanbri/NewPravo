<?php
  $_POST = json_decode(file_get_contents('php://input'), true);
  $path = $_POST['path'];
  mkdir($path);
?>
