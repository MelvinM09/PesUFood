<?php
session_start();
session_destroy();
header("Location: /PesUFood/index.php");
exit();
