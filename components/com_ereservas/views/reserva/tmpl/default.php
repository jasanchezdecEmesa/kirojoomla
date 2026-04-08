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
?>

<div class="item_fields">

	<table class="table">
		

		<tr>
			<th><?php echo JText::_('COM_ERESERVAS_FORM_LBL_RESERVA_NOMBRE'); ?></th>
			<td><?php echo $this->item->nombre; ?></td>
		</tr>

		<tr>
			<th><?php echo JText::_('COM_ERESERVAS_FORM_LBL_RESERVA_TELEFONO'); ?></th>
			<td><?php echo $this->item->telefono; ?></td>
		</tr>

		<tr>
			<th><?php echo JText::_('COM_ERESERVAS_FORM_LBL_RESERVA_EMAIL'); ?></th>
			<td><?php echo $this->item->email; ?></td>
		</tr>

		<tr>
			<th><?php echo JText::_('COM_ERESERVAS_FORM_LBL_RESERVA_ID_VISITA'); ?></th>
			<td><?php echo $this->item->id_visita; ?></td>
		</tr>

		<tr>
			<th><?php echo JText::_('COM_ERESERVAS_FORM_LBL_RESERVA_ID_IDIOMA'); ?></th>
			<td><?php echo $this->item->id_idioma; ?></td>
		</tr>

		<tr>
			<th><?php echo JText::_('COM_ERESERVAS_FORM_LBL_RESERVA_PERSONAS'); ?></th>
			<td><?php echo $this->item->personas; ?></td>
		</tr>

		<tr>
			<th><?php echo JText::_('COM_ERESERVAS_FORM_LBL_RESERVA_TARJETA'); ?></th>
			<td><?php echo $this->item->tarjeta; ?></td>
		</tr>

		<tr>
			<th><?php echo JText::_('COM_ERESERVAS_FORM_LBL_RESERVA_PAGADO'); ?></th>
			<td><?php echo $this->item->pagado; ?></td>
		</tr>

		<tr>
			<th><?php echo JText::_('COM_ERESERVAS_FORM_LBL_RESERVA_ESTADO'); ?></th>
			<td><?php echo $this->item->estado; ?></td>
		</tr>

		<tr>
			<th><?php echo JText::_('COM_ERESERVAS_FORM_LBL_RESERVA_ID_USUARIO'); ?></th>
			<td><?php echo $this->item->id_usuario; ?></td>
		</tr>

		<tr>
			<th><?php echo JText::_('COM_ERESERVAS_FORM_LBL_RESERVA_ID_PAIS'); ?></th>
			<td><?php echo $this->item->id_pais; ?></td>
		</tr>

		<tr>
			<th><?php echo JText::_('COM_ERESERVAS_FORM_LBL_RESERVA_CPOSTAL'); ?></th>
			<td><?php echo $this->item->cpostal; ?></td>
		</tr>

		<tr>
			<th><?php echo JText::_('COM_ERESERVAS_FORM_LBL_RESERVA_OBSERVACIONES'); ?></th>
			<td><?php echo nl2br($this->item->observaciones); ?></td>
		</tr>

		<tr>
			<th><?php echo JText::_('COM_ERESERVAS_FORM_LBL_RESERVA_ID_TIPO_CLIENTE'); ?></th>
			<td><?php echo $this->item->id_tipo_cliente; ?></td>
		</tr>

		<tr>
			<th><?php echo JText::_('COM_ERESERVAS_FORM_LBL_RESERVA_REFERENCIA'); ?></th>
			<td><?php echo $this->item->referencia; ?></td>
		</tr>

		<tr>
			<th><?php echo JText::_('COM_ERESERVAS_FORM_LBL_RESERVA_VINOS'); ?></th>
			<td><?php echo $this->item->vinos; ?></td>
		</tr>

		<tr>
			<th><?php echo JText::_('COM_ERESERVAS_FORM_LBL_RESERVA_TIPO_CATA'); ?></th>
			<td><?php echo $this->item->tipo_cata; ?></td>
		</tr>

		<tr>
			<th><?php echo JText::_('COM_ERESERVAS_FORM_LBL_RESERVA_APERITIVO'); ?></th>
			<td><?php echo $this->item->aperitivo; ?></td>
		</tr>

		<tr>
			<th><?php echo JText::_('COM_ERESERVAS_FORM_LBL_RESERVA_ALERGENOS'); ?></th>
			<td><?php echo $this->item->alergenos; ?></td>
		</tr>

		<tr>
			<th><?php echo JText::_('COM_ERESERVAS_FORM_LBL_RESERVA_MENU_ESPECIAL'); ?></th>
			<td><?php echo $this->item->menu_especial; ?></td>
		</tr>

		<tr>
			<th><?php echo JText::_('COM_ERESERVAS_FORM_LBL_RESERVA_TOKEN'); ?></th>
			<td><?php echo $this->item->token; ?></td>
		</tr>

		<tr>
			<th><?php echo JText::_('COM_ERESERVAS_FORM_LBL_RESERVA_FECHA_CREACION'); ?></th>
			<td><?php echo $this->item->fecha_creacion; ?></td>
		</tr>

		<tr>
			<th><?php echo JText::_('COM_ERESERVAS_FORM_LBL_RESERVA_FECHA_MODIFICACION'); ?></th>
			<td><?php echo $this->item->fecha_modificacion; ?></td>
		</tr>

		<tr>
			<th><?php echo JText::_('COM_ERESERVAS_FORM_LBL_RESERVA_ASISTENCIA'); ?></th>
			<td><?php echo $this->item->asistencia; ?></td>
		</tr>

		<tr>
			<th><?php echo JText::_('COM_ERESERVAS_FORM_LBL_RESERVA_PRECIO'); ?></th>
			<td><?php echo $this->item->precio; ?></td>
		</tr>

	</table>

</div>

<?php if($canEdit && $this->item->checked_out == 0): ?>

	<a class="btn" href="<?php echo JRoute::_('index.php?option=com_ereservas&task=reserva.edit&id='.$this->item->id); ?>"><?php echo JText::_("COM_ERESERVAS_EDIT_ITEM"); ?></a>

<?php endif; ?>

<?php if (JFactory::getUser()->authorise('core.delete','com_ereservas.reserva.'.$this->item->id)) : ?>

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
			<a href="<?php echo JRoute::_('index.php?option=com_ereservas&task=reserva.remove&id=' . $this->item->id, false, 2); ?>" class="btn btn-danger">
				<?php echo JText::_('COM_ERESERVAS_DELETE_ITEM'); ?>
			</a>
		</div>
	</div>

<?php endif; ?>