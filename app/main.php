<?php

# Load twitter interface library
require('../lib/TwitterAPIExchange.php');

# Load and store configuration  
$config = require('../conf/twitter.php');

# Get OAuth from config settings
$auth = $config['auth'];

/**
* Checks if an error has been returned from the twitter api
*/

function request_error_exists( $data ) {

  $errors = isset( $data->errors );
  $page_not_found_code = 34; //If we cannot find the page we assume user does not exist

  if( $errors && $data->errors[0]->code == $page_not_found_code ){
    throw new Exception('Screen name not found');
  } elseif ( $errors ) {
    throw new Exception('There was a problem completing the request. '.$data->errors[0]->message);
  }

}

try {

  # Sanitise input
  $sanitise_pattern = '/[^A-Za-z0-9_]/';
  $screen_name = preg_replace($sanitise_pattern,'',$_REQUEST['screen_name']);
  
  # Create request uri to send to twitter api.
  $api_url = $config['base_url'] . "/statuses/user_timeline.json";
  $request_uri = "?screen_name=" . $screen_name . "&count=" . $config['tweets_to_retrieve'];

  # Launch request to twitter
  $twitter = new TwitterAPIExchange($auth);
  $response = $twitter->setGetfield( $request_uri )
                      ->buildOauth( $api_url, 'GET' )
                      ->performRequest(); 

  $data = json_decode( $response );
  
  # Check if reponse contains errors
  request_error_exists( $data );

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

  exit ( json_encode( array( 'status' => false, 'payload' => 'Error: '.$e->getMessage() ) ) );

}
