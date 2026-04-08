function comprobarLocalhost() {
    if(window.location.hostname ==="localhost"){
        return "http://localhost/kirojoomla"
    }else{
        return ""
    }
}
jQuery( document ).ready(function() {
    jQuery('ul.menu li').on('click', function (event) {
        jQuery('.offcanvas-init').removeClass('offcanvas-active full-offcanvas');
    });

    jQuery('.boton-enviar-lista-espera button').on('click', function (event) {
        var datos = {
            "nombre": jQuery('.formulario-lista-espera #sppb-form-builder-field-0').val() || null,
            "email": jQuery('.formulario-lista-espera #sppb-form-builder-field-1').val() || null,
            "comensales": jQuery('.formulario-lista-espera #sppb-form-builder-field-2').val() || null,
            "fecha": jQuery('.formulario-lista-espera #sppb-form-builder-field-3').val() || null,
            "condiciones": jQuery('.formulario-lista-espera #policy-1686217195583').is(':checked') ? true : false
        };
        console.log(datos);
        var url = comprobarLocalhost() + "/index.php?option=com_ereservas&task=listaespera.crear_registro&format=json";
        if(datos.condiciones === true){
            jQuery.ajax({
                url: url,
                method: "POST",
                data: datos,
            });
        }
    });
});

