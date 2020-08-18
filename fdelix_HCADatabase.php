<?php
# FileName="Connection_php_mysql.htm"
# Type="MYSQL"
# HTTP="true"
error_reporting(E_ALL ^ E_DEPRECATED);
$database_fdelix_HCADatabase = "mfc";
$username_fdelix_HCADatabase = "root";
$password_fdelix_HCADatabase = "";
$hostname_fdelix_HCADatabase = "localhost";
$fdelix_HCADatabase = mysqli_connect($hostname_fdelix_HCADatabase, $username_fdelix_HCADatabase, $password_fdelix_HCADatabase) or die(mysqli_error());

if (!$fdelix_HCADatabase) {
	echo "no connection";
	exit();
}

?>