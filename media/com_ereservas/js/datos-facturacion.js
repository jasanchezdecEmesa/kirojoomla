document.addEventListener('DOMContentLoaded', function() {
    jQuery( "#datos-facturacion" ).change( function() {

        if ( jQuery(this).is(":checked") ) {
            jQuery( "#formulario-reserva-campos" ).show();
        } else if ( jQuery(this).not(":checked") ) {
            jQuery( "#formulario-reserva-campos" ).hide();
        }
    });

    jQuery( "#acepto-politica" ).change( function() {
        if ( jQuery(this).is(":checked") ) {
            jQuery( "#boton-guardar" ).prop("disabled", false);
        } else if ( jQuery(this).not(":checked") ) {
            jQuery( "#boton-guardar" ).prop("disabled", true);
        }
    });
})