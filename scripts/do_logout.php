<?php
session_start();
unset($_SESSION['user_id']); // = $row['id'];
unset($_SESSION['user_login']); // = $row['login'];
unset($_SESSION['user_name']); // = $row['real_name'];
unset($_SESSION['user_level']); // = $row['level'];
unset($_SESSION['user_expired']); // = $row['expired_date'];
unset($_SESSION['add_user']); // = $row['id'];
unset($_SESSION['edit_user']); // = $row['login'];
unset($_SESSION['delete_user']); // = $row['real_name'];
header("location:../login.php");
?>