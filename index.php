<? require('config.php'); ?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>Mixtapr // A High Fidelity Inspired Spotify Mixtape Generator</title>
<link rel="icon" type="image/png" href="images/favicon.png" />

<meta http-equiv="Content-Type" content="text/html;charset=utf-8" />
<meta property="og:url" content="http://mixtapr.rocks" />
<meta property="og:title" content="Mixtapr // A High Fidelity Inspired Spotify Mixtape Generator" />
<meta name="description" content="Send a friend a carefully considered Spotify playlist using Mixtapr, requiring you to make a little effort for a more rewarding playlist." />
<meta property="og:image" content="http://mixtapr.rocks/images/og.jpg" />
<meta property="og:site_name" content="Mixtapr" />
<meta property="fb:admins" content="529506836"/>
<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.8.2/jquery.min.js"></script>
<script src="http://ajax.googleapis.com/ajax/libs/jqueryui/1.10.3/jquery-ui.min.js"></script>
<script type="text/javascript" src="js/jquery.datetimepicker.js"></script>
<script src="//use.typekit.net/ldb1dco.js"></script>
<script>try{Typekit.load();}catch(e){}</script>
<link href='http://fonts.googleapis.com/css?family=Rock+Salt' rel='stylesheet' type='text/css'>
<link href="font/stylesheet.css" rel="stylesheet" type="text/css" />
<link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/font-awesome/4.3.0/css/font-awesome.min.css">
<script type="text/javascript" src="js/soundmanager2-nodebug-jsmin.js"></script>
<script type="text/javascript" src="js/jquery.marquee.min.js"></script>
<link rel="stylesheet" href="js/jquery.datetimepicker.css" type="text/css" />
<script type="text/javascript">
var baseurl = "<? echo $baseurl; ?>";
var spotifyapp = "<? echo $spotifyapp; ?>";
var accesstoken = "";
var playlistid = "";
var username = "";
var useremail = "";
var latestsnapshot = "";
var playlistname = "";
var playlisturi = "";

/* Function to build a query string URL from an array, for the Spotify auth link */
  function toQueryString(obj) {
      var parts = [];
      for (var i in obj) {
          if (obj.hasOwnProperty(i)) {
              parts.push(encodeURIComponent(i) + "=" + encodeURIComponent(obj[i]));
          }
      }
      return parts.join("&");
  }

  createtime = function(nMSec, bAsString) {
    var nSec = Math.floor(nMSec/1000),
        min = Math.floor(nSec/60),
        sec = nSec-(min*60);

    return (bAsString?(min+':'+(sec<10?'0'+sec:sec)):{'min':min,'sec':sec});
};

soundManager.url = 'js/swf/';

soundManager.flashVersion = 9;
soundManager.useFlashBlock = false;
soundManager.useHighPerformance = true;
soundManager.wmode = 'transparent';
soundManager.useFastPolling = true;

