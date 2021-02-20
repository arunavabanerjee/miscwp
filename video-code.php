<?php
/**
 * Template Name: Video Call Provider Page
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package Doccure
 * @since 1.0
 */

get_header();
/*if(!isset($_GET['bkid'])){ 
  echo '<meta http-equiv="refresh" content="0;url='.get_bloginfo('url').'"/>';
}
if(empty($_GET['bkid'])){ 
  echo '<meta http-equiv="refresh" content="0;url='.get_bloginfo('url').'"/>';
}*/
if( isset($_GET['cid']) && !empty($_GET['cid']) ){ 
  $booking_customer_link = '';    
  $customerId = $_GET['cid']; $customer = get_user_by('ID', $customerId);
  //check if query sring match the db 
  if(isset($_GET['bkid'])){
    $bookingId = $_GET['bkid']; 
    $booking_customer_link = get_post_meta($bookingId, 'doccure_customer_meeting_link', true); 
  }    
  /*if(!strstr($booking_customer_link, $_SERVER['QUERY_STRING'])){ 
     //echo '<p style="text-align: center;background: #CCC;padding: 20px;font-size: 18px;color: #000;">';
     //echo 'Meeting Link Has Been Rescheduled. Please check email.</p>';
     //echo '<meta http-equiv="refresh" content="3;url='.get_bloginfo('url').'"/>'; 
  }*/
}elseif( isset($_GET['pid']) && !empty($_GET['pid']) ){ 
  $booking_provider_link = '';    
  $providerId = $_GET['pid']; $provider = get_user_by('ID', $providerId); 
  //check if query sring match the db 
  if(isset($_GET['bkid'])){ 
    $bookingId = $_GET['bkid']; 
    $booking_provider_link = get_post_meta($bookingId, 'doccure_provider_meeting_link', true); 
  }    
  /*if(!strstr($booking_provider_link, $_SERVER['QUERY_STRING'])){ 
     //echo '<p style="text-align: center;background: #CCC;padding: 20px;font-size: 18px;color: #000;">';
     //echo 'Meeting Link Has Been Rescheduled. Please check email.</p>';
     //echo '<meta http-equiv="refresh" content="3;url='.get_bloginfo('url').'"/>'; 
  }*/
}else{
  echo '<meta http-equiv="refresh" content="0;url='.get_bloginfo('url').'"/>';
}//var_dump($_SERVER); 
/* //check time 
if(isset($_GET['t']) && !empty($_GET['t'])){
   $meetingTimestamp = $_GET['t']; 
   $meetingTimestamp15MinsBefore = $meetingTimestamp - (15*60); 
   $meetingTimestamp5HrsHence = $meetingTimestamp + (5*60*60); 
   $currentTimestamp = time();
   if($currentTimestamp < $meetingTimestamp15MinsBefore){ 
     echo '<p style="text-align: center;background: #CCC;padding: 20px;font-size: 18px;color: #000;">';
     echo 'Meeting has not started yet. Please check back later.</p>';
     //echo '<meta http-equiv="refresh" content="3;url='.get_bloginfo('url').'"/>'; 
   }elseif($currentTimestamp > $meetingTimestamp5HrsHence){ 
     echo '<p style="text-align: center;background: #CCC;padding: 20px;font-size: 18px;color: #000;">';
     echo 'Meeting Link Has Expired</p>';
     echo '<meta http-equiv="refresh" content="3;url='.get_bloginfo('url').'"/>'; 
   }
}else{ 
  echo '<meta http-equiv="refresh" content="0;url='.get_bloginfo('url').'"/>';
}*/ 

require_once __DIR__ . '/../vendor/twilio-php-main/src/Twilio/autoload.php';
/*use \Twilio\Rest\Client;
$acctnSID  = "ACd0be536740bbb939494ab5713ee3b2b7";
$acctnTok  = "2a53ddf7e858bd9821d3c3dcbd7d7c7f";
$conversation_resource = "CHbff3357ff51646b78a5040e3cea1c70c"; 
$main_phno = "+14043415407";*/ 

use Twilio\Jwt\AccessToken;
use Twilio\Jwt\Grants\VideoGrant;

// Required for all Twilio access tokens
//$twilioAccountSid = 'ACd0be536740bbb939494ab5713ee3b2b7';
//$twilioApiKey = 'SK09f2468fba633f28759f07166dd986a4';
//$twilioApiSecret = 'if0yKQgp19t5OHPVYi8ZkpxdHmI4tMFM'; 

