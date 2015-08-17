<?
include('config.php');

# Include the Autoloader (see "Libraries" for install instructions)
require 'vendor/autoload.php';
use Mailgun\Mailgun;

 session_start();
 $timezone = $_SESSION['time'];

# Instantiate the client.
$mgClient = new Mailgun($mailgunkey);
$domain = "mixtapr.rocks";


$youremail = $_POST['youremail'];
$yourname = $_POST['yourname'];
$theirname = $_POST['theirname'];
$theiremail = $_POST['theiremail'];
$playlisturi = $_POST['playlisturi'];
$playlistid = $_POST['playlistid'];
$message = $_POST['message'];
$whentosend = $_POST['whentosend'];

$playlisthttp = explode(":",$playlisturi);
$playlisthttp = "http://open.spotify.com/user/" . $playlisthttp['2'] . "/playlist/" . $playlistid;

$tophtml = '
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
         <title>Mixtapr</title>
        <style type="text/css">

#outer_wrapper { width: 600px;
}

#banner { background-color: #264d72;
}

#top_nav {background-color: #264d72;
	height: 39px;
	color: #FFFFFF;
}


@media only screen and (max-width: 480px)  {
	/* Start - Styles for header and page layout*/
	
	html, body {
		   -webkit-text-size-adjust:100%;
		   -ms-text-size-adjust:100%;
		   font-family: Arial, Helvetica, sans-serif;
	 }
			
	*[id=outer_wrapper] { 
		width: 100% !important;
	}
	
	*[id=leadin_txt] {
		text-align: center;
		padding-left: 0 !important;
		display: none !important;
		height: 0 !important;
	}
	
	
	
	*[class=banner_600px] {
		display: none;
	}
	
	*[class=hero_650px] {
		display: none;
	}
	
	
	*[id=top_nav] {
		display: none !important;
		height: 0 !important;
		width: 100%;
		background-color: #264d72;
		font-size: 12px !important;
		padding-top: 2px !important;
		padding-bottom: 2px !important;
		padding-left: 5px !important;
		padding-right: 5px !important;
		color: #FFF;
	}
	
	/* END - Styles for header and page layout*/
	
	/* START - Styles for news story and news blurbs*/
	*[class=picture] {
		display:none !important;
		width:0 !important;
	}
	
	*[class=content] {
		padding-top: 3% !important;
		padding-left: 0 !important;
		font-size: 1em !important;
	}
	
	*[class=headline] {
		padding-left: 2% !important;
		padding-right: 2% !important;
		padding-bottom: 2%  !important;
	}
	
	*[class=headline_newsblurb] {
		padding-left: 2% !important;
		padding-right: 2% !important;
		padding-bottom: 2%  !important;
		text-align: center !important;
	}
	
	*[class=text] {
		padding-left: 3% !important;
		padding-right: 3% !important;
	}
	
	
	*[class=hero_picture_rd] {
		width: 100%!important;
		height: auto !important;

	}

	*[class=video_image1] {
		width: 280px!important;
		height: auto !important;

	}
	
	*[class=merch_container] {
	width: 320px !important;
	margin: 0 auto !important;
	padding-left: 20px !important;
	mso-table-lspace:8pt !important; 
	mso-table-rspace:8pt !important;
	}
	  
}
</style>
    </head>
     <body bgcolor="#264d72" style="font-family: Arial, Helvetica, sans-serif;">
<TABLE height="100%" width="100%" bgColor=#264d72>  <TR>    <TD>
        <table width="650" border="0"  align="center" cellpadding="0" cellspacing="0" id="outer_wrapper" style="width: 650px;">
            <!-- start outer table -->
            <tbody>
                <tr>
                    <td width="650" style="color: #fff; font-family: Helvetica; Sans-Serif; text-align: center; font-size: 10pt;">
                    <div style="width: 100%; margin: 0 auto; max-width: 500px; padding: 10px; color: #fff; text-align: center;">
';