$(document).ready(function(){
soundManager.onready(function() {

	soundManager.createSound({
	    id: 'cdrecord',
	    url: 'sfx/cd-record.mp3'
	});

	soundManager.createSound({
        id: 'stoptape',
        url: 'sfx/stop.mp3'
    });
    
    soundManager.createSound({
    	id: 'rewind',
    	url: 'sfx/rewind.mp3'
    });


	function spotifylogin() {

		/*Variables for authWindow*/
		var width = 400,height = 500;
		var left = (screen.width / 2) - (width / 2);
		var top = (screen.height / 2) - (height / 2);

		var params = {
			client_id: spotifyapp,
			redirect_uri: baseurl + '/callback.php',
          scope: 'playlist-modify user-read-email user-read-private', //Establish the permissions you wish to get from Spotify, documented here: 
          response_type: 'token'
      };
      authWindow = window.open(
      	"https://accounts.spotify.com/authorize?" + toQueryString(params),
      	"Spotify",
      	'menubar=no,location=no,resizable=no,scrollbars=no,status=no, width=' + width + ', height=' + height + ', top=' + top + ', left=' + left
      );
  }

  $('#spotifyconnect').click(function(){
  	spotifylogin();
  	return false;
  });

  /*Browser message event listener and trigger after successful authentication from Spotify API. Deals with info sent from callback.php*/
  function receiveMessage(event){

      if (event.origin !== baseurl) {
          return; //If the event was not sent by this app, then do nothing
      }

      if (authWindow) {
          authWindow.close(); //Close the Spotify AuthWindow
      }

      messageparts = event.data.split("|");
      var token = messageparts[0];
      var tapefor = messageparts[1];

 		accesstoken = token;
      spotifyresponse(token); //Pass to the function to handle the Spotify response

      console.log(accesstoken);

      $('#notebook h2').text('Tape for ' + tapefor);

      playlistname = "Tape for " + tapefor;


      $('#theirname').val(tapefor);




      accesstoken = token;
  }

  window.addEventListener("message", receiveMessage, false);

  /*Now that we have an Auth token, build a Spotify call and retrieve the user's info. Then proceed, hiding the Spotify login and show the Facebook login*/

	function spotifyresponse(accesstoken) {

	    $.ajax({
	        url: 'https://api.spotify.com/v1/me', //Get information about logged in user
	        headers: {
	            'Authorization': 'Bearer ' + accesstoken
	        },
	        success: function(spotifyresponse) {
	            usercountry = spotifyresponse.country;
	            username = spotifyresponse.id;
	            useremail = spotifyresponse.email;
	            var theirname = spotifyresponse.display_name;
	            try{
	            	var nameparts = theirname.split(' ');
	            	var thename = nameparts[0];
	            }catch(error){
	            	var thename = username;
	            }
	            $('#tracktext').text('Hi ' + thename);

	            ga('send', 'event', 'Mixtapr', 'Login',username + " - " + useremail);

	            $('#yourname').val(thename);
	            $('#youremail').val(useremail);

	            $('#screen1').fadeOut('slow',function(){
	            	$('#screen2').show();
	            	$('#screen2 h1').slideDown();
	            	$('#screen2 .ninesixty').fadeIn('normal',function(){
	            		$('#search').focus();
	            	});
	            	dosizes();
	            });
	        }
	    });
	}


	$('#searchform').submit(function(){
		$('#resultslist').text("Loading");

		ga('send', 'event', 'Mixtapr', 'Search',$('#search').val());

		$.ajax({
		    url: 'https://api.spotify.com/v1/search?query=' + encodeURIComponent($('#search').val()) + '&type=track&market=from_token',
		    headers: {
		            'Authorization': 'Bearer ' + accesstoken
		    },
		    success: function(response) {
		    	$('#resultslist').text('');
		    	$('#searchresults').show();
		    	$(response.tracks.items).each(function(index,track){
		    		if (index % 2 === 0) { var trackclass = "even"; }else{ var trackclass = "odd"; }
		    		var tracktitle = track.artists[0].name + ' - ' + track.name;
		    		var artists = "";
		    		$(track.artists).each(function(i,artist){
		    			artists = artists + ", " + artist.name;
		    		});

		    		artists = artists.substring(2);

		    		$('#resultslist').append('<div data-previewurl="' + track.preview_url + '" class="trackselect ' + trackclass + '" data-trackname="' + encodeURIComponent(tracktitle) + '" data-spotifyid="' + track.id + '"><a href="#" class="preview"><i class="fa fa-play"></i><i class="fa fa-pause"></i></a><span class="tracktitle">' + track.name + '<br />' + artists + '</span><a href="#" class="add"><i class="fa fa-plus"></i></a><div style="clear: both"></div></div>');
		    	})
		    	

		    }
		});
		return false;
	});

		$(document).on('click','.add',function(){
			var tracktitle = $(this).closest('.trackselect').data('trackname');
			var spotifyid = $(this).closest('.trackselect').data('spotifyid');
			var previewurl = $(this).closest('.trackselect').data('previewurl');

			if($('.full').length==12){
				alert("There's no room left on your tape. Remove a song to make room.");
			}

			ga('send', 'event', 'Mixtapr', 'Added To Notepad',tracktitle);

			$('.track:not(.full):first .notetracktitle').text(decodeURIComponent(tracktitle));
			$('.track:not(.full):first').attr({'data-spotifyid':spotifyid,'data-previewurl':previewurl}).addClass('full');

			$('#record.disabled').removeClass('disabled');

			setarrows();

			return false;
		});

		$(document).on('mousedown','.preview',function(){
			var previewurl = $(this).closest('.trackselect').data('previewurl');
			var trackid = $(this).closest('.trackselect').data('spotifyid');
			soundManager.createSound({
	            id: trackid,
	            url: previewurl
	        });
			soundManager.play(trackid);
			$(this).addClass('playing');
			ga('send', 'event', 'Mixtapr', 'Preview',trackid);

			return false;

		});

		$(document).on('mouseup','.preview',function(){
			var trackid = $(this).closest('.trackselect').data('spotifyid');
			$(this).removeClass('playing');
			soundManager.stop(trackid);
			return false;
		});

		$(document).on('mouseout','.preview',function(){
			var trackid = $(this).closest('.trackselect').data('spotifyid');

			try{
				soundManager.stop(trackid);
			}catch(error){
				console.log(error);
			}

			$(this).removeClass('playing');
		});

		$(document).on('click','.minus',function(){
			$(this).closest('.tracks').append('<li class="track"><span class="notetracktitle"></span><div class="actions"><a href="#" class="up"><i class="fa fa-arrow-up"></i></a><a href="#" class="down"><i class="fa fa-arrow-down"></i></a><a href="#" class="minus"><i class="fa fa-minus"></i></a></li>');
			var notetracktitle = $(this).closest('.notetracktitle').text();

			$(this).closest('.track').remove();

			setarrows();

			if($('.full').length<1){
				$('#record').addClass('disabled');
			}

			ga('send', 'event', 'Mixtapr', 'Removed from Notepad',notetracktitle);


			return false;
		});

		$(document).on('click','.up',function(){
			var thisindex = $(this).closest('.track').index('.track');
			var thisline = $(this).closest('.track');

			var previndex = thisindex-1;

			$(this).closest('.track').remove();

			$('.track').eq(previndex).before(thisline);

			setarrows();

			return false;
		});

		$(document).on('click','.down',function(){
			var thisindex = $(this).closest('.track').index('.track');
			var thisline = $(this).closest('.track');

			$(this).closest('.track').remove();

			$('.track').eq(thisindex).after(thisline);

			setarrows();

			return false;
		});

		$(document).on('click','.disabled',function(){
			return false;
		});

		$(document).on('click','#record:not(.disabled)',function(){

			$('html,body').animate({'scrollTop':$(document).height()},1000);

			$(this).addClass('disabled');
			$('#finish').addClass('disabled');
			$('#rewind').addClass('disabled');

			var numberoftracks = $('.full:not(.recorded)').length;

			console.log(numberoftracks);

			ga('send', 'event', 'Mixtapr', 'Recording','Pressed Record',numberoftracks);


			if(playlistid==""){
				$.ajax({
			    url: 'https://api.spotify.com/v1/users/' + username + '/playlists/',
			    type: 'POST',
			    data: JSON.stringify({ "name": $('#notebook h2').text() + ' // Mixtapr', "public" : true }),
			    headers: {
			      'Authorization': 'Bearer ' + accesstoken,
			      'Content-Type': 'application/json'
			    },
			    success: function(r) {
			    	playlistid = r.id;
			    	playlisturi = r.uri;

			    },
			    error: function(r) {
			      //console.log(r);
			    }
			   });
			}

			$('.full:not(.recorded)').each(function(i){

				var previewurl = $(this).data('previewurl');

				soundManager.createSound({

		            id: $(this).attr('id'),
		            url: previewurl,
		            onplay: function(){
		            	$('#tape').addClass('recording');
		            	$('.track.recording').removeClass('recording');
		            	var thisid = $(this).attr('id');
		            	$('#' + thisid).addClass('recording');
		            	$('#playertext .rectext').text('REC');
		            	$('#tracktext').text($('#' + thisid + ' .notetracktitle').text()).marquee({duplicated: true});
		            	setarrows();
		            },
		            whileplaying:function() {
                    	var currenttime = createtime(this.position,true);
                    	$('#counter').text(currenttime);
                    },
		            onfinish: function(){
		            	$('#counter').text('0:00');
		            	$('#tracktext').text('');
		            	$('#tape').removeClass('recording');
		            	$('.track.recording').removeClass('recording');
		            	var thisid = $(this).attr('id');
		            	$('#' + thisid).addClass('recorded');

					      var tracklink = 'spotify:track:' + $('#' + thisid).data('spotifyid');

					      var url = 'https://api.spotify.com/v1/users/' + username + '/playlists/' + playlistid + '/tracks?uris=' + tracklink;

							$.ajax({
		                        url: url,
		                        type: 'POST',
		                        dataType: 'text',
		                        headers: {
		                          'Authorization': 'Bearer ' + accesstoken
		                        },
		                        success: function(r) {
		                        	//console.log("Recorded " + $('#' + thisid + ' .notetracktitle').text());
		                        	var response = JSON.parse(r);
		                        	latestsnapshot = response.snapshot_id;
		                        	//console.log(response);
		                        	//console.log(latestsnapshot);
		                        },
		                        error: function(r) {
		                      		console.log("Failed " + $('#' + thisid + ' .notetracktitle').text());

		                        }
		                    });


		            	//setarrows();
		            	//console.log('Number of tracks ' + $('.track.full').length);

		            	//console.log('Number of remaining tracks ' + $('.track.full:not(.recorded)').length);

		            	if($('.track.full:not(.recorded):first').length>0){
		            		var nextid = $('.track.full:not(.recorded):first').attr('id');
		            		soundManager.play('stoptape',{
		            			multiShotEvents: true,
		            			onfinish:function(){
		            				soundManager.play('cdrecord',{
		            					multiShotEvents: true,
		            					onplay:function(){
		            						$('#playertext .rectext').text('OPN');
		            					},
		            					onfinish:function(){
		            						$('#playertext .rectext').text('REC');
		            						soundManager.play(nextid);
		            					}
		            				})
		            			}
		            		});
		            	}else{
		            		soundManager.play('stoptape');
		            		$('#playertext').hide();

		            		$('#finish').removeClass('disabled');
		            		$('#rewind').removeClass('disabled');
		            		setarrows();


		            		// alert("DONE");
		            	}

		            	if($('.full').length == $('.recorded').length){
		            		$('#record').addClass('disabled');
		            	}

		            }
		        });
			});

			var firstsound = $('.full:not(.recorded):first').attr('id');

			soundManager.play('cdrecord',{
			    multiShotEvents: true,
			    onplay:function(){
			    	$('#playertext').show();
			    	$('#playertext .rectext').text('OPN');
			    },
			    onfinish:function() {
			        soundManager.play(firstsound);
			    }
			 });

			return false;
		});

	});
	
	$(document).on('click','#rewind:not(.disabled)',function(){
		$('#record').addClass('disabled');
		$(this).addClass('disabled');
		$('#finish').addClass('disabled');
		$('#playertext').show();
		$('#playertext .rectext').text('REW');
		$('#counter').text('<<<');

		ga('send', 'event', 'Mixtapr', 'Rewind','Rewind Track');

		soundManager.play('rewind',{
			multiShotEvents: true,
			onfinish:function(){

				var url = 'https://api.spotify.com/v1/users/' + username +
			      '/playlists/' + playlistid + '/tracks';

			      var trackurl = 'spotify:track:' + $('.recorded:last').data('spotifyid');
			      var trackpos = [$('.recorded:last').index('.track')];

			    var spotifytracks = {'uri':trackurl,'positions':trackpos,'snapshot_id':latestsnapshot};
			    var logdump = {'tracks':spotifytracks};
			       //console.log(logdump);

			      $.ajax({
			        url: url,
			        type: 'DELETE',
			        data: JSON.stringify(spotifytracks),
			        headers: {
			          'Authorization': 'Bearer ' + accesstoken
			        },
			        success: function(r) {
			          $('#playertext').hide();
			          $('.recorded:last').removeClass('recorded');
			          if($('.recorded').length==0){
			          	$('#rewind').addClass('disabled');
			          }else{
			          	$('#rewind').removeClass('disabled');
			          }


			        },
			        error: function(r) {
			        	//console.log(r);
			        }
			      });

				if($('.full').length!=0){
					$('#record').removeClass('disabled');
				}else{
					$('#finish').addClass('disabled');
				}
			}
		})
		return false;
	});

	$(document).on('click','#finish',function(){

		var totaltracks = $('.recorded').length;

		console.log(totaltracks);

		ga('send', 'event', 'Mixtapr', 'Finish','Total Tracks',totaltracks);


		$('#spotifylink').attr('href',playlisturi);

		var thedata = {};
		thedata['accesstoken'] = accesstoken;
		thedata['playlistid'] = playlistid;
		thedata['playlisturi'] = playlisturi;
		thedata['playlistname'] = playlistname;
		console.log(thedata);
		$.post("savetape.php", thedata).success(function(gotback){
			console.log(gotback);
			$('#tapeoutput img').attr('src',gotback.image);
			$('#screen2').hide();
			$('#screen3').show();
			$('#playlistid').val(playlistid);
			$('#playlisturi').val(playlisturi);
		}).error(function(e){
			console.log(e);
		});

		return false;

	});

	$(document).on('click','#sendimmediately_no',function(){
		$('#when').slideDown();
	});

	$(document).on('click','#sendimmediately_yes',function(){
		$('#when').slideUp();
	});

	jQuery('#whentosend').datetimepicker({minDate: 0,maxDate:'+1970/01/03'});

	$('#send').submit(function(){



		if($('#yourname').val()==""){
			alert("Please enter your name");
			return false;
		}else if($('#youremail').val()==""){
			alert("Please enter your email");
			return false;
		}else if($('#theiremail').val()==""){
			alert("Please enter their email");
			return false;
		}else if($('#theirname').val()==""){
			alert("Please enter their name");
			return false;
		}else{
			$('#send').slideUp();
			$.post( "send.php", $( "#send" ).serialize() ).success(function(response){
				$('#alldone').html("<h1>Thanks for using Mixtapr</h1><p>We've sent your playlist and we've sent you a copy of the email</p><p><a href='" + playlisturi + "' target='_blank'>Click here</a> to open your playlist in Spotify, or listen below</p><p><iframe src='https://embed.spotify.com/?uri=" + playlisturi + "' width='300' height='380' frameborder='0' allowtransparency='true'></iframe></p>").slideDown();
			});

			ga('send', 'event', 'Mixtapr', 'Send',$('#yourname').val() + ' - Tape for ' + $('#theirname').val());
			return false;
		}


	});

});

