<?php
  $_POST = json_decode(file_get_contents('php://input'), true);
  $path = $_POST['path'];

  function dirDel($dir){
    $d=opendir($dir);
    while(($entry=readdir($d))!==false) {
        if ($entry != "." && $entry != "..") {
            if (is_dir("$dir/$entry")) {
                dirDel("$dir/$entry");
            }
            else {
                unlink ("$dir/$entry");
            }
        }
    }
    closedir($d);
    rmdir ($dir);
  }

  dirDel($path);
?>
