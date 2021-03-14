<?php
  // $_POST = json_decode(file_get_contents('php://input'), true);
  $path = $_POST['path'];
  move_uploaded_file($_FILES['file']['tmp_name'], "$path/".$_FILES['file']['name']);
?>