// Required for all Twilio access tokens
$twilioAccountSid = get_option('twilio_acctn_sid'); 
$twilioApiKey = get_option('twilio_apikey_videoservice'); 
$twilioApiSecret = get_option('twilio_apisecret_videoservice'); 

$identity = ''; //$identity="alice";
if(isset($customer)){ $identity = $customer->user_login; }
if(isset($provider)){ $identity = $provider->user_login; }

// The specific Room we'll allow the user to access
$roomName = '';
if( isset($bookingId) && !empty($bookingId) ){
  $bookingTitle = get_the_title($bookingId); 
  $modbookingTitle = str_replace(' ', '', $bookingTitle);
  $modbookingTitle = str_replace('-', '', $modbookingTitle);
  $roomName = $modbookingTitle.'-'.$bookingId;
}else{ $roomName = 'DailyStandup'; }

// Create access token, which we will serialize and send to the client
$token = new AccessToken($twilioAccountSid, $twilioApiKey, $twilioApiSecret, 3600, $identity);

// Create Video grant
$videoGrant = new VideoGrant();
$videoGrant->setRoom($roomName);

// Add grant to token
$token->addGrant($videoGrant);

?>
			
<!-- Page Content -->
<div class="content">
<div class="container-fluid">

				
<!-- Call Wrapper -->
<div class="call-wrapper">
<div class="call-main-row">
<div class="call-main-wrapper">
<div class="call-view">
<div class="call-window"> 


<?php 
//global $acctnSID, $acctnTok; 
//$twilio = new Client($acctnSID, $acctnTok);
//$room = $twilio->video->v1->rooms->create(["uniqueName" => "DailyStandup"]);
//var_dump($room); 
?>
									
<!-- Call Header -->
<div class="fixed-header">
<div class="navbar">
	<div class="user-details">
	<?php $username = ''; 
	  if(isset($customer) && !empty($customer)){ $username = $customer->display_name; }
	  elseif(isset($provider) && !empty($provider)){ $username = $provider->display_name; } ?>
	<div class="float-left user-img">
		<a class="avatar avatar-sm mr-2" href="javascript:void(0);" title="<?php echo $username; ?>">
		  <?php /*
		  <img src="<?php echo get_stylesheet_directory_uri(); ?>/assets/img/patients/patient1.jpg" alt="User Image" class="rounded-circle"> */ ?>
		  <span class="status online"></span>
		</a>
	</div>
	<div class="user-info float-left">
		<a href="javascript:void(0);"><span><?php echo $username; ?></span></a><span class="last-seen">Online</span>
	</div>
	</div><!-- /user-details -->
												
	<ul class="nav float-right custom-menu">
		<li class="nav-item dropdown dropdown-action">
			<a href="#" class="nav-link dropdown-toggle" data-toggle="dropdown" aria-expanded="false"><i class="fa fa-cog"></i></a>
			<div class="dropdown-menu dropdown-menu-right"><a href="javascript:void(0)" class="dropdown-item">Settings</a></div>
		</li>
	</ul>
</div>
</div>
<!-- /Call Header -->
										
<!-- Call Contents -->
<div class="call-contents">
<div class="call-content-wrap">
	<!--<div class="user-video"><img src="<?php //echo get_stylesheet_directory_uri(); ?>/assets/img/video-call.jpg" alt="User Image"></div>--> 
	<!--<div class="col-md-6 col-lg-6">-->
	<!--<div class="user-video" id="local-media" style="width:42%; float:left; background-color:#DCDCDC;height:100%; margin:0 10px;position:relative;">
	</div>-->
	<!--</div>-->
	<!--<div class="my-video">
	<ul><li>
	<img src="<?php //echo get_stylesheet_directory_uri(); ?>/assets/img/patients/patient1.jpg" class="img-fluid" alt="User Image">
	</li></ul>
	</div>-->
	<!--<div class="col-md-6 col-lg-6">-->
	<!--<div id="remote-media" style="width:50%; background-color:#ECECEC;float:right;height:100%;margin:0 10px;"></div>-->
	<!--</div>-->

	<div class="user-video" id="remote-media" style="width:100%;background-color:#ECECEC;"></div>
	<div class="my-video" id="local-media" style="width:100px;height:34%;left:20px;border:1px solid #dadada;background-color:#DCDCDC;"></div>