function setarrows(){
	$('.first').removeClass('first');
	$('.track.full:not(.recorded,.recording):first').addClass('first');
	$('.last').removeClass('last');
	$('.track.full:not(.recorded):last').addClass('last');

	if($('.recorded').length>0){
		$('#rewind').removeClass('disabled');
	}
}

function dosizes(){
	var bottomheight = $('#shelf').outerHeight();
	var topheight = $('#top').outerHeight();


	// if($('#wrapper').height<$(window).height()){
	// 	$('#shelf').addClass('absolute');
	// }else{
	// 	$('#shelf').removeClass('absolute');
	// }
}

$(document).ready(dosizes);
$(window).resize(dosizes);
</script>
</head>
<style>
body {
	background: url('images/bgwall.jpg') top center no-repeat #000;
	background-size: 100% auto;
	margin: 0;
	padding: 0;
	color: #fff;
	font-size: 10pt;
	font-family: "proxima-nova",sans-serif;
	min-height: 100vh;
}

#top {
}

#shelf {
	background: url('images/shelfgradient.jpg') repeat-x center bottom;
	padding-bottom: 218px;
	text-align: center;
	width: 100%;
}

#shelf.absolute {
	position: absolute;
		bottom: 0;
		left: 0;
}

a {
	color: #fff;
}

