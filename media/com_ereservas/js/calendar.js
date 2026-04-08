document.addEventListener('DOMContentLoaded', function() {
    jQuery('#formularioreserva').keypress(function(e) {
        var keycode = (e.keyCode ? e.keyCode : e.which);
        if (keycode == '13' && !jQuery(e.target).is('textarea')) {
            e.preventDefault();
            return false;
        }
    });

    var calendarEl = document.getElementById('calendar');
    //Asignamos las principales variables que tendra el objeto calendario
    var calendar = new FullCalendar.Calendar(calendarEl, {
        timeZone: 'Europe/Madrid',
        locale: 'es',
        plugins: ['interaction', 'dayGrid', 'momentPlugin'],
        defaultView: 'dayGridMonth',
        selectable: true,
        aspectRatio: 0.9,
        firstDay: 1,
        header: {
            left: 'title',
            center: '',
            right: 'today prev,next'
        },
        //Para cargar los eventos, hacemos esta llamada ajax
        events: {
           url:comprobarLocalhost()+ "/index.php?option=com_ereservas&task=obtenereventos.visitas&format=json",
            method: 'POST',
            success: function(data) {
                //Llamamos a la funcion que pondra en morado los dias que tengan una visita
                pintarDiasCalendario(data);
                //Llamamos a la funcion donde controlaremos los cierres que haya
                PintarCierres();
            },
            error: function(data) {
                alert("Ha habido un error de conexion al realizar la tarea Main Calendario")
            }
        },
        //Aqui detectamos cuando se hace click en un dia (no hace falta que tenga una visita o no)
        dateClick: function(info) {
            pintarEventos(info.dateStr);
        }
    });
    calendar.render();


    /**
     * Evento que se ejecuta cuando se pulsa el boton de "ver" en una visita. Mostramos las reservas que hay asignadas para ese dia.
     */
    jQuery('#eventos').on('click', '.pulsamiento', function() {

        activarLoader();



        jQuery("#resultado").empty();
        jQuery("#formularioreserva").css("display", "block");
        datos = {};

        //preparamos aqui el array de datos para mandarlo directamente a la funcion y asi no tener que tratarla dentro de esta misma
        datos.visitaid = jQuery(this).attr("id");
        pintarReservas(datos);
    });
    jQuery('#eventos').on('click', '.borrar-visita', function() {
        var mensaje = prompt("Escribe BORRAR si realmente quieres borrar esta visita");

        if (mensaje != "BORRAR") {
            return false;
        } else {
            datos = {};

            //preparamos aqui el array de datos para mandarlo directamente a la funcion y asi no tener que tratarla dentro de esta misma
            datos.visitaid = jQuery(this).attr("id");
            borrarVisita(datos);
        }
    });

    jQuery('#resultado').on('click', '#cancelar', function() {
        var mensaje = prompt("Escribe CANCELAR si realmente quieres cancelar esta visita");

        if (mensaje != "CANCELAR") {
            return false;
        } else {
            datos = {};

            //preparamos aqui el array de datos para mandarlo directamente a la funcion y asi no tener que tratarla dentro de esta misma
            datos.reservaid = jQuery(this).attr("data-valor");
            cancelarReserva(datos);
        }
    });

    jQuery('.boton-canceladas').on('click', '#mostrar_canceladas', function() {
        jQuery(".reservacancelada").toggle(function() {
            if (jQuery(this).is(":visible")) {
                jQuery("#mostrar_canceladas").html("Ocultar reservas canceladas")
                jQuery(".texto-evento-canceladas").hide();
            } else {
                jQuery("#mostrar_canceladas").html("Mostrar reservas canceladas")
                jQuery(".texto-evento-canceladas").show();
            }
        })
    })

    /**
     * Al pulsar sobre el boton de visitas rapidas, se ejecuta este evento que pone en marcha el insert de las visitas
     */
    jQuery('#formulariovisita').on('click', '.enviarvisita', function(event) {
        activarLoader();

        event.preventDefault();
        var valores = {};
        var datos = {};

        //cogemos los valores que han insertado en el formulario y los metemos en el array
        valores.tipovisita = jQuery("#jform_tipovisita").val();
        valores.idioma = jQuery("#jform_idioma_visita").val();
        valores.hora = jQuery("#jform_hora").val();
        valores.fecha = jQuery("#jform_fecha").val();

        /**
         * Comprobaciones antes de enviar via cliente
         */

        //Si no hemos seleccionado ninguna fecha, lanzaremos el siguiente error
        if (valores.fecha.length !== 10) {
            jQuery('#system-message-container').addClass('completa');
            Joomla.renderMessages({ "error": ["Por favor seleccione una fecha antes de generar la visita."] });
            jQuery('#system-message-container').on('click', function() {
                jQuery(this).empty().removeClass('completa');
            });
        }

        //Si no hemos elegido que tipo de visita es, lanzaremos el siguiente error
        else if (valores.tipovisita.length == 0) {
            jQuery('#system-message-container').addClass('completa');
            Joomla.renderMessages({ "error": ["Por favor seleccione un tipo de visita."] });
            jQuery('#system-message-container').on('click', function() {
                jQuery(this).empty().removeClass('completa');
            });
        }

        //Si no hemos elegido una hora, lanzaremos el siguiente error
        else if (valores.hora.length !== 5) {
            jQuery('#system-message-container').addClass('completa');
            Joomla.renderMessages({ "error": ["Por favor seleccione una hora."] });
            jQuery('#system-message-container').on('click', function() {
                jQuery(this).empty().removeClass('completa');
            });
        }

        //Si ha pasado la verificacion, procedemos a preparar la llamada AJAX
        else {
            datos.dia = valores.dateStr;
            url = comprobarLocalhost()+ '/index.php?option=com_ereservas&task=obtenereventos.visitasrapidas&format=json;';
            jQuery.ajax({
                url: url,
                type: 'POST',
                data: valores,
                success: function(data) {
                    //si ha ido correcta la llamada, le añadiremos a esa fecha la clase tienereserva (en caso de que no la tenga)
                    var info = {};
                    info.dateStr = valores.fecha;
                    jQuery("[data-date=" + info.dateStr + "]").addClass("tienereserva");

                    //Volvemos a recargar el calendario
                    pintarEventos(info.dateStr);
                    desactivarLoader();
                },
                error: function(data) {
                    alert("Ha habido un error de conexion al realizar la tarea Main Calendario");
                    desactivarLoader();
                }
            })
        }
    });
    /**
     * Al pulsar sobre el boton de enviar en el formulario de reserva, se ejecuta este evento que pone en marcha el insert de las reservas
     */
    jQuery('#formularioreserva').on('click', '.enviarreserva', function(event) {
        activarLoader();

        event.preventDefault();
        jQuery(this).attr("disabled", true);

        //guardamos en un array los valores insertados en el formulario
        var valores = {};
        valores.nombre = jQuery("#jform_nombre").val();
        valores.email = jQuery("#jform_email").val();
        valores.telefono = jQuery("#jform_telefono").val();
        valores.tarjeta = jQuery("#jform_tarjeta").val();
        valores.codigopostal = jQuery("#jform_codigopostal").val();
        valores.personas = jQuery("#jform_personas").val();
        valores.idioma = jQuery("#jform_idioma").val();
        valores.usuario = jQuery("#jform_usuario").val();
        valores.pais = jQuery("#jform_pais").val();
        valores.tipocliente = jQuery("#jform_tipo_cliente").val();
        valores.pagado = jQuery("#jform_pagado").val();
        valores.precio = jQuery("#jform_precio").val();
        valores.estado = jQuery("#jform_estado").val();
        valores.asistencia = jQuery("#jform_asistencia").val();
        valores.observaciones = jQuery("#jform_observaciones").val();
        valores.visitaid = jQuery("#jform_idvisita").val();
        valores.reservaid = jQuery("#jform_idreserva").val();
        if (jQuery("#jform_mandarcorreo").is(":checked")) {
            valores.enviarcorreo = "pago";
        } else {
            valores.enviarcorreo = "confirmacion";
        }

        //Comprobamos que no nos vengan vacios los campos que son obligatorios, si hay alguno que venga asi, mostramos un mensaje de error
        if (!jQuery("#jform_nombre")[0].checkValidity() || !jQuery("#jform_personas")[0].checkValidity() || jQuery("#jform_email").val() == "" || !jQuery("#jform_email")[0].checkValidity() || !jQuery("#jform_idioma")[0].checkValidity() || !jQuery("#jform_usuario")[0].checkValidity()) {
            jQuery('#system-message-container').addClass('completa');
            Joomla.renderMessages({ "error": ["Por favor rellena los campos nombre, email, personas, idioma y usuario de la reserva"] });
            jQuery("button.enviarreserva").attr("disabled", false);
            jQuery('#system-message-container').on('click', function() {
                jQuery(this).empty().removeClass('completa');
            });
        }
        //si ha pasado la verficacion, comprobamos que tenga un idvisita asignado, en caso negativo, mostramos mensaje de error
        else if (valores.visitaid < 1) {
            jQuery('#system-message-container').addClass('completa');
            Joomla.renderMessages({ "error": ["Por favor seleccione una visita antes de generar una reserva."] });
            jQuery("button.enviarreserva").attr("disabled", false);
            jQuery('#system-message-container').on('click', function() {
                jQuery(this).empty().removeClass('completa');
            });
        } else if (valores.reservaid != "") {
            editarreserva(valores);
        }
        //Si no ha habido ningun problema con las verificaciones anteriores, podemos empezar a crear la llamada AJAX
        else {
            url =comprobarLocalhost()+ '/index.php?option=com_ereservas&task=obtenereventos.reservasrapidas&format=json;';
            jQuery.ajax({
                url: url,
                type: 'POST',
                data: valores,
                success: function(data) {
                    jQuery("#resultado").empty();
                    datos.id = valores.visitaid;

                    //si la llamada AJAX ha ido correctamente, ejecutamos la funcion que añadira a la tabla de reservas la que se acaba de crear, junto con la funcion de "resetear" el calendario
                    pintarReservas(datos);
                    fecha = jQuery("#fechavisitaform").text();
                    pintarEventos(fecha);
                    vaciarValores();
                    desactivarLoader();
                },
                error: function(data) {
                    alert("Ha habido un error de conexion al realizar la tarea insertar reserva");
                    desactivarLoader();
                }
            })
        }
        //jQuery(".LoaderOverlay").css("display","none");
    });

    /************************************************************************
     * Funcion donde creamos la tabla de las visitas que hay en ese dia     *
     ************************************************************************/
    function pintarEventos(fecha) {
        activarLoader();
        jQuery("#mostrar_canceladas").css("display", "none");
        jQuery("#resultado").empty();
        jQuery("#jform_fecha").val(fecha);
        jQuery("#fechavisitaform").empty();
        jQuery("#fechavisitaform").append(fecha);


        var date = new Date(fecha + 'T00:00:00');
        var fechaArr = fecha.split('-');
        var fecha_mostrar = fechaArr[2] + '-' + fechaArr[1] + '-' + fechaArr[0];

        jQuery("#fechavisita").empty();
        jQuery("#fechavisita").append(fecha_mostrar);
        //si el dia que hemos pulsado no tiene la clase "tienereserva", significara que no hay visitas para ese dia, por lo que mostramos este mensaje en el lateral
        if (jQuery("[data-date=" + fecha + "]").hasClass("tienereserva") == false) {
            jQuery("#eventos").empty().append('<h3>Vistas para el día ' + fecha_mostrar + '</h3><p><em>No hay visitas...</em></p>');
        }
        //en caso de que haya, preparamos la funcion ajax para traer las visitas
        else {
            var datos = {};
            datos.dia = fecha;
            jQuery.ajax({
               url:comprobarLocalhost()+ "/index.php?option=com_ereservas&task=obtenereventos.visitasdias&format=json",
                type: 'POST',
                data: datos,
                success: function(data) {
                    var visita_dia = data.data;

                    //empezamos a generar el codigo que tendra la tabla que mostrara las visitas
                    var codigo = "<table><thead><tr><td class='texto-evento' colspan='8'>Visitas para el dia " + fecha_mostrar + "</td></tr>";
                    codigo += "<tr class='cabeceras-evento'><td align='center'>Reservas</td><td>Hora</td><td>Idioma</td><td>Visita</td><td>Disponible</td><td>Ocupado</td><td align='center'>Edición</td><td>Borrar Visita</td></tr></thead><tbody>"

                    //por cada visita que haya, crearemos un row en la tabla, mostrando los datos
                    visita_dia.forEach(function(element) {
                        if (element.idioma == null) element.idioma = "Español";
                        codigo += '<tr>';

                        //mostramos el boton para que se puedan ver las reservas que hay para ese dia (importante la clase "pulsamiento")
                        codigo += '<td align="center"><a class="pulsamiento" id="' + element.id + '" data-cabecera="' + element.hora_inicio.substring(0, 5) + ' - ' + element.nombre + '"><span class="fa fa-eye">&nbsp;</span> Ver</a></td>';
                        codigo += '<td>' + element.hora_inicio.substring(0, 5) + '</td>';
                        codigo += '<td>' + element.idioma + '</td>';
                        codigo += '<td>' + element.nombre + '</td>';

                        //Hacemos este calculo para marcar las plazas disponibles, siempre vamos a coger el valor maximo entre el aforo_privado o el aforo_web y lo restaremos al aforo ocupado, ese resultado se mostrara
                        var aforo_disponible = (Number(Math.max(element.aforo_privado, element.aforo_web)) - Number(element.aforo_ocupado));
                        if (aforo_disponible < 0) {
                            codigo += '<td class="aforo-negativo">' + aforo_disponible + '</td>';
                        } else {
                            codigo += '<td>' + aforo_disponible + '</td>';
                        }

                        codigo += '<td>' + Number(element.aforo_ocupado) + '</td>';

                        //Mostramos el boton para que se puedan ver las reservas que hay para ese dia (importante la clase "pulsamiento")
                        // codigo += '<td align="center"><a href="index.php?option=com_ereservas&task=visitaform.edit&id=' + element.id + '"><span class="fa fa-edit">&nbsp;</span>Editar Visita</a></td>';
                        codigo += '<td align="center"><button id="cambiarvisitabutton" data-valor=' + element.id + ' data-bs-toggle="modal" data-bs-target="#cambiovisitamodal" ><span class="fa fa-edit"></span>Editar visita</button></td>';
                        if (element.aforo_ocupado == 0 && element.cancelaciones == null) {
                            codigo += '<td align="center"><a class="borrar-visita" id="' + element.id + '"><span class="fa fa-ban">&nbsp;</span>Borrar Visita</a></td>';
                        } else {
                            codigo += '<td align="center"><em>--</em></td>'
                        }
                        codigo += '</tr>';
                    });

                    codigo += "</tbody></table>";

                    //Imprimimos el codigo html en el id #eventos
                    jQuery('#eventos').html(codigo);
                    controlarNegativos();
                    desactivarLoader();
                },
                error: function(data) {
                    alert("Ha habido un error de conexion al realizar la tarea Pintar eventos");
                    desactivarLoader();
                }
            });
        }
    }

    /*********************************************************************************************
     * Funcion donde pintamos el calendario con sus fechas en morado en caso de que haya dias    *
     * @param data                                                                               *
     **********************************************************************************************/
    function pintarDiasCalendario(data) {
        activarLoader();
        //Mostramos texto informativo por defecto en la tabla lateral para que para "iniciar" esta aplicacion
        var codigo = "<table><thead><tr><td class='texto-evento'>Listado de Visitas</td></tr></thead>";
        codigo += "<tbody><tr><td class='texto-evento'><em>Pulsa en el calendario para obtener las visitas de un día concreto" +
            "<br/>Los días con visitas están marcados con color.</em></td></tr></tbody></table>";

        resultado = data.data;

        resultado.forEach(function(element) {
            //Comprobamos si esta dentro del mes actual
            var fecha_comparar = element.fecha.split("-", 2).join("-");
            var fecha_calendario_comparar = calendar.getDate().toISOString().split("-", 2).join("-");
            if (fecha_comparar == fecha_calendario_comparar) {
                //ahora miramos dia por dia
                var fecha_comparar_dia = element.fecha.split(" ", 1).join("-");
                //Le añadimos clase a todos aquellos dias a los que coinciden con las visitas
                jQuery("[data-date=" + fecha_comparar_dia + "]").addClass("tienereserva");
                //generamos el codigo para que la seccion lateral de todos los eventos que hay en el mes
                //codigo+='<a class="pulsamiento" href="#" id="'+element.id+'">' + element.nombre + '-' + element.fecha + '</a><br>';
            } else {
                "No hay eventos para este mes";
            }
        })
        jQuery('#eventos').html(codigo);
        controlarNegativos();
        desactivarLoader();
    }

    /*****************************************************************************
     * Funcion donde mostramos los cierres, para luego mostrarlos de colores     *
     * en el calendario, diferenciando asi cada uno del tipo de cierre           *
     *****************************************************************************/

    function PintarCierres() {
        activarLoader();
        url =comprobarLocalhost()+ '/index.php?option=com_ereservas&task=obtenereventos.cierres&format=json';
        jQuery.ajax({
            url: url,
            type: 'POST',
            success: function(data) {
                var cierres = data.data;

                cierres.forEach(function(cierre) {
                    var cierre_inicio = cierre.fecha_inicio.replace(' 00:00:00', '').split("-");
                    var cierre_fin = cierre.fecha_fin.replace(' 00:00:00', '').split("-");

                    //llamamos a la funcion GetCierres, donde formateamos la fecha
                    var fechas_cerrado = getCierres(new Date(cierre_inicio), new Date(cierre_fin));

                    fechas_cerrado.forEach(function(fecha_cierre) {
                        var fecha = fecha_cierre.getFullYear() + '-' + ((fecha_cierre.getMonth() > 8) ? (fecha_cierre.getMonth() + 1) : ('0' + (fecha_cierre.getMonth() + 1))) + '-' + ((fecha_cierre.getDate() > 9) ? fecha_cierre.getDate() : ('0' + fecha_cierre.getDate()));
                        jQuery("[data-date=" + fecha + "]").addClass("tienecierre cierre-" + cierre.tipo_cierre);
                    });
                });
                desactivarLoader();
            },
            error: function(data) {
                alert("Ha habido un error de conexion al realizar la tarea Pintar Cierres");
                desactivarLoader();
            }
        });
    }

    /*****************************************************************************
     * Funcion donde mostramos las reservas que hay para un dia en concreto      *
     * @param datos -> es el id visita que se ha pulsado / recibido              *
     *****************************************************************************/
    function pintarReservas(datos) {
        activarLoader();
        fecha = jQuery("#fechavisitaform").text();
        url =comprobarLocalhost()+ '/index.php?option=com_ereservas&task=obtenereventos.reservas_recibir&format=json';
        jQuery.ajax({
            url: url,
            type: 'POST',
            data: datos,
            success: function(data) {
                reserva = data.data;
                var fechaArr = fecha.split('-');
                var fecha_mostrar = fechaArr[2] + '-' + fechaArr[1] + '-' + fechaArr[0];
                //Creamos la variable codigo donde iremos generando HTML segun se vayan dando los casos
                var codigo = "<table><thead><tr><td class='texto-evento' colspan='14'>Reservas: " + jQuery('#' + datos.visitaid).attr('data-cabecera') + ' - ' + fecha_mostrar + "<a href='./index.php?option=com_ereservas&task=excel.exportarReservas&format=json&idvisita=" + datos.visitaid + "'>Exportar</a> </td></tr>";
                if (reserva.length == 0) {
                    //Si no tenemos reservas confirmados, mostramos el siguiente texto informativo
                    codigo += "<tbody><tr><td class='texto-evento'><em>No hay reservas...</em></td></tr></tbody></table>";
                    jQuery("#mostrar_canceladas").css("display", "none");
                }
                //Si hay reservas, generamos la tabla con las cabeceras
                else {
                    codigo += "<tr class='cabeceras-evento'><td align='center'>Id</td><td align='center'>Nombre</td><td>Email</td><td>Personas</td><td>Precio</td><td>Idioma</td><td>Pagado</td><td>Estado</td><td>Num. Pedido</td><td>Fecha pedido</td><td>Asistencia</td><td>Enviar Pago</td><!--<td>Enviar Confirmacion</td>--><td>Cancelar</td><td align='center'>Edición</td></tr></thead><tbody>"
                    var longitud = reserva.length;
                    var canceladas = 0;
                    reserva.forEach(function(i) {
                        if (i.state == 0) {
                            canceladas++;
                        }
                    })

                    if (reserva.length == canceladas) {
                        codigo += "<td class='texto-evento-canceladas' colspan='14'>Solo hay reservas canceladas, pulsa el boton de <b>mostrar reservas canceladas</b> para verlas</td>";
                    }
                    reserva.forEach(function(element) {

                        //Si la reserva esta confirmada, a esta linea, le ponemos una clase predeterminada para que destaque de las que no
                        if (element.asistencia == 'si') {
                            codigo += '<tr class="asistenciaconfirmada">';
                        } else if (element.state == 0) {
                            codigo += '<tr class="reservacancelada" style="display: none">';
                        } else {
                            codigo += '<tr>';
                        }
                        codigo += '<td><button id="cambiarfechabutton" data-valor="' + element.id + '" data-bs-toggle="modal" data-bs-target="#cambiofechamodal" ><span class="fa fa-calendar"></span></button>' + element.id + '</td>';

                        codigo += '<td>' + element.nombre + '</td>';
                        codigo += '<td>' + element.email + '</td>';
                        codigo += '<td>' + element.personas + '</td>';
                        codigo += '<td>' + element.precio + '</td>';
                        codigo += '<td>' + element.idioma + '</td>';
                        codigo += '<td>' + element.pagado + '</td>';
                        codigo += '<td>' + traducirvalores(element.estado) + '</td>';
                        if (element.num_pedido == null) {
                            codigo += '<td align="center">--</td>';
                        } else {
                            codigo += '<td>' + element.num_pedido + '</td>';
                        }

                        var fecha_pedido_separada = element.fecha_pedido.split(" ");
                        var fechaArr = fecha_pedido_separada[0].split('-');
                        var fecha_mostrar = fechaArr[2] + '-' + fechaArr[1] + '-' + fechaArr[0];
                        codigo += '<td>' + fecha_mostrar + '</td>';

                        //Si la reserva esta no confirmada mostramos la posibilidad de confirmar
                        if (element.asistencia != "si" && element.estado != 'cancelado') {
                            codigo += ' <td><button id="confirmarasistencia" data-valor="' + element.id + '"><span class="fa fa-user">&nbsp;</span>Marcar Asistencia</button></td>';

                            //Si ya está pagado no se puede volver a enviar el mail
                            if (element.pagado != 'no') {
                                codigo += '<td align="center">-</td>';
                            } else {
                                codigo += '<td><button id="enviarpago" data-valor="' + element.id + '"><span class="fa fa-shopping-cart">&nbsp;</span>Enviar Pago</button></td>';
                            }

                            //Si ya está confirmado no se puede volver a solicitar la confirmación
                            if (element.pagado != 'no') {
                                //codigo += '<td align="center">-</td>';
                            } else {
                                //codigo += '<td><button id="enviarconfirmacion" data-valor="' + element.id + '"><span class="fa fa-check">&nbsp;</span>Enviar Confirmacion</button></td>';
                            }

                            codigo += '<td><button id="cancelar" data-valor="' + element.id + '"><span class="fa fa-ban">&nbsp;</span>Cancelar</button></td>';

                        }
                        //Si la asistencia esta confirmada, mostraremos los - para que no se puedan hacer modificaciones
                        else {
                            codigo += '<td align="center">' + element.asistencia + '</td>';
                            codigo += '<td align="center">-</td>';
                            //codigo += '<td align="center">-</td>';
                            codigo += '<td align="center">-</td>';
                        }
                        //Boton de editar reserva
                        // codigo += '<td align="center"><a href="index.php?option=com_ereservas&task=reserva.edit&id=' + element.id + '><span class="fa fa-edit">&nbsp;</span>Editar Reserva</a></td>';
                        if (element.observaciones != '') {
                            codigo += '<td> <button id="editarreserva" data-valor="' + element.id + '" ><span class="fa fa-edit">&nbsp;</span>Editar Reserva</button> <span class="badge bg-danger">!</span></td>';
                        } else {
                            codigo += '<td><button id="editarreserva" data-valor="' + element.id + '"><span class="fa fa-edit">&nbsp;</span>Editar Reserva</button></td>';
                        }
                        codigo += "</tr>";
                    });

                    codigo += '</tbody></table>';

                    codigo += '<div id="idvisitareservas" style="display: none">' + datos.visitaid + '</div>';
                    (canceladas > 0) ? jQuery("#mostrar_canceladas").css("display", "block"): jQuery("#mostrar_canceladas").css("display", "none");
                }

                //Insertamos el codigo
                jQuery("#resultado").html(codigo);
                jQuery("#jform_idvisita").val(datos.visitaid);
                controlarNegativos();
                vaciarValores();
                desactivarLoader();

            },
            error: function(data) {
                alert("Ha habido un error de conexion al realizar la tarea Pintar reservas");
                desactivarLoader();
            }
        })
    }

    /*****************************************************************************
     * Funcion donde borramos las visitas que hay dado el id de esta misma       *
     * @param datos -> es el id visita que se ha pulsado / recibido              *
     *****************************************************************************/

    function borrarVisita(datos) {
        activarLoader();

        var fecha = jQuery("#jform_fecha").val();
        url = comprobarLocalhost()+ '/index.php?option=com_ereservas&task=obtenereventos.borrarvisita&format=json;';
        jQuery.ajax({
            url: url,
            type: 'POST',
            data: datos,
            success: function(data) {
                pintarEventos(fecha);
                desactivarLoader();
            },
            error: function(data) {
                alert("Ha habido un error de conexion al realizar la tarea Borrar visita");
                desactivarLoader();
            }
        })
    }


    /**
     * Seccion donde tenemos los eventos de los botones de la reserva
     */

    /**
     * Al pulsar sobre el boton de enviar pago
     */
    jQuery('#resultado').on('click', '#enviarpago', function(event) {
        activarLoader();
        event.preventDefault();
        var reservaid = jQuery(this).attr('data-valor');
        var datos = {};
        var visita = {};
        visita.visitaid = jQuery("#idvisitareservas").text();
        datos.reservaid = reservaid;
        datos.visitaid = visita.visitaid;
        pintarReservas(visita);
        url =comprobarLocalhost()+ '/index.php?option=com_ereservas&task=obtenereventos.enviarpago&format=json';
        jQuery.ajax({
            url: url,
            type: 'POST',
            data: datos,
            success: function(data) {
                var fecha = jQuery("#fechavisitaform").text();
                pintarEventos(jQuery("#jform_fecha").val());
                pintarReservas(visita);
                desactivarLoader();
            },
            error: function(data) {
                alert("Ha habido un error de conexion al realizar la tarea Enviar pago");
                desactivarLoader();
            }
        });
    });

    /**
     * Al pulsar sobre el boton de editar fecha de la reserva (al lado del id)
     */
    jQuery('#resultado').on('click', "#cambiarfechabutton", function(event) {
        var reservaid = jQuery(this).attr('data-valor');
        jQuery("#nueva-fecha-reserva-modal").val("");
        jQuery("#id-reserva-modal").val(reservaid);
        var datos = {};
        datos.reservaid = reservaid;
        jQuery.ajax({
           url:comprobarLocalhost()+ "/index.php?option=com_ereservas&task=obtenereventos.getreserva&format=json",
            type: 'POST',
            data: datos,
            success: function(data) {
                var reserva = data.data;
                jQuery("#nombre-reserva-modal").attr({
                    placeholder: reserva.nombre,
                    value: reserva.nombre
                });
                jQuery("#email-reserva-modal").attr({
                    placeholder: reserva.email,
                    value: reserva.email
                });
                cambiosFechaModal(reservaid);
            },
            error: function(data) {
                alert("Ha habido un error imprimiendo los valores de la reserva")
            }
        });
    });

    /**
     * Funcion que ejecutamos cuando la fecha de dentro de la modal se cambia, lo que hacemos es traer las visitas
     * de ese dia
     * @param idreserva -> Saber el id reserva que estamos modificando
     */

    function cambiosFechaModal(idreserva) {
        jQuery("#nueva-fecha-reserva-modal").change(function() {
            var fecha = jQuery(this).val();
            var datos = {};
            datos.dia = fecha;
            jQuery.ajax({
               url:comprobarLocalhost()+ "/index.php?option=com_ereservas&task=obtenereventos.visitasdias&format=json",
                type: 'POST',
                data: datos,
                success: function(data) {
                    var visita_dia = data.data;

                    //empezamos a generar el codigo que tendra la tabla que mostrara las visitas
                    var codigo = "<table><thead><tr><td class='texto-evento' colspan='6'>Visitas para el dia " + fecha + "</td></tr>";
                    codigo += "<tr class='cabeceras-evento'><td>Hora</td><td>Idioma</td><td>Visita</td><td>Disponible</td><td align='center'>Cambiar</td></tr></thead><tbody>"

                    //por cada visita que haya, crearemos un row en la tabla, mostrando los datos
                    visita_dia.forEach(function(element) {
                        if (element.idioma == null) element.idioma = "Español";
                        codigo += '<tr>';

                        //mostramos el boton para que se puedan ver las reservas que hay para ese dia (importante la clase "pulsamiento")
                        codigo += '<td>' + element.hora_inicio.substring(0, 5) + '</td>';
                        codigo += '<td>' + element.idioma + '</td>';
                        codigo += '<td>' + element.nombre + '</td>';

                        //Hacemos este calculo para marcar las plazas disponibles, siempre vamos a coger el valor maximo entre el aforo_privado o el aforo_web y lo restaremos al aforo ocupado, ese resultado se mostrara
                        var aforo_disponible = (Number(Math.max(element.aforo_privado, element.aforo_web)) - Number(element.aforo_ocupado));
                        if (aforo_disponible < 0) {
                            codigo += '<td class="aforo-negativo" align="center">' + aforo_disponible + '</td>';
                        } else {
                            codigo += '<td align="center">' + aforo_disponible + '</td>';
                        }
                        codigo += '<td align="center"><a class="pulsamiento-nueva-fecha" id="' + element.id + '" data-reserva="' + idreserva + '"><span class="fa fa-exchange-alt">&nbsp;</span> Cambiar</a></td>';

                        codigo += '</tr>';
                    });

                    codigo += "</tbody></table>";

                    //Imprimimos el codigo html en el id #eventos
                    jQuery('#nueva-disponibilidad').html(codigo);
                },
                error: function(data) {
                    alert("Error mostrando las visitas en la modal con la fecha señalada")
                }
            });
        })
    }

    /**
     * Funcion que se ejecuta cuando pulsamos el boton de cambiar de fecha dentro de la modal
     * Lo que hacemos es modificar el id visita al seleccionado de dicha reserva.
     * Si ha ido ok, cerramos la modal y pintamos los eventos de nuevo
     */
    jQuery('#nueva-disponibilidad').on('click', '.pulsamiento-nueva-fecha', function(event) {
        var id_visita = jQuery(this).attr("id");
        var id_reserva = jQuery(this).attr("data-reserva");
        var datos = {};
        datos.id_visita = id_visita;
        datos.id_reserva = id_reserva;
        jQuery.ajax({
           url:comprobarLocalhost()+ "/index.php?option=com_ereservas&task=obtenereventos.cambiardiareserva&format=json",
            type: 'POST',
            data: datos,
            success: function(event) {
                jQuery("#cambiofechamodal").modal("hide");
                fecha = jQuery("#fechavisitaform").text();
                pintarEventos(fecha);
                //pintarReservas(id_visita);
                vaciarValores();
            },
            error: function(event) {
                alert("Ha habido un error a la hora de actualizar la nueva reserva")
            }
        });
    })



    /**
     * Al pulsar sobre el boton de enviar Confirmacion
     */
    jQuery('#resultado').on('click', '#enviarconfirmacion', function(event) {
        activarLoader();

        event.preventDefault();
        var reservaid = jQuery(this).attr('data-valor');
        var datos = {};
        var visita = {};
        visita.visitaid = jQuery("#idvisitareservas").text();
        datos.reservaid = reservaid;
        datos.visitaid = visita.visitaid;
        pintarReservas(visita);
        var url=comprobarLocalhost()+ '/index.php?option=com_ereservas&task=obtenereventos.enviarconfirmacion&format=json';
        jQuery.ajax({
            url: url,
            type: 'POST',
            data: datos,
            success: function(data) {
                alert(data);
                var fecha = jQuery("#fechavisitaform").text();
                pintarEventos(fecha);
                pintarReservas(visita);
                desactivarLoader();
            },
            error: function(data) {
                alert("Ha habido un error de conexion al realizar la tarea Enviar confirmacion");
                desactivarLoader();
            }
        });
    });

    /**
     * Al pulsar sobre el boton de Confirmar asistencia
     */
    jQuery('#resultado').on('click', '#confirmarasistencia', function(event) {
        activarLoader();

        event.preventDefault();
        reservaid = jQuery(this).attr('data-valor');
        datos = {};
        visita = {};
        visita.visitaid = jQuery("#idvisitareservas").text();
        datos.reservaid = reservaid;
        datos.visitaid = visita.visitaid;
        pintarReservas(visita);
        url =comprobarLocalhost()+ '/index.php?option=com_ereservas&task=obtenereventos.confirmarasistencia&format=json';
        jQuery.ajax({
            url: url,
            type: 'POST',
            data: datos,
            success: function(data) {
                fecha = jQuery("#fechavisitaform").text();
                pintarEventos(fecha);
                pintarReservas(visita);
                jQuery("#enviarpago").attr('disabled', true);
                jQuery("#cancelar").attr('disabled', true);
                jQuery("#enviarconfirmacion").attr('disabled', true);
                desactivarLoader();
            },
            error: function(data) {
                alert("Ha habido un error de conexion al realizar la tarea Confirmar asistencia");
                desactivarLoader();
            }
        });

    });

    /**
     * Al pulsar sobre el boton de cancelar asistencia
     */
    function cancelarReserva(datos) {
        activarLoader();
        reservaid = datos.reservaid;
        datos = {};
        visita = {};
        visita.visitaid = jQuery("#idvisitareservas").text();

        datos.reservaid = reservaid;
        datos.visitaid = visita.visitaid;
        pintarReservas(visita);
        url =comprobarLocalhost()+ '/index.php?option=com_ereservas&task=obtenereventos.cancelar&format=json';
        jQuery.ajax({
            url: url,
            type: 'POST',
            data: datos,
            success: function(data) {
                fecha = jQuery("#fechavisitaform").text();
                pintarEventos(fecha);
                pintarReservas(visita);
                desactivarLoader();
            },
            error: function(data) {
                alert("Ha habido un error de conexion al realizar la tarea Cancelar Reservas");
                desactivarLoader();
            }
        });
    }


    /**
     * Funcion para traducir los valores que tenemos en la base de datos para asi ahorrar lineas de codigo,
     * @param estado -> es la variable que queremos "traducir"
     */
    function traducirvalores(estado) {
        switch (estado) {
            case "pendiente":
                return "Pendiente";
                break;
            case "pendiente-pago":
                return "Esperando Pago Online";
                break;
            case "pendiente-conf":
                return "Esperando Confirmación";
                break;
            case "confirmado":
                return "Confirmado";
                break;
            case "cancelado":
                return "Cancelado";
                break;
        }
    }

    /**
     * Funcion donde controlamos los negativos que puedan haber en el formulario, marcandolos asi como null
     */
    function controlarNegativos() {
        jQuery(':input[type="number"]').change(function() {
            if (this.value < 1) this.value = null;
        });

    }
    /**
     * Funcion donde traemos los datos de la reserva para mostrarlas en la modal para editar
     * Se ejecuta cuando pulsamos el boton de editar reserva
     */
    jQuery('#resultado').on('click', '#editarreserva', function(event) {
        activarLoader();

        jQuery('#cambio-personas-mensaje').hide();
        reservaid = jQuery(this).attr('data-valor');
        var datos = {};
        datos.reservaid = reservaid;
        jQuery.ajax({
           url :comprobarLocalhost()+ "/index.php?option=com_ereservas&task=obtenereventos.getreserva&format=json",
            type: 'POST',
            data: datos,
            success: function(data) {
                var reserva = data.data;
                jQuery("#formularioreserva h3").html("Editar reserva de " + reserva.nombre);

                jQuery("#jform_idreserva").val(reserva.id);
                jQuery("#id-visita").val(reserva.id_visita);
                jQuery("#jform_nombre").attr({
                    placeholder: reserva.nombre,
                    value: reserva.nombre
                });
                jQuery("#jform_email").attr({
                    placeholder: reserva.email,
                    value: reserva.email
                });
                jQuery("#jform_telefono").attr({
                    placeholder: reserva.telefono,
                    value: reserva.telefono
                });
                jQuery("#jform_codigopostal").attr({
                    placeholder: reserva.cpostal,
                    value: reserva.cpostal
                });
                jQuery("#jform_personas").attr({
                    placeholder: reserva.personas,
                    value: reserva.personas
                });
                jQuery("#jform_precio").attr({
                    placeholder: reserva.precio,
                    value: reserva.precio
                });
                jQuery("#jform_pais").chosen();
                jQuery("#jform_pais").val(reserva.id_pais);
                jQuery("#jform_pais").trigger("liszt:updated");

                jQuery("#jform_usuario").chosen();
                jQuery("#jform_usuario").val(reserva.id_usuario);
                jQuery("#jform_usuario").trigger("liszt:updated");

                jQuery("#jform_idioma").chosen();
                jQuery("#jform_idioma").val(reserva.id_idioma);
                jQuery("#jform_idioma").trigger("liszt:updated");

                jQuery("#jform_tipo_cliente").chosen();
                jQuery("#jform_tipo_cliente").val(reserva.id_tipo_cliente);
                jQuery("#jform_tipo_cliente").trigger("liszt:updated");

                jQuery('#jform_pagado').chosen();
                jQuery('#jform_pagado').val(reserva.pagado);
                jQuery('#jform_pagado').trigger("liszt:updated");

                /**
                 * Comentamos lo siguiente para que no se bloquee el campo precio si ya esta pagado
                 */
                // if(reserva.pagado != "no"){
                //     jQuery("#jform_pagado").prop('disabled', 'disabled');
                //     jQuery("#jform_precio").prop('disabled', 'disabled');
                //     jQuery("#jform_pagado").trigger( "liszt:updated" );
                // }else{
                //     jQuery("#jform_pagado").removeAttr("disabled");
                //     jQuery("#jform_precio").removeAttr("disabled");
                //     jQuery("#jform_pagado").trigger( "liszt:updated" );
                // }


                jQuery("#jform_asistencia").chosen();
                jQuery("#jform_asistencia").val(reserva.asistencia);
                jQuery("#jform_asistencia").trigger("liszt:updated");

                jQuery('#jform_estado').chosen();
                jQuery('#jform_estado').val(reserva.estado);
                jQuery('#jform_estado').trigger("liszt:updated");

                jQuery("#jform_observaciones").attr({
                    placeholder: reserva.observaciones,
                    value: reserva.observaciones
                });
                //Cargamos las funciones de cambio para detectar cambios en los inputs de personas y precio

                cambiosPersonas(reserva.personas);
                jQuery('#cambio-precio-mensaje').hide();
                jQuery("#editando-reserva-mensaje").show();
                jQuery("#editando-reserva-mensaje #reserva_id_alerta").html(reserva.id);
                cambiosPrecio(reserva.precio);
                bloquearPrecio();
                desactivarLoader();
            },
            error: function(data) {
                alert("Ha habido un error de conexion al realizar la tarea Resetear Reservas")
            }
        })
    })

    /**
     * Funcion para mandar los datos de la reserva que hemos editado en la modal
     * Se ejecuta cuando se pulsa el boton de enviar dentro de la modal
     */
    function editarreserva(valores) {
        activarLoader();

        //Traemos los valores del formulario
        var idvisitaenviar = {};
        idvisitaenviar.visitaid = datos.visitaid;
        jQuery.ajax({
           url :comprobarLocalhost()+ "/index.php?option=com_ereservas&task=obtenereventos.editarreserva&format=json",
            type: 'POST',
            data: valores,
            success: function(data) {
                fecha = jQuery("#fechavisitaform").text();
                pintarEventos(fecha);
                pintarReservas(idvisitaenviar);
                vaciarValores();
                desactivarLoader();
            },
            error: function(data) {
                alert("Ha habido un error de conexion al realizar la tarea Editar Reserva");
                desactivarLoader();
            }
        });
    }

    jQuery('#eventos').on('click', '.delete-visita ', function(event) {

    });

    /**************************************************************************************
     * Funcion donde formateamos y calculamos los dias de diferencia dadas las fechas     *
     * @param dia_inicio -> es el 1º dia del cierre                                       *
     * @param dia_fin    -> es el ultimo dia del cierre                                   *
     *************************************************************************************/
    function getCierres(dia_inicio, dia_fin) {
        var cierres = [],
            diaactual = dia_inicio,
            addDays = function(dias) {
                var dia = new Date(this.valueOf());
                dia.setDate(dia.getDate() + dias);
                return dia;
            };
        while (diaactual <= dia_fin) {
            cierres.push(diaactual);
            diaactual = addDays.call(diaactual, 1);
        }
        return cierres;
    }

    /**************************************************************************************
     * Funcion donde traemos los datos de la visita que son necesarios para modificar     *
     *************************************************************************************/

    jQuery('#eventos').on('click', '#cambiarvisitabutton', function(event) {
        var datos = {};
        datos.visitaid = jQuery(this).attr("data-valor");
        getvisita(datos);
    });
    function getvisita(datos){
        url =comprobarLocalhost()+ '/index.php?option=com_ereservas&task=obtenereventos.getVisitaFront&format=json;';
        jQuery.ajax({
            url: url,
            type: 'POST',
            data: datos,
            success: function (data) {

                var visita = data.data;
                var fecha_format = visita.fecha.split(" ");
                jQuery("#id-visita-modal").val(visita.id);
                jQuery("#aforo-web-visita-modal").val(visita.aforo_web);
                jQuery("#aforo-privado-visita-modal").val(visita.aforo_privado);
                jQuery("#cambiovisitamodal #visita_fecha").text(fecha_format[0]);
                jQuery("#cambiovisitamodal #visita_hora").text(visita.hora_inicio);
            },
            error: function(data) {
                alert("Ha habido un error trayendo la visita ")
            }
        })
    }
    jQuery("#cambiovisitamodal").on('click', "#EnviarModificacionVisita", function (){
        var datos = {};
        datos.id_visita = jQuery("#id-visita-modal").val();
        datos.aforo_web = jQuery("#aforo-web-visita-modal").val();
        datos.aforo_privado =  jQuery("#aforo-privado-visita-modal").val();
        url =comprobarLocalhost()+ '/index.php?option=com_ereservas&task=obtenereventos.actualizarVisitaAforo&format=json;';
        jQuery.ajax({
            url: url,
            type: 'POST',
            data: datos,
            success: function (data) {
                location.reload();
            },
            error: function(data) {
                alert("Ha habido un error modificando el aforo de la visita")
            }
        })

    })

});

