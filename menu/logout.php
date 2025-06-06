<?php
session_start();
session_unset();
session_destroy();
header("Location: /project/l_or_s.html");
exit;
?>