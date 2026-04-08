/**
 * Funcion que detecta el id visita que se esta pasando
 */
jQuery(document).ready(function() {
    var separado= window.location.search.split("=");
    for(index=0, len=separado.length; index<len; index++) {
        if (separado[index] == "?id_visita") {
            jQuery('#form-reserva #jform_id_visita').val(separado[index+1]);
            //console.log(separado[index+1]);
            jQuery("#form-reserva #jform_id_visita").trigger( "change" );
        }
    }
});
