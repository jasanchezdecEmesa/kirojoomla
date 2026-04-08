/**
 * Funcion para mandar los datos que hemos mandado, ya sea a la hora de crear o de editar
 * @param id
 */
function guardarCierre(id){
    var dia_inicio  = jQuery("#dia_inicio").val() + ' 00:00:00';
    var dia_fin     = jQuery("#dia_fin").val() + ' 00:00:00';
    var concepto    = jQuery("#concepto").val();
    var tipo_cierre = jQuery("#tipo_cierre").val();

    var url             = comprobarLocalhost()+"/index.php?option=com_ereservas&task=cierres.comprobarid&format=json";
    var datos           = {};
    datos.id            =   id;
    datos.dia_inicio    =   dia_inicio;
    datos.dia_fin       =   dia_fin;
    datos.concepto      =   concepto;
    datos.tipo_cierre   = tipo_cierre;

    jQuery.ajax({
        url: url,
        type: "POST",
        data: datos,
        success: function(data) {
            jQuery('#formulario').modal('hide');
            console.log(data);
            reiniciocierre();
        },
        error: function(data){
            //console.log(data + "Error");
        },
    });
}

/**
 * Funcion que recarga la tabla cuando se ha insertado, modificado o eliminado un dato
 */

function reiniciocierre(){
    jQuery("#tabla_cierres").empty();
    var codigo='';
    jQuery.ajax({
        url:comprobarLocalhost()+ '/index.php?option=com_ereservas&task=cierres.reset&format=json',
        type: 'POST',
        success: function (data) {
            var cierres= data.data;
            codigo='<thead><tr><th>Fecha Inicio</th><th>Fecha Fin</th><th>Tipo de cierre</th><th>Concepto</th><th>Acción</th></tr></thead><tbody>';
            cierres.forEach(function(cierre){
                var fechainicio = cierre.fecha_inicio.split(' ')[0];
                var fechafin= cierre.fecha_fin.split(' ')[0];
                traducirTipoCierre(cierre.tipo_cierre);
                codigo+='<td>'+traducirfecha(new Date(fechainicio))+'</td>';
                codigo+='<td>'+traducirfecha(new Date (fechafin))+'</td>';
                codigo+= '<td>'+traducirTipoCierre(cierre.tipo_cierre)+'</td>';
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
            jQuery("#formulario #formularioTitulo").text("Crear Cierre");
            jQuery("#guardar").remove();
            jQuery(".modal-footer").html('<button type="button" class="btn btn-primary" id="guardar" onclick="guardarCierre('+cierre.id+')">Guardar</button>' +
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
        url:comprobarLocalhost()+ '/index.php?option=com_ereservas&task=cierres.editar&format=json',
        type: "POST",
        data: datos,
        success: function(data) {
            cierre = data.data;
            /**
             * Modificar toda la modal
             */
            var fechainicio = cierre.fecha_inicio.split(' ')[0];
            var fechafin= cierre.fecha_fin.split(' ')[0];
            jQuery("#formulario #formularioTitulo").text("Editar "+fechainicio + " al " + fechafin);
            jQuery("#formulario #dia_inicio").val(fechainicio);
            jQuery("#formulario #dia_fin").val(fechafin);
            jQuery("#formulario #concepto").val(cierre.concepto);
            jQuery('#formulario #tipo_cierre').chosen();
            jQuery('#formulario #tipo_cierre').val(cierre.tipo_cierre);
            jQuery('#formulario #tipo_cierre').trigger("liszt:updated");
            jQuery("#guardar").remove();
            jQuery(".modal-footer").html('<button type="button" class="btn btn-primary" id="guardar" onclick="guardarCierre('+cierre.id+')">Guardar</button>' +
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
        url:comprobarLocalhost()+ '/index.php?option=com_ereservas&task=cierres.editar&format=json',
        type: "POST",
        data: datos,
        success: function(data) {
            cierre = data.data;
            /**
             * Modificar toda la modal
             */
            var fechainicio = cierre.fecha_inicio.split(' ')[0];
            var fechafin= cierre.fecha_fin.split(' ')[0];
            jQuery("#modal-eliminar #formularioTituloBorrar").text("Borrar "+fechainicio+ " // "+fechafin);
            jQuery("#modal-eliminar #dia-inicio").text(fechainicio);
            jQuery("#modal-eliminar #dia-fin").text(fechafin);
            jQuery("#borrar").remove();
            jQuery(".modal-footer").html('<button type="button" class="btn btn-danger" id="borrar" onclick="borrarcierre('+cierre.id+')">Borrar</button>' +
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

function borrarcierre(id){
    var datos= {};
    datos.id=id;
    jQuery.ajax({
       url:comprobarLocalhost()+ "/index.php?option=com_ereservas&task=cierres.borrarcierre&format=json",
        type: "POST",
        data: datos,
        success: function (data) {
            jQuery('#modal-eliminar').modal('hide');
            reiniciocierre();
        },
        error: function(data){

        }
    });
}
jQuery( document ).ready(function() {
    jQuery(".boton-crear button").click(function(){
        jQuery("#formulario #dia_inicio").val("");
        jQuery("#formulario #dia_fin").val("");
        jQuery("#formulario #concepto").val("");
        jQuery("#formulario #formularioTitulo").text("Crear Cierre");
        jQuery("#guardar").remove();
        jQuery(".modal-footer").html('<button type="button" class="btn btn-primary" id="guardar" onclick="guardarCierre(0)">Guardar</button>' +
            '<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>');
    })
});
function traducirTipoCierre(cierre) {
    switch(cierre) {
        case "cierre":
            return "Cierre";
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