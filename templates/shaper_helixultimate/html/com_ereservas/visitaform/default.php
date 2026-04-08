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
$doc->addScript(Uri::base() . '/media/com_ereservas/js/visitas.js?v=2');

$user    = Factory::getUser();
$canEdit = EreservasHelpersEreservas::canUserEdit($this->item, $user);


?>

<div class="visita-edit front-end-edit">
	<?php if (!$canEdit) : ?>
		<h3>
			<?php throw new Exception(Text::_('COM_ERESERVAS_ERROR_MESSAGE_NOT_AUTHORISED'), 403); ?>
		</h3>
	<?php else : ?>
		<?php if (!empty($this->item->id)): ?>
			<h1>Editar Visita</h1>
		<?php else: ?>
			<h1>Crear Visita</h1>
		<?php endif; ?>

		<form id="form-visita"
			  action="<?php echo Route::_('index.php?option=com_ereservas&task=visita.save'); ?>"
			  method="post" class="form-validate form-horizontal" enctype="multipart/form-data">

			<input type="hidden" name="jform[id]" value="<?php echo $this->item->id; ?>" />

			<input type="hidden" name="jform[ordering]" value="<?php echo $this->item->ordering; ?>" />

			<input type="hidden" name="jform[state]" value="<?php echo $this->item->state; ?>" />

			<input type="hidden" name="jform[checked_out]" value="<?php echo $this->item->checked_out; ?>" />

			<input type="hidden" name="jform[checked_out_time]" value="<?php echo $this->item->checked_out_time; ?>" />

			<div class="row">
				<div class="col-sm-6">
					<?php echo $this->form->getInput('created_by'); ?>
					<?php echo $this->form->getInput('modified_by'); ?>
					<?php //echo $this->form->renderField('nombre'); ?>
					<?php echo $this->form->renderField('fecha'); ?>

					<?php echo $this->form->renderField('hora_inicio'); ?>

					<?php echo $this->form->renderField('hora_fin'); ?>

					<?php echo $this->form->renderField('precio'); ?>

					<?php echo $this->form->renderField('idioma'); ?>

					<?php foreach((array)$this->item->idioma as $value): ?>
						<?php if(!is_array($value)): ?>
							<input type="hidden" class="idioma" name="jform[idiomahidden][<?php echo $value; ?>]" value="<?php echo $value; ?>" />
						<?php endif; ?>
					<?php endforeach; ?>

					<?php echo $this->form->renderField('guia'); ?>

					<?php foreach((array)$this->item->guia as $value): ?>
						<?php if(!is_array($value)): ?>
							<input type="hidden" class="guia" name="jform[guiahidden][<?php echo $value; ?>]" value="<?php echo $value; ?>" />
						<?php endif; ?>
					<?php endforeach; ?>

					<?php echo $this->form->renderField('observaciones'); ?>
				</div>
				<div class="col-sm-6">
					<?php echo $this->form->renderField('id_tipo_visita'); ?>

					<?php foreach((array)$this->item->id_tipo_visita as $value): ?>
						<?php if(!is_array($value)): ?>
							<input type="hidden" class="id_tipo_visita" name="jform[id_tipo_visitahidden][<?php echo $value; ?>]" value="<?php echo $value; ?>" />
						<?php endif; ?>
					<?php endforeach; ?>

					<?php echo $this->form->renderField('id_sala'); ?>

					<?php foreach((array)$this->item->id_sala as $value): ?>
						<?php if(!is_array($value)): ?>
							<input type="hidden" class="id_sala" name="jform[id_salahidden][<?php echo $value; ?>]" value="<?php echo $value; ?>" />
						<?php endif; ?>
					<?php endforeach; ?>

					<?php echo $this->form->renderField('aforo_web'); ?>

					<?php echo $this->form->renderField('aforo_privado'); ?>

					<?php echo $this->form->renderField('aforo_ocupado'); ?>

					<?php echo $this->form->renderField('cata'); ?>

					<?php echo $this->form->renderField('comida'); ?>


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
					   href="<?php echo Route::_('index.php?option=com_ereservas&task=visitaform.cancel'); ?>"
					   title="<?php echo Text::_('JCANCEL'); ?>">
						<?php echo Text::_('JCANCEL'); ?>
					</a>
				</div>
			</div>

			<input type="hidden" name="option" value="com_ereservas"/>
			<input type="hidden" name="task"
				   value="visitaform.save"/>
			<?php echo HTMLHelper::_('form.token'); ?>
		</form>
	<?php endif; ?>
</div>
