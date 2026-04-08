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
$lang = JFactory::getLanguage();
$document->addScript('media/com_ereservas/js/corecalendar.js');
$document->addScript('media/com_ereservas/js/maindaygrid.js');
$document->addScript('media/com_ereservas/js/interaction.js');
$document->addScript('media/com_ereservas/js/calendar.js');
$document->addScript('media/com_ereservas/js/momentmain.js');
$document->addScript('media/com_ereservas/js/locales/es.js');
$document->addStyleSheet('media/com_ereservas/css/maindaygrid.css');
$document->addStyleSheet('media/com_ereservas/css/maincore.css');
$document->addStyleSheet('media/com_ereservas/css/calendario.css');
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
<div class="margen reservaskiro">
	<h1>KIRO RESERVAS</h1>
	<div class="row">
		<div class="col-lg-6 col-xl-6 descripcion">
		<img src="images/kiro_tradicional_reservas-1-600x629.jpg">
		<?php if ($lang->getname() == 'English (en-GB)'){?>
			<p sytle="text-align:center;">The reservation system will be updated periodically <b>every 3 months</b> allowing reservations in the 3 months after the current one, following the established criteria:</p>
		<ul>
			<li><b>In December: </b> Reservations for January, February and March.</li>
			<li><b>In March: </b> Reservations for April, May and June..</li>
			<li><b>In June: </b> Reservations for July, August and September.</li>
			<li><b>In September: </b> Reservations for October, November and December.</li>
		</ul>
		<h1 class="precio">Price Menu</h1>
		<h1>90€ IVA INCLUDED (WITHOUT DRINKS)</h1>
		<?php }else{?>
		<p sytle="text-align:center;">El sistema de reservas se actualizará de forma periodica <b>cada 3 meses</b> permitiendo hacer reservas en los 3 meses posteriores al actual, siguiendo el criterio establecido:</p>
		<ul>
		
			<li><b>En Diciembre: </b> Reservas para Enero, Febrero y Marzo.</li>
			<li><b>En Marzo: </b> Reservas para Abril, Mayo y Junio.</li>
			<li><b>En Junio: </b> Reservas para Julio, Agosto y Septiembre.</li>
			<li><b>En Septiembre: </b> Reservas para Octubre, Noviembre y Diciembre.</li>
		</ul>
		<h1 class="precio">Precio Menu</h1>
		<h1>90€ IVA INCLUIDO (SIN BEBIDAS)</h1>
		<?php } ?>
		</div>
		<div class="col-lg-6 col-xl-6">
		<?php if ($lang->getname() == 'English (en-GB)'){?>
					<h1>Reservations</h1>
					<p>Welcome to the KIRO reservation system, we ask you, before formalizing it, to carefully read our  <a href="cancellation-policy-and-reservations/" target="_blank" rel="noopener">reservations and cancellation policy</a>, as well as the <a href="restaurant-rules/" target="_blank" rel="noopener">basic recommendations</a> to enjoy our restaurant.</p>
					<p>The recommended reserve is 4 diners per service..</p>
					<p>We have adopted this measure to try to optimize to the maximum possible the stay of our clients, trying to evoke in this way a unique and unforgettable experience, reflection of the philosophy that we try to transmit in our restaurant through our Traditional cuisine in a very exclusive atmosphere, where the real trip begins just entering through the door.</p>
			<?php }else{?>
					<h1>Reservas</h1><hr>
					<p><p>Bienvenidos al sistema de reservas de KIRO, le rogamos que, antes de formalizar la misma, lea con atención nuestra <a href="politica-de-cancelacion-y-reservas/" target="_blank" rel="noopener">política de reservas y cancelaciones</a>, así como las <a href="normas-del-restaurante/" target="_blank" rel="noopener">recomendaciones básicas</a> para disfrutar de nuestro restaurante.</p>
					<p>La reserva recomendada es de 4 comensales por servicio.</p>
					<p>Hemos adoptado esta medida para tratar de optimizar al máximo posible la estancia de nuestros clientes, tratando de evocar de esta manera una experiencia única e inolvidable, reflejo de la filosofía que tratamos de transmitir en nuestro restaurante mediante nuestra cocina tradicional en un ambiente muy exclusivo, donde el verdadero viaje comienza nada mas entrar por la puerta.</p></p>
			<?php } ?>
			<div id="calendar"></div>
			<div id="eventos"></div>
			<div id="resultado"></div>
			<div id="formularioreserva" style="display: none;">
			<?php if ($lang->getname() == 'English (en-GB)'){?>
				<h3>Reservation
				<span id="fechavisitaform"></span></h3>
				<form id="form-guia-res"
					  action="#"
					  method="post">
					<div class="row">

						<div class="col-sm-6">
							<label id="jform_nombre-lbl" for="jform_nombre">Name</label>
							<input type="text" name="jform[nombre]" id="jform_nombre" class="form-control" placeholder="Name" required>
						</div>
						<div class="col-sm-6">
							<label id="jform_email-lbl" for="jform_email">Email</label>
							<input type="email" name="jform[email]" id="jform_email" class="form-control" placeholder="Email">
						</div>
					</div>
					<div class="row">
						<div class="col-sm-6">
							<label id="jform_telefono-lbl" for="jform_telefono">Telephone</label>
							<input type="text" name="jform[telefono]" id="jform_telefono" class="form-control" placeholder="Telephone">
						</div>
						<div class="col-sm-6">
							<label id="jform_observaciones-lbl" for="jform_observaciones">Observations</label>
							<textarea name="jform[observaciones]" id="jform_observaciones" class="form-control" placeholder="Observations"></textarea>
						</div>
					</div>
					<div class="row">
					<div class="col-sm-6">
							<label id="jform_personas-lbl" for="jform_personas">Dinners</label>
							<input type="number" name="jform[personas]" id="jform_personas" value="" class="form-control" placeholder="Dinners" required>
							<input type="hidden" name="jform[idvisita]" id="jform_idvisita" value="" class="form-control">
						</div>
						<div class="col-sm-6">
							<button class="validate enviarreserva btn btn-primary">
								Enviar
							</button>
						</div>
					</div>

			</form>
			<?php }else{?>
		<h3>Generar una nueva reserva 
				<span id="fechavisitaform"></span></h3>
				<form id="form-guia-res"
					  action="#"
					  method="post">
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
						<div class="col-sm-6">
							<label id="jform_observaciones-lbl" for="jform_observaciones">Observaciones</label>
							<textarea name="jform[observaciones]" id="jform_observaciones" class="form-control" placeholder="Observaciones"></textarea>
						</div>
					</div>
					<div class="row">
					<div class="col-sm-6">
							<label id="jform_personas-lbl" for="jform_personas">Personas</label>
							<input type="number" name="jform[personas]" id="jform_personas" value="" class="form-control" placeholder="Personas" required>
							<input type="hidden" name="jform[idvisita]" id="jform_idvisita" value="" class="form-control">
						</div>
						<div class="col-sm-6">
							<button class="validate enviarreserva btn btn-primary">
								Enviar
							</button>
						</div>
					</div>

			</form>


		<?php }?>
</div>
	</div>
</div>
</div>




