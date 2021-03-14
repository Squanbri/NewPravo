<?php
  require 'libs/redBean/db.php';

  $data = $_POST;
  if( isset($data['do_login']) ) {
    $user = R::findOne('users', 'login = ?', [$data['login']]);
    if( $user ) {
      if( password_verify($data['password'], $user->password) ){
        $_SESSION['logged_user'] = $user;
        header('location: /');
      }
    }
  }
?>

<!DOCTYPE html>
<html lang="en" dir="ltr">
  <head>
    <meta charset="utf-8">
    <meta name="google" content="notranslate">

    <!-- <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-BmbxuPwQa2lc/FVzBcNJ7UAyJxM6wuqIj61tLrc4wSX0szH/Ev+nYRRuWlolflfl" crossorigin="anonymous"> -->
    <link rel="stylesheet" href="css/bootstrap.min.css">
    <link rel="stylesheet" href="css/style.css">
    <title>Файл-Менеджер</title>
  </head>
  <body>
    <div id="app">

      <!-- Шапка -->
      <nav class="text-white d-flex py-2" style="background-color: #e3f2fd;">
        <div class="container">
          <div class="row">
            <h6 class="text-white col-3 mt-2 mb-2">Введение в гос. службу</h6>

            <div class="col-9 ps-0 d-flex">
              <div class="input-form ms-1 w-60">
                <input type="text" class="input" v-model="inputSearch" @keyup="search()">
                <img src="icons/search.svg" v-if="inputSearch === ''">
              </div>

              <form id="frm" action="php/uploadFile.php" method="post" enctype="multipart/form-data">
                <input id="file" ref="file" type=file name="file" class="d-none">
              </form>

              <button class="px-3 btn-green h-100" @click="$refs.file.click()">
                <img src="icons/upload.svg" class="me-1">
                <span>ЗАГРУЗИТЬ</span>
              </button>

              <button type="button" class="ms-3 me-4 px-3 btn-green h-100" data-bs-toggle="modal" data-bs-target="#exampleModal"
                @click="model = 'create'"
              >
                <img src="icons/plus.svg" class="me-1">
                <span>СОЗДАТЬ</span>
              </button>

              <?php if( isset($_SESSION['logged_user']) ) : ?>
                <a class="ms-auto" href="logout.php">
                  <button class="px-3 btn-green h-100">
                    <img src="icons/sign-out.svg" class="me-1 sign-in">
                    <span>Выйти</span>
                  </button>
                </a>
              <?php else : ?>
                <div class="ms-auto">
                  <button class="px-3 btn-green h-100" data-bs-toggle="modal" data-bs-target="#signinModal">
                    <img src="icons/sign-in.svg" class="me-1 sign-in">
                    <span>Войти</span>
                  </button>
                </div>
              <?php endif; ?>

            </div>

          </div>
        </div>
      </nav>
      <!-- ------- -->

      <main class="container d-flex pt-4">
        <aside class="col-3">
          <h6>Навигация</h6>

          <ul type="none" id="sidebar-folders">

            <li
              v-for="(item, index) in mainFolders"
              @click="showFolder(item)"
            >
              <img src="icons/folder.svg">
              <span>{{ item.title }}</span>
            </li>

          </ul>
        </aside>

        <section class="col-9 mb-3">
          <div id="path">
            <button
              class="path bg-green text-white py-1 mb-3 me-2"
              v-for="(item, index) in path"
              @click="comeBack(index)"
            >
              {{ item }}
            </button>
          </div>

          <article>

            <div class="item px-2 text-left"
              v-for="(item, index) in items"
            >
            <?php if( isset($_SESSION['logged_user']) ) : ?>
              <div class="dropdown">
                <button class="more" id="dropdownMenuButton1" data-bs-toggle="dropdown" aria-expanded="false"> <img src="icons/more.svg"> </button>
                <!-- if -->
                <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton1"
                  v-if="item.type === 'folder'"
                >
                  <li class="dropdown-item" data-bs-toggle="modal" data-bs-target="#exampleModal"
                    @click="model='edit', basicName=item.title, inputModal=item.title"
                  >
                    Изменить
                  </li>
                  <li class="dropdown-item" data-bs-toggle="modal" data-bs-target="#deleteModal"
                    @click="selItem = item, model = 'deleteFolder'"
                  >
                    Удалить
                  </li>
                  <li class="dropdown-item">Скрыть</li>
                </ul>
                <!-- else -->
                <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton1"
                  v-else
                >
                <li class="dropdown-item"
                  @click="downloadFile(item.title)"
                >
                  Скачать
                </li>
                <li class="dropdown-item" data-bs-toggle="modal" data-bs-target="#exampleModal"
                  @click="model='edit', basicName=item.title, inputModal=item.title"
                >
                  Изменить
                </li>
                <li class="dropdown-item" data-bs-toggle="modal" data-bs-target="#deleteModal"
                  @click="selItem = item, model = 'deleteFile'"
                >
                  Удалить
                </li>
                <li class="dropdown-item">Скрыть</li>
                </ul>
                <!-- endif -->
              </div>
            <?php else : ?>
              <div class="dropdown" v-if="item.type === 'file'">
                <button class="more" id="dropdownMenuButton1" data-bs-toggle="dropdown" aria-expanded="false"> <img src="icons/more.svg"> </button>
                <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton1">
                  <li class="dropdown-item" @click="downloadFile(item.title)"> Скачать </li>
                </ul>
                <!-- endif -->
              </div>
            <?php endif; ?>


              <div class="d-flex flex-column"
                @click="item.type === 'folder' ? showFolder(item) : showFile(item)"
                v-bind:title="item.title"
              >
                <img :src="'icons/'+item.type+item.extension+'.svg'" class="my-3 ml-auto" :class="{ fileImg: item.type === 'file' }">
                <hr>
                <strong> {{ item.title }} </strong>
                <small v-if="item.type === 'folder'"> Содержимое: {{ item.items }} </small>
              </div>
            </div>

          </article>
        </section>
      </main>

      <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true" ref="modal">
        <div class="modal-dialog">
          <div class="modal-content">
            <div class="modal-header text-white rounded-0">
              <h5 class="modal-title" id="exampleModalLabel" v-if="model === 'create'">Создание каталога</h5>
              <h5 class="modal-title" id="exampleModalLabel" v-if="model === 'edit'">Редактирование каталога</h5>
              <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
              <label>Навзание</label>
              <input type="text" class="form-control" id="inputModal" v-model="inputModal" @change="inputValid()" v-if="model === 'create'">
              <input type="text" class="form-control" id="inputModal" v-model="inputModal" @change="inputValid()" v-if="model === 'edit'">
            </div>
            <div class="modal-footer pt-0">
              <button type="button" class="btn-green py-1" data-bs-dismiss="modal">Закрыть</button>
              <button type="button" class="btn-green py-1" @click="createFolder()" v-if="model === 'create'" ref="close">Создать</button>
              <button type="button" class="btn-green py-1" @click="rename()" v-if="model === 'edit'" ref="close">Редактировать</button>
            </div>
          </div>
        </div>
      </div>

      <div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true" ref="modal">
        <div class="modal-dialog">
          <div class="modal-content">
            <div class="modal-header text-white rounded-0">
              <h5 class="modal-title" id="deleteModalLabel" v-if="model === 'deleteFile'">Удалить файл</h5>
              <h5 class="modal-title" id="deleteModalLabel" v-if="model === 'deleteFolder'">Удалить каталог</h5>
              <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
              <span v-if="model === 'deleteFile'">Вы уверены что хотите удалить файл?</span>
              <span v-if="model === 'deleteFolder'">Вы уверены что хотите удалить каталог?</span>
            </div>
            <div class="modal-footer pt-0">
              <button type="button" class="btn-green py-1" data-bs-dismiss="modal">Отмена</button>
              <button type="button" class="btn-green btn-delete py-1" data-bs-dismiss="modal" @click="removeFile()" v-if="model === 'deleteFile'">Удалить</button>
              <button type="button" class="btn-green btn-delete py-1" data-bs-dismiss="modal" @click="removeFolder()" v-if="model === 'deleteFolder'">Удалить</button>
            </div>
          </div>
        </div>
      </div>

      <div class="modal fade" id="signinModal" tabindex="-1" aria-labelledby="signinModalLabel" aria-hidden="true" ref="modal">
        <div class="modal-dialog">
          <div class="modal-content">
            <div class="modal-header text-white rounded-0">
              <h5 class="modal-title" id="signinModalLabel">Войти в систему</h5>
              <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body px-4">

              <form class="" action="index.php" method="post">
                <section class="d-flex">
                  <label for="login" class="me-3">Логин:&nbsp;&nbsp;&nbsp;</label>
                  <input type="text" name="login" class="form-control">
                </section>
                <section class="d-flex mt-3">
                  <label for="login" class="mt-2 me-3">Пароль:</label>
                  <input type="text" name="password" class="form-control w-100">
                </section>

                <div class="modal-footer mt-2 me-0 p-0">
                  <button type="button" class="btn-green py-1" data-bs-dismiss="modal">Отмена</button>
                  <button type="sumbit" name="do_login" class="btn-green py-1">Войти</button>
                </div>
              </form>

            </div>
          </div>
        </div>
      </div>

    </div>
  </body>
  <script src="js/jquery-3.6.0.js" integrity="sha256-H+K7U5CnXl1h5ywQfKtSj8PCmoN9aaq30gDh27Xc0jk=" crossorigin="anonymous"></script>
  <script src="js/jquery.form.js"></script>
  <script src="js/axios.min.js" charset="utf-8"></script>
  <script src="js/vue.min.js"></script>
  <script src="js/bootstrap.bundle.js" charset="utf-8"></script>
  <script src="js/script.js" charset="utf-8"></script>
</html>
