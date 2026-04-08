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

$canEdit = JFactory::getUser()->authorise('core.edit', 'com_ereservas');

if (!$canEdit && JFactory::getUser()->authorise('core.edit.own', 'com_ereservas'))
{
	$canEdit = JFactory::getUser()->id == $this->item->created_by;
}
//PARA MARIO, con este método sabemos el idioma, para traducir a inglés o castellano cuando sea:
//Deremos elegir las traducciones nombre_en, presentacion_en y descripcion_en dependiendo de esta variable. El resto se traducirán por constantes.
$idioma = JFactory::getLanguage()->getTag();

?>

<div class="item_fields">

	<table class="table">
		

		<tr>
			<th><?php echo JText::_('COM_ERESERVAS_FORM_LBL_VISITA_NOMBRE'); ?></th>
			<td><?php echo $this->item->nombre; ?></td>
		</tr>

		<tr>
			<th><?php echo JText::_('COM_ERESERVAS_FORM_LBL_VISITA_ID_SALA'); ?></th>
			<td><?php echo $this->item->id_sala; ?></td>
		</tr>

		<tr>
			<th><?php echo JText::_('COM_ERESERVAS_FORM_LBL_VISITA_AFORO_WEB'); ?></th>
			<td><?php echo $this->item->aforo_web; ?></td>
		</tr>

		<tr>
			<th><?php echo JText::_('COM_ERESERVAS_FORM_LBL_VISITA_AFORO_PRIVADO'); ?></th>
			<td><?php echo $this->item->aforo_privado; ?></td>
		</tr>

		<tr>
			<th><?php echo JText::_('COM_ERESERVAS_FORM_LBL_VISITA_COMIDA'); ?></th>
			<td><?php echo $this->item->comida; ?></td>
		</tr>

		<tr>
			<th><?php echo JText::_('COM_ERESERVAS_FORM_LBL_VISITA_CATA'); ?></th>
			<td><?php echo $this->item->cata; ?></td>
		</tr>

		<tr>
			<th><?php echo JText::_('COM_ERESERVAS_FORM_LBL_VISITA_OBSERVACIONES'); ?></th>
			<td><?php echo nl2br($this->item->observaciones); ?></td>
		</tr>

		<tr>
			<th><?php echo JText::_('COM_ERESERVAS_FORM_LBL_VISITA_PRECIO'); ?></th>
			<td><?php echo $this->item->precio; ?></td>
		</tr>

		<tr>
			<th><?php echo JText::_('COM_ERESERVAS_FORM_LBL_VISITA_FECHA'); ?></th>
			<td><?php echo $this->item->fecha; ?></td>
		</tr>

		<tr>
			<th><?php echo JText::_('COM_ERESERVAS_FORM_LBL_VISITA_HORA_INICIO'); ?></th>
			<td><?php echo $this->item->hora_inicio; ?></td>
		</tr>

		<tr>
			<th><?php echo JText::_('COM_ERESERVAS_FORM_LBL_VISITA_HORA_FIN'); ?></th>
			<td><?php echo $this->item->hora_fin; ?></td>
		</tr>

		<tr>
			<th><?php echo JText::_('COM_ERESERVAS_FORM_LBL_VISITA_IDIOMA'); ?></th>
			<td><?php echo $this->item->idioma; ?></td>
		</tr>

		<tr>
			<th><?php echo JText::_('COM_ERESERVAS_FORM_LBL_VISITA_ID_TIPO_VISITA'); ?></th>
			<td><?php echo $this->item->id_tipo_visita; ?></td>
		</tr>

		<tr>
			<th><?php echo JText::_('COM_ERESERVAS_FORM_LBL_VISITA_AFORO_OCUPADO'); ?></th>
			<td><?php echo $this->item->aforo_ocupado; ?></td>
		</tr>

		<tr>
			<th><?php echo JText::_('COM_ERESERVAS_FORM_LBL_VISITA_GUIA'); ?></th>
			<td><?php echo $this->item->guia; ?></td>
		</tr>

	</table>

</div>

<?php if($canEdit && $this->item->checked_out == 0): ?>

	<a class="btn" href="<?php echo JRoute::_('index.php?option=com_ereservas&task=visita.edit&id='.$this->item->id); ?>"><?php echo JText::_("COM_ERESERVAS_EDIT_ITEM"); ?></a>

<?php endif; ?>

<?php if (JFactory::getUser()->authorise('core.delete','com_ereservas.visita.'.$this->item->id)) : ?>

	<a class="btn btn-danger" href="#deleteModal" role="button" data-bs-toggle="modal">
		<?php echo JText::_("COM_ERESERVAS_DELETE_ITEM"); ?>
	</a>

	<div id="deleteModal" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="deleteModal" aria-hidden="true">
		<div class="modal-header">
			<button type="button" class="close" data-bs-dismiss="modal" aria-hidden="true">&times;</button>
			<h3><?php echo JText::_('COM_ERESERVAS_DELETE_ITEM'); ?></h3>
		</div>
		<div class="modal-body">
			<p><?php echo JText::sprintf('COM_ERESERVAS_DELETE_CONFIRM', $this->item->id); ?></p>
		</div>
		<div class="modal-footer">
			<button class="btn" data-bs-dismiss="modal">Close</button>
			<a href="<?php echo JRoute::_('index.php?option=com_ereservas&task=visita.remove&id=' . $this->item->id, false, 2); ?>" class="btn btn-danger">
				<?php echo JText::_('COM_ERESERVAS_DELETE_ITEM'); ?>
			</a>
		</div>
	</div>

<?php endif; ?>