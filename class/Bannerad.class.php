<?php
@session_start();
@include_once("../config.php");
date_default_timezone_set("Asia/Tokyo");
define('DEFAULT_TIMEZONE_INT', 9);
/* 
FILE NAME : Bannerad.class.php
FILE LOCATION : class/Bannerad.class.php
*/
class Bannerad
{
	private $bannerAttributes = array();
	public static $specialIp = array("10.0.0.1","10.0.0.2");
	
	//CONSTRUCT METHOD, ALL PARAMS GIVEN DEFAULT VALUE, PROGRAM TERMINATE IMMEDIATELY IF ERROR OCCURS
	public function __construct($startTime="1993-03-13T18:00:35+0900", $endTime="2030-10-06T18:00:35+0900", $targetIp="127.0.0.1", $bannerImg="https://s3-ap-northeast-1.amazonaws.com/bannerobj/banner1.gif", $targetUrl="https://rocky-savannah-96297.herokuapp.com", $showStatus=false)
	{
		$this->setstartTime($startTime);
		$this->setendTime($endTime);
		$this->compareTime($this->bannerAttributes['startTime'], $this->bannerAttributes['endTime']);
		$this->setIp($targetIp);
		$this->setBanner($bannerImg);
		$this->setUrl($targetUrl);
		$this->setStatus();
	}

	//SETTERS: SET METHODS TO SET OBJECT ATTRIBUTES
	public function setstartTime($startTime)
	{
		$validtime = $this->checkTime($startTime);
		if (!$validtime)
		{
			$this->showError();
			die();
		}
		$this->bannerAttributes['startTime'] = $startTime;
		return true;
	}

	public function setendTime($endTime)
	{
		$validtime = $this->checkTime($endTime);
		if(!$validtime)
		{
			$this->showError();
			die();
		}
		$this->bannerAttributes['endTime'] = $endTime;
		return true;
	}

	public function setIp($targetIp)
	{
		$validIp = $this->checkIp($targetIp);
		if(!$validIp)
		{
			$this->showError();
			die();
		}
		$this->bannerAttributes['targetIp'] = $targetIp;
		return true; 
	}


	public function setBanner($banner)
	{
		$validurl = $this->checkBanner($banner);
		if(!$validurl)
		{
			$this->showError();
			die();
		}
		$this->bannerAttributes['bannerImg'] = $banner; 
		return true;
	}

	public function setUrl($targetUrl)
	{
		$validurl = $this->checkUrl($targetUrl);
		if(!$validurl)
		{
			$this->showError();
			die("Bannerad.class.php, Function setUrl Error: invalid parameter.");
		}
		$this->bannerAttributes['targetUrl'] = $targetUrl;
		return true;
	}

	public function setStatus()
	{
		$current_time = date("Y-m-dTH:i:s+0900");
		$unicurrentTime = strtotime($current_time);
		$unistartTime = strtotime($this->bannerAttributes['startTime']);
		$uniendTime = strtotime($this->bannerAttributes['endTime']);
		if (($unicurrentTime >= $unistartTime) && ($unicurrentTime <= $uniendTime))
		{
			$this->bannerAttributes['showStatus'] = true;
			return true;
		}
		if (in_array($this->bannerAttributes['targetIp'], self :: $specialIp))
		{
			$this->bannerAttributes['showStatus'] = true;
			return true;
		}
		$this->bannerAttributes['showStatus'] = false;
		return false;
	}

	//GETTERS: GET METHODS TO RETRIEVE OBJECT ATTRIBUTES
	public function getstartTime()
	{
		return $this->bannerAttributes['startTime'];
	}

	public function getendTime()
	{
		return $this->bannerAttributes['endTime'];
	}

	public function getIp()
	{
		return $this->bannerAttributes['targetIp']; 
	}

	public function getBanner()
	{
		return $this->bannerAttributes['bannerImg']; 
	}

	public function getUrl()
	{
		return $this->bannerAttributes['targetUrl'];
	}

	public function getStatus()
	{
		return $this->bannerAttributes['showStatus']; 
	}


	//ALL CHECK METHODS TO VALIDATE THE GIVEN PARAMS
	public function checkTime($isotime)
	{
		if(empty($isotime))
		{
			$_SESSION['message'][]="Function checkTime error: parameters can not be empty";
			return false;
		}
		else
		{
			$valid = $this->assertISO8601Date($isotime);
			if (!$valid)
			{
				$_SESSION['message'][]="Function checkTime error: Inappropriatedly formatted time string, must be ISO8601 format with timezone";
				return false;
			}
		}
		return true;
	}

	public function checkIp($targetIp)
	{
		if(empty($targetIp))
		{
			$_SESSION['message'][] = "Function checkIp error: parameter can not be empty";
			return false;
		}
		else
		{
			$validip = filter_var($targetIp, FILTER_VALIDATE_IP);
			if(!$validip)
			{
				$_SESSION['message'][] = "Function checkIp error: Invalid parameter";
				return false;
			}
		}
		return true;
	}

