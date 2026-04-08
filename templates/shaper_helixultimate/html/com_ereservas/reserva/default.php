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
JHtml::_('behavior.tooltip');
 JHTML::_('behavior.modal', 'a.modal');

$canEdit = JFactory::getUser()->authorise('core.edit', 'com_ereservas');
$doc = JFactory::getDocument();
$doc->addScript(JUri::base() . '/media/com_ereservas/js/desencriptar.js');

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
			<th><?php echo JText::_('COM_ERESERVAS_FORM_LBL_RESERVA_APELLIDOS'); ?></th>
			<td><?php echo $this->item->apellidos; ?></td>
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
			<td>
<!--				--><?php //echo $this->item->tarjeta; ?>
				*********
				<button type="button" class="btn btn-primary btn-desencriptar" data-toggle="modal" id='btn-desencriptar' data-target="#exampleModalCenter">
					Desencriptar
				</button>

		</tr>

		<tr>
			<th><?php echo JText::_('COM_ERESERVAS_FORM_LBL_RESERVA_PAGADO'); ?></th>
			<td></td>
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
		<input type="hidden" id="reserva_id" name="reserva_id" value="<?php echo $this->item->id; ?>">

	</table>

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
					<p>Holaa</p>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-secondary" data-dismiss="modal">Ok</button>
				</div>
			</div>
		</div>
	</div>






<?php if($canEdit && $this->item->checked_out == 0): ?>

	<a class="btn" href="<?php echo JRoute::_('index.php?option=com_ereservas&task=reserva.edit&id='.$this->item->id); ?>"><?php echo JText::_("COM_ERESERVAS_EDIT_ITEM"); ?></a>

<?php endif; ?>

<?php if (JFactory::getUser()->authorise('core.delete','com_ereservas.reserva.'.$this->item->id)) : ?>

	<a class="btn btn-danger" href="#deleteModal" role="button" data-toggle="modal">
		<?php echo JText::_("COM_ERESERVAS_DELETE_ITEM"); ?>
	</a>

	<div id="deleteModal" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="deleteModal" aria-hidden="true">
		<div class="modal-header">
			<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
			<h3><?php echo JText::_('COM_ERESERVAS_DELETE_ITEM'); ?></h3>
		</div>
		<div class="modal-body">
			<p><?php echo JText::sprintf('COM_ERESERVAS_DELETE_CONFIRM', $this->item->id); ?></p>
		</div>
		<div class="modal-footer">
			<button class="btn" data-dismiss="modal">Close</button>
			<a href="<?php echo JRoute::_('index.php?option=com_ereservas&task=reserva.remove&id=' . $this->item->id, false, 2); ?>" class="btn btn-danger">
				<?php echo JText::_('COM_ERESERVAS_DELETE_ITEM'); ?>
			</a>
		</div>
	</div>

<?php endif; ?>