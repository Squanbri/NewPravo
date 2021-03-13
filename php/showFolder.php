<?php
  $_POST = json_decode(file_get_contents('php://input'), true);
  $folder = $_POST['path'];

  $value = array();
  // $items;
  // Получаем полный список файлов и каталогов внутри $folder
  $files = scandir( $folder );
  foreach( $files as $file ){
    global $value;
    // global $items;
    // Отбрасываем текущий и родительский каталог
    if ( ( $file == '.' ) || ( $file == '..' )) continue;
    // Получаем полный путь к файлу
    $path = $folder.'/'.$file;
    // Если это файл, то просто выводим название файла
    $item = array(); // Создаём подобие объекта JS
    array_push($item, array("title",$file)); // Вкладываем название файла
    if (is_dir($path)){
      array_push($item, array("type","folder")); // Указываем тип(Файл или папка   )
      array_push($item, array("path","$path")); // Указываем тип(Файл или папка   )

      // Количество папок и файлов в этой папке
      $dir2 = opendir($path);
      $count = 0;
      while($file2 = readdir($dir2)){
          if($file2 == '.' || $file2 == '..'){
              continue;
          }
          $count++;
      }
      // ----- //

      // Запись файла в массив
      array_push($item, array("items",$count)); // Если это папка вкладываем количество эллементов в ней

      array_push($value, $item);
      // ------- //
    }
    else {
      array_push($item, array("type","file")); // Указываем тип(Файл или папка   )
      array_push($item, array("path","$path")); // Указываем тип(Файл или папка   )

      // Запись файла в массив
      array_push($value, $item);
      // ------- //
    }
  }

// Пересылаем данные в js
// echo json_encode(array("value" => $value, "items" => $items));
echo json_encode(array("value" => $value));
?>
