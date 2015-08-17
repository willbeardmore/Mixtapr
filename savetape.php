<?

	$accesstoken = $_POST['accesstoken'];
	$playlisturi = $_POST['playlisturi'];
	$playlistname = $_POST['playlistname'];

	$playlistparts = explode(":",$playlisturi);
	$playlistid = end($playlistparts);
	$user = $playlistparts[2];

	$request = "https://api.spotify.com/v1/users/" . $user . "/playlists/" . $playlistid . "/tracks";

	$opts = array(
	  'http'=>array(
	    'method'=>"GET",
	    'header'=>"Authorization: Bearer " . $accesstoken
	  )
	);

	$context = stream_context_create($opts);

	$file = file_get_contents($request, false, $context);
	$playlist = json_decode($file,TRUE);


	$tracks = array();

	foreach($playlist['items'] as $track){
		$artists = array();

		foreach($track['track']['artists'] as $theartist){
			array_push($artists,$theartist['name']);
		}
		
		switch (count($artists)) {
			case 1:
				$artist = $artists[0];
			case 2:
				$artist = $artists[0] . " and " . $artists[1];
			default:
				$artist = join(' and ', array_filter(array_merge(array(join(', ', array_slice($artists, 0, -1))), array_slice($artists, -1))));
		}

		$trackname = $artist . " - " . $track['track']['name'];

		if(strlen($trackname)>60){
			$trackname = substr($trackname,0,57) . "...";
		}

		array_push($tracks,$trackname);

	}

	$textplacement = array(
		array('x'=>35,'y'=>265),
		array('x'=>35,'y'=>315),
		array('x'=>35,'y'=>365),
		array('x'=>35,'y'=>415),
		array('x'=>35,'y'=>465),
		array('x'=>35,'y'=>515),
		array('x'=>320,'y'=>265),
		array('x'=>320,'y'=>315),
		array('x'=>320,'y'=>365),
		array('x'=>320,'y'=>415),
		array('x'=>320,'y'=>465),
		array('x'=>320,'y'=>515)
	);

	$image = imagecreatefromjpeg('images/tapebg.jpg');
	$colour = imagecolorallocate($image, 0, 0, 0);

	imagefttext($image, 20, 0, 45, 160, $colour, 'RockSalt.ttf', $playlistname);



	foreach($tracks as $key=>$track){
		$tracktext = $key+1 . ". " . $track;
		
		if(strlen($tracktext)>35){
			$tracktext = wordwrap($tracktext,30,'<br />');
			$tracktext = explode("<br />",$tracktext);
			if(count($tracktext)>2){
				$tracktext[1] = $tracktext[1] . $tracktext[2];
			}

			imagefttext($image, 10, 0,$textplacement[$key]['x'], $textplacement[$key]['y'], $colour, 'RockSalt.ttf', $tracktext[0]);
			imagefttext($image, 10, 0,$textplacement[$key]['x']+15, $textplacement[$key]['y']+22, $colour, 'RockSalt.ttf', $tracktext[1]);
		}else{
			imagefttext($image, 10, 0,$textplacement[$key]['x'], $textplacement[$key]['y']+7, $colour, 'RockSalt.ttf', $tracktext);
		}

		
	}

	imagejpeg($image, 'images/tapes/'.$playlistid . '.jpg',80);

	imagedestroy($image);

	$output = array(
		"image" => "images/tapes/" . $playlistid . ".jpg"
	);

	header('Content-Type: application/json');
	echo json_encode($output);

?>