a:hover {
	color: #eee;
}

#player {
	display: block;
	margin: 0 auto;
	background: url('images/player.png');
	width: 882px;
	height: 265px;
	position: relative;
}

#playertext {
	position: absolute;
	left: 350px;
	top: 45px;
	width: 190px;
	height: 30px;
	font-family: 'open_24_display_stregular';
	color: #fff;
	font-size: 14pt;
	text-align: left;
	line-height: 30px;
	display: none;
}

#playertext .rectext {
	display: block;
	width: auto;
	float: left;
	color: #ff0000;
	margin-right: 10px;
}

#playertext #counter {
	width: 55px;
	float: left;
}

#playertext #tracktext {
	width: 100px;
	overflow: hidden;
	float: left;
	height: 30px;
}

#tape {
	background-image: url('images/tape.gif');
	background-position: top;
	height: 60px;
	width: 165px;
	position: absolute;
	top: 58px;
	left: 66px;
}

#tape.recording {
	background-position: bottom;
}

#screen1 {
	/*display: none;*/
}

#screen1 h1 {
	text-align: center;
}

#screen1 h1 img {
	width: 100%;
	max-width: 766px;
	height: auto;
	margin: 0;
	padding: 0;
}

#introvid {
	float: left;
	margin-right: 10px;
	margin-bottom: 10px;
}

