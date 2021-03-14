<?php
  $_POST = json_decode(file_get_contents('php://input'), true);
  $search = $_POST['search'];

  $listFolders = array();
  $listFiles = array();
  function search($folder) {
    global $listFolders, $listFiles, $search;
    // Получаем полный список файлов и каталогов внутри $folder
    $files = scandir( $folder );
    foreach( $files as $file ){
      // Отбрасываем текущий и родительский каталог
      if ( ( $file == '.' ) || ( $file == '..' )) continue;
      // Получаем полный путь к файлу
      $path = $folder.'/'.$file;

      $item = array();
      // Если это директория
      if ( is_dir( $path ) ){
        // Добавляем в массив название директории
        if(stristr($file, $search) != false){
          array_push($item, array("title",$file));
          array_push($item, array("type","folder")); // Указываем тип(Файл или папка )
          array_push($item, array("path","$path")); // Указываем путь до файла
          array_push($item, array("extension", "")); // Указывает расширения файла пустым(От расширения файла зависит иконка)
          array_push($listFolders, $item);
        }
        search($path, $space);
      }
      if ( is_file($path) ){ // Если это файл, то просто выводим название файла
        // Добавляем в массив название файла
        if(stristr($file, $search) !== false){
          array_push($item, array("title",$file));
          array_push($item, array("type","file")); // Указываем тип(Файл или папка )
          array_push($item, array("path","$path")); // Указываем путь до файла

          // Указывает расширения файла
          if     (substr($file, -4, 4) === ".doc" || substr($file, -4, 4) === "docx") { array_push($item, array("extension", "doc")); }
          else if(substr($file, -4, 4) === ".xls" || substr($file, -4, 4) === "xlsx") { array_push($item, array("extension", "xls")); }
          else if(substr($file, -4, 4) === ".ppt" || substr($file, -4, 4) === "pptx") { array_push($item, array("extension", "ppt")); }
          else { array_push($item, array("extension", "")); }

          array_push($listFiles, $item);
        }
      }
    }
  }
  // Запускаем функцию для текущего каталога
  search( '../folders');

  echo json_encode(array("folders" => $listFolders, "files" => $listFiles));
?>