/**
 * Funcion para vaciar todos los valores del formulario de cuando se crea/edita una reserva
 */
function vaciarValores() {

    jQuery("#jform_idreserva").attr({
        value: ""
    });

    jQuery("#jform_nombre").attr({
        placeholder: "Nombre",
        value: ""
    });
    jQuery("#jform_email").attr({
        placeholder: "Email",
        value: ""
    });
    jQuery("#jform_telefono").attr({
        placeholder: "Teléfono",
        value: ""
    });
    jQuery("#jform_codigopostal").attr({
        placeholder: "Código Postal",
        value: ""
    });
    jQuery("#jform_personas").attr({
        placeholder: "Personas",
        value: ""
    });
    jQuery("#jform_precio").attr({
        placeholder: "",
        value: "0"
    });
    jQuery("#jform_observaciones").attr({
        placeholder: "Observaciones",
        value: ""
    });

    jQuery('#jform_usuario').chosen();
    jQuery('#jform_usuario').val("");
    jQuery('#jform_usuario').trigger("liszt:updated");

    jQuery('#jform_pais').chosen();
    jQuery('#jform_pais').val("");
    jQuery('#jform_pais').trigger("liszt:updated");

    jQuery('#jform_idioma').chosen();
    jQuery('#jform_idioma').val("");
    jQuery('#jform_idioma').trigger("liszt:updated");

    jQuery('#jform_tipo_cliente').chosen();
    jQuery('#jform_tipo_cliente').val("");
    jQuery('#jform_tipo_cliente').trigger("liszt:updated");

    jQuery('#jform_pagado').chosen();
    jQuery('#jform_pagado').val("no");
    jQuery('#jform_pagado').trigger("liszt:updated");

    jQuery("#jform_estado").chosen();
    jQuery('#jform_estado').val("pendiente");
    jQuery('#jform_estado').trigger("liszt:updated");

    jQuery("#jform_asistencia").chosen();
    jQuery('#jform_asistencia').val("no");
    jQuery('#jform_asistencia').trigger("liszt:updated");

    jQuery("button.enviarreserva").attr("disabled", false);
    jQuery("#jform_mandarcorreo").prop("checked", false);
    jQuery("#formularioreserva h3").text("Generar una nueva reserva");
    jQuery("#editando-reserva-mensaje #reserva_id_alerta").html("");
    jQuery("#editando-reserva-mensaje").hide();

    cambiosPersonas(0);
    cambiosPrecio(0);

}

