<?php
session_start();
session_unset();
session_destroy();

header("Location: /website/tropangmaselan-motorshop-main/php/Home/html/home.php");
exit();
