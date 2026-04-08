//Scrip que se encarga de desencriptar el valor de la tarjeta en cliente

//Esta funcion se ejecuta al entrar en la vista de la reserva
// Primero comprobamos si la reserva en sí tiene el campo tarjeta rellenado en la base de datos, en caso positivo,
//pondremos unos asteriscos indicando que existe, sino no se rellenara nada
jQuery(document).ready(function() {
    var reserva = {};
    reserva.id_reserva=jQuery('input#reserva_id').val();
    //peticion AJAX
    jQuery.ajax({
        url: url,
        type: "POST",
        data: reserva,
        success: function(data) {
            //si el AJAX va bien, parseamos los datos
            var json= jQuery.parseJSON(data);
            //solo nos interesa el array 0 de data y dentro de ella, el campo tarjeta (de momento)
            var desencriptado=(json.data[0]['tarjeta']);
            //ahora tendremos que hacer el alert con la tarjeta descifrada
            if(desencriptado == ""){
                jQuery('#btn-desencriptar').hide();
            }else{
                jQuery('#jform_tarjeta').attr({
                  placeholder:"********"
                })
            }
        },
        error: function(data){
            console.log(data + "Error");
        },
    });

    //Al pulsar sobre el boton de desencriptar, crearemos este AJAX, donde el resultado sera la tarjeta desencriptada
    jQuery('#btn-desencriptar').click(function(){
        var reserva={};
        reserva.id_reserva=jQuery('input#reserva_id').val();
        //peticion AJAX
        jQuery.ajax({
            url: url,
            type: "POST",
            data: reserva,
            success: function(data) {
                //si el AJAX va bien, parseamos los datos
                var json= jQuery.parseJSON(data);
                //solo nos interesa el array 0 de data y dentro de ella, el campo tarjeta (de momento)
                var desencriptado=(json.data[0]['tarjeta']);
                //ahora tendremos que hacer el alert con la tarjeta descifrada
                jQuery('#texto-tarjeta').html("<p>La tarjeta es: "+desencriptado+"</p>");
            },
            error: function(data){
                console.log(data + "Error");
            },
        });
    })
});
