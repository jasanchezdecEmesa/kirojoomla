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
		
	js('input:hidden.id_visita').each(function(){
		var name = js(this).attr('name');
		if(name.indexOf('id_visitahidden')){
			js('#jform_id_visita option[value="'+js(this).val()+'"]').attr('selected',true);
		}
	});
	js("#jform_id_visita").trigger("liszt:updated");
	js('input:hidden.id_idioma').each(function(){
		var name = js(this).attr('name');
		if(name.indexOf('id_idiomahidden')){
			js('#jform_id_idioma option[value="'+js(this).val()+'"]').attr('selected',true);
		}
	});
	js("#jform_id_idioma").trigger("liszt:updated");
	js('input:hidden.id_usuario').each(function(){
		var name = js(this).attr('name');
		if(name.indexOf('id_usuariohidden')){
			js('#jform_id_usuario option[value="'+js(this).val()+'"]').attr('selected',true);
		}
	});
	js("#jform_id_usuario").trigger("liszt:updated");
	js('input:hidden.id_pais').each(function(){
		var name = js(this).attr('name');
		if(name.indexOf('id_paishidden')){
			js('#jform_id_pais option[value="'+js(this).val()+'"]').attr('selected',true);
		}
	});
	js("#jform_id_pais").trigger("liszt:updated");
	js('input:hidden.id_tipo_cliente').each(function(){
		var name = js(this).attr('name');
		if(name.indexOf('id_tipo_clientehidden')){
			js('#jform_id_tipo_cliente option[value="'+js(this).val()+'"]').attr('selected',true);
		}
	});
	js("#jform_id_tipo_cliente").trigger("liszt:updated");
	});

	Joomla.submitbutton = function (task) {
		if (task == 'reserva.cancel') {
			Joomla.submitform(task, document.getElementById('reserva-form'));
		}
		else {
			
			if (task != 'reserva.cancel' && document.formvalidator.isValid(document.id('reserva-form'))) {
				
				Joomla.submitform(task, document.getElementById('reserva-form'));
			}
			else {
				alert('<?php echo $this->escape(Text::_('JGLOBAL_VALIDATION_FORM_FAILED')); ?>');
			}
		}
	}
</script>

<form
	action="<?php echo Route::_('index.php?option=com_ereservas&layout=edit&id=' . (int) $this->item->id); ?>"
	method="post" enctype="multipart/form-data" name="adminForm" id="reserva-form" class="form-validate">

	<div class="form-horizontal">
		<?php echo HTMLHelper::_('bootstrap.startTabSet', 'myTab', array('active' => 'general')); ?>

		<?php echo JHtml::_('bootstrap.addTab', 'myTab', 'general', Text::_('COM_ERESERVAS_TITLE_RESERVA', true)); ?>
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
				<?php echo $this->form->renderField('telefono'); ?>
				<?php echo $this->form->renderField('email'); ?>
				<?php echo $this->form->renderField('id_visita'); ?>

			<?php
				foreach((array)$this->item->id_visita as $value): 
					if(!is_array($value)):
						echo '<input type="hidden" class="id_visita" name="jform[id_visitahidden]['.$value.']" value="'.$value.'" />';
					endif;
				endforeach;
			?>				<?php echo $this->form->renderField('id_idioma'); ?>

			<?php
				foreach((array)$this->item->id_idioma as $value): 
					if(!is_array($value)):
						echo '<input type="hidden" class="id_idioma" name="jform[id_idiomahidden]['.$value.']" value="'.$value.'" />';
					endif;
				endforeach;
			?>				<?php echo $this->form->renderField('personas'); ?>
				<?php echo $this->form->renderField('tarjeta'); ?>
				<?php echo $this->form->renderField('pagado'); ?>
				<?php echo $this->form->renderField('estado'); ?>
				<?php echo $this->form->renderField('id_usuario'); ?>

			<?php
				foreach((array)$this->item->id_usuario as $value): 
					if(!is_array($value)):
						echo '<input type="hidden" class="id_usuario" name="jform[id_usuariohidden]['.$value.']" value="'.$value.'" />';
					endif;
				endforeach;
			?>				<?php echo $this->form->renderField('id_pais'); ?>

			<?php
				foreach((array)$this->item->id_pais as $value): 
					if(!is_array($value)):
						echo '<input type="hidden" class="id_pais" name="jform[id_paishidden]['.$value.']" value="'.$value.'" />';
					endif;
				endforeach;
			?>				<?php echo $this->form->renderField('cpostal'); ?>
				<?php echo $this->form->renderField('observaciones'); ?>
				<?php echo $this->form->renderField('id_tipo_cliente'); ?>

			<?php
				foreach((array)$this->item->id_tipo_cliente as $value): 
					if(!is_array($value)):
						echo '<input type="hidden" class="id_tipo_cliente" name="jform[id_tipo_clientehidden]['.$value.']" value="'.$value.'" />';
					endif;
				endforeach;
			?>				<?php echo $this->form->renderField('referencia'); ?>
				<?php echo $this->form->renderField('vinos'); ?>
				<?php echo $this->form->renderField('tipo_cata'); ?>
				<?php echo $this->form->renderField('aperitivo'); ?>
				<?php echo $this->form->renderField('alergenos'); ?>
				<?php echo $this->form->renderField('menu_especial'); ?>
				<?php echo $this->form->renderField('token'); ?>
				<?php echo $this->form->renderField('fecha_creacion'); ?>
				<?php echo $this->form->renderField('fecha_modificacion'); ?>


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
