<?php

"""
THIS FILE IS CREATED FOR THE DEMONSTRATE THE CREATED BANNER CLASS
USE THE GUI TO SELECT DISPLAY STARTTIME, ENDTIME, REDIRECT_URL, BANNER_IMG_URL AND TARGET IP ADDRESS 
TO CREATE THE BANNER OBJECT. THE BANNER INFO WILL BE ADDED INTO DATABASE UNPON SUCCESSFUL CREATION
"""
@include("config.php");
@include("class/Bannerad.class.php");
date_default_timezone_set("Asia/Tokyo");
$startTime = $endTime = $targetIp = $bannerImg = $targetUrl = "";
$startTimeErr = $endTimeErr = $targetIpErr = $bannerImgErr = $timeDiffErr= $targetUrlErr="";

//SET THE DEFAULT TARGET IP ADDRESS TO BE THE CLIENT IP
$targetIp = getenv('HTTP_CLIENT_IP')?:
getenv('HTTP_X_FORWARDED_FOR')?:
getenv('HTTP_X_FORWARDED')?:
getenv('HTTP_FORWARDED_FOR')?:
getenv('HTTP_FORWARDED')?:
getenv('REMOTE_ADDR');
$banner;

function sanitize_input($data)
{
	$data = trim($data);
	$data = stripslashes($data);
	$data = htmlspecialchars($data);
	return $data;
}


function print_data()
{
	global $cnf;
	$conn = new mysqli($cnf['dbhost'], $cnf['dbuser'], $cnf['dbpass'], $cnf['database']);
	if($conn->connect_error)
	{
		die("demonstrate.php, select_data function,Connection failed: ".$conn->connect_error);
	}
	else
	{
		$conn->select_db($cnf['database']);
		if ($result = $conn->query("SELECT DATABASE()")){}
		else
		{
			die("demonstrate.php, Function select_data, Error occured when selecting the database: ".$conn->error."<br/>");
		}
	}
	$sql = "SELECT * FROM banner";
	$result = $conn->query($sql);
	if ($result -> num_rows >0)
	{
		while ($row = $result->fetch_assoc())
		{
			echo "<tr>";
			echo "<td>".$row["id"]."</td>";
			echo "<td>".$row["banner_url"]."</td>";
			echo "<td>".$row["dest_url"]."</td>";
			echo "<td class='intargetip'>".$row["targetip"]."</td>";
			echo "<td>".$row["starttime"]."</td>";
			echo "<td>".$row["endtime"]."</td>";
			echo "<td>";
			echo $row["show_status"]?'true':'false';
			echo "</td>";
			echo "<td>".$row["tstmp"]."</td>";
			echo "<td><button onclick='display(this)'>show effect</button></td>";
			echo "</tr>";
		}
		$conn->close();
		return true;
	}
	$conn->close();
	return false;
}

