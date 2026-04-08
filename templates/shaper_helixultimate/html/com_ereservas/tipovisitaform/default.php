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
JHtml::_('behavior.keepalive');
JHtml::_('behavior.tooltip');
JHtml::_('behavior.formvalidation');
JHtml::_('formbehavior.chosen', 'select');



// Load admin language file
$lang = JFactory::getLanguage();
$lang->load('com_ereservas', JPATH_SITE);
$doc = JFactory::getDocument();
$doc->addScript(JUri::base() . '/media/com_ereservas/js/form.js');

$user    = JFactory::getUser();
$canEdit = EreservasHelpersEreservas::canUserEdit($this->item, $user);


?>

<div class="tipovisita-edit front-end-edit">
	<?php if (!$canEdit) : ?>
		<h3>
			<?php throw new Exception(JText::_('COM_ERESERVAS_ERROR_MESSAGE_NOT_AUTHORISED'), 403); ?>
		</h3>
	<?php else : ?>
		<?php if (!empty($this->item->id)): ?>
			<h1><?php echo ucfirst(JText::sprintf('COM_ERESERVAS_EDIT_ITEM_TITLE', $this->item->nombre)); ?></h1>
		<?php else: ?>
			<h1><?php echo JText::_('COM_ERESERVAS_ADD_ITEM_TITLE'); ?></h1>
		<?php endif; ?>

		<form id="form-tipovisita"
			  action="<?php echo JRoute::_('index.php?option=com_ereservas&task=tipovisita.save'); ?>"
			  method="post" class="form-validate form-horizontal" enctype="multipart/form-data">
			
	<input type="hidden" name="jform[id]" value="<?php echo $this->item->id; ?>" />

	<input type="hidden" name="jform[ordering]" value="<?php echo $this->item->ordering; ?>" />

	<input type="hidden" name="jform[state]" value="<?php echo $this->item->state; ?>" />

	<input type="hidden" name="jform[checked_out]" value="<?php echo $this->item->checked_out; ?>" />

	<input type="hidden" name="jform[checked_out_time]" value="<?php echo $this->item->checked_out_time; ?>" />

				<?php echo $this->form->getInput('created_by'); ?>
				<?php echo $this->form->getInput('modified_by'); ?>
			<div class="row campos">
				<div class="col-sm-6">
	<?php echo $this->form->renderField('nombre'); ?>
				</div>
				<div class="col-sm-6">
	<?php echo $this->form->renderField('aforo_web'); ?>
				</div>
						<div class="col-sm-6">
	<?php echo $this->form->renderField('aforo_privado'); ?>
						</div>
				<div class="col-sm-6">
	<?php echo $this->form->renderField('comida'); ?>

</div>
				<div class="col-sm-6">
	<?php echo $this->form->renderField('cata'); ?>

</div>
				<div class="col-sm-6">
	<?php echo $this->form->renderField('observaciones'); ?>

</div>
				<div class="col-sm-6">
	<?php echo $this->form->renderField('precio'); ?>
</div>
				<div class="col-sm-6">
	<?php echo $this->form->renderField('id_sala'); ?>
	</div>
			</div>
			<div class="info-publica text-center">
				<h3>Informacion Publica</h3>
			</div>
            <div class="row">
                <div class="col-sm-6">
                    <h4>Información General</h4>
                    <div class="col-sm-12">
                        <?php echo $this->form->renderField('imagen'); ?>
                    </div>
                    <div class="col-sm-12">
                        <?php echo $this->form->renderField('presentacion'); ?>
                    </div>
                    <div class="col-sm-12">
                        <?php echo $this->form->renderField('descripcion'); ?>
                    </div>
                </div>
                <div class="col-sm-6">
                    <h4>Información en inglés</h4>
                    <div class="col-sm-12">
                        <?php echo $this->form->renderField('nombre_en'); ?>
                    </div>
                    <div class="col-sm-12">
                        <?php echo $this->form->renderField('presentacion_en'); ?>
                    </div>
                    <div class="col-sm-12">
                        <?php echo $this->form->renderField('descripcion_en'); ?>
                    </div>
                </div>
            </div>

</div>
	<?php foreach((array)$this->item->id_sala as $value): ?>
		<?php if(!is_array($value)): ?>
			<input type="hidden" class="id_sala" name="jform[id_salahidden][<?php echo $value; ?>]" value="<?php echo $value; ?>" />
		<?php endif; ?>
	<?php endforeach; ?>
			<div class="control-group">
				<div class="controls">

					<?php if ($this->canSave): ?>
						<button type="submit" class="validate btn btn-primary">
							<?php echo JText::_('JSUBMIT'); ?>
						</button>
					<?php endif; ?>
					<a class="btn"
					   href="<?php echo JRoute::_('index.php?option=com_ereservas&task=tipovisitaform.cancel'); ?>"
					   title="<?php echo JText::_('JCANCEL'); ?>">
						<?php echo JText::_('JCANCEL'); ?>
					</a>
				</div>
			</div>

			<input type="hidden" name="option" value="com_ereservas"/>
			<input type="hidden" name="task"
				   value="tipovisitaform.save"/>
			<?php echo JHtml::_('form.token'); ?>
		</form>
	<?php endif; ?>
</div>