</div>
</div>
<!-- Call Contents -->
																				
<!-- Call Footer -->
<div class="call-footer">
	<div class="call-icons"><!--<span class="call-duration">00:59</span>-->
	<ul class="call-items" id="room-controls">
	<li class="call-item">
	    <!--<a href="" title="Enable Video" data-placement="top" data-toggle="tooltip"><i class="fas fa-video camera"></i></a>-->
	    <button id="button-preview" title="Enable Video" data-placement="top" data-toggle="tooltip"><i class="fas fa-video camera"></i></button>
	</li>
	<li class="call-item">
	    <!--<a href="" title="Enable Video" data-placement="top" data-toggle="tooltip"><i class="fas fa-video camera"></i></a>-->
	    <button id="button-join" title="Join Room/Call" data-placement="top" data-toggle="tooltip"><i class="fa fa-user-plus"></i></button>
	</li>
	<li class="call-item">
	    <!--<a href="" title="Mute Audio" data-placement="top" data-toggle="tooltip"><i class="fa fa-microphone microphone"></i></a>-->
	    <button id="mute-audio" title="Mute/Unmute Audio" data-placement="top" data-toggle="tooltip" style="display:none;"><i class="fa fa-microphone microphone"></i></button>
	</li>
	<li class="call-item">
	    <!--<a href="" title="Mute Audio" data-placement="top" data-toggle="tooltip"><i class="fa fa-microphone microphone"></i></a>-->
	    <button id="disable-video" title="Enable/Disable Video" data-placement="top" data-toggle="tooltip" style="display:none;"><i class="fa fa-video camera"></i></button>
	</li>
	<!--<li class="call-item">
		<a href="" title="Add User" data-placement="top" data-toggle="tooltip"><i class="fa fa-user-plus"></i></a>
	</li>-->
	<li class="call-item">
	    <!--<a href="" title="Full Screen" data-placement="top" data-toggle="tooltip"><i class="fas fa-arrows-alt-v full-screen"></i></a>-->
	    <button id="button-fullscreen" title="Go Full Screen" data-placement="top" data-toggle="tooltip" style="display:none;"><i class="fas fa-arrows-alt-v full-screen"></i></button>
	</li>
	</ul>
	<div class="end-call" style="top:-2px;">
	    <!--<a href="javascript:void(0);"><i class="material-icons">call_end</i></a>-->
    	<button id="button-leave" title="Leave Room/End Call" data-placement="top" data-toggle="tooltip"><i class="material-icons" style="font-size:17px;">call_end</i></button>
	</div>
	</div>
<div id="log" style="margin:20px auto;width:40%;height:40px;background:#f7f7f7;overflow-y:scroll;">Twilio Access Log:</div>									
</div>
<!-- /Call Footer -->
										
</div>
</div>								
</div>
</div>
</div>
<!-- /Call Wrapper -->
					
</div>
</div>		
<!-- /Page Content -->

<script type="text/javascript">

var activeRoom;
var previewTracks;
var identity;
var roomName;

// Attach the Tracks to the DOM.
function attachTracks(tracks, container) {
  tracks.forEach(function(track) {
    container.appendChild(track.attach());
  });
}

// Attach the Participant's Tracks to the DOM.
function attachParticipantTracks(participant, container) {
  var tracks = Array.from(participant.tracks.values());
  attachTracks(tracks, container);
}

// Detach the Tracks from the DOM.
function detachTracks(tracks) {
  tracks.forEach(function(track) {
    track.detach().forEach(function(detachedElement) {
      detachedElement.remove();
    });
  });
}

// Detach the Participant's Tracks from the DOM.
function detachParticipantTracks(participant) {
  var tracks = Array.from(participant.tracks.values());
  detachTracks(tracks);
}

// When we are about to transition away from this page, disconnect
// from the room, if joined.
window.addEventListener('beforeunload', leaveRoomIfJoined);
identity = '<?php echo $identity;?>';
document.getElementById('room-controls').style.display = 'block'; 
document.getElementById('mute-audio').style.display = 'none';
document.getElementById('disable-video').style.display = 'none';  
document.getElementById('button-fullscreen').style.display = 'none';  