	public function checkBanner($bannerurl)
	{
		$allow_format = array('gif', 'jpg', 'png');
		$validurl = $this->checkUrl($bannerurl);
		if(!$validurl)
		{
			$this->ShowError();
			die("The banner img url is invalid<br/>");
		}
		$path_parts = pathinfo($bannerurl);
		$extension = $path_parts['extension'];
		$extension = strtolower($extension);
		if (!(in_array($extension, $allow_format)))
		{
			die("Bannerad.class.php, Function checkBanner Error: Check the file extension to be gif,jpg or png ");
		}
		return true;
	}

	public function checkUrl($targeturl)
	{
		if(empty($targeturl))
		{
			$_SESSION['message'][] = "Function checkUrl error1: Parameter targeturl can not be empty";
			$this->ShowError();
			die();
		}
		else
		{
			$targeturl= filter_var($targeturl, FILTER_SANITIZE_URL);
			$validurl = filter_var($targeturl, FILTER_VALIDATE_URL);
			if(!$validurl)
			{
				$_SESSION['message'][] = "Function checkUrl error2: Parameter targeturl not valid";
				$this->ShowError();
				die();
			}
		}
		return true;
	}

	public static function assertISO8601Date($dateStr) 
	{
		if (preg_match('/^([\+-]?\d{4}(?!\d{2}\b))((-?)((0[1-9]|1[0-2])(\3([12]\d|0[1-9]|3[01]))?|W([0-4]\d|5[0-2])(-?[1-7])?|(00[1-9]|0[1-9]\d|[12]\d{2}|3([0-5]\d|6[1-6])))([T\s]((([01]\d|2[0-3])((:?)[0-5]\d)?|24\:?00)([\.,]\d+(?!:))?)?(\17[0-5]\d([\.,]\d+)?)?([zZ]|([\+-])([01]\d|2[0-3]):?([0-5]\d)?)?)?)?$/', $dateStr) > 0)
		{
			return true;
		} 
		else 
		{
			return false;
		}
	}

	public function compareTime($starttime, $endtime)
	{
		$startTime = strtotime($starttime);
		$endTime = strtotime($endtime);
		$timeDiff = $endTime - $startTime;
		if ($timeDiff <= 0)
		{
			$_SESSION['message'][] = "Function compareTime error:endTime ahead of startTime";
			$this->ShowError();
			die();
		}
		return true;
	}

	public function japanizeTimestring($timestring)
	{
		global $tokyo_time;
		$validstring = $this->assertISO8601Date($timestring);
		if (!$validstring)
		{
			die("Banner.class.php, Function japanizeTimestring error: not a valid ISO time string");
		}
		$timeint = strtotime($timestring);
		$date_obj = new DateTime("@$timeint");
		$date_obj->setTimezone($tokyo_time);
		$timestring = $date_obj->format('Y-m-d H:i:s');
		return $timestring;
	}

	public function __toString()
	{
		$status = 'false';
		if ($this->bannerAttributes['showStatus'])
		{
			$status = 'true';
		}
		return sprintf("----------<br/>"."Banner Objec Info: <br/>startTime: ".$this->bannerAttributes['startTime']."<br/>endTime: ".$this->bannerAttributes['endTime']."<br/>targetIp: ".$this->bannerAttributes['targetIp']."<br/>bannerUrl: ".$this->bannerAttributes['bannerImg']."<br/>targetUrl: ".$this->bannerAttributes['targetUrl']."<br/>showStatus: ".$status)."<br/>";
	}
	
	public function storeInfo()
	{
		global $cnf;
		$conn = new mysqli($cnf['dbhost'], $cnf['dbuser'], $cnf['dbpass'], $cnf['database']);
		if($conn->connect_error)
		{
			die("Banner.class.php, storeInfo function,Connection failed: ".$conn->connect_error);
		}
		else
		{
			$conn->select_db($cnf['database']);
			if ($result = $conn->query("SELECT DATABASE()")){}
			else
			{
				die("Bannerad.class.php, Function storeInfo, Error occured when selecting the database: ".$conn->error."<br/>");
			}
		}
		$db_starttime = $this->japanizeTimestring($this->getstartTime());
		$db_endtime = $this->japanizeTimestring($this->getendTime());
		$db_targetip = ip2long($this->getIp());
		sprintf('%u',ip2long($db_targetip));
		$db_bannerurl = urlencode($this->getBanner());
		$db_bannerurl = $conn->real_escape_string($db_bannerurl);
		$db_targeturl = urlencode($this->getUrl());
		$db_targeturl = $conn->real_escape_string($db_targeturl);
		$db_status;
		if($this->getStatus())
		{
			$db_status = 1;
		}
		else
		{
			$db_status = 0;
		}

		$sql = "INSERT INTO banner (starttime, endtime, targetip, banner_url, dest_url, show_status) VALUES ('$db_starttime', '$db_endtime', $db_targetip, '$db_bannerurl', '$db_targeturl', $db_status)";
		if ($conn->query($sql) === true)
		{
			echo "New record created successfully<br/>";
		}
		else
		{
			die("Bannerad.class.php, Function stroeInfo, Error: ".$sql."<br>".$conn->error);
		}
		$conn->close();
		return true;
	}

	//ERROR HANDLING
	public function ShowError()
	{
		if(isset($_SESSION['message']) && !empty($_SESSION['message']))
		{
			foreach ($_SESSION['message'] as $key => $value)
			{
				echo '<div>'.stripslashes($value).'</div>';
			}	
		}
		unset($_SESSION['message']);
	}
}
?>
