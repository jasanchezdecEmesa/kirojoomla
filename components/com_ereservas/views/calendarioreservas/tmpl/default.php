<?php
/**
 * @version    CVS: 1.0.0
 * @package    Com_Ereservas
 * @author     Equipos Mecanizados,S.L. <rsainz@emesa.com>
 * @copyright  2019 Equipos Mecanizados,S.L.
 * @license    Licencia Pública General GNU versión 2 o posterior. Consulte LICENSE.txt
 */
// No direct access
defined('_JEXEC') or die;

JHtml::addIncludePath(JPATH_COMPONENT . '/helpers/html');
JHtml::_('bootstrap.tooltip');
JHtml::_('behavior.multiselect');
JHtml::_('formbehavior.chosen', 'select');
$document = JFactory::getDocument();
$document->addScript('media/com_ereservas/js/corecalendar.js?v=1');
$document->addScript('media/com_ereservas/js/maindaygrid.js?v=12');
$document->addScript('media/com_ereservas/js/interaction.js?v=12');
$document->addScript('media/com_ereservas/js/calendar.js?v=1');
$document->addScript('media/com_ereservas/js/momentmain.js');
$document->addScript('media/com_ereservas/js/locales/es.js?v=12');
$document->addStyleSheet('media/com_ereservas/css/maindaygrid.css');
$document->addStyleSheet('media/com_ereservas/css/maincore.css');
$document->addStyleSheet('media/com_ereservas/css/calendario.css?v=5');
$user       = JFactory::getUser();
$userId     = $user->get('id');
$canCreate  = $user->authorise('core.create', 'com_ereservas') && file_exists(JPATH_COMPONENT . DIRECTORY_SEPARATOR . 'models' . DIRECTORY_SEPARATOR . 'forms' . DIRECTORY_SEPARATOR . 'reservaform.xml');
$canEdit    = $user->authorise('core.edit', 'com_ereservas') && file_exists(JPATH_COMPONENT . DIRECTORY_SEPARATOR . 'models' . DIRECTORY_SEPARATOR . 'forms' . DIRECTORY_SEPARATOR . 'reservaform.xml');
$canCheckin = $user->authorise('core.manage', 'com_ereservas');
$canChange  = $user->authorise('core.edit.state', 'com_ereservas');
$canDelete  = $user->authorise('core.delete', 'com_ereservas');
$tipovisitas= $this->gettipovisita();
$idiomas= $this->getidiomas();
$usuarios= $this->getusers();
$paises= $this->getpais();
$clientes= $this->tipocliente();
?>
<div class="LoaderOverlay" style="display: none">
    <div class="loader"></div>
    <h1>Cargando...</h1>
