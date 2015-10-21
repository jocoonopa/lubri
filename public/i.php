<?php
$string = "jocoonopa@gmail.com' OR email='123";
$password = '123';
$sql = "SELECT user_name FROM users WHERE email='$string' AND password='" . MD5($password)."'"; 

echo $sql;

echo phpinfo();