/**************************************************************************************
 * Funcion donde comprobamos que el campo personas se cambia, en caso afirmativo,     *
 * se muestra un div informativo                                                      *
 * @param personas -> es el valor que tiene el input personas en ese momento          *
 *************************************************************************************/
function cambiosPersonas(personas) {
    jQuery("#jform_personas").change(function() {
        if (jQuery(this).val() != personas && personas != 0) {
            jQuery('#cambio-personas-mensaje').show();
        } else {
            jQuery('#cambio-personas-mensaje').hide();
        }
    })
}

/**************************************************************************************
 * Funcion donde comprobamos que el campo precio se cambia, en caso afirmativo,       *
 * se muestra un div informativo                                                      *
 * @param precio -> es el valor que tiene el input precio en ese momento              *
 *************************************************************************************/
function cambiosPrecio(precio) {
    jQuery("#jform_precio").change(function() {
        if (jQuery(this).val() != precio && precio != 0) {
            jQuery('#cambio-precio-mensaje').show();
        } else {
            jQuery('#cambio-precio-mensaje').hide();
        }
    })
}

function bloquearPrecio() {
    jQuery("#jform_pagado").change(function() {
        if (jQuery(this).val() != "no") {
            jQuery("#jform_precio").prop('disabled', 'disabled');
        } else {
            jQuery("#jform_precio").removeAttr("disabled");
        }
    })
}

function desactivarLoader() {
    jQuery(".LoaderOverlay").css("display", "none");
}

function activarLoader() {
    jQuery(".LoaderOverlay").css("display", "block");
}


