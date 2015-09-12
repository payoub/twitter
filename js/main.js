console.log('main.js loaded');

// Load google api for histogram chart
google.load('visualization', '1', {'packages':['corechart']});

//Set up event handlers
jQuery(document).ready( function( $ ) {


    //Form submit handler
    $( "#form-submit" ).click( function() {

        var usrinput = $( "#screen_name" ).val();

        //hide any lingering errors
        $( "#alerts" ).hide();

        if ( usrinput.length > 0 ) {

            var request = $.ajax({
                url: 'app/main.php',
                data: { screen_name: usrinput },
                dataType: "JSON"
            });

            request.done( function( msg ) {
                console.log( msg );
                if( msg.status == true ){
                    console.log('ajax completed successfully:'+msg.payload);
                    var data = new google.visualization.arrayToDataTable( msg.payload );
                    var chart = new google.visualization.Histogram( document.getElementById("chart_div") );
                    var chart_width = $("#chart_div").width();
                    var chart_height = (chart_width) / 2;
                    chart.draw( data,
                                { width: chart_width , height: chart_height } );
                } else {
                    $( "#alerts" ).html( msg.payload ).fadeIn();
                    console.log('ajax completed with errors: '+msg.payload);
                }
    
            });      

        } else {
            $( "#alerts" ).html('Please enter a valid Twitter Screen Name').fadeIn();
        }


    }); //end submit handler

}); //end document ready
