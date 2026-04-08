/**
 * Funcion para mandar los datos que hemos mandado, ya sea a la hora de crear o de editar
 * @param id
 */

function guardarServicio(id){

    var dia_semana = jQuery("#dia_semana").val();
    var hora = jQuery("#hora").val();
    var aforo = jQuery("#aforo").val();

    var url =comprobarLocalhost()+ "/index.php?option=com_ereservas&task=serviciossemanales.comprobarid&format=json";
    var datos = {};
    datos.id = id;
    datos.dia_semana= dia_semana;
    datos.hora = hora;
    datos.aforo = aforo;
    //peticion AJAX
    jQuery.ajax({
        url: url,
        type: "POST",
        data: datos,
        success: function(data) {
            jQuery('#formulario').modal('hide');
            reinicioservicios();
        },
        error: function(data){
            //console.log(data + "Error");
        },
    });
}
/**
 * Funcion que recarga la tabla cuando se ha insertado, modificado o eliminado un dato
 */

function reinicioservicios(){
    jQuery("#tabla_servicios").empty();
    var codigo='';
    jQuery.ajax({
        url: comprobarLocalhost()+ '/index.php?option=com_ereservas&task=serviciossemanales.reset&format=json',
        type: 'POST',
        success: function (data) {
            var servicios= data.data;
            codigo='<thead><tr><th>Día de la semana</th><th>Hora</th><th>Aforo</th><th>Editar</th></tr></thead><tbody>';
            servicios.forEach(function(servicio){
                codigo+='<tr>';
               codigo+= '<td>'+versemana(servicio.dia_semana)+'</td>'
                codigo+='<td>'+servicio.hora+'</td>';
                codigo+='<td>'+servicio.aforo+'</td>';
                codigo+='<td><button class="btn btn-success editarservicio" onclick="modal_editar('+servicio.id+')" data-toggle="modal" data-target="#formulario" type="button">Editar</button> ' +
                    '<button class="btn btn-danger borrarservicio" onclick="modal_eliminar('+servicio.id+')" data-toggle="modal" data-target="#modal-eliminar" type="button">Borrar</button>  </td>';
                codigo+='</tr>'
            });
            codigo+="</tbody>";
            jQuery("#tabla_servicios").html(codigo);
            jQuery('select').chosen();
            jQuery('select').val("");
            jQuery('select').trigger("liszt:updated");
            jQuery("#formulario #hora").val("");
            jQuery("#formulario #aforo").val("");
            jQuery("#formulario #formularioTitulo").text("Crear Servicio");
            jQuery("#guardar").remove();
            jQuery(".modal-footer").html('<button type="button" class="btn btn-primary" id="guardar" onclick="guardarServicio(0)">Guardar</button>' +
                '<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>');
        }
    });

}

/**
 * Funcion para traer y cambiar los datos de la modal cuando se pulsa el boton de enviar
 * @param id
 */

function modal_editar(id){
    var datos= {};
    datos.id=id;
    jQuery.ajax({
        url:comprobarLocalhost()+ '/index.php?option=com_ereservas&task=serviciossemanales.editar&format=json',
        type: "POST",
        data: datos,
        success: function(data) {
            servicio = data.data;
            /**
             * Modificar toda la modal
             */
            jQuery("#formulario #formularioTitulo").text("Editar "+versemana(servicio.dia_semana) + " a las " +servicio.hora);
            jQuery('select').chosen();

                jQuery('select').val(servicio.dia_semana);
                jQuery('select').trigger("liszt:updated");
            jQuery("#formulario #hora").val(servicio.hora);
            jQuery("#formulario #aforo").val(servicio.aforo);
            jQuery("#guardar").remove();
            jQuery(".modal-footer").html('<button type="button" class="btn btn-primary" id="guardar" onclick="guardarServicio('+servicio.id+')">Guardar</button>' +
                '<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>');
        },
        error: function(data){
            //console.log(data + "Error");
        },
    });
}

/**
 * Funcion para traer datos necesarios para mostrar informacion de lo que se esta a punto de borrar
 * @param id
 */

function modal_eliminar(id){
    var datos= {};
    datos.id=id;
    jQuery.ajax({
        url:comprobarLocalhost()+ '/index.php?option=com_ereservas&task=serviciossemanales.editar&format=json',
        type: "POST",
        data: datos,
        success: function(data) {
            servicio = data.data;
            /**
             * Modificar toda la modal
             */
            jQuery("#modal-eliminar #formularioTituloBorrar").text("Borrar "+versemana(servicio.dia_semana) +" - " + servicio.hora);
            jQuery("#modal-eliminar #semana-span").text(versemana(servicio.dia_semana));
            jQuery("#modal-eliminar #hora-span").text(servicio.hora);
            jQuery("#borrar").remove();
            jQuery(".modal-footer").html('<button type="button" class="btn btn-danger" id="borrar" onclick="borrarservicio('+servicio.id+')">Borrar</button>' +
                '<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>');
        },
        error: function(data){
            //console.log(data + "Error");
        },
    });
}

/**
 * Funcion que es una llamada ajax para borrar un campo y asi poder recargar la tabla
 * @param id
 */

function borrarservicio(id){
    var datos= {};
    datos.id=id;
    jQuery.ajax({
       url:comprobarLocalhost()+ "/index.php?option=com_ereservas&task=serviciossemanales.borrarserviciossemanal&format=json",
        type: "POST",
        data: datos,
        success: function (data) {
            jQuery('#modal-eliminar').modal('hide');
            reinicioservicios();
        },
        error: function(data){

        }
    });
}
function versemana(dia){
    var devolver='';
    switch (+dia) {
        case 1:
            devolver= "Lunes";
            break;
        case 2:
            devolver= "Martes";
            break;
        case 3:
            devolver= "Miércoles";
            break;
        case 4:
            devolver= "Jueves";
            break;
        case 5:
            devolver= "Viernes";
            break;
        case 6:
            devolver= "Sábado";
            break;
        case 7:
            devolver= "Domingo";
            break;
    }
    return devolver
}
jQuery( document ).ready(function() {
    jQuery(".boton-crear button").click(function(){
        jQuery('select').chosen();
        jQuery('select').val("");
        jQuery('select').trigger("liszt:updated");
        jQuery("#formulario #hora").val("");
        jQuery("#formulario #aforo").val("");
        jQuery("#formulario #formularioTitulo").text("Crear Servicio");
        jQuery("#guardar").remove();
        jQuery(".modal-footer").html('<button type="button" class="btn btn-primary" id="guardar" onclick="guardarServicio(0)">Guardar</button>' +
            '<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>');
    })
});