</div>
<div class="margen">
	<h1>Gestión de Reservas</h1>
    <div class="leyenda">
        <h3>Leyenda de colores del calendario</h3>
        <div class="texto">
        <p>
            <span><span class="color tienereserva">&nbsp;</span> Día con visitas.</span>
            <!--<span class="esizd"><span class="color cierre-festivo-laboral">&nbsp;</span> Festivo laboral.</span>-->
            <span class="esizd"><span class="color tienereserva cierre-festivo-laboral">&nbsp;</span> Festivo laboral con visitas.</span>
            <span class="esizd"><span class="color cierre-cierre">&nbsp;</span> Día de cierre.</span>
            <span class="esizd"><span class="color tienereserva cierre-cierre"> </span> Día de cierre con visitas disponibles.</span>
            <span class="esizd"><span class="color cierre-sin-visita">&nbsp;</span> Día de cierre con tienda abierta.</span>
        </p>
        </div>
    </div>
	<div class="row">
		<div class="col-lg-6 col-xl-4">
			<div id="calendar"></div>
		</div>
		<div class="col-lg-6 col-xl-8">
			<div id="eventos"></div>
			<div id="formulariovisita">
				<h3>Generar una nueva visita  <span id="fechavisita"><em>(elija un día del calendario)</em></span><span id="fechavisitaform"></span></h3>
				<form id="form-guia" action="#" method="post">
					<div class="row">
						<div class="col-sm-5">
							<label id="jform_tipovisita-lbl" for="jform_tipovisita">Tipo Visita</label>
							<select class="form-control" name="jform[tipovisita]" id="jform_tipovisita">
								<option></option>
								<?php foreach($tipovisitas as $key => $tipovisita):?>
									<option value="<?php echo $tipovisita['id'];?>"><?php echo $tipovisita["nombre"]; ?></option>
								<?php endforeach; ?>
							</select>
						</div>
						<div class="col-sm-2">
							<label id="jform_hora-lbl" for="jform_hora">Hora</label>
							<input type="time" name="jform[hora]" id="jform_hora" value="" class="form-control" placeholder="hora">
						</div>
                        <div class="col-sm-3">
                            <label id="jform_idioma-lbl" for="jform_idioma_visita">Idioma</label>
                            <select class="form-control" name="jform[idioma]" id="jform_idioma_visita" required>
                                <option></option>
                                <?php foreach($idiomas as $key => $idioma):?>
                                    <option value="<?php echo $idioma['id'];?>"><?php echo $idioma["nombre"]; ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
						<div class="col-sm-2">
							<label id="jform_fecha-lbl" for="jform_fecha" class="d-none">Fecha</label>
							<input type="date" name="jform[fecha]" id="jform_fecha" class="d-none" value="" class="form-control" placeholder="fecha" disabled="true">
							<button class="validate enviarvisita btn btn-primary">
								Enviar
							</button>
						</div>
					</div>
				</form>
			</div>
		</div>
		<div class="col-sm-12">
            <div class="row">
                <div class="col-sm-12 boton-canceladas">
            <button class="btn btn-primary" style="display: none;" id="mostrar_canceladas">Mostrar reservas canceladas</button>
                </div>
                </div>
			<div id="resultado"></div>
			<div id="formularioreserva" style="display: none;">
				<h3>Generar una nueva reserva <span id="fechavisitaform"></span></h3>
				<form id="form-guia-res"
					  action="#"
					  method="post">
					<input type="hidden" name="jform[idvisita]" id="jform_idvisita" value="" class="form-control">
                    <input type="hidden" name="jform[idreserva]" id="jform_idreserva" value="" class="form-control">
                    <p class="alert-success" style="display: none" id="editando-reserva-mensaje">Estás modificando la reserva con ID <b><span id="reserva_id_alerta"></span></b> Si quieres crear una nueva reserva, pulsa <a onclick="vaciarValores()" style="cursor: pointer;text-decoration: underline"><b> aqui.</b></a></p>
					<div class="row">
						<div class="col-sm-6">
							<label id="jform_nombre-lbl" for="jform_nombre">Nombre</label>
							<input type="text" name="jform[nombre]" id="jform_nombre" class="form-control" placeholder="Nombre" required>
						</div>
						<div class="col-sm-6">
							<label id="jform_email-lbl" for="jform_email">Email</label>
							<input type="email" name="jform[email]" id="jform_email" class="form-control" placeholder="Email">
						</div>
					</div>
					<div class="row">
						<div class="col-sm-6">
							<label id="jform_telefono-lbl" for="jform_telefono">Telefono</label>
							<input type="text" name="jform[telefono]" id="jform_telefono" class="form-control" placeholder="Telefono">
						</div>
