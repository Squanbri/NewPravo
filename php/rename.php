<?php
  $_POST = json_decode(file_get_contents('php://input'), true);
  $oldName = $_POST['oldPath'];
  $newName = $_POST['newPath'];

  var_dump($_POST);

  rename("$oldName", "$newName");
?>
