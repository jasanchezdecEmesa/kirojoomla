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


HTMLHelper::addIncludePath(JPATH_COMPONENT . '/helpers/html');
HTMLHelper::_('behavior.tooltip');
HTMLHelper::_('behavior.formvalidation');
HTMLHelper::_('formbehavior.chosen', 'select');
HTMLHelper::_('behavior.keepalive');

// Import CSS
$document = Factory::getDocument();
$document->addStyleSheet(Uri::root() . 'media/com_ereservas/css/form.css');
?>
<script type="text/javascript">
	js = jQuery.noConflict();
	js(document).ready(function () {
		
	js('input:hidden.id_sala').each(function(){
		var name = js(this).attr('name');
		if(name.indexOf('id_salahidden')){
			js('#jform_id_sala option[value="'+js(this).val()+'"]').attr('selected',true);
		}
	});
	js("#jform_id_sala").trigger("liszt:updated");
	js('input:hidden.idioma').each(function(){
		var name = js(this).attr('name');
		if(name.indexOf('idiomahidden')){
			js('#jform_idioma option[value="'+js(this).val()+'"]').attr('selected',true);
		}
	});
	js("#jform_idioma").trigger("liszt:updated");
	js('input:hidden.id_tipo_visita').each(function(){
		var name = js(this).attr('name');
		if(name.indexOf('id_tipo_visitahidden')){
			js('#jform_id_tipo_visita option[value="'+js(this).val()+'"]').attr('selected',true);
		}
	});
	js("#jform_id_tipo_visita").trigger("liszt:updated");
	js('input:hidden.guia').each(function(){
		var name = js(this).attr('name');
		if(name.indexOf('guiahidden')){
			js('#jform_guia option[value="'+js(this).val()+'"]').attr('selected',true);
		}
	});
	js("#jform_guia").trigger("liszt:updated");
	});

	Joomla.submitbutton = function (task) {
		if (task == 'visita.cancel') {
			Joomla.submitform(task, document.getElementById('visita-form'));
		}
		else {
			
			if (task != 'visita.cancel' && document.formvalidator.isValid(document.id('visita-form'))) {
				
				Joomla.submitform(task, document.getElementById('visita-form'));
			}
			else {
				alert('<?php echo $this->escape(Text::_('JGLOBAL_VALIDATION_FORM_FAILED')); ?>');
			}
		}
	}
</script>

<form
	action="<?php echo Route::_('index.php?option=com_ereservas&layout=edit&id=' . (int) $this->item->id); ?>"
	method="post" enctype="multipart/form-data" name="adminForm" id="visita-form" class="form-validate">

	<div class="form-horizontal">
		<?php echo HTMLHelper::_('bootstrap.startTabSet', 'myTab', array('active' => 'general')); ?>

		<?php echo JHtml::_('bootstrap.addTab', 'myTab', 'general', Text::_('COM_ERESERVAS_TITLE_VISITA', true)); ?>
		<div class="row-fluid">
			<div class="span10 form-horizontal">
				<fieldset class="adminform">

									<input type="hidden" name="jform[id]" value="<?php echo $this->item->id; ?>" />
				<input type="hidden" name="jform[ordering]" value="<?php echo $this->item->ordering; ?>" />
				<input type="hidden" name="jform[state]" value="<?php echo $this->item->state; ?>" />
				<input type="hidden" name="jform[checked_out]" value="<?php echo $this->item->checked_out; ?>" />
				<input type="hidden" name="jform[checked_out_time]" value="<?php echo $this->item->checked_out_time; ?>" />

				<?php echo $this->form->renderField('created_by'); ?>
				<?php echo $this->form->renderField('modified_by'); ?>				<?php echo $this->form->renderField('nombre'); ?>
				<?php echo $this->form->renderField('id_sala'); ?>

			<?php
				foreach((array)$this->item->id_sala as $value): 
					if(!is_array($value)):
						echo '<input type="hidden" class="id_sala" name="jform[id_salahidden]['.$value.']" value="'.$value.'" />';
					endif;
				endforeach;
			?>				<?php echo $this->form->renderField('aforo_web'); ?>
				<?php echo $this->form->renderField('aforo_privado'); ?>
				<?php echo $this->form->renderField('comida'); ?>
				<?php echo $this->form->renderField('cata'); ?>
				<?php echo $this->form->renderField('observaciones'); ?>
				<?php echo $this->form->renderField('precio'); ?>
				<?php echo $this->form->renderField('fecha'); ?>
				<?php echo $this->form->renderField('hora_inicio'); ?>
				<?php echo $this->form->renderField('hora_fin'); ?>
				<?php echo $this->form->renderField('idioma'); ?>

			<?php
				foreach((array)$this->item->idioma as $value): 
					if(!is_array($value)):
						echo '<input type="hidden" class="idioma" name="jform[idiomahidden]['.$value.']" value="'.$value.'" />';
					endif;
				endforeach;
			?>				<?php echo $this->form->renderField('id_tipo_visita'); ?>

			<?php
				foreach((array)$this->item->id_tipo_visita as $value): 
					if(!is_array($value)):
						echo '<input type="hidden" class="id_tipo_visita" name="jform[id_tipo_visitahidden]['.$value.']" value="'.$value.'" />';
					endif;
				endforeach;
			?>				<?php echo $this->form->renderField('aforo_ocupado'); ?>
				<?php echo $this->form->renderField('guia'); ?>

			<?php
				foreach((array)$this->item->guia as $value): 
					if(!is_array($value)):
						echo '<input type="hidden" class="guia" name="jform[guiahidden]['.$value.']" value="'.$value.'" />';
					endif;
				endforeach;
			?>

					<?php if ($this->state->params->get('save_history', 1)) : ?>
					<div class="control-group">
						<div class="control-label"><?php echo $this->form->getLabel('version_note'); ?></div>
						<div class="controls"><?php echo $this->form->getInput('version_note'); ?></div>
					</div>
					<?php endif; ?>
				</fieldset>
			</div>
		</div>
		<?php echo HTMLHelper::_('bootstrap.endTab'); ?>

		

		<?php echo HTMLHelper::_('bootstrap.endTabSet'); ?>

		<input type="hidden" name="task" value=""/>
		<?php echo HTMLHelper::_('form.token'); ?>

	</div>
</form>
