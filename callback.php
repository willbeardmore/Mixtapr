<?
	require("config.php");
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>Who's the tape for?</title>
<meta http-equiv="Content-Type" content="text/html;charset=utf-8" />
<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.8.2/jquery.min.js"></script>
<script type='text/javascript'>//<![CDATA[ 
window.onload=function(){
var target = window.self === window.top ? window.opener : window.parent; //Define the target window to send auth info back to it

$('#tapefor').focus();

var hash = window.location.hash;
if (hash) {
	var token = window.location.hash.split('&')[0].split('=')[1]; //Get the auth token from the URL and put into a variable
	$('#hash').val(token);

}

$('#whosthetapefor').submit(function(){
	if($('#tapefor').val()==""){
		alert("Who's the tape for?");
	}else{
		var themessage = token + "|" + $('#tapefor').val();
		target.postMessage(themessage, '<? echo $baseurl; ?>/'); //Send the auth token back to the parent window
	}
	return false;
});

$('#submit').click(function(){
	$('#whosthetapefor').submit();
	return false;
})

}//]]>  

</script>
<script src="//use.typekit.net/ldb1dco.js"></script>
<script>try{Typekit.load();}catch(e){}</script>
<link href='http://fonts.googleapis.com/css?family=Rock+Salt' rel='stylesheet' type='text/css'>
<style>
body {
	background: #000;
	text-align: center;
	color: #fff;
	font-size: 10pt;
	font-family: "proxima-nova",sans-serif;
}

#submit {
	display: block;
	text-align: center;
	padding: 10px;
	text-transform: uppercase;
	background: #990000;
	transition: 0.2s linear all;
	color: #fff;
	margin: 10px auto;
	width: 90%;
	text-decoration: none;
}

#submit:hover {
	background: #ff0000;
}

#tapefor {
	display: block;
	text-align: center;
	font-size: 16pt;
	border: 0;
	background: transparent;
	border-bottom: 1px solid #fff;
	font-family: 'Rock Salt', cursive;
	margin: 30px auto;
	color: #fff;
	text-decoration: none;
	width: 90%;
	outline: none;
	padding: 10px;
}
</style>

<body>
<h1>Who's the tape for?</h1>
<form action="post" name="whosthetapefor" id="whosthetapefor" method="post">
<input type="hidden" name="hash" id="hash" />
<input type="text" name="tapefor" id="tapefor" />

<a href="#" id="submit">Submit</a>


</form>
</body>
</html>