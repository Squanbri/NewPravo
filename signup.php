<?php
  require "libs/redBean/db.php";

  $data = $_POST;
  if( isset($data['do_signup']) ){
    $errors = array();
    if( R::count('users', "login = ?", [$data['login']] ) > 0 ){
      echo 'Такой логин уже есть';
    }
    else{
      $user = R::dispense('users');
      $user->login = $data['login'];
      $user->password = password_hash( $data['password'], PASSWORD_DEFAULT);
      R::store($user);
    }
  }
?>

<form action="/signup.php" method="post">
  <p>
    <label>Логин</label>
    <input type="text" name="login">
  </p>
  <p>
    <label>Пароль</label>
    <input type="password" name="password">
  </p>
  <p>
    <button type="submit" name="do_signup">Зарегестрировать</button>
  </p>
</form>