.ninesixty {
	width: 100%;
	max-width: 960px;
	margin: 0 auto;
}

#screen1 {
	background: rgba(0,0,0,0.6);
	max-width: 820px;
	margin: 0 auto;
	width: 90%;
	margin-bottom: 20px;
	padding: 10px;
	border-radius: 20px;
	margin-top: 10px;
}

#spotifyconnect {
	display: inline-block;
	width: 300px;
	height: 18px;
	background: #639006;
	text-decoration: none;
	text-align: center;
	padding-top: 7px;
	padding-bottom: 5px;
	border-radius: 500px;
	-moz-border-radius: 500px;
	-webkit-border-radius: 500px;
	color: #fff;
	text-transform: uppercase;

}

#spotifyconnect:hover {
	background: #83be0b;
}

#introvid {
	border: 1px solid #fff;
}

#screen2 {
	display: none;
}

#screen2 h1 {
	margin: 0;
	padding: 0;
	background: rgba(0,0,0,0.6);
	width: 100%;
	display: none;
}

#screen2 h1 img {
	margin: 5px;
}

#screen2 .ninesixty {
	display: none;
}

#notebook {
	display: block;
	background: url('images/notepadsmaller.png');
	width: 473px;
	height: 562px;
	float: left;
	font-family: 'Rock Salt', cursive;
	position: relative;
	color: #000;
}

#notebook h2 {
	padding: 0;
	margin: 0;
	margin-top: 20px;
	margin-left: 75px;
	font-size: 10pt;
	width: 390px;
	text-align: center;
	height: 34px;
	overflow: hidden;
	text-overflow: ellipsis;
}

#notebook h3 {
	display: block;
	margin: 0;
	padding: 0;
	margin-left: 90px;
	line-height: 32px;
}

#notebook .tracks {
	margin: 0;
	padding: 0;
	margin-left: 90px;
	line-height: 32px;
	font-size: 8pt;

}

