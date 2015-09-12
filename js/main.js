console.log('main.js loaded');

//Set up event handlers
jQuery(document).ready( function( $ ) {

    //Form submit handler
    $( "#form-submit" ).click( function() {

        var usrinput = $( "#screen_name" ).val();

        if ( usrinput.length > 0 ) {

            var request = $.ajax({
                method: "POST",
                url: 'app/main.php',
                data: { screen_name: usrinput },
                dataType: "JSON"
            });

        } else {
            console.log('TODO: Display error message that field is required');
        }

        request.done( function( msg ) {
            console.log( msg );
            if( msg.status == true ){
                console.log('ajax completed successfully:'+msg.payload);
                $( "#chart_div" ).html( msg.payload );
            } else {
                console.log('ajax completed with errors: '+msg.payload);
            }

        });      

    }); //end submit handler

}); //end document ready
