<?php

/**
*
*  Contains twitter specific configuration settings
*
*/

return array(

        /**  
         *  OAuth settings which can be found in your twitter account
         *  after creating an app. See https://apps.twitter.com
         */      
        'auth'  =>  array(
                        'oauth_access_token'          =>  '',
                        'oauth_access_token_secret'   =>  '',
                        'consumer_key'                =>  '',
                        'consumer_secret'             =>  ''
                    ),
        /**
         *  Specifies the number of tweets to retrieve from a users timeline
         */
        'tweets_to_retrieve'  => 500
);


