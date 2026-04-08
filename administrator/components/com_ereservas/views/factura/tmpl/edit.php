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
		
	js('input:hidden.id_reserva').each(function(){
		var name = js(this).attr('name');
		if(name.indexOf('id_reservahidden')){
			js('#jform_id_reserva option[value="'+js(this).val()+'"]').attr('selected',true);
		}
	});
	js("#jform_id_reserva").trigger("liszt:updated");
	});

	Joomla.submitbutton = function (task) {
		if (task == 'factura.cancel') {
			Joomla.submitform(task, document.getElementById('factura-form'));
		}
		else {
			
			if (task != 'factura.cancel' && document.formvalidator.isValid(document.id('factura-form'))) {
				
				Joomla.submitform(task, document.getElementById('factura-form'));
			}
			else {
				alert('<?php echo $this->escape(Text::_('JGLOBAL_VALIDATION_FORM_FAILED')); ?>');
			}
		}
	}
</script>

<form
	action="<?php echo JRoute::_('index.php?option=com_ereservas&layout=edit&id=' . (int) $this->item->id); ?>"
	method="post" enctype="multipart/form-data" name="adminForm" id="factura-form" class="form-validate form-horizontal">

	
	<input type="hidden" name="jform[id]" value="<?php echo $this->item->id; ?>" />
	<input type="hidden" name="jform[ordering]" value="<?php echo $this->item->ordering; ?>" />
	<input type="hidden" name="jform[state]" value="<?php echo $this->item->state; ?>" />
	<input type="hidden" name="jform[checked_out]" value="<?php echo $this->item->checked_out; ?>" />
	<input type="hidden" name="jform[checked_out_time]" value="<?php echo $this->item->checked_out_time; ?>" />
	<?php echo $this->form->renderField('created_by'); ?>
	<?php echo $this->form->renderField('modified_by'); ?>
	<?php echo JHtml::_('bootstrap.startTabSet', 'myTab', array('active' => 'factura')); ?>
	<?php echo JHtml::_('bootstrap.addTab', 'myTab', 'factura', JText::_('COM_ERESERVAS_TAB_FACTURA', true)); ?>
	<div class="row-fluid">
		<div class="span10 form-horizontal">
			<fieldset class="adminform">
				<legend><?php echo JText::_('COM_ERESERVAS_FIELDSET_FACTURA'); ?></legend>
				<?php echo $this->form->renderField('nombre'); ?>
				<?php echo $this->form->renderField('nif'); ?>
				<?php echo $this->form->renderField('cpostal'); ?>
				<?php echo $this->form->renderField('direccion'); ?>
				<?php echo $this->form->renderField('municipio'); ?>
				<?php echo $this->form->renderField('precio'); ?>
				<?php echo $this->form->renderField('id_reserva'); ?>
				<?php
				foreach((array)$this->item->id_reserva as $value)
				{
					if(!is_array($value))
					{
						echo '<input type="hidden" class="id_reserva" name="jform[id_reservahidden]['.$value.']" value="'.$value.'" />';
					}
				}
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
	<?php echo JHtml::_('bootstrap.endTab'); ?>

	
	<?php echo JHtml::_('bootstrap.endTabSet'); ?>

	<input type="hidden" name="task" value=""/>
	<?php echo JHtml::_('form.token'); ?>

</form>