#notebook .tracks li {
	line-height: 32px;
	height: 32px;
	text-overflow: ellipsis;
	display: list-item;
 	list-style-position: inside;
 	overflow: hidden;
 	padding: 0;
 	margin: 0;
 	position: relative;
}

#searchbox {
	width: 50%;
	float: left;
	height: 200px;
}

.box {
	background: rgba(0,0,0,0.6);
	width: 100%;
	margin-bottom: 20px;
	padding: 10px;
	border-radius: 20px;
	margin-top: 10px;
	box-sizing: border-box;
	text-align: center;
}

#search {
	display: block;
	text-align: center;
	font-size: 16pt;
	border: 0;
	background: transparent;
	border-bottom: 1px solid #fff;
	font-family: 'Rock Salt', cursive;
	color: #fff;
	text-decoration: none;
	width: 90%;
	outline: none;
	padding: 10px;
	margin: 0 auto;
}

#searchresults {
	display: none;
}

#resultslist {
	background: #000;
	width: 100%;
	max-height: 250px;
	overflow: auto;
}

.trackselect {
	display: block;
	padding: 4px;
	text-align: left;
	clear: both;
	position: relative;
	padding-top: 10px;
	padding-bottom: 10px;
}

.trackselect.even {
	background: #333;
}

.preview {
	display: block;
	border: 1px solid #fff;
	border-radius: 50%;
	padding: 4px;
	transition: 0.2s linear all;
	float: left;
	margin-right: 10px;
	font-size: 10pt;
	text-decoration: none;
	color: #fff;
}

.preview .fa-pause {
	display: none;
}

.preview.playing .fa-pause{ 
	display: block;
}

.preview.playing .fa-play {
	display: none;
}

.preview:hover {
	background: #fff;
	color: #000;
}

.add {
	display: block;
	color: #fff;
	text-decoration: none;
	width: auto;
	padding: 4px;
	position: absolute;
	top: 10px;
	right: 20px;
	transition: 0.2s linear all;
	border-radius: 50%;
	font-size: 14pt;
}

.notetracktitle {
	max-width: 270px;
	text-overflow: ellipsis;
	overflow: hidden;
	clear: both;
	display: block;
	position: absolute;
	left: 20px;
	top: 0;
	width: 0;
	transition: 0.6s linear all;
}

.full .notetracktitle {
	width: 270px;
}

.track.recording {
	color: #ff0000;
}

.track.recorded .notetracktitle {
	text-decoration: line-through;
}

.add:hover {
	background: #fff;
	color: #000;
}

.actions,.status {
	position: absolute;
	right: 20px;
	top: 0;
	display: none;
}

.full .actions {
	display: block;
}

.recording .actions, .recorded .actions {
	display: none;
}

.recording .status {
	display: block;
}

.first .actions .up {
	display: none;
}

.last .actions .down {
	display: none;
}

.actions a {
	display: inline-block;
	width: auto;
	font-size: 12pt;
	color: #000;
	text-decoration: none;
	border-radius: 50%;
	padding: 5px;
}

.actions a:hover {
	background: #000;
	color: #fff;
}

.actions a.minus:hover {
	background: #990000;
}

.actionbuttons {
	display: block;
	margin-top: 4px;
	margin-right: 20px;
	text-align: center;
	width: 380px;
	float: right;
	margin-right: 10px;
}

.actionbuttons a {
	display: inline-block;
	width: auto;
	margin-right: 5px;
	margin-left: 5px;
	border: 2px solid #000;
	font-family: 'Rock Salt', cursive;
	color: #000;
	text-align: center;
	text-decoration: none;
	transition: 0.2s linear opacity;
	opacity: 1;
	padding: 1px;
	padding-left: 10px;
	padding-right: 10px;
}

#record {
	border: 2px solid #990000;
	color: #990000;
}	

.disabled {
	opacity: 0.2 !important;
	cursor: default;
}

#record:hover {
	background: #990000;
	color: #fff;
}

#record.disabled:hover {
	background: none;
	color: #990000;
}

#screen3 {
	display: none;
	background: rgba(0,0,0,0.6);
	max-width: 820px;
	margin: 0 auto;
	width: 90%;
	margin-bottom: 20px;
	padding: 10px;
	border-radius: 20px;
	margin-top: 10px;
}

#tapeoutput {
	width: 450px;
	float: left;
	margin-right: 20px;
	margin-bottom: 10px;
}

#tapeoutput img {
	width: 100%;
	height: auto;
	border: 2px solid #fff;
}

#finalise {
	width: 330px;
	float: left;
}

#finalise h1 {
	margin: 0;
	margin-bottom: 5px;
	padding: 0;
}

#finalise label {
	display: block;
	margin-bottom: 4px;
}

