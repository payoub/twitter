console.log('main.js loaded');

// Load google api for histogram chart
google.load('visualization', '1', {'packages':['corechart']});

//Set up event handlers
jQuery(document).ready( function( $ ) {

    //Loading image handlers
    $(this).bind('ajaxStart', function() {
      $('#chart_div').html('<div class="spinner"> <div class="double-bounce1"></div> <div class="double-bounce2"></div> </div>');
    }).bind('ajaxStop', function() {
      $('.spinner').remove();
    });

    //Enter key handler for text box, enter key is code 13
    $( "#screen_name" ).keypress( function( e ) {
      if ( e.which == 13 ) { $( "#form-submit" ).click(); } 
    });
    
    //Form submit handler
    $( "#form-submit" ).click( function() {

        var usrinput = $( "#screen_name" ).val();

        //hide any lingering errors
        $( "#alerts" ).hide();
        //hide any lingering charts
        $( "#chart_div").html("");

        if ( usrinput.length > 0 ) {

            var request = $.ajax({
                url: 'app/main.php',
                data: { screen_name: usrinput },
                dataType: "JSON"
            });

            request.done( function( msg ) {

                if( msg.status == true ){

                    var data = new google.visualization.arrayToDataTable( msg.payload );
                    var chart = new google.visualization.Histogram( document.getElementById("chart_div") );

                    var chart_width = $("#chart_div").width();
                    var chart_height = (chart_width) / 2; //Dynamically generate height so chart is always a rectangle

                    var options = {
                        width: chart_width,
                        height: chart_height,
                        title: 'Distribution Of Tweets Over Time',
                        legend: {position: 'bottom', alignment: 'end'},
                        vAxis: {title: 'Number of tweets'},
                        hAxis: {title: 'Time (24h)'},
                        colors: ['#337ab7']
                    };
 
                    chart.draw( data, options);

                } else {
                    $( "#alerts" ).html( msg.payload ).fadeIn();
                }
    
            });      

        } else {
            $( "#alerts" ).html('Error: Please enter a valid Twitter Screen Name').fadeIn();
        }


    }); //end submit handler

}); //end document ready