// Bind button to join Room.
document.getElementById('button-join').onclick = function() {
    roomName = ("<?php echo $roomName; ?>");
    if (!roomName) {  alert('Please enter a room name.'); return; }
    log("Joining room '" + roomName + "'...");
    var connectOptions = { name: roomName, logLevel: 'debug' };

    if (previewTracks) {
      connectOptions.tracks = previewTracks;
    }

    // Join the Room with the token from the server and the
    // LocalParticipant's Tracks.
    Twilio.Video.connect("<?php echo $token; ?>", connectOptions).then(roomJoined, function(error) {
      log('Could not connect to Twilio: ' + error.message);
    });
};

// Bind button to leave Room.
document.getElementById('button-leave').onclick = function() {
    log('Leaving room...');
    activeRoom.disconnect();
};


// Successfully connected!
function roomJoined(room) {
  window.room = activeRoom = room; 
  var callContents = document.getElementsByClassName('call-contents')[0];
  //var uservideo = document.getElementsByClassName('user-video')[0]; 
  var uservideo = document.getElementById('local-media');	
  var remotemedia = document.getElementById('remote-media');

  log("Joined as '" + identity + "'"); 
  document.getElementById('button-preview').style.display = 'none'; 
  document.getElementById('button-join').style.display = 'none'; 
  document.getElementById('mute-audio').style.display = 'inline';
  document.getElementById('disable-video').style.display = 'inline';  
  document.getElementById('button-leave').style.display = 'inline';
  document.getElementById('button-fullscreen').style.display = 'inline';  
  //callContents.style.display = 'block'; callContents.style.overflow = 'hidden'; 
  //callContents.style.height = 'auto'; uservideo.style.height = 'auto'; 
  //remotemedia.style.height = 'auto';
  
  // Attach LocalParticipant's Tracks, if not already attached.
  var previewContainer = document.getElementById('local-media');
  if (!previewContainer.querySelector('video')) {
    attachParticipantTracks(room.localParticipant, previewContainer);
  }

  // Attach the Tracks of the Room's Participants.
  room.participants.forEach(function(participant) {
    log("Already in Room: '" + participant.identity + "'");
    var previewContainer = document.getElementById('remote-media');
    attachParticipantTracks(participant, previewContainer);
  });

  // When a Participant joins the Room, log the event.
  room.on('participantConnected', function(participant) {
    log("Joining: '" + participant.identity + "'");
  });

  // When a Participant adds a Track, attach it to the DOM.
  room.on('trackAdded', function(track, participant) {
    log(participant.identity + " added track: " + track.kind);
    var previewContainer = document.getElementById('remote-media');
    attachTracks([track], previewContainer);
  });

  // When a Participant removes a Track, detach it from the DOM.
  room.on('trackRemoved', function(track, participant) {
    log(participant.identity + " removed track: " + track.kind);
    detachTracks([track]);
  });

  // When a Participant leaves the Room, detach its Tracks.
  room.on('participantDisconnected', function(participant) {
    log("Participant '" + participant.identity + "' left the room");
    detachParticipantTracks(participant);
  });

  // Once the LocalParticipant leaves the room, detach the Tracks
  // of all Participants, including that of the LocalParticipant.
  room.on('disconnected', function() {
    log('Left');
    if (previewTracks) {
      previewTracks.forEach(function(track) {
        track.stop();
      });
    }
    detachParticipantTracks(room.localParticipant);
    room.participants.forEach(detachParticipantTracks);
    activeRoom = null;
    document.getElementById('button-preview').style.display = 'inline';
    document.getElementById('button-join').style.display = 'inline'; 
    document.getElementById('button-leave').style.display = 'none';
    document.getElementById('mute-audio').style.display = 'none';
    document.getElementById('disable-video').style.display = 'none'; 
    document.getElementById('button-fullscreen').style.display = 'none';  
    //callContents.style.display = 'table-row'; callContents.style.height = '100%';
    window.location.reload();
  });
}

// Preview LocalParticipant's Tracks.
document.getElementById('button-preview').onclick = function() {
  var localTracksPromise = previewTracks ? Promise.resolve(previewTracks) : Twilio.Video.createLocalTracks(); 
  localTracksPromise.then( function(tracks) {
    window.previewTracks = previewTracks = tracks;
    var previewContainer = document.getElementById('local-media');	
    if (!previewContainer.querySelector('video')) {
      attachTracks(tracks, previewContainer);
    }
  }, function(error) {
    console.error('Unable to access local media', error);
    log('Unable to access Camera and Microphone');
  });
};

