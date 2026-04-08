/**
 * Funcion para mandar los datos que hemos mandado, ya sea a la hora de crear o de editar
 * @param id
 */
function guardarApertura(id){
    var datos           = {};

    datos.id            =   id;
    datos.fecha_inicio = jQuery("#formulario #fecha_inicio").val();
    datos.fecha_fin = jQuery("#formulario #fecha_fin").val();
    datos.id_tipo_visita = jQuery('#formulario #id_tipo_visita').val();

    //Validamos los campos mínimos
    //TODO: no ser tan ñapas y hacer mejor
    if (
        datos.fecha_inicio.length < 1 ||
        datos.fecha_fin.length < 1
    ) {
        alert('Por favor, rellene las fechas de inicio y finalización.');
        return;
    }

    //Llamada Ajax
    var url =comprobarLocalhost()+ "/index.php?option=com_ereservas&task=aperturassemanales.comprobarid&format=json";

    jQuery.ajax({
        url: url,
        type: "GET",
        data: datos,
        success: function(data) {
            console.log(data);
            jQuery('#formulario').modal('hide');

            reinicioapertura();
        },
        error: function(data){
            //console.log(data + "Error");
        },
    });
}

/**
 * Funcion que recarga la tabla cuando se ha insertado, modificado o eliminado un dato
 */

function reinicioapertura(){

    location.reload();

    //TODO HAcer bien
    /*
    jQuery("#tabla_aperturas").empty();
    var codigo='';
    jQuery.ajax({
        url: '/index.php?option=com_ereservas&task=aperturassemanales.reset&format=json',
        type: 'POST',
        success: function (data) {
            var cierres= data.data;
            codigo='<thead><tr><th>Fecha Inicio</th><th>Fecha Fin</th><th>Tipo de cierre</th><th>Concepto</th><th>Acción</th></tr></thead><tbody>';
            cierres.forEach(function(cierre){
                var fechainicio = cierre.fecha_inicio.split(' ')[0];
                var fechafin= cierre.fecha_fin.split(' ')[0];
                traducirTipoApertura(cierre.tipo_cierre);
                codigo+='<td>'+traducirfecha(new Date(fechainicio))+'</td>';
                codigo+='<td>'+traducirfecha(new Date (fechafin))+'</td>';
                codigo+= '<td>'+traducirTipoApertura(cierre.tipo_cierre)+'</td>';
                codigo+='<td>'+cierre.concepto+'</td>';
                codigo+='<td><button class="btn btn-success editarcierre" onclick="modal_editar('+cierre.id+')" data-toggle="modal" data-target="#formulario" type="button">Editar</button> ' +
                    '<button class="btn btn-danger borrarservicio" onclick="modal_eliminar('+cierre.id+')" data-toggle="modal" data-target="#modal-eliminar" type="button">Borrar</button>  </td>';
                codigo+='</tr>'
            });
            codigo+="</tbody>";
            jQuery("#tabla_cierres").html(codigo);
            jQuery("#formulario #dia_inicio").val("");
            jQuery("#formulario #dia_fin").val("");
            jQuery("#formulario #concepto").val("");
            jQuery("#formulario #formularioTitulo").text("Crear Apertura");
            jQuery("#guardar").remove();
            jQuery(".modal-footer").html('<button type="button" class="btn btn-primary" id="guardar" onclick="guardarApertura('+cierre.id+')">Guardar</button>' +
                '<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>');
        }
    });

     */
}
/**
 * Funcion para traer y cambiar los datos de la modal cuando se pulsa el boton de enviar
 * @param id
 */
function modal_editar(id){
    var datos= {};
    datos.id=id;
    jQuery.ajax({
        url:comprobarLocalhost()+ '/index.php?option=com_ereservas&task=aperturassemanales.editar&format=json',
        type: "POST",
        data: datos,
        success: function(data) {
            var apertura = data.data;
            /**
             * Modificar toda la modal
             */
            var fechainicio = apertura.fecha_inicio.split(' ')[0];
            var fechafin= apertura.fecha_fin.split(' ')[0];
            jQuery("#formulario #formularioTitulo").text("Editar "+fechainicio + " al " + fechafin);
            jQuery("#formulario #fecha_inicio").val(fechainicio);
            jQuery("#formulario #fecha_fin").val(fechafin);
            jQuery('#formulario #id_tipo_visita').chosen();
            jQuery('#formulario #id_tipo_visita').val(apertura.id_tipo_visita);
            jQuery('#formulario #id_tipo_visita').trigger("liszt:updated");
            jQuery('#formulario #id_tipo_visita').trigger("liszt:updated");

            jQuery("#guardar").remove();
            jQuery(".modal-footer").html('<button type="button" class="btn btn-primary" id="guardar" onclick="guardarApertura('+apertura.id+')">Guardar</button>' +
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
        url:comprobarLocalhost()+ '/index.php?option=com_ereservas&task=aperturassemanales.editar&format=json',
        type: "POST",
        data: datos,
        success: function(data) {
            apertura = data.data;
            console.log(apertura);
            /**
             * Modificar toda la modal
             */
            var fechainicio = apertura.fecha_inicio.split(' ')[0];
            var fechafin = apertura.fecha_fin.split(' ')[0];
            jQuery("#modal-eliminar #formularioTituloBorrar").text("Borrar Visitas");
            jQuery("#modal-eliminar #borrar-dia-inicio").text(fechainicio);
            jQuery("#modal-eliminar #borrar-dia-fin").text(fechafin);
            jQuery("#borrar").remove();
            jQuery(".modal-footer").html('<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>' +
                '<button type="button" class="btn btn-danger" id="borrar" onclick="borrarApertura('+apertura.id+')">Borrar</button>');
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

function borrarApertura(id){
    var datos= {};
    datos.id=id;
    jQuery.ajax({
       url:comprobarLocalhost()+ "/index.php?option=com_ereservas&task=aperturassemanales.borrarApertura&format=json",
        type: "POST",
        data: datos,
        success: function (data) {
            jQuery('#modal-eliminar').modal('hide');

            reinicioapertura();
        },
        error: function(data){

        }
    });
}

function traducirTipoApertura(cierre) {
    switch(cierre) {
        case "cierre":
            return "Apertura";
            break;
        case "festivo-laboral":
            return "Festivo Laboral";
            break;
        case 'sin-visita':
            return "Sin Visita";
        default:
            return "--";
    }
}
function traducirfecha(fecha){
    var meses = [
        "Enero", "Febrero", "Marzo",
        "Abril", "Mayo", "Junio", "Julio",
        "Agosto", "Septiembre", "Octubre",
        "Noviembre", "Diciembre"
    ];
    var semana = [
        "Lunes", "Martes", "Miercoles",
        "Jueves", "Viernes", "Sabado", "Domingo"
    ];

    var diasemananum= fecha.getDay();
    var dia = fecha.getDate();
    var mesnum = fecha.getMonth();
    var ano = fecha.getFullYear();

    return semana[diasemananum] +', ' + dia + ' ' + meses[mesnum] + ' ' + ano;
}