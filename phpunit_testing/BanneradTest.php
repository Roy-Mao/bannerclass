<?php
use PHPUnit\Framework\TestCase;

require 'Bannerad.php';

class BanneradTest extends TestCase
{
	protected $banner;

	public function setUp()
	{
		$this->banner = new \App\Models\Bannerad;
	}

	public function testGetStartTime()
	{
		$this->banner->setstartTime('2017-10-09T23:59:59+09:00');
		$this->assertEquals($this->banner->getstartTime(), '2017-10-09T23:59:59+09:00');
	}

	public function testGetEndTime()
	{
		$this->banner->setendTime('2008-08-08T23:59:59+09:00');
		$this->assertEquals($this->banner->getendTime(), '2008-08-08T23:59:59+09:00');
	}

	public function testGetIp()
	{
		for($x=0; $x<=5000; $x++)
		{
			$randIP = "".mt_rand(0,255).".".mt_rand(0,255).".".mt_rand(0,255).".".mt_rand(0,255);
			$this->banner->setIp($randIP);
			$this->assertEquals($this->banner->getIp(), $randIP);	
		}
	}

	public function testGetBanner()
	{
		$this->banner->setBanner('https://s3-ap-northeast-1.amazonaws.com/bannerobj/banner1.gif');
		$this->assertEquals($this->banner->getBanner(), 'https://s3-ap-northeast-1.amazonaws.com/bannerobj/banner1.gif');
	}

	public function testGetUrl()
	{
		$this->banner->setUrl('https://rocky-savannah-96297.herokuapp.com');
		$this->assertEquals($this->banner->getUrl(), 'https://rocky-savannah-96297.herokuapp.com');
	}

	public function testThatIpIsValid()
	{
		$this->assertFalse($this->banner->checkIp(''));
		for($x=0; $x<=5000; $x++)
		{
			$randIP = "".mt_rand(0,255).".".mt_rand(0,255).".".mt_rand(0,255).".".mt_rand(0,255);
			$this->assertTrue($this->banner->checkIp($randIP));
		}
		$this->assertTrue($this->banner->checkIp('127.0.0.1'));
	}

	public function testCheckBanner()
	{
		$this->assertTrue($this->banner->checkBanner('https://i.imgur.com/O7oavu3.jpg'));
	}

	public function testCheckUrl()
	{
		$this->assertTrue($this->banner->checkUrl('https://www.sohu.com'));
	}

	public function testAssertIso8601Date()
	{
		$this->assertTrue($this->banner->assertISO8601Date('1993-03-13T10:34:04+0800'));
		$this->assertFalse($this->banner->assertISO8601Date('19930313080808'));
	}

	public function testCompareTime()
	{
		$this->assertTrue($this->banner->compareTime('1993-03-13T10:34:04+0800', '2003-03-13T10:34:04+0800'));
	}

}