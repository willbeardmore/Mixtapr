<?

/* Local environment detection */
if($_SERVER['REMOTE_ADDR']=='127.0.0.1'){
	$baseurl = "http://local.mixtapr.com";
	$basedomain = "http://local.mixtapr.com";
	$spotifyapp = "31c6e294fc23410a94d9ff740664ad3b"; //Spotify Client ID (Test version)
}else{
	$baseurl = "http://mixtapr.rocks";
	$basedomain = "http://mixtapr.rocks/";
	$spotifyapp = "8022decb281c4f67a9c72453839a8073"; //Spotify Client ID (Live version)
}

$mailgunkey = "key-ff9c580f8d93ea62ec69fa121dd4a61d";

?>