Mixtapr
=======

Inspired by High Fidelity perfect mixtape, only build a playlist after you've listened to a song

User journey:
 - Log in with Spotify
 - Add song to notebook
 - Record song to tape
 - Send tape to friend (Email. Will add social sharing)

 Note, config.php file not included on GitHub, this is to remove API keys.

 Structure of file appears like so:
 ```<?

/* Local environment detection */
if($_SERVER['REMOTE_ADDR']=='127.0.0.1'){
	$baseurl = "http://local.mixtapr.com";
	$basedomain = "http://local.mixtapr.com";
	$spotifyapp = ""; //Spotify Client ID (Test version)
}else{
	$baseurl = "http://mixtapr.rocks";
	$basedomain = "http://mixtapr.rocks/";
	$spotifyapp = ""; //Spotify Client ID (Live version)
}

$mailgunkey = "";
?>```