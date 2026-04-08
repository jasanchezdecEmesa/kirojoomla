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

use \Joomla\CMS\HTML\HTMLHelper;
use \Joomla\CMS\Factory;
use \Joomla\CMS\Uri\Uri;
use \Joomla\CMS\Router\Route;
use \Joomla\CMS\Language\Text;

HTMLHelper::_('behavior.keepalive');
HTMLHelper::_('behavior.tooltip');
HTMLHelper::_('behavior.formvalidation');
HTMLHelper::_('formbehavior.chosen', 'select');

// Load admin language file
$lang = Factory::getLanguage();
$lang->load('com_ereservas', JPATH_SITE);
$doc = Factory::getDocument();
$doc->addScript(Uri::base() . '/media/com_ereservas/js/form.js');
$doc->addScript(Uri::base() . '/media/com_ereservas/js/datos-facturacion.js');

$user    = Factory::getUser();
$canEdit = EreservasHelpersEreservas::canUserEdit($this->item, $user);
$app  = Factory::getApplication();

$pagado = ($this->token_consulta->pagado == 'no')?false:true;
?>

<div class="reserva-edit front-end-edit">

	<?php if (!$canEdit) : ?>
		<h3>
			<?php throw new Exception(Text::_('COM_ERESERVAS_ERROR_MESSAGE_NOT_AUTHORISED'), 403); ?>
		</h3>
	<?php else : ?>
		<?php if (!empty($this->item->id)): ?>
			<h1><?php echo Text::sprintf('COM_ERESERVAS_EDIT_ITEM_TITLE', $this->item->id); ?></h1>
		<?php else: ?>
			<h1>Confirmación de pago de la reserva</h1>
			<?php
			if($pagado){
				echo '<p class="avisopago">La reserva ya se ha completado, no es necesario volver a rellenar los datos</p>';
			} else {
				echo '<p class="avisopago">Por favor, antes de pagar la reserva, verifique la información que le mostramos a continuación:</p>';
			}
			?>

			<div class="row">
				<div class="col-md-6 col-sm-12">
					<div class="cajapago">
						<?php foreach ($this->consulta_reserva as $i => $datosreserva) :

							$cadenafecha = JHtml::_('date', $datosreserva->fecha, 'DATE_FORMAT_LC1');
							$cadenafecha = str_replace(' 20', ' de 20', $cadenafecha);
							?>
							<ul>
								<li><strong>Nombre: </strong><?php echo $datosreserva->nombre; ?></li>
								<li><strong>Email: </strong><?php echo $datosreserva->email; ?></li>
								<li><strong>Telefono: </strong> <?php echo $datosreserva->telefono; ?></li>
								<li><strong>Visita: </strong> <?php echo $datosreserva->visita; ?></li>
								<li><strong>Fecha: </strong> <?php echo $cadenafecha.' a las '.substr($datosreserva->hora_inicio,0,5);  ?></li>
							</ul>
						<?php endforeach ?>
					</div>
				</div>
				<div class="col-md-6 col-sm-12">
					<?php if(!$pagado) { ?>
                        <?php foreach ($this->consulta_reserva as $i => $datosreserva) :?>
                        <table class="table tabla-informacionPago">
                            <tr>
                                <th>Personas</th>
                                <th>Precio/persona</th>
                                <th>Precio final</th>
                            </tr>
                            <tr>
                                <td><?php echo $datosreserva->personas; ?></td>
                                <td><?php echo $datosreserva->precio; ?> €</td>
                                <td><b><?php echo $this->total[0]->total; ?> €</b></td>
                            </tr>
                            <tr class="colorDoradoUltimoRow">
                                <td colspan="2">Total a pagar:</td>
                                <td><?php echo $this->total[0]->total; ?> €</td>
                            </tr>
                        </table>
                        <?php endforeach;?>

					<form id="form-reserva"
						  action="<?php echo Route::_('index.php?option=com_ereservas&task=reserva.save'); ?>"
						  method="post" class="form-validate form-horizontal formpago" enctype="multipart/form-data">
                        <label for="factura"><input type="checkbox" id="datos-facturacion" name="jform[factura]"> Quiero factura</label><br>
                        <div id="formulario-reserva-campos" style="display: none">

						<input type="hidden" name="jform[id]" value="<?php echo $this->item->id; ?>" />

						<input type="hidden" name="jform[ordering]" value="<?php echo $this->item->ordering; ?>" />

						<input type="hidden" name="jform[state]" value="<?php echo $this->item->state; ?>" />

						<input type="hidden" name="jform[checked_out]" value="<?php echo $this->item->checked_out; ?>" />

						<input type="hidden" name="jform[checked_out_time]" value="<?php echo $this->item->checked_out_time; ?>" />
						<?php if($this->id_reserva): ?>
							<input type="hidden" name="jform[id_reserva]" value="<?php echo $this->id_reserva; ?>" />
						<?php else:?>
							<input type="hidden" name="jform[id_reserva]" value="<?php echo $this->reserva_get; ?>" />
						<?php endif?>

						<input type="hidden" name="jform[precio]" value="<?php echo $this->total[0]->total;; ?>" />

						<?php echo $this->form->getInput('created_by'); ?>
						<?php echo $this->form->getInput('modified_by'); ?>
						<?php echo $this->form->renderField('nombre',null,$datosreserva->nombre ); ?>

						<?php echo $this->form->renderField('nif'); ?>

						<?php echo $this->form->renderField('cpostal',null,$datosreserva->cpostal); ?>

						<?php echo $this->form->renderField('direccion'); ?>

						<?php echo $this->form->renderField('municipio',null,$datosreserva->municipio); ?>
                        </div>

                        <hr/>
                        <label for="acepto-politica"><input type="checkbox" id="acepto-politica" > He leído y acepto la <a href="index.php?Itemid=285" terget="_blank">política de protección de datos</a></label><br>
                        <label for="info_comercial"><input type="checkbox" name="jform[info_comercial]"> Autorizo el envío de comunicaciones comerciales referentes a nuestra actividad, promociones, eventos que pudieran ser de su interés. </label><br>

						<div class="control-group finalizarpago">
							<div class="controls">

								<?php if ($this->canSave): ?>
									<button type="submit" class="validate btn btn-primary" id="boton-guardar" disabled="true">
										<?php echo 'Pagar' ?>
									</button>
								<?php endif; ?>
								<a class="btn"
								   href="<?php echo Route::_('index.php?option=com_ereservas&task=pagoform.cancel'); ?>"
								   title="<?php echo Text::_('JCANCEL'); ?>">
									<?php echo Text::_('JCANCEL'); ?>
								</a>
							</div>
						</div>

						<input type="hidden" name="option" value="com_ereservas"/>
						<input type="hidden" name="task"
							   value="pagoform.save"/>
						<?php echo HTMLHelper::_('form.token'); ?>
					</form>
					<?php } ?>
				</div>
			</div>
		<?php endif; ?>


	<?php endif; ?>
</div>