#finalise .inputfield {
	margin-bottom: 15px;
	display: block;
	font-size: 14pt;
	padding: 4px;
	width: 100%;
	border: 0;
	background: rgba(255,255,255,0.8);
}

#submit {
	display: block;
	text-transform: uppercase;
	background: #ffcc00;
	border-radius: 0;
	padding: 5px;
	width: 100%;
	border: 0;
	box-sizing: border-box;
	color: #000;
	font-weight: bold;
	font-size: 14pt;
}

#submit:hover {
	background: #990000;
}

#immediately label {
	width: auto;
	display: inline-block;
	margin-right: 20px;
	padding-bottom: 10px;
}

#when {
	display: none;
}

#alldone {
	display: none;
}

#termsetc {
	font-size: 8pt;
	padding: 20px;
	width: 100%;
	background: #000;
	color: #fff;
	text-align: center;
}

</style>
<body>
<script>
  (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
  (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
  m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
  })(window,document,'script','//www.google-analytics.com/analytics.js','ga');

  ga('create', 'UA-3299015-4', 'auto');
  ga('send', 'pageview');

</script>
<div id="wrapper">
<div id="top">
	<div id="screen1">
		<h1><img src="images/h1.png" alt="Mixtapr: A High Fidelity Inspired Spotify Mixtape Generator" /></h1>

		<iframe id="introvid" width="450" height="253" src="//www.youtube.com/embed/kF5EPoB3KaU?rel=0&amp;controls=0&amp;showinfo=0" frameborder="0" allowfullscreen></iframe>
		
		<p>Making a playlist is too simple.<br /><br />You can just drag 100s of songs across and never have to listen to them.<br /><br />The age of putting some love into a good mixtape has gone.<br /><br />Inspired by Rob Gordon's rules about creating a good mixtape, you can now spend some time putting your tape together... with a 12 track maximum.<br /><br />You must listen to 30 seconds of each track to 'record' it to your playlist.</p>
		<p><a href="#" id="spotifyconnect">Login With Spotify</a>
		
		<div style="clear: both;"></div>

	</div>

	<div id="screen2">
		<h1><img src="images/mixtapr-small.png" alt="Mixtapr" /></h1>
		<div class="ninesixty">
		<div id="searchbox">
			<div class="box">
				<h2>Search Spotify</h2>
				<p>Search for the songs to add to your notebook.<br />Once your notebook is full, hit record.</p>
				<form id="searchform" name="searchform" action="#" method="post"><input type="text" name="search" id="search" /></form>
			</div>

			<div class="box" id="searchresults">
				<p>Add and preview songs below</p>
				<div id="resultslist">
					
				</div>
			</div>
		</div>
		<div id="notebook">
			<h2>Tape for Will</h2>
			<h3>Side 1</h3>
			<ol class="tracks">
				<li id="track1" class="track"><span class="notetracktitle"></span><div class="actions"><a href="#" class="up"><i class="fa fa-arrow-up"></i></a><a href="#" class="down"><i class="fa fa-arrow-down"></i></a><a href="#" class="minus"><i class="fa fa-minus"></i></a></div><div class="status">Recording</div></li>
				<li id="track2"  class="track"><span class="notetracktitle"></span><div class="actions"><a href="#" class="up"><i class="fa fa-arrow-up"></i></a><a href="#" class="down"><i class="fa fa-arrow-down"></i></a><a href="#" class="minus"><i class="fa fa-minus"></i></a></div><div class="status">Recording</div></li>
				<li id="track3"  class="track"><span class="notetracktitle"></span><div class="actions"><a href="#" class="up"><i class="fa fa-arrow-up"></i></a><a href="#" class="down"><i class="fa fa-arrow-down"></i></a><a href="#" class="minus"><i class="fa fa-minus"></i></a></div><div class="status">Recording</div></li>
				<li id="track4"  class="track"><span class="notetracktitle"></span><div class="actions"><a href="#" class="up"><i class="fa fa-arrow-up"></i></a><a href="#" class="down"><i class="fa fa-arrow-down"></i></a><a href="#" class="minus"><i class="fa fa-minus"></i></a></div><div class="status">Recording</div></li>
				<li id="track5"  class="track"><span class="notetracktitle"></span><div class="actions"><a href="#" class="up"><i class="fa fa-arrow-up"></i></a><a href="#" class="down"><i class="fa fa-arrow-down"></i></a><a href="#" class="minus"><i class="fa fa-minus"></i></a></div><div class="status">Recording</div></li>
				<li id="track6"  class="track"><span class="notetracktitle"></span><div class="actions"><a href="#" class="up"><i class="fa fa-arrow-up"></i></a><a href="#" class="down"><i class="fa fa-arrow-down"></i></a><a href="#" class="minus"><i class="fa fa-minus"></i></a></div><div class="status">Recording</div></li>
			</ol>
			<h3>Side 2</h3>
			<ol class="tracks">
				<li id="track7"  class="track"><span class="notetracktitle"></span><div class="actions"><a href="#" class="up"><i class="fa fa-arrow-up"></i></a><a href="#" class="down"><i class="fa fa-arrow-down"></i></a><a href="#" class="minus"><i class="fa fa-minus"></i></a></div><div class="status">Recording</div></li>
				<li id="track8"  class="track"><span class="notetracktitle"></span><div class="actions"><a href="#" class="up"><i class="fa fa-arrow-up"></i></a><a href="#" class="down"><i class="fa fa-arrow-down"></i></a><a href="#" class="minus"><i class="fa fa-minus"></i></a></div><div class="status">Recording</div></li>
				<li id="track9"  class="track"><span class="notetracktitle"></span><div class="actions"><a href="#" class="up"><i class="fa fa-arrow-up"></i></a><a href="#" class="down"><i class="fa fa-arrow-down"></i></a><a href="#" class="minus"><i class="fa fa-minus"></i></a></div><div class="status">Recording</div></li>
				<li id="track10"  class="track"><span class="notetracktitle"></span><div class="actions"><a href="#" class="up"><i class="fa fa-arrow-up"></i></a><a href="#" class="down"><i class="fa fa-arrow-down"></i></a><a href="#" class="minus"><i class="fa fa-minus"></i></a></div><div class="status">Recording</div></li>
				<li id="track11"  class="track"><span class="notetracktitle"></span><div class="actions"><a href="#" class="up"><i class="fa fa-arrow-up"></i></a><a href="#" class="down"><i class="fa fa-arrow-down"></i></a><a href="#" class="minus"><i class="fa fa-minus"></i></a></div><div class="status">Recording</div></li>
				<li id="track12"  class="track"><span class="notetracktitle"></span><div class="actions"><a href="#" class="up"><i class="fa fa-arrow-up"></i></a><a href="#" class="down"><i class="fa fa-arrow-down"></i></a><a href="#" class="minus"><i class="fa fa-minus"></i></a></div><div class="status">Recording</div></li>
			</ol>

			<div class="actionbuttons">
				<a href="#" id="record" class="disabled">Record</a><a href="#" id="rewind" class="disabled">Rewind</a><a href="#" id="finish" class="disabled">Finish &amp; Send</a>
			</div>
		</div>
	</div>
	<div style="clear: both;"></div>
	</div>

	<div id="screen3">
		<div class="ninesixty">

			<div id="tapeoutput">
				<a href="#" id="spotifylink" target="_blank"><img src="images/tapebg.jpg" alt="Blank Tape" /></a>
			</div>

			<div id="finalise">
				<form id="send" name="send" action="send.php" method="post">
				<input type="hidden" name="playlistid" id="playlistid" />
				<input type="hidden" name="playlisturi" id="playlisturi" />
				<h1>Send your tape</h1>
				<label for="yourname">Your Name</label>
				<input type="text" name="yourname" id="yourname" class="inputfield" />
				<label for="yourname">Your Email</label>
				<input type="email" name="youremail" id="youremail" class="inputfield" />
				<label for="theirname">Their Name</label>
				<input type="name" name="theirname" id="theirname" class="inputfield" />
				<label for="theiremail">Their Email</label>
				<input type="email" name="theiremail" id="theiremail" class="inputfield" />
				<label for="message">Message to include</label>
				<textarea name="message" id="message" class="inputfield"></textarea>
				<div id="immediately">
				<input type="radio" name="sendimmediately" value="Yes" id="sendimmediately_yes" checked="checked" /><label for="sendimmediately_yes">Send Immediately</label>
				<input type="radio" name="sendimmediately" value="No" id="sendimmediately_no" /><label for="sendimmediately_no">Delay Send</label>
				</div>
				<div id="when">
					<label for="whentosend">When would you like to send your tape? (Max 3 days in the future)</label>
					<input type="text" name="whentosend" id="whentosend" class="inputfield" />		
				</div>
				<input type="submit" name="submit" id="submit" value="Send" />
				</form>
				<div id="alldone">

				</div>
			</div>
			
		</div>
		<div style="clear: both;"></div>
	</div>


</div>

	<div id="shelf">
		<div id="player">
			<div id="playertext"><div class="rectext"></div><div id="counter">00:00</div><div id="tracktext">Hello</div></div>
			<div id="tape"></div>
		</div>
	</div>
<div id="termsetc">
Developed by <a href="http://twitter.com/willbeardmore">Will Beardmore</a>
</div>
</div>
</body>
</html>	