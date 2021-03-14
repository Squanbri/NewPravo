<?php
  $_POST = json_decode(file_get_contents('php://input'), true);
  $path = $_POST['path'];
  $file = $_POST['file'];

  echo $path;
  echo $file;
  $arrayPath = explode("/", $path);

  $dir = "../foldersPDF";
  foreach ($arrayPath as &$itemPath) {
    global $dir;
    $dir = "$dir/$itemPath";
    echo $itemPaths;

    if(!is_dir($dir)) {
      mkdir($dir);
    }
  }

  $dir = "$dir/$file";
  echo "<br>$dir<br>";
  if( file_exists($dir) ){
    echo "file exists";
  }
  else{
    $fp = fopen($dir,"wb");
    fwrite($fp,"123");
    fclose($fp);
  }
?>