document.getElementById('mute-audio').onclick = function(){
    var localParticipant = activeRoom.localParticipant;
    localParticipant.audioTracks.forEach(function (audioTrack) {
        if ( audioTrack.isEnabled == true ) { 
	   audioTrack.disable();
	   log(localParticipant.identity + " muted track: " + audioTrack.kind); 
	   let btnElement = document.getElementById('mute-audio'); 
	   btnElement.style.color='#F00';
        } else { 
	   audioTrack.enable(); 
	   log(localParticipant.identity + " enabled track: " + audioTrack.kind);
	   let btnElement = document.getElementById('mute-audio'); 
	   btnElement.style.color='#000';
	}
    }); 
};

document.getElementById('disable-video').onclick = function(){
    var localParticipant = activeRoom.localParticipant;
    localParticipant.videoTracks.forEach(function (videoTrack) {
        if ( videoTrack.isEnabled == true ) { 
	   videoTrack.disable();
	   log(localParticipant.identity + " muted track: " + videoTrack.kind);
	   let btnElement = document.getElementById('disable-video'); 
	   btnElement.style.color='#F00';
        } else { 
	   videoTrack.enable(); 
	   log(localParticipant.identity + " enabled track: " + videoTrack.kind);
	   let btnElement = document.getElementById('disable-video'); 
	   btnElement.style.color='#000';
	}
    }); 
};

// Select the node that will be observed for mutations
const localmediatargetNode = document.getElementById('local-media');
const config = { childList: true };
const callback = function(mutationsList, observer) {
  /*for(const mutation of mutationsList) {
    if (mutation.type === 'childList') { console.log(mutation); }
  }*/
  //console.log(localmediatargetNode);
  for (let i = 0; i < localmediatargetNode.children.length; i++) { 
     var localmediatargetNodechild = localmediatargetNode.children[i]; 
     //console.log(localmediatargetNodechild); console.log(localmediatargetNodechild.tagName);
     if(localmediatargetNodechild.tagName == 'VIDEO'){ //set styles for uservideo 
	console.log(localmediatargetNodechild.tagName);
  	localmediatargetNodechild.style.width = '100px'; 
	localmediatargetNodechild.style.height = '100px'; 
     }	
  }
};
const localmediaobserver = new MutationObserver(callback);
localmediaobserver.observe(localmediatargetNode, config);

// Select the node that will be observed for mutations
const remotemediatargetNode = document.getElementById('remote-media');
const remoteconfig = { childList: true };
const remotecallback = function(mutationsList, observer) {
  /*for(const mutation of mutationsList) {
    if (mutation.type === 'childList') { console.log(mutation); }
  }*/
  //console.log(localmediatargetNode);
  for (let i = 0; i < remotemediatargetNode.children.length; i++) { 
     var remotemediatargetNodechild = remotemediatargetNode.children[i]; 
     //console.log(localmediatargetNodechild); console.log(localmediatargetNodechild.tagName);
     if(remotemediatargetNodechild.tagName == 'VIDEO'){ //set styles for uservideo 
	console.log(remotemediatargetNodechild.tagName);
  	remotemediatargetNodechild.style.width = '100%'; 
	remotemediatargetNodechild.style.height = '100%'; 
     }	
  }
};
const remotemediaobserver = new MutationObserver(remotecallback);
remotemediaobserver.observe(remotemediatargetNode, remoteconfig);


/*document.getElementById('button-fullscreen').onclick = function(){ 
    var fullScrDiv = document.getElementById('button-fullscreen'); 
    var uservideo = document.getElementsByClassName('user-video')[0]; 
    var remotemedia = document.getElementById('remote-media');
    if(fullScrDiv.classList.contains('fullscr')){ 
	fullScrDiv.classList.remove('fullscr'); 
	uservideo.style.width = '50%'; 
	uservideo.style.float = 'left';	
	uservideo.style.position = 'relative';
	uservideo.style.height = 'auto';
	uservideo.style.overflow = 'auto';
	remotemedia.style.width = '45%';
    }else{ 
	fullScrDiv.classList.add('fullscr'); 
	uservideo.style.width = '15%'; 
	uservideo.style.float = 'left';
	uservideo.style.position = 'absolute';
	uservideo.style.height = '30%';
	uservideo.style.overflow = 'hidden';		
	remotemedia.style.width = '95%';
    }
}; */

