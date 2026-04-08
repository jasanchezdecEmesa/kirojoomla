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
$doc->addScript(Uri::base() . '/media/com_ereservas/js/reservas.js?v=2');
$doc->addScript(Uri::base() . '/media/com_ereservas/js/reserva_btn.js?v=2');

$user    = Factory::getUser();
$canEdit = EreservasHelpersEreservas::canUserEdit($this->item, $user);
?>

<div class="reserva-edit front-end-edit">
	<?php if (!$canEdit) : ?>
		<h3>
			<?php throw new Exception(Text::_('COM_ERESERVAS_ERROR_MESSAGE_NOT_AUTHORISED'), 403); ?>
		</h3>
	<?php else : ?>
		<?php if (!empty($this->item->id)): ?>
			<h1><?php echo Text::sprintf($this->item->nombre); ?></h1>
		<?php else: ?>
			<h1>Nueva Reserva</h1>
		<?php endif; ?>

		<form id="form-reserva"
			  action="<?php echo Route::_('index.php?option=com_ereservas&task=reserva.save'); ?>"
			  method="post" class="form-validate form-horizontal" enctype="multipart/form-data">

			<input type="hidden" name="jform[id]" id="reserva_id" value="<?php echo $this->item->id; ?>" />

			<input type="hidden" name="jform[ordering]" value="<?php echo $this->item->ordering; ?>" />

			<input type="hidden" name="jform[state]" value="<?php echo $this->item->state; ?>" />

			<input type="hidden" name="jform[checked_out]" value="<?php echo $this->item->checked_out; ?>" />

			<input type="hidden" name="jform[checked_out_time]" value="<?php echo $this->item->checked_out_time; ?>" />

			<div class="row">
				<div class="col-sm-6">
					<?php echo $this->form->getInput('created_by'); ?>
					<?php echo $this->form->getInput('modified_by'); ?>
					<?php echo $this->form->renderField('nombre'); ?>
					<?php echo $this->form->renderField('apellidos'); ?>
					<?php echo $this->form->renderField('id_visita'); ?>
					<?php foreach((array)$this->item->id_visita as $value): ?>
						<?php if(!is_array($value)): ?>
							<input type="hidden" class="id_visita" id="id_visitahidden" name="jform[id_visitahidden][<?php echo $value; ?>]" value="<?php echo $value; ?>" />
						<?php endif; ?>
					<?php endforeach; ?>
<!--					<div class="tarjeta">-->
<!--						--><?php //if (!empty($this->item->tarjeta)): ?>
<!--							<button type="button" class="btn btn-primary btn-desencriptar" data-toggle="modal" id='btn-desencriptar' data-target="#exampleModalCenter">-->
<!--								<i class="fa fa-eye"></i> Ver Tarjeta-->
<!--							</button>-->
<!--						--><?php //endif; ?>
<!--						--><?php //echo $this->form->renderField('tarjeta'); ?>
<!--					</div>-->
					<?php echo $this->form->renderField('cpostal'); ?>
					<?php echo $this->form->renderField('id_pais'); ?>
					<?php foreach((array)$this->item->id_pais as $value): ?>
						<?php if(!is_array($value)): ?>
							<input type="hidden" class="id_pais" name="jform[id_paishidden][<?php echo $value; ?>]" value="<?php echo $value; ?>" />
						<?php endif; ?>
					<?php endforeach; ?>
					<?php echo $this->form->renderField('id_tipo_cliente'); ?>
					<?php foreach((array)$this->item->id_tipo_cliente as $value): ?>
						<?php if(!is_array($value)): ?>
							<input type="hidden" class="id_tipo_cliente" name="jform[id_tipo_clientehidden][<?php echo $value; ?>]" value="<?php echo $value; ?>" />
						<?php endif; ?>
					<?php endforeach; ?>


					<?php echo $this->form->renderField('id_idioma'); ?>

					<?php foreach((array)$this->item->id_idioma as $value): ?>
						<?php if(!is_array($value)): ?>
							<input type="hidden" class="id_idioma" name="jform[id_idiomahidden][<?php echo $value; ?>]" value="<?php echo $value; ?>" />
						<?php endif; ?>
					<?php endforeach; ?>
					<?php echo $this->form->renderField('estado'); ?>
					<?php echo $this->form->renderField('id_usuario'); ?>

					<?php foreach((array)$this->item->id_usuario as $value): ?>
						<?php if(!is_array($value)): ?>
							<input type="hidden" class="id_usuario" name="jform[id_usuariohidden][<?php echo $value; ?>]" value="<?php echo $value; ?>" />
						<?php endif; ?>
					<?php endforeach; ?>


					<?php echo $this->form->renderField('observaciones'); ?>
                    <?php echo $this->form->renderField('menu_especial'); ?>
				</div>
				<div class="col-sm-6">
					<?php echo $this->form->renderField('email'); ?>
					<?php echo $this->form->renderField('personas'); ?>
					<div class="control-group">
						<label id="jform_precio-lbl" for="jform_precio">Precio</label>
						<?php if($this->item->precio): ?>
							<input type="text" name="jform[precio]" class="form-control" id="jform_precio" value="<?php echo $this->item->precio ?>">
                        <?php elseif($this->item->precio == 0): ?>
                            <input type="text" name="jform[precio]" class="form-control" id="jform_precio" placeholder="Precio">
                        <?php else: ?>
							<input type="text" name="jform[precio]" class="form-control" id="jform_precio" value="<?php echo $this->consulta_total[0]->total ?>">
						<?php endif ?>
					</div>
					<?php echo $this->form->renderField('pagado'); ?>
					<?php echo $this->form->renderField('asistencia'); ?>
					<?php echo $this->form->renderField('referencia'); ?>

					<?php echo $this->form->renderField('vinos'); ?>
					<?php echo $this->form->renderField('tipo_cata'); ?>
					<?php echo $this->form->renderField('aperitivo'); ?>
					<?php echo $this->form->renderField('alergenos'); ?>



				</div>
			</div>

			<div class="control-group">
				<div class="controls">

					<?php if ($this->canSave): ?>
						<button type="submit" class="validate btn btn-primary">
							<?php echo Text::_('JSUBMIT'); ?>
						</button>
					<?php endif; ?>
					<a class="btn"
					   href="<?php echo Route::_('index.php?option=com_ereservas&task=reservaform.cancel'); ?>"
					   title="<?php echo Text::_('JCANCEL'); ?>">
						<?php echo Text::_('JCANCEL'); ?>
					</a>
				</div>
			</div>

			<input type="hidden" name="option" value="com_ereservas"/>
			<input type="hidden" name="task"
				   value="reservaform.save"/>
			<?php echo HTMLHelper::_('form.token'); ?>
		</form>
	<?php endif; ?>
</div>
<!-- Modal -->
<div class="modal fade" id="exampleModalCenter" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
	<div class="modal-dialog modal-dialog-centered" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="exampleModalLongTitle">Tarjeta de <?php echo $this->item->nombre ?></h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<div class="modal-body" id="texto-tarjeta">
				<p>Tarjeta:</p>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-secondary" data-dismiss="modal">Ok</button>
			</div>
		</div>
	</div>
</div>
