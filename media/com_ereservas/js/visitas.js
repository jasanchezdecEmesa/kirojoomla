/**
 * Funcion AJAX que nos autorellena los campos cuando cambiamos el select del tipo visita, esta llamada coge los valores del
 * tipo visita seleccionado y los coincidentes se insertan como valores en los inputs
 *
 */

var url = comprobarLocalhost()+"/index.php?option=com_ereservas&task=obtenercampos.recibir_visita&format=json";
jQuery(document).ready(function() {
    jQuery('#jform_id_tipo_visita').change(function() {
        var datos = {};
        datos.opcion = jQuery("#jform_id_tipo_visita").val();//seguramente le tengamos que pasar el .val()
        //peticion AJAX
        jQuery.ajax({
            url: url,
            type: "POST",
            data: datos,
            success: function(data) {
                // console.log("ok");
                //si el AJAX va bien, parseamos los datos
                var json= jQuery.parseJSON(data);
                //solo nos interesa el array 0 de data (de momento)
                var tipo_visita=(json.data[0]);
                //ahora tendremos que rellenar los campos y desactivarlos
                jQuery("#jform_aforo_web").val(tipo_visita.aforo_web);
                jQuery("#jform_aforo_privado").val(tipo_visita.aforo_privado);
                jQuery("#jform_comida_chzn .chzn-single span").text(tipo_visita.comida);
                jQuery("jform_comida").val(tipo_visita.comida);
                jQuery("#jform_cata_chzn .chzn-single span").text(tipo_visita.cata);
                jQuery("jform_cata").val(tipo_visita.cata);
                jQuery("#jform_precio").val(tipo_visita.precio);

                //Ajustamos los checkbox de Sala (MULTIPLES)
                var salas=tipo_visita.id_sala.split(",");
                jQuery('#jform_id_sala input[type="checkbox"]').removeAttr('checked');
                for(var i=0; i<salas.length; i++){
                    jQuery('#jform_id_sala input[type="checkbox"][value="'+salas[i]+'"]').attr('checked','checked');
                }

                //Ajustamos Comida (SIMPLES)
                jQuery('#jform_comida option').removeAttr('selected');
                jQuery('#jform_comida option[value="'+tipo_visita.comida+'"]').attr('selected','selected');

                //Ajustamos Cata (SIMPLES)
                jQuery('#jform_cata option').removeAttr('selected');
                jQuery('#jform_cata option[value="'+tipo_visita.cata+'"]').attr('selected','selected');

            },
            error: function(data){
                console.log(data + "Error");
            },
        });
    });
});