document.getElementById('button-fullscreen').onclick = function(){ 
    var callcontentWrap = document.getElementsByClassName('call-content-wrap')[0]; 
	if (callcontentWrap.requestFullscreen) {
    		callcontentWrap.requestFullscreen();
  	} else if (callcontentWrap.webkitRequestFullscreen) { /* Safari */
    		callcontentWrap.webkitRequestFullscreen();
  	} else if (callcontentWrap.msRequestFullscreen) { /* IE11 */
    		callcontentWrap.msRequestFullscreen();
  	}
};

document.addEventListener("fullscreenchange", onFullScreenChange, false);
document.addEventListener("webkitfullscreenchange", onFullScreenChange, false);
document.addEventListener("mozfullscreenchange", onFullScreenChange, false); 
function onFullScreenChange(){ //console.log(window);
  //if( window.innerHeight == window.screen.height) { // browser is fullscreen 
  //console.log(window.outerHeight + ' ' + window.screen.height); 
  //if( window.outerHeight == window.screen.height) { // browser is fullscreen 
  
  var fullscreenElement = document.fullscreenElement || document.mozFullScreenElement || document.webkitFullscreenElement || document.msFullscreenElement; 
  console.log(fullscreenElement);
  
  if(typeof fullscreenElement === "undefined") {
      console.log('Screen Changed To Normal Mode');
      var fullScrDiv = document.getElementById('button-fullscreen'); 
      //var uservideo = document.getElementsByClassName('user-video')[0]; 
      var uservideo = document.getElementById('local-media');	
      var remotemedia = document.getElementById('remote-media');
      var callcontentWrap = document.getElementsByClassName('call-content-wrap')[0]; 

      //change the styles accordingly. 
      uservideo.style.width = '42%'; 
      uservideo.style.float = 'left';	
      uservideo.style.position = 'relative';
      uservideo.style.height = 'auto';
      uservideo.style.overflow = 'auto';
      //local media 
      //localmedia.style.width = '80%'; 
      //localmedia.style.height = '430px'; 
      //remote media
      remotemedia.style.width = '50%'; 
      remotemedia.style.height = 'auto'; 
  }      

  if (fullscreenElement != null) {
      console.log('Screen Changed To Full Screen Mode'); 
      var fullScrDiv = document.getElementById('button-fullscreen'); 
      var uservideo = document.getElementsByClassName('user-video')[0]; 
      //var localmedia = document.getElementById('local-media');	
      var remotemedia = document.getElementById('remote-media');
      var callcontentWrap = document.getElementsByClassName('call-content-wrap')[0]; 
      var remotevideo = document.getElementById('remote-media').childNodes; var i;

      //change the styles accordingly.
      uservideo.style.width = '20%'; 
      uservideo.style.float = 'left';
      uservideo.style.position = 'absolute';
      uservideo.style.height = '22%';
      uservideo.style.overflow = 'hidden'; 
      //local media 
      //localmedia.style.width = '100%'; 
      //localmedia.style.height = 'auto'; 
      //remote media		
      remotemedia.style.width = '98%'; 
      remotemedia.style.height = '100%'; 
      for (i = 0; i < remotevideo.length; i++) {
        //console.log( remotevideo[i].nodeName ); 
        if( remotevideo[i].nodeName == 'VIDEO'){ 
          remotevideo[i].style.width = '100%'; 
          //remotevideo[i].style.object-fit = 'cover';
        }
      }
  }
}

/*document.getElementById('mute-audio').onclick = function(){
    var localParticipant = activeRoom.localParticipant;
    localParticipant.audioTracks.forEach(function (audioTrack) {
        audioTrack.track.disable();
    });
}*/

// Activity log.
function log(message) {
  var logDiv = document.getElementById('log');
  logDiv.innerHTML += '<p>&gt;&nbsp;' + message + '</p>';
  logDiv.scrollTop = logDiv.scrollHeight;
}

// Leave Room.
function leaveRoomIfJoined() {
  if (activeRoom) {
    activeRoom.disconnect(); 
    localmediaobserver.disconnect();
    remotemediaobserver.disconnect();
  }
}
</script>

<?php
get_footer();
