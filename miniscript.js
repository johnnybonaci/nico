$(document).ready(function(){
    

    $.ajax({
        url: "http://localhost/plantillas/nico/nico.php",
        data: {"id": "1112180", "div": "1"},
        dataType: "json",
        async: false,
        success:  function(data) {
           items = JSON.parse(JSON.stringify(data));
           $.each(data, function(i, item) {

            $( "#div" ).append( item['div'] );
            });
        }
    });

});       
