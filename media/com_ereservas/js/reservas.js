/**
 * Este scrip se ejecuta en el formulario de las reservas, tanto creando como editando
 */
jQuery(document).ready(function() {
    desencriptar();
    //Al cambiar de valor el id_visita (select), haremos las siguiente funciones
    jQuery('#jform_id_visita').change(function(){
        aforo();
        preciototal();
        preciopersona();
    });
    //Al pulsar sobre desencriptar se ejecuta la funcion
    jQuery('#btn-desencriptar').click(function(){
        desencriptar_btn();
    });
    //Al cambiar el input de personas, se ejecutara la funcion
    jQuery('#jform_personas').change(function(){
        preciototal();
    });
});


/**
 * Funcion AJAX donde mostramos el aforo que hay disponible en un texto rojo y pequeño encima del inptu
 */
function aforo(){
    var url =comprobarLocalhost()+ "/index.php?option=com_ereservas&task=obtenercampos.recibir_reservas&format=json";
    var datos = {};
    datos.opcion = jQuery("#jform_id_visita").val();
    //peticion AJAX
    jQuery.ajax({
        url: url,
        type: "POST",
        data: datos,
        success: function(data) {
            //si el AJAX va bien, parseamos los datos
            var json= jQuery.parseJSON(data);
            //solo nos interesa el array 0 de data (de momento)
            var tipo_visita=(json.data[0]);
            //ahora tendremos que rellenar los campos
            jQuery('.disponible').remove();
            var codigo="<div class='disponible'>(Aforo web:"+tipo_visita.aforo_web+" Aforo privado:"+tipo_visita.aforo_privado +" Aforo Ocupado:"+ tipo_visita.aforo_ocupado+" )</div>";
            jQuery("#jform_personas-lbl").append(codigo);
        },
        error: function(data){
            //console.log(data + "Error");
        },
    });
}

/**
 * Funcion AJAX que desencripta la tarjeta en el formulario para ver si los datos son correctos
 */
function desencriptar() {
    var url =comprobarLocalhost()+ "/index.php?option=com_ereservas&task=obtenercampos.desencriptar_recibir&format=json";
    var reserva = {};
    reserva.id_reserva = jQuery('input#reserva_id').val();
    //peticion AJAX
    jQuery.ajax({
        url: url,
        type: "POST",
        data: reserva,
        success: function (data) {
            //si el AJAX va bien, parseamos los datos
            var json = jQuery.parseJSON(data);
            //solo nos interesa el array 0 de data y dentro de ella, el campo tarjeta (de momento)
            if (json.data) {
                var desencriptado = (json.data[0]['tarjeta']);
                //ahora tendremos que hacer el alert con la tarjeta descifrada
                if (desencriptado == "") {
                    jQuery('#btn-desencriptar').hide();
                } else {
                    jQuery('#jform_tarjeta').attr({
                        placeholder: "********"
                    })
                }
            }
        },
        error: function (data) {
            console.log(data + "Error");
        },
    });
}
function desencriptar_btn() {
    var url = comprobarLocalhost()+"/index.php?option=com_ereservas&task=obtenercampos.desencriptar_recibir&format=json";
    var reserva = {};
    reserva.id_reserva = jQuery('input#reserva_id').val();
    //peticion AJAX
    jQuery.ajax({
        url: url,
        type: "POST",
        data: reserva,
        success: function (data) {
            //si el AJAX va bien, parseamos los datos
            var json = jQuery.parseJSON(data);
            //solo nos interesa el array 0 de data y dentro de ella, el campo tarjeta (de momento)
            var desencriptado = (json.data[0]['tarjeta']);
            //ahora tendremos que hacer el alert con la tarjeta descifrada
            jQuery('#texto-tarjeta').html("<p>La tarjeta es: " + desencriptado + "</p>");
        },
        error: function (data) {
            console.log(data + "Error");
        },
    });
}

/**
 * Funcion AJAX que muestra el precio total que seria, este valor se calcula cuando se elige el tipo visita y las personas que quieren ir
 */
function preciototal(){
        var url = comprobarLocalhost()+'/index.php?option=com_ereservas&task=obtenercampos.recibirprecio&format=json';
        var parametros = {};
        parametros.idvisita = jQuery('#jform_id_visita').val();
        parametros.personas = jQuery('input#jform_personas').val();
        jQuery.ajax({
            url: url,
            type: "POST",
            data: parametros,
            success: function (data) {
                var json = jQuery.parseJSON(data);
                var preciorecalc = json.data[0]['total'];
                jQuery('input#jform_precio').empty();
                jQuery('input#jform_precio').val(preciorecalc);
            },
            error: function(data){
                console.log(data + "error");
            },
        });
}

/**
 * Mostramos el precio por persona de esa reserva, en un texto rojo y en la parte superior del campo de texto "precio"
 */
    function preciopersona(){
        var url = comprobarLocalhost()+ '/index.php?option=com_ereservas&task=obtenercampos.recibirpreciopersona&format=json';
        var datos = {};
        datos.idvisita = jQuery('#jform_id_visita').val();
        jQuery.ajax({
            url: url,
            type: "POST",
            data: datos,
            success: function(data){
                var json = jQuery.parseJSON(data);
                var ppp = json.data[0]['precio'];
                console.log(ppp);
                jQuery('.preciopersona').remove();
                var codigo="<div class='preciopersona'>El precio por persona es de: "+ppp+"€</div>";
                jQuery("#jform_precio-lbl").append(codigo);
            },
            error: function (data){
                console.log(data + "Error");
            },
        })
    }


