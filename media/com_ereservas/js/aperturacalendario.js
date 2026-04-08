/**
 * Funcion para mandar los datos que hemos mandado, ya sea a la hora de crear o de editar
 * @param id
 */

function guardarapertura(id){

    var fecha_apertura = jQuery("#fecha_apertura").val();
    var fecha_final = jQuery("#fecha_final").val();
    var url = comprobarLocalhost()+ "/index.php?option=com_ereservas&task=aperturacalendario.comprobarid&format=json";
    var datos = {};
    datos.id = id;
    datos.fecha_apertura= fecha_apertura;
    datos.fecha_final = fecha_final;
    //peticion AJAX
    jQuery.ajax({
        url: url,
        type: "POST",
        data: datos,
        success: function(data) {
            jQuery('#formulario').modal('hide');
            reinicioaperturas();
        },
        error: function(data){
            //console.log(data + "Error");
        },
    });
}

/**
 * Funcion que recarga la tabla cuando se ha insertado, modificado o eliminado un dato
 */

function reinicioaperturas(){
    jQuery("#tabla_apertura").empty();
    var codigo='';
    jQuery.ajax({
        url:comprobarLocalhost()+ '/index.php?option=com_ereservas&task=aperturacalendario.reset&format=json',
        type: 'POST',
        success: function (data) {
            var datos= data.data;
            codigo='<thead><tr><th>Día Apertura</th><th>Último día</th><th>Acción</th></tr></thead><tbody>';
            datos.forEach(function(dato){
                codigo+='<tr>';
                codigo+='<td>'+dato.fecha_apertura+'</td>';
                codigo+='<td>'+dato.fecha_final+'</td>';
                codigo+='<td><button class="btn btn-success editarservicio" onclick="modal_editar('+dato.id+')" data-toggle="modal" data-target="#formulario" type="button">Editar</button> <button class="btn btn-danger borrarservicio" onclick="modal_eliminar('+dato.id+')" data-toggle="modal" data-target="#modal-eliminar" type="button">Borrar</button> </td>';
                codigo+='</tr>'
            });
            codigo+="</tbody>";
            jQuery("#tabla_apertura").html(codigo);
            jQuery('select#fecha_apertura').chosen();
            jQuery('select#fecha_apertura').val("");
            jQuery('select#fecha_apertura').trigger("liszt:updated");

            jQuery('select#fecha_final').chosen();
            jQuery('select#fecha_final').val("");
            jQuery('select#fecha_final').trigger("liszt:updated");

            jQuery("#formulario #formularioTitulo").text("Crear Apertura");
            jQuery("#guardar").remove();
            jQuery(".modal-footer").html('<button type="button" class="btn btn-primary" id="guardar" onclick="guardarapertura(0)">Guardar</button>' +
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
        url:comprobarLocalhost()+ '/index.php?option=com_ereservas&task=aperturacalendario.editar&format=json',
        type: "POST",
        data: datos,
        success: function(data) {
            datos = data.data;
            /**
             * Modificar toda la modal
             */
            jQuery("#formulario #formularioTitulo").text("Editar apertura "+datos.fecha_apertura+" de "+datos.fecha_final);
            jQuery('#fecha_apertura').val(datos.fecha_apertura.replace(" ", "T"));

            jQuery('#fecha_final').val(datos.fecha_final);

            jQuery("#guardar").remove();
            jQuery(".modal-footer").html('<button type="button" class="btn btn-primary" id="guardar" onclick="guardarapertura('+datos.id+')">Guardar</button> ' +
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
        url:comprobarLocalhost()+ '/index.php?option=com_ereservas&task=aperturacalendario.editar&format=json',
        type: "POST",
        data: datos,
        success: function(data) {
            datos = data.data;
            /**
             * Modificar toda la modal
             */

            jQuery("#modal-eliminar #formularioTituloBorrar").text("Borrar "+ datos.fecha_apertura + " - "+datos.fecha_final);
            jQuery("#modal-eliminar #apertura-span").text(traducirfecha(datos.fecha_apertura));
            jQuery("#modal-eliminar #final-span").text( traducirfecha(datos.fecha_final));
            jQuery("#borrar").remove();
            jQuery(".modal-footer").html('<button type="button" class="btn btn-danger" id="borrar" onclick="borrarapertura('+datos.id+')">Borrar</button>' +
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

function borrarapertura(id){
    var datos= {};
    datos.id=id;
    jQuery.ajax({
       url:comprobarLocalhost()+ "/index.php?option=com_ereservas&task=aperturacalendario.borrarapertura&format=json",
        type: "POST",
        data: datos,
        success: function (data) {
            jQuery('#modal-eliminar').modal('hide');
            reinicioaperturas();
        },
        error: function(data){

        }
    });
}
function traducirfecha(fecha){
    var fecha_separada=fecha.split(" ");
    var fecha_formatear= fecha_separada[0].split("-");
    console.log(fecha_separada.length);
    if (fecha_separada.length == 2){
        return fecha_formatear[2] + "-" + fecha_formatear[1] + "-" + fecha_formatear[0] + " "+ fecha_separada[1] ;
    }else{
        return fecha_formatear[2] + "-" + fecha_formatear[1] + "-" + fecha_formatear[0] ;
    }
}

jQuery( document ).ready(function() {
    jQuery(".boton-crear button").click(function(){
        jQuery('select#fecha_apertura').chosen();
        jQuery('select#fecha_apertura').val("");
        jQuery('select#fecha_apertura').trigger("liszt:updated");

        jQuery('select#fecha_final').chosen();
        jQuery('select#fecha_final').val("");
        jQuery('select#fecha_final').trigger("liszt:updated");

        jQuery("#formulario #formularioTitulo").text("Crear Apertura");
        jQuery("#guardar").remove();
        jQuery(".modal-footer").html('<button type="button" class="btn btn-primary" id="guardar" onclick="guardarapertura(0)">Guardar</button>' +
            '<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>');
    })
});
