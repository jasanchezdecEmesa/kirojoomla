document.addEventListener("DOMContentLoaded", function () {
  var idvisita = jQuery("#idvisita").val();
  var calendarEl = document.getElementById("seccion-calendario");
  //Asignamos las principales variables que tendra el objeto calendario
  var calendar = new FullCalendar.Calendar(calendarEl, {
    timeZone: "Europe/Madrid",
    locale: "es",
    plugins: ["interaction", "dayGrid", "momentPlugin"],
    defaultView: "dayGridMonth",
    showNonCurrentDates:false,
    fixedWeekCount:false,
    selectable: true,
    aspectRatio: 1.2,
    firstDay: 1,
    header: {
      left: "prev",
      center: "title",
      right: "next",
    },
    events: {
      url:
        comprobarLocalhost() +
        "/index.php?option=com_ereservas&task=gestionarvisitas.visitas&format=json",
      method: "GET",
      extraParams: {
        idtipovisita: idvisita,
        frontal: "1",
      },
      success: function (data) {
        //Llamamos a la funcion donde controlaremos los cierres que haya
        PintarCierres();
        //Llamamos a la funcion que pondra en morado los dias que tengan una visita
        pintarDiasCalendario(data);
      },
    },
    //Aqui detectamos cuando se hace click en un dia (no hace falta que tenga una visita o no)
    dateClick: function (info) {
      pintarEventos(info.dateStr, idvisita);
    },
  });
  calendar.render();

  /*********************************************************************************************
   * Funcion donde pintamos el calendario con sus fechas en morado en caso de que haya dias    *
   * @param data                                                                               *
   **********************************************************************************************/
  function pintarDiasCalendario(data) {
    resultado = data.data;

    var disponible = 0;
    var dia_comparar = "";
    resultado.forEach(function (element, key) {
      var fecha = element.fecha.split(" ", 1).join("-");

      var diff = element.aforo_web - element.aforo_ocupado;

      //si estamos en el mismo dia que el anterior
      if (dia_comparar == fecha) {
        if (diff > 0) {
          disponible += diff;
        }
      } else {
        //Cuando cambiemos de fecha, añadimos la clase oportuna a la fecha anterior
        if (disponible > 0) {
          jQuery("[data-date=" + dia_comparar + "]").addClass("tienereserva");
        } else if (dia_comparar !== "") {
          jQuery("[data-date=" + dia_comparar + "]").addClass("sinplazas");
        }
        //TODO: en la fecha anterior intentar pintar la cantidad de plazas disponibles

        //Al cambiar de día reseteamos el contador, si la diferencia de este primer día es mayor que cero, la contabilizamos
        disponible = 0;
        if (diff > 0) {
          disponible = diff;
        }
      }
      dia_comparar = fecha;
    });

    //Al acabar el bucle, hacemos la misma operación para la última fecha y disponible registrado
    if (disponible > 0) {
      jQuery("[data-date=" + dia_comparar + "]").addClass("tienereserva");
    } else {
      jQuery("[data-date=" + dia_comparar + "]").addClass("sinplazas");
    }

    jQuery("#eventos").html(codigo);
    controlarNegativos();
  }

  /************************************************************************
   * Funcion donde creamos la tabla de las visitas que hay en ese dia     *
   ************************************************************************/
  function pintarEventos(fecha, idtipovisita) {
    jQuery("#eventos").empty();

    var date = new Date(fecha + "T00:00:00");
    var fechaArr = fecha.split("-");
    var fecha_mostrar = fechaArr[2] + "-" + fechaArr[1] + "-" + fechaArr[0];

    //si el dia que hemos pulsado no tiene la clase "tienereserva", significara que no hay visitas para ese dia, por lo que mostramos este mensaje en el lateral
    if (
      jQuery("[data-date=" + fecha + "]").hasClass("tienereserva") == false &&
      jQuery("[data-date=" + fecha + "]").hasClass("sinplazas") == false
    ) {
      if (
        jQuery("[data-date=" + fecha + "]").hasClass("cierre-cierre") === true
      ) {
        jQuery("#eventos")
          .empty()
          .append(
            "<h4>Vistas para el día " +
              fecha_mostrar +
              "</h4><p><em>El restaurante permanecerá cerrado el día seleccionado.</em></p>"
          );
      } else {
        jQuery("#eventos")
          .empty()
          .append(
            "<h4>Vistas para el día " +
              fecha_mostrar +
              "</h4><p><em>No hay visitas...</em></p>"
          );
      }

      jQuery("#adultos").hide();

      jQuery("html, body").animate(
        {
          scrollTop: jQuery("#eventos").offset().top - 100,
        },
        500
      );
    }
    //en caso de que haya, preparamos la funcion ajax para traer las visitas
    else {
      var datos = {};
      datos.idtipovisita = idtipovisita;
      datos.dia = fecha;
      jQuery.ajax({
        url:
          comprobarLocalhost() +
          "/index.php?option=com_ereservas&task=gestionarvisitas.visitasdias&format=json",
        type: "POST",
        data: datos,
        success: function (data) {
          var visita_dia = data.data;

          //empezamos a generar el codigo que tendra la tabla que mostrara las visitas
          var urlweb = window.location.href;

          var palabra = "HORARIOS";
          if (urlweb.search("/en/") > 0) {
            palabra = "SCHEDULE";
          }

          var datosEnviarPersonas = {};

          var codigo =
            "<div><h3>" +
            palabra +
            "</h3></div><table class='visitas-publicas'><thead><tr><td class='texto-evento' colspan='8'>Visitas programadas para el día " +
            fecha_mostrar +
            "</td></tr>";
          codigo +=
            "<tr class='cabeceras-evento'><td>Hora</td><td>Disponibilidad</td></tr></thead><tbody>";

          //por cada visita que haya, crearemos un row en la tabla, mostrando los datos
          visita_dia.forEach(function (element) {
            //Hacemos este calculo para marcar las plazas disponibles, siempre vamos a coger el valor maximo entre el aforo_privado o el aforo_web y lo restaremos al aforo ocupado, ese resultado se mostrara
            var aforo_disponible =
              Number(element.aforo_web) - Number(element.aforo_ocupado);

            if (element.idioma == null) element.idioma = "Español";

            codigo +=
              aforo_disponible <= 0 ? '<tr class="table-danger">' : "<tr>";

            codigo += "<td>" + element.hora_inicio.substring(0, 5) + "</td>";

            codigo +=
              aforo_disponible <= 0
                ? '<td align="center" class="aforo-negativo">Lleno</td>'
                : '<td align="center"><a class="pulsamiento" id="' +
                  element.id +
                  '" data-cabecera="' +
                  element.hora_inicio.substring(0, 5) +
                  " - " +
                  element.nombre +
                  '" data-personas="' +
                  aforo_disponible +
                  '">' +
                  aforo_disponible +
                  " Plaza/s</a></td>";

            codigo += "</tr>";
            if(aforo_disponible > 0){
              datosEnviarPersonas.visitaid = element.id;
              datosEnviarPersonas.personas = aforo_disponible;
              //Mandamos abrir el selector de personas
              pintarPersonas(datosEnviarPersonas);
            }
          });

          codigo += "</tbody></table>";

          //Imprimimos el codigo html en el id #eventos
          jQuery("#eventos").html(codigo);

          jQuery("html, body").animate(
            {
              scrollTop: jQuery("#eventos").offset().top - 100,
            },
            500
          );

          controlarNegativos();
        },
        error: function (data) {
          console.log("error");
        },
      });
    }
  }

  jQuery("#eventos").on("click", ".pulsamiento", function () {
    jQuery("#resultado").empty();
    jQuery("#formularioreserva").css("display", "block");
    datos = {};

    //preparamos aqui el array de datos para mandarlo directamente a la funcion y asi no tener que tratarla dentro de esta misma
    datos.visitaid = jQuery(this).attr("id");
    datos.personas = jQuery(this).attr("data-personas");
    pintarPersonas(datos);
  });

  function pintarPersonas(datos) {
    jQuery("#adultos").show();
    jQuery("#jform_id_tipo_visita").val(datos.visitaid);

    jQuery("input[name='adultos']").TouchSpin({
      min: 1,
      max: datos.personas,
      stepinterval: 1,
      maxboostedstep: false,
      buttondown_class: "btn btn-secondary boton-adultos",
      buttonup_class: "btn btn-secondary boton-adultos",
    });
    jQuery("input[name='adultos']").trigger("touchspin.updatesettings", {
      max: datos.personas,
    });
  }

  jQuery("#adultos").on("click", "#enviar-info", function () {
    var datos = {};

    datos.visitaid = jQuery("#jform_id_tipo_visita").val();
    datos.personas = jQuery("#jform_adultos").val();

    jQuery.ajax({
      url:
        comprobarLocalhost() +
        "/index.php?option=com_ereservas&task=gestionarvisitas.insertarvisita&format=json",
      type: "POST",
      data: datos,
      success: function (data) {
        var error = data.data;
        if (error) {
          Joomla.renderMessages({
            error: ["El campo personas excede el máximo disponible"],
          });
          throw new Error(
            "Hay apuntadas mas personas que la reserva puede aceptar"
          );
        } else {
          pintarReservas(datos);
        }
      },
      error: function (data) {
        console.log(data);
      },
    });
  });

  function pintarReservas(datos) {
    jQuery("#jform_adultos").attr("disabled", "disabled");
    jQuery("#campos-reserva").show();
    jQuery("html, body").animate(
      {
        scrollTop: jQuery("#campos-reserva").offset().top - 200,
      },
      500
    );
  }

  jQuery("#campos-reserva").on("click", "#enviar-reserva", function () {
    //Comprobamos correos
    var correoOriginal = jQuery("#jform_email_reserva").val();
    var confirmarCorreo = jQuery("#jform_email_reserva-2").val();
    
    console.log(correoOriginal, confirmarCorreo);

    if (correoOriginal === confirmarCorreo) {
      //cogemos los datos
      var datos = {};
      var precio = jQuery("#jform_precio").val();

      datos.visitaid = jQuery("#jform_id_tipo_visita").val();
      datos.personas = jQuery("#jform_adultos").val();
      datos.nombre = jQuery("#jform_nombre_reserva").val();
      datos.correo = jQuery("#jform_email_reserva").val();
      datos.telefono = jQuery("#jform_telefono_reserva").val();
      datos.pais = jQuery("#jform_pais_reserva").val();
      datos.cpostal = jQuery("#jform_cpostal_reserva").val();
      datos.municipio = jQuery("#jform_municipio_reserva").val();
      datos.observaciones = jQuery("#jform_observaciones_reserva").val();
      datos.preciototal = parseInt(precio * datos.personas);

      if (
        jQuery("#jform_nombre_reserva").val() == "" ||
        jQuery("#jform_email_reserva").val() == "" ||
        jQuery("#jform_telefono_reserva").val() == "" ||
        jQuery("#jform_pais_reserva").val() == "" ||
        jQuery("jform_cpostal_reserva").val() == ""
      ) {
        jQuery("#system-message-container").addClass("completa");
        Joomla.renderMessages({
          error: ["Por favor rellena los campos del formulario"],
        });
        jQuery("#enviarreserva").attr("disabled", false);
        jQuery("#system-message-container").on("click", function () {
          jQuery(this).empty().removeClass("completa");
        });
      } else {
        jQuery.ajax({
          url:
            comprobarLocalhost() +
            "/index.php?option=com_ereservas&task=gestionarvisitas.insertarreserva&format=json",
          type: "GET",
          data: datos,
          success: function (data) {
            var datos_recibidos = data.data;
            console.log(datos_recibidos.token);
            location.replace(
              comprobarLocalhost() +
                "/index.php?option=com_ereservas&view=pagoform&reservaidcorreo=" +
                datos_recibidos.id_insertado +
                "&token=" +
                datos_recibidos.token +
                "&idioma_web=" +
                datos_recibidos.idioma_web
            );
          },
          error: function (data) {
            console.log(data);
          },
        });
      }
    } else {
      jQuery("#mensaje_error_correo_validacion").show();
    }
  });

  /*****************************************************************************
   * Funcion donde mostramos los cierres, para luego mostrarlos de colores     *
   * en el calendario, diferenciando asi cada uno del tipo de cierre           *
   *****************************************************************************/

  function PintarCierres() {
    console.log("cierres");
    url =
      comprobarLocalhost() +
      "/index.php?option=com_ereservas&task=gestionarvisitas.cierres&format=json";
    jQuery.ajax({
      url: url,
      type: "POST",
      success: function (data) {
        var cierres = data.data;

        cierres.forEach(function (cierre) {
          var cierre_inicio = cierre.fecha_inicio
            .replace(" 00:00:00", "")
            .split("-");
          var cierre_fin = cierre.fecha_fin.replace(" 00:00:00", "").split("-");

          //llamamos a la funcion GetCierres, donde formateamos la fecha
          var fechas_cerrado = getCierres(
            new Date(cierre_inicio),
            new Date(cierre_fin)
          );

          fechas_cerrado.forEach(function (fecha_cierre) {
            var fecha =
              fecha_cierre.getFullYear() +
              "-" +
              (fecha_cierre.getMonth() > 8
                ? fecha_cierre.getMonth() + 1
                : "0" + (fecha_cierre.getMonth() + 1)) +
              "-" +
              (fecha_cierre.getDate() > 9
                ? fecha_cierre.getDate()
                : "0" + fecha_cierre.getDate());
            jQuery("[data-date=" + fecha + "]").addClass(
              "tienecierre cierre-" + cierre.tipo_cierre
            );
          });
        });
      },
    });
  }

  /**
   * Funcion donde controlamos los negativos que puedan haber en el formulario, marcandolos asi como null
   */
  function controlarNegativos() {
    jQuery(':input[type="number"]').change(function () {
      if (this.value < 1) this.value = null;
    });
  }

  /**************************************************************************************
   * Funcion donde formateamos y calculamos los dias de diferencia dadas las fechas     *
   * @param dia_inicio -> es el 1º dia del cierre                                       *
   * @param dia_fin    -> es el ultimo dia del cierre                                   *
   *************************************************************************************/
  function getCierres(dia_inicio, dia_fin) {
    var cierres = [],
      diaactual = dia_inicio,
      addDays = function (dias) {
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
});
