var client_ip;
var client_timezone = moment().format('Z');
$.getJSON("http://jsonip.com/?callback=?", function (data){
	client_ip = data.ip;
})
$(document).ready(function(){
	document.getElementById('ip_display').innerHTML = "Your current IP: " + client_ip;
	$(function () {
		$('#datetimepicker1').datetimepicker({
            		format: 'YYYY-MM-DDTHH:mm:ss'+client_timezone
		});
        	$('#datetimepicker2').datetimepicker({
        		format: 'YYYY-MM-DDTHH:mm:ss'+client_timezone
        	});
    	});
	$('#countdowntimer').countdown('2017/12/12 14:30:00').on('update.countdown', function(event) {
		var $this = $(this).html(event.strftime(''
			+ '%H Hours '
			+ '%M Minutes '
			+ '%S Seconds'
        	));
    	});
	$('#banner_list').change(function(){
		var bannerSource = $(this).val();
		if(bannerSource && bannerSource != ""){
			$('#banner_location').html('<img src="'+bannerSource+'" width="300px" height="250px" margin="auto">');
		}
		else {
	      $('#banner_location').html('');
		}
	})

	$('#ipbtn').click(function(){
		var txt_auto;
		var generate_ip = (Math.floor(Math.random() * 255) + 1)+"."+(Math.floor(Math.random() * 255) + 0)+"."+(Math.floor(Math.random() * 255) + 0)+"."+(Math.floor(Math.random() * 255) + 0);
		txt_auto = generate_ip;
		$('#ipid').val(txt_auto);
	})
})

function ValidateIPaddress(ipaddress)   
{  
	if (/^(25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\.(25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\.(25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\.(25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)$/.test(ipaddress))  
	{  
		return (true)  
	}  
	alert("You have entered an invalid IP address! Avoid entering space at the end")  
	return (false)  
}  

function display(obj)
{
	//var ip_str = this.closest('tr').find('td:nth-child(4)').text();
	var ip_str = $(obj).closest('tr').find('td:nth-child(4)').text();
	var bool_str = $(obj).closest('tr').find('td:nth-child(7)').text();
	var ip_int = Number(ip_str);
	var target_ip = num2dot(ip_int);
	var img_url = $(obj).closest('tr').find('td:nth-child(2)').text();
	var link_url = $(obj).closest('tr').find('td:nth-child(3)').text();
	if (client_ip !== target_ip)
	{
		var content = "<p>Declined: Client Ip address is not the target Ip</p>"
	}
	else if (bool_str === "false")
	{
		var content = "<p>Declined: Not in the right period of time</p>"
	}
	else
	{
		var decodeimgurl = decodeURIComponent(img_url);
		var decodelinkurl = decodeURIComponent(link_url);
		var content = "<a href='"+ decodelinkurl + "'><img src='"+ decodeimgurl + "'></a>"
	}
	$("#result_area").empty();
	$("#result_area").html(content);
}

function num2dot(num) 
{
    var d = num%256;
    for (var i = 3; i > 0; i--) 
    { 
        num = Math.floor(num/256);
        d = num%256 + '.' + d;
    }
    return d;
}

function ValidateIsoDate(datestring)
{
	re = /^([\+-]?\d{4}(?!\d{2}\b))((-?)((0[1-9]|1[0-2])(\3([12]\d|0[1-9]|3[01]))?|W([0-4]\d|5[0-2])(-?[1-7])?|(00[1-9]|0[1-9]\d|[12]\d{2}|3([0-5]\d|6[1-6])))([T\s]((([01]\d|2[0-3])((:?)[0-5]\d)?|24\:?00)([\.,]\d+(?!:))?)?(\17[0-5]\d([\.,]\d+)?)?([zZ]|([\+-])([01]\d|2[0-3]):?([0-5]\d)?)?)?)?$/
	if (re.test(datestring))
	{
		return (true)
	}
	alert("The date string entered is invalid, please check again and avoid entering space at the end.")
	return (false)
}
