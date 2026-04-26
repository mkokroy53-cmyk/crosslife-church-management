<?php
require_once 'config.php';
session_regenerate_id(true);
unset($_SESSION['member_id']);
unset($_SESSION['member_name']);
unset($_SESSION['is_member']);
session_destroy();
header("Location: member_login.php");
exit();
?>
