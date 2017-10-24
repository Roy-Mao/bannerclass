<?php
@session_start();
$cnf['dbhost'] = "127.0.0.1";
$cnf['dbuser'] = "root";
$cnf['dbpass'] = "mr930313y";
$cnf['database'] = "banner_object_info";
$tokyo_time = new DateTimeZone("Asia/Tokyo");
//DATABASE CONNECTION
//CREATE CONNECTION
$conn = new mysqli($cnf['dbhost'], $cnf['dbuser'], $cnf['dbpass']);
//CHECK CONNECTION
if ($conn->connect_error)
{
	die("config.php, Connection to database faild: ".$conn->connect_error."<br/>");
}

//CREATE DATABASE
$sql = "CREATE DATABASE IF NOT EXISTS banner_object_info";
if ($conn->query($sql) === TRUE)
{
	echo "Database created successfully<br/>";
	$conn -> select_db($cnf['database']);
	if ($result = $conn->query("SELECT DATABASE()"))
	{
		echo "Select database successfully<br/>";
		if ($set_timezone = $conn->query("SET TIME_ZONE = '+9:00'"))
		{
			$row = $result ->fetch_row();
			printf("Default database is: %s<br/>", $row[0]);
			$result->close();
			echo "Set time zone to Asia/Tokyo successfully<br/>";
		}
		else
		{
			die("config.php, Error ocurred when setting time zone to Asia/Tokyo");
		}
	}
	else
	{
		die("config.php, Error ocurred when trying to select a database");
	}
}
else
{
	die("config.php, Error occured when creating the database: ".$conn->error."<br/>");
}

//SQL TO CREATE TABLE
$tablesql = "CREATE TABLE IF NOT EXISTS banner (
id INT(6) UNSIGNED AUTO_INCREMENT NOT NULL, 
starttime DATETIME NOT NULL,
endtime DATETIME NOT NULL,
targetip INT(11) UNSIGNED NOT NULL, 
banner_url VARCHAR(1024) CHARACTER SET utf8 NOT NULL,
dest_url VARCHAR(1024) CHARACTER SET utf8 NOT NULL,
show_status BOOLEAN,
tstmp TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
PRIMARY KEY(id),
UNIQUE (starttime, endtime, targetip)
)";

if ($conn->query($tablesql) === TRUE)
{
	echo "Table banner created successfully<br/>";
}
else
{
	echo "config.php, Error occured when createing table: ".$conn->error."<br/>";
}

$conn -> close();
?>