//SERVER-SIDE CHECK IF USER INPUT IS VALID;CHECK IF THERE IS ANY ERROR IN USER INPUT
if ($_SERVER["REQUEST_METHOD"] == "POST") 
{
	$startTime = sanitize_input($_POST["banner_start_time"]);
	$endTime = sanitize_input($_POST["banner_end_time"]);
	$targetIp = sanitize_input($_POST["target_ip"]);
	$targetUrl =sanitize_input($_POST["target_url"]);
	$bannerImg = sanitize_input($_POST["banner_options"]);
	$err_exist = false;
	if (empty($startTime)){
		$startTimeErr = "Banner start time is required.";
		$err_exist = true;
	}
	if (empty($endTime)){
		$endTimeErr = "Banner end time is required.";
		$err_exist = true;
	}
	if((!empty($startTime)) and (!empty($endTime)))
	{
		$unistartTime = strtotime($startTime);
		$uniendTime = strtotime($endTime);
		$timeDiff = $uniendTime - $unistartTime;
		if ($timeDiff <= 0)
		{
			$timeDiffErr = "Start time should be ahead of End time";
			$err_exist = true;
		}
	}
	if (empty($targetIp)){
  		$targetIpErr = "TargetIp is required.";
  		$err_exist = true;
	}
	if (empty($bannerImg))
	{
		$bannerImgErr = "Invalid Banner Image URL: can not be empty or null.";
		$err_exist = true;
	}
	if (empty($targetUrl))
	{
		$targetUrlErro = "Banner redirect link invalid.";
		$err_exist = true;
	}
	if (!$err_exist)
	{
		$banner = new Bannerad($startTime, $endTime, $targetIp, $bannerImg, $targetUrl);
		$can_store = $banner->storeInfo();
		if(!$can_store)
		{
			die("Something went wrong when trying to store object information.");
		}
		$now = time();
		$page = $_SERVER['PHP_SELF'];
		if ($banner->getStatus())
		{
			$endtime = strtotime($banner->getendTime());
			$refreshTime = $endtime - $now;
			header("Refresh: $refreshTime; url=$page");
		}
		else
		{
			$starttime = strtotime($banner->getstartTime());
			$refreshTime = $starttime - $now;
			if ($refreshTime > 0)
			{
				header("Refresh: $refreshTime; url=$page");
			}
		}
	}
	else
	{
		echo "There is some error in your submitted form. Please correct.";
	}
}
?>
<html>
	<meta charset="UTF-8">
	<head>
		<title>Banner class demonstration</title>
		<script language="JavaScript" type="text/javascript" src = "https://cdn.bootcss.com/jquery/3.2.1/jquery.min.js"></script>
		<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.18.1/moment.min.js"></script>
		<script type="text/javascript" src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
		<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.47/js/bootstrap-datetimepicker.min.js"></script>
		<script type="text/javascript" src="js/jquery.countdown.min.js"></script>
		<link rel="stylesheet" type="text/css" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
		<link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.7.1/css/bootstrap-datepicker.css">
		<link rel="stylesheet" type="text/css" href="css/mycss.css">
		<script type="text/javascript" src="js/myjscript.js"></script>
	</head>
	<body>
		<div id="inner">
		<h3>Banner Demonstration</h3>
		<div id="ip_display"></div>
		<!--NOTICE: need to use htmlspecialchars() to escape html characters-->
		<form method="post" enctype="multipart/form-data" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
			<div class="row1">
				<div><p>Please choose a banner img that you wanna display: </p><p>The S3 URL will be used to create the banner object</p></div>
				<div>
				<select id="banner_list" name="banner_options">
					<option value="https://s3-ap-northeast-1.amazonaws.com/bannerobj/banner1.gif" selected="selected">Banner Image One</option>
					<option value="https://s3-ap-northeast-1.amazonaws.com/bannerobj/banner2.gif">Banner Image Two</option>
					<option value="https://s3-ap-northeast-1.amazonaws.com/bannerobj/banner3.gif">Banner Image Three</option>
					<option value="https://s3-ap-northeast-1.amazonaws.com/bannerobj/banner4.gif">Banner Image Four</option>
				</select>
				<hr>
				<div id="banner_location"><img id="cimg" alt="banner_img" src="https://s3-ap-northeast-1.amazonaws.com/bannerobj/banner1.gif" width="300px" height="250px"/></div>
				<div class="error"><?php echo $bannerImgErr;?></div>
				<hr>
			</div>
			<div class="row1">
				<div><button type="button" id="ipbtn">Generate IPv4 Address.</button></div>
				<p>This Ip address will be used as the target client ip to create the banner object</p>
				<input type="text" id="ipid" name="target_ip" value="127.0.0.1">
				<div class="error"><?php echo $targetIpErr;?></div>
			</div>
			<div class="row1">
				<div>Redirect link url:</div>
				<p>This url link will be used as the redirect link to generate the banner object</p>
				<input type="text" id="redir_link" name="target_url" value="https://rocky-savannah-96297.herokuapp.com">
				<div class="error"><?php echo $targetUrlErr;?></div>
			</div>
			<div class="row1">
				<div><p>Set Banner instance start time: </p></div>	
                <div class='input-group date' id='datetimepicker1' onkeydown='return false'>
                    <input name="banner_start_time" id="start_time" type='text' class="form-control" />
                    <span class="input-group-addon">
                        <span class="glyphicon glyphicon-time"></span>
                    </span>
                </div>
                <div class="error"><?php echo $startTimeErr;?></div>
                <div class="error"><?php echo $timeDiffErr;?></div>
			</div>
			<br>
			<div class="row1">
				<div><p>Set Banner instance end time: </p></div>
				<div class='input-group date' id='datetimepicker2' onkeydown='return false'>
                    <input name="banner_end_time" id="end_time" type='text' class="form-control" />
                    <span class="input-group-addon">
                        <span class="glyphicon glyphicon-time"></span>
                    </span>
                </div>
                <div class="error"><span class="error"><?php echo $endTimeErr;?></span></div>	
			</div>
			<br>
			<div class="row1">
				<input type="submit" id="form_btn" name="submit_btn" value="Create banner Object">
			</div>	
		</form>
		<hr>
		<h3>Created Banner Object Info in MYSQL</h3>
		<table width="100%" border="1" cellpadding="3" cellspacing="3">
			<tr>
				<th>id</th>
				<th>bannerImg</th>
				<th>destUrl</th>
				<th>targetIp</th>
				<th>startTime</th>
				<th>endTime</th>
				<th>readyStatus</th>
				<th>timestamp</th>
				<th>Display</th>
			</tr>
			<?php
			if (!print_data())
			{
				echo "<tr>";
				for ($i=0; $i<=6; $i++)
				{
					echo "<td>No record</td>";
				}
				echo "</tr>";
			}
			?>
		</table>
		<hr>
		<h3>Final effect</h3>
		<div id="result_area"></div>
		<hr>
	</div>
	</body>
</html>

