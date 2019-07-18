$(document).ready(function(){
    $.ajax({
        url: "nico.php",
        type: "GET",
        dataType: "json",

    }).done(function(respuesta){

            console.log(JSON.parse(JSON.stringify(respuesta)));
            //console.log(JSON.stringify((respuesta)));
    
    }).fail(function( jqXHR, textStatus, errorThrown ) {
         
         console.log( "La solicitud a fallado: " +  textStatus);

   });   

});        