<!--						<div class="col-sm-6">-->
<!--							<label id="jform_tarjeta-lbl" for="jform_tarjeta">Tarjeta</label>-->
<!--							<input type="text" name="jform[tarjeta]" id="jform_tarjeta" class="form-control" placeholder="Tarjeta">-->
<!--						</div>-->
						<div class="col-sm-6">
							<label id="jform_codigopostal-lbl" for="jform_codigopostal">Codigo Postal</label>
							<input type="text" name="jform[codigopostal]" id="jform_codigopostal" class="form-control" placeholder="Codigo Postal">
						</div>

						<div class="col-sm-6">
							<label id="jform_personas-lbl" for="jform_personas">Personas</label>
							<input type="number" name="jform[personas]" id="jform_personas" value="" class="form-control" placeholder="Personas" required>
                            <p class="alert-danger" style="display: none" id="cambio-personas-mensaje"><b>Aviso</b> Estás modificando el numero de personas para esta reserva.
                                <br> <b>IMPORTANTE: Revisa de nuevo el precio.</b></p>
						</div>

						<div class="col-sm-6">
							<label id="jform_pais-lbl" for="jform_pais">Pais</label>
							<select class="form-control" name="jform[pais]" id="jform_pais">
								<option></option>
								<?php foreach($paises as $key => $pais):?>
									<option value="<?php echo $pais['id'];?>"><?php echo $pais["nombre"]; ?></option>
								<?php endforeach; ?>
							</select>
						</div>
						<div class="col-sm-6">
							<label id="jform_usuario-lbl" for="jform_usuario">Usuario</label>
							<select class="form-control" name="jform[usuario]" id="jform_usuario" required>
								<option></option>
								<?php foreach($usuarios as $key => $usuario):?>
									<option value="<?php echo $usuario['id'];?>"><?php echo $usuario["name"]; ?></option>
								<?php endforeach; ?>
							</select>
						</div>
						<div class="col-sm-6">
							<label id="jform_idioma-lbl" for="jform_idioma">Idioma</label>
							<select class="form-control" name="jform[idioma]" id="jform_idioma" required>
								<option></option>
								<?php foreach($idiomas as $key => $idioma):?>
									<option value="<?php echo $idioma['id'];?>"><?php echo $idioma["nombre"]; ?></option>
								<?php endforeach; ?>
							</select>
						</div>
						<div class="col-sm-6">
							<label id="jform_tipo_cliente_label" for="jform_tipo_cliente">Tipo Cliente</label>
							<select class="form-control" name="jform[tipo_cliente]" id="jform_tipo_cliente">
								<option></option>
								<?php foreach($clientes as $key => $cliente):?>
									<option value="<?php echo $cliente["id"];?>"><?php echo $cliente["nombre"]; ?></option>
								<?php endforeach; ?>
							</select>
						</div>
                        <div class="col-sm-6">
                            <label>Pagado</label>
                            <select id="jform_pagado" name="pagado-reserva">
                                <option value="no" selected>Sin pagar</option>
                                <option value="online">Pagado Online</option>
                                <option value="tienda">Pagado En Tienda</option>
                                <option value="invitacion">Invitación</option>
                                <option value="tarjeta-regalo">Tarjeta Regalo</option>
                                <option value="agencia">Facturar Agencia</option>
                            </select>
                        </div>
                        <div class="col-sm-6">
                            <label>Precio</label><br>
                            <i><small>Si quieres que la aplicacion calcule el precio, ponga el precio a 0.</small></i>
                            <input type="number" name="precio-reserva" id="jform_precio" value="0" min="0">
                            <p class="alert-danger" style="display: none" id="cambio-precio-mensaje"><b>Aviso</b> Estás modificando el precio para esta reserva</p>
                        </div>
                        <div class="col-sm-6">
                            <label>Estado</label>
                            <select id="jform_estado" name="estado-reserva">
                                <option value="pendiente">Pendiente</option>
                                <option value="pendiente-pago">Esperando Pago Online</option>
                                <option value="pendiente-conf">Esperando Confirmacion</option>
                                <option value="confirmado">Confirmado</option>
                                <option value="cancelado">Cancelado</option>
                            </select>
                        </div>
                        <div class="col-sm-6">
                            <label>Asistencia</label>
                            <select id="jform_asistencia" name="estado-reserva">
                                <option value="no">No</option>
                                <option value="si">Si</option>
                            </select>
                        </div>
                        <div class="col-sm-6 checkbox-enviarcorreo">
                            <input type="checkbox" name="jform[mandarcorreo]" id="jform_mandarcorreo">Enviar correo de pago</input>
                        </div>
						<div class="col-sm-6">
							<label id="jform_observaciones-lbl" for="jform_observaciones">Observaciones</label>
							<textarea name="jform[observaciones]" id="jform_observaciones" class="form-control" cols="20" rows="5" placeholder="Observaciones"></textarea>
						</div>
                    </div>
					<div class="row">
						<div class="col-sm-12">
							<button class="validate enviarreserva btn btn-primary">
								Enviar
							</button>
						</div>
					</div>
			</form>
		</div>
	</div>
</div>
</div>

<div id="cambiofechamodal" class="modal" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="modal-title">Cambio de fecha reserva</h3>
                <button type="button" class="close" data-bs-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
                <label>ID Reserva:</label>
                <input type="text" name="nombre-reserva-modal" id="id-reserva-modal" disabled>
                <label>Nombre</label>
                <input type="text" name="nombre-reserva" id="nombre-reserva-modal" disabled>
                <label>Email</label>
                <input type="email" name="email-reserva" id="email-reserva-modal" disabled>
                <label>Nueva fecha</label>
                <input type="date" name="nueva-fecha-reserva" id="nueva-fecha-reserva-modal">
                <div id="nueva-disponibilidad"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-bs-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>
<div id="cambiovisitamodal" class="modal" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="modal-title">Cambio de visita <span id="visita_fecha"></span> <span id="visita_hora"></span> </h3>
                <button type="button" class="close" data-bs-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
                <label>ID Visita:</label>
                <input type="text" name="nombre-reserva-modal" id="id-visita-modal" disabled>
                <label>Aforo Web</label>
                <input type="text" name="email-reserva" id="aforo-web-visita-modal">
                <label>Aforo Privado</label>
                <input type="text" name="nueva-fecha-reserva" id="aforo-privado-visita-modal">
            </div>
            <div class="modal-footer">
                <button id="EnviarModificacionVisita" class="btn btn-primary">Modificar Visita</button>
                <button type="button" class="btn btn-default" data-bs-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>


