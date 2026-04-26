<?php
require_once 'config.php';
session_regenerate_id(true);
session_destroy();
header("Location: welcome.php");
exit();
?>
