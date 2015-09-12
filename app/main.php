<?php

# Load twitter interface library
require('../lib/TwitterAPIExchange.php');

# Load and store configuration  
$config = require('../conf/twitter.php');

# Get OAuth from config settings
$auth = $config['auth'];

/**
* Checks if a screen name exists on the twitter platform
* Exits if name does not exist 
*/
function check_screen_name_exists( $name ) {
  //TODO
  $name_found = true;
  if( $name_found === false ) {
    exit( json_encode( array( 'status' => false , 'payload' => 'Screen name does not exist') ) );
  }
}

/**
* Checks if an error has been returned from the twitter api
* Returns error message or False
*/

function request_error_exists( $data ) {
  // TODO
  if( isset( $data->errors ) ){
    return $data->errors[0]->message;
  }
  return false;
}

# Sanitise input
$sanitise_pattern = '/[^A-Za-z0-9_]/';
$screen_name = preg_replace($sanitise_pattern,'',$_REQUEST['screen_name']);

# Check if screen name exists
check_screen_name_exists( $screen_name );

# Create request uri to send to twitter api.
$api_base_url = $config['base_url'] . "/statuses/user_timeline.json";
$request_uri = "?screen_name=" . $screen_name . "&count=" . $config['tweets_to_retrieve'];

try {

  # Launch request to twitter
  $twitter = new TwitterAPIExchange($auth);
  $response = $twitter->setGetfield( $request_uri )
                      ->buildOauth( $api_base_url, 'GET' )
                      ->performRequest(); 

  $data = json_decode( $response );
  
  # Check if reponse contains errors
  $error = request_error_exists( $data );
  if( $error !== false ) {

    exit ( json_encode( array( 'status' => false, 'payload' => $error ) ) );

  }

  # Transform data into histogram input
  
  $hour_start_pos = 11; // string position of hour digits
  $hour_length = 2; // 24 hour time so hour is represented in 2 digits
  $histogram_data = array( array("Tweet","Hour Posted") );

  foreach( $data as $tweet ) {

    $hour_created = substr( $tweet->created_at, $hour_start_pos, $hour_length );
    $text = $tweet->text;
    array_push( $histogram_data, array( $text, intval( $hour_created ) ) );

  }

  # Success, return data to browser
  exit ( json_encode( array( 'status' => true, 'payload' => $histogram_data) ) );
  
} catch ( Exception $e ) {

  exit ( json_encode( array( 'status' => false, 'payload' => 'Something went wrong while trying to contact twitter. '.$e->getMessage() ) ) );

}
