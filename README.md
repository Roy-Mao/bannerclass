![Imgur](https://i.imgur.com/o5ZBNMN.png)
## About this project
Skill set interview (mercari souzoh):

A banner advertisement class creator.

## Language and Environment

* Backend: PHP(7.1.7) 
* Frontend: Javascript(6.11.0)
* OS: macOS 10.13 (17A365) 64bits
* Database: MYSQL(5.7.19)
* FileStorage: AWS S3
* Unit Testing: phpunit(6.4.1)
   * Not that familiar with php and php unit testing in general, still learning these stuff.
## Installation / Deployment / Concerns

1. Local Installation
* Please connect the demonstrate.php to your local mysql database. db_Password and db_Username can be found in the config.php
(Not 100% sure if it works with your operating system though, I test it on my local machine, works smoothly. ^_^ )

2. Future deployment
* I actually wanted to deploy this small project on heroku for an easier effect renderation, but heroku uses Postgres instead of MySQL, so I decide not to do that at the moment.

3. Concerns
* Due to time limit README is only written in English. However, interview in Japanese is ok.
* I am not a CS major, and not so familiar with php compared to python. However, the object oriented programming thought is the same. Please let me know where should I improve regarding to the banner class that I made. Thanks for your tolerance.


## Directory Structure
```
root
│   README.md
│   file001.txt
|   NOTE.md
|   README.md
|   banner_database.sql
|   config.php
|   demonstrate.php
└───class
│   │   Bannerad.class.php  
└───js
|   │   jquery.countdown.min.js
|   │   myjscript.js
|
└───css
|   |  mycss.css
|
└───phpunit_testing
   |  Bannerad.php
   |  BanneradTest.php
   |  composer.json
   |  phpunit.xml
```

## Assumptions

#### Banner size:
* The specifics does not require the banner, size. According to google, the most effective banner size with highly click rate is: 300px * 250px.
    * Reference: https://ppc.news/top10-banner-sizes/; Therefore, I choose this as the default banner size in demonstrate.php.
* User Experience. Speed issue is critical for banner advertisement. Ideally, the size of a banner should not exceed 150kb for a faster renderation.

#### Target IP:
* I assume that all the target IP addrsses are IPv4 not IPv6. Therefore I choose the UNSIGNGED INT for IP address data storage. If IPv6 is allowed, it would be better to change the DATATYPE to BINARY instead of INT.

#### Client Browser Compatability:
* Only consider the modern browser Chrome, Safari, Firefox, may not be backed up for old version of IE explore.

## Gernal Thinking Flow:  

### 1. MYSQL - 

* A banner table to store basic banner object attributes
    * Maybe need another table of detailed banner info linked by foreign key(id) if there are more info need to be stored, such as banner click counter / view counter / other image meta data (size, format)
    * Only a basic table named banner is created in demonstrate.php for demonstration purpose.

### 2. AWS S3 -

* A new bucked in my account to hold the uploaded img files
    * Use the AWS S3 banner image file path as the banner image url parameter when creating the banner object.
    * Choose not to store file on disk due to possible size problems when performing queries. Especially in the case of displaying banner where speed should be prioritized.

### 3. PHP backend -

* User upload the image. Store the image in S3 bucket and return a valid url for banner object creation, after which we store object info in database.
     * Double validation to ensure user input. (resize, convert etc). Frontend-Javascript, Backend-PHP.
     * PHP send the validated banner image file to S3 bucket -> Create the banner object -> Store banner object info in database corresponding table. 

## DATABASE DESIGN:

* DATATYPE AND FIELDS:
    * Use MySQLi object oriented method to query and connet the database for security reason.
    * Use multiple Unique values to prevent storing duplicate object information.
    * IP Address using 4 bytes Unsigned Int. Save space, faster, convinient to index.
        * However, if ipv6 address allowed, then we should use Binary(16) to instore.
    * Starttime and endtime. DATETIME instead of TIMESTAMP, because I want the data always come out as Tokyo time
        * Becareful about timezone conversion before inserting info into the databse.
    * URL. Since all the url used for retrieving image is from AWS S3 bucked. Varchar(1024)should be sufficient.   
        * Reference: http://docs.aws.amazon.com/AmazonS3/latest/dev/UsingMetadata.html
        * It is interesting to calculate the allowed space for each column when declaring a field in MYSQL.    
        * Reference: https://dev.mysql.com/doc/refman/5.7/en/char.html
    * TIMESTAMP. Should also update automatically everytime the stored object info is updated.

## Dangerous point:

* **Double check** in both front-end and back-end to prevent SQL injection attack.
    (Spaces, illegal charactersin url, 0, Null, html special characters escape etc)

* **MYSQL time zone issue:**
    * MYSQL DATETIME can not store ISO8601 datetime string timezone info directly, need do some manipulation before save the object data before insert it into database.
    
    * NOTICE: By default MYSQL DATETIME use the time zone of the server's time. Make sure to set the timezone constant using Tokyo time as stantard. 
    
    * MYSQL TIMESTAMP DATATYPE is automatically using UTC time. To avoid confusion, we need to set the timezone enviromental varibale to `Asia/Tokyo(+0900)` to ensure all the datetime date inserted to and retrieved from the database is based on +0900 timezone.   
    
* **BE CAREFUL about URL format:**  max lengt/encode/decode method when insert url into database.Always use absolute path instead of relative path. Use urlencode()and mysql_real_escape_string()    

* **USING PHP ip2long function:**  It might return a negative value on 32-bit system (192.0.0.0, possibilities increases when ip address is bigger) while always positive on 64-bit system.
    * Workaroud: Always to use %u to output the converted value to an unsigned integer.    
    
* **BOOLEAN INSERTION:** Do not insert boolean value in to mysql directly, change to TINIINT even though it is the same. MYSQL will complain when encounter not converted false value. 
### 


## How to use the Bannerad class

### Initialization

* Initiation: ```$banner = new Bannerad($startTime, $endTime, $targetIp, $bannerImg, $targetUrl, $showStatus)```
    *  $startTime: String. Required. ISO8601 Format string with timezone. Default to "1993-03-13T18:00:35+0900". Default time zone: +0900 if not given.
    *  $endTime: String. Required. ISO8601 Format string with timezone. Default to "2030-10-06T18:00:35+0900". Default time zone: +0900 if not given.
    *  $targetIp: String. Required. Valid ipv4 address. Default to "127.0.0.1"
    *  $bannerImg: String. Required. Valid url string. Default to "https://s3-ap-northeast-1.amazonaws.com/bannerobj/banner1.gif"
    *  $targetUrl: String. Required. Valid url of banner redirect link.Default to "https://rocky-savannah-96297.herokuapp.com"
    *  $showStatus: Bool. Required. Default to false

### Method

* `$banner->setstartTime($startTime)`
    * function: set banner display start time 
    * input:  String. Required. ISO8601 Format string with timezone. Default to "1993-03-13T18:00:35+0900". Default time zone: +0900 if not given.
    * return: true
* `$banner->setendTime($endTime)`
    * function: set banner display end time
    * input:  String. Required. ISO8601 Format string with timezone. Default to "2030-10-16T18:00:35+0900". Default time zone: +0900 if not given.
    * return: true
* `$banner->setIp($targetIp)`
    * function: set target client Ipv4 address
    * input:  String. Required. Legal Ipv4 address
    * return: true
* `$banner->setBanner($banner)`
    * function: set the banner img url.
    * input:  String. Required. Legal url address.Only allow: jpg, png and gif.
    * return: true
* `$banner->setUrl($targetUrl)`
    * function: set the redirect link url when user click the banner
    * input:  String. Required. Legal Ipv4 address
    * return: true
* `$banner->setStatus()`
    * function: set the pre-determined display status of banner object
    * input:  no input required.
    * The return value is determined internally based on *the time object being created* / *the banner display starttime* / *the banner display endtime*  
    * return: 
        * true: if the time at which this banner object being created is within display starttime and endtime
        * flase: if the time at which this banner being created is outside the range between the display starttime and endtime
* `$banner->getstartTime()`
    * return: String. An ISO8601 Formatted string.
    * get the display starttime of this banner object.
* `$banner->getendTime()`
    * return: String. An ISO8601 Formatted string.
    * get the display endtime of this banner object.
* `$banner->getIp()`
    * return: String. A ipv4 string. 
    * get the target client ipv4 address of this banner object.
* `$banner->getBanner()`
    * return: String. A image url with the only allowed extension: gif, jpg and gif
    * get the display banner image url of this banner object.
* `$banner->getUrl()`
    * return : String. A redirect url link when user clicked the banner image.
    * get the redirect link url of the banner object.
* `$banner->getStatus()`
    * return: boolean
        * true: if creation time is between starttime and endtime
        * false: other wise
    * get the show status of this banner object
* `$banner->checkTime($isotime)`
    * input: String. a valid ISO8601 formatted datetime string 
    * return: boolean
        * true: if the ISO8601 string is legal
        * false: if the ISO8601 string is illegal
    * check if a datetime string is properly ISO8601 formatted 
* `$banner->chekcIp($targetIp)`
    * input: String. a valid ipv4 address
    * return: boolean
        * true: if the string provided is legal ipv4 address.
        * false: if the string provided is illegal ipv4 address.
    * validate the provided ipv4 string.
* `$banner->getStatus($bannerurl)`
    * input: String. A full path valid url with allowed extension: gif, jpg and png
    * return: true
    * terminate: imediately if invalid url is provided.
    * validate the banner image url. Only png, jpg, gif extension allowed.
* `$banner->checkUrl($targeturl)`
    * input: String. A full path valid url for the redirect link url.
    * return: true
    * terminate: imediately if invalid url is provided.
    * validate the redirect link url after the banner is clicked.
* `$banner->assertISO8601Date($dateStr)`
    * input: String. An ISO8601 formatted string.
    * return: boolean
        * true: if the string provided is legal ISO8601 string
        * flase: if the string provided is illegal ISO8601 string
    * validate if the string provided is properly ISO8601 formatted
* `$banner->compareTime($starttime, $endtime)`
    * input:
        * $starttime: String. ISO8601 formatted datetime string. The display starttime of the banner object.
        * $endtime: String. ISO8601 formatted datetime string. The display endtime of the banner object.
    * return: true
    * terminate: immediately if the endtime is prior to the starttime
    * ensure that banner display starttime is ahead of the display endtime
* `$banner->japanizeTimestring($timestring)`
    * input: String. ISO8601 formatted datetime string.
    * Defualt time_zone: If timezone is not specified in the ISO8601 string, then *`"Asia/Tokyo(+0900)"`* will be set to the default.
    * return: timedate string based on Japanese standard time with out timezone. Format: `Y-m-d H:i:s`
    * change a ISO8601 datetime string to its Japanese time zone counterpart.
* `$banner->storeInfo()`
    * return: true;
    * terminate: immediately if error occured when trying to connect or manipulate with  the database.
    * store the object information into MYSQL database.
* `$banner->ShowError()`
    * return: void
    * display the error information stored in the session.

## Questions
Please contact me if you have any concerns using this class

* Email:(ruoyu.mao#icloud.com, change # to @)
* facebook: [@Roymao](https://www.facebook.com/ruoyu.mao)
* github: [@Roymao](https://github.com/Roy-Mao)

## Appreciation
Special thanks to mercuri/souzoh for the skill set test.

* [mou](http://mouapp.com/)
* [mercuri/souzoh](https://www.souzoh.com/jp/)