$middlehtml = '
                    <strong style="font-size: 16pt; text-align: center; display: block; text-transform: uppercase;">Hey ' . htmlspecialchars($theirname) . '!</strong><br />
                    Your friend ' .htmlspecialchars($yourname).' has put you together a Spotify playlist using Mixtapr.<br />
                    </div>
                   	
                    <table width="100%" border="0" bgcolor="#FFF" align="center" cellspacing="0" cellpadding="0" style="background-color:#FFF;">
                        <!-- start content table -->
                        <tbody>
                        		
                              <tr bgcolor="#264d72">
                                <!-- start header table row -->
                                <td align="center"><a href="' . $playlisthttp . '" target="_blank"><img width="650" border="0" class="hero_picture_rd" src="'. $baseurl . '/images/tapes/' .$playlistid . '.jpg" alt="Tape For ' . htmlspecialchars($theirname) . '" /></a>
                                </td>
                            </tr>
 
                          
                             <tr>
                                <!-- start legal row -->
                                <td bgcolor="#264d72" align="center" style="color: #fff; font-family: Helvetica; Sans-Serif; text-align: center; font-size: 10pt;">
                                <div style="margin: 10px auto; width: 100%; max-width: 500px; color: #fff;">
                                <p style="color: #fff;">

                    Now, before you go underestimating ' . htmlspecialchars($yourname) . ', they\'ve put some real effort into this one!<br /><br />In order to compile your playlist, ' . htmlspecialchars($yourname) . '\'s had to listen to every song they\'ve put on there, painstakingly considering the order and what makes the cut.<br /><br />
      ';
      if($message){
      	$middlehtml .= 'Here\'s a little message from '.htmlspecialchars($yourname).' to go with your playlist:<br /><br /><strong>"';
      	$middlehtml .= htmlspecialchars($message);
      	$middlehtml .= '"</strong>';
      }

      $bottomhtml = '</p></div>
                    <a href="' . $playlisthttp . '" style="display: block; width: 80%; background: #fff; color: #000; padding-top: 10px; padding-bottom: 10px; text-align: center; text-transform: uppercase; margin: 20px auto; font-weight: bold; text-decoration: none; font-size: 14pt;">Click here to listen to your playlist</a>

                    <a href="http://mixtapr.rocks" style="display: block; width: 80%; background: #fff; color: #000; padding-top: 10px; padding-bottom: 10px; text-align: center; text-transform: uppercase; margin: 20px auto; font-weight: bold; text-decoration: none; font-size: 14pt;">Create your own Mixtape playlist on Mixtapr</a>
<hr style="border-bottom: 1px solid #fff; width: 80%; margin: 20px auto;"/>

              <p style="font-family: Arial, Helvetica, sans-serif; font-size: 10px; color: #ffffff; line-height:15px"><br>This email was sent to: ' . $theiremail . '<br>
 <br>
 This email was sent via:<br>
 <a href="http://mixtapr.rocks" target="_blank" style="color: #fff;">Mixtapr.rocks</a><br>
 <br>
<!--  We respect your right to privacy - <a name="privacy_policy" class="legal" style="color:#FFFFFF" href="http://www.wminewmedia.com/privacy/" target="_blank">view our policy</a><br>
 <a name="manage_subscriptions" class="legal" style="color:#FFFFFF" href="%%subscription_center_url%%" target="_blank">Manage Subscriptions</a> | <a name="update_profile" class="legal" style="color:#FFFFFF" href="%%profile_center_url%%" target="_blank">Update Profile</a> | <a name="unsubscribe" class="legal" style="color:#FFFFFF" href="%%unsub_center_url%%" target="_blank">One-Click Unsubscribe</a></p>
                              -->   
                                </td>
                            </tr>
                            <!-- end legal row -->
                        </tbody>
                    </table>
                    </td>
                </tr>

            </tbody>
        </table>
     </td>
</tr>
</table>
     </body>
</html>
';

$email = $tophtml . $middlehtml . $bottomhtml;


# Next, instantiate a Message Builder object from the SDK.
$msgBldr = $mgClient->MessageBuilder();

# Define the from address.
$msgBldr->setFromAddress("tape@mixtapr.rocks", 
                          array("first"=>$yourname, "last" => "via Mixtapr"));
# Define a to recipient. 
$msgBldr->addToRecipient($theiremail, 
                          array("first" => $theirname));

$msgBldr->addCcRecipient($youremail, 
                          array("first" => $yourname));

if($_POST['sendimmediately']=="no"){
	$msgBldr->setDeliveryTime($whentosend, $timezone);
}

# Define the subject. 
$msgBldr->setSubject("Hey " . $theirname . "! " . $yourname . " Has made you a playlist.");
# Define the body of the message. 
$msgBldr->setHtmlBody($email);



# Finally, send the message. 
$mgClient->post('mixtapr.rocks/messages', $msgBldr->getMessage());

echo json_encode(array("Status"=>"Success"));

//echo $email;

//header("Location: /");
?>