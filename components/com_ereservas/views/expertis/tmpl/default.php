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
HTMLHelper::_('bootstrap.tooltip');
HTMLHelper::_('behavior.multiselect');
HTMLHelper::_('formbehavior.chosen', 'select');

$user       = Factory::getUser();
$userId     = $user->get('id');
$listOrder  = $this->state->get('list.ordering');
$listDirn   = $this->state->get('list.direction');
$canCreate  = $user->authorise('core.create', 'com_ereservas') && file_exists(JPATH_COMPONENT . DIRECTORY_SEPARATOR . 'models' . DIRECTORY_SEPARATOR . 'forms' . DIRECTORY_SEPARATOR . 'reservaform.xml');
$canEdit    = $user->authorise('core.edit', 'com_ereservas') && file_exists(JPATH_COMPONENT . DIRECTORY_SEPARATOR . 'models' . DIRECTORY_SEPARATOR . 'forms' . DIRECTORY_SEPARATOR . 'reservaform.xml');
$canCheckin = $user->authorise('core.manage', 'com_ereservas');
$canChange  = $user->authorise('core.edit.state', 'com_ereservas');
$canDelete  = $user->authorise('core.delete', 'com_ereservas');
$selectExpertis = $this->selectExpertis;

?>

<div>
    <form action="<?php echo htmlspecialchars(Uri::getInstance()->toString()); ?>" method="post" >
        <div class="row">
            <div class="col-sm-3">
                <label for="fecha-desde">Fecha desde <input type="date" name="fecha-desde" value="<?php if($this->fechadesde) echo date('Y-m-d',strtotime($this->fechadesde)); ?>" required></label>
            </div>
            <div class="col-sm-3">
                <label for="fecha-desde">Fecha hasta <input type="date" name="fecha-hasta"value="<?php if($this->fechahasta) echo date('Y-m-d',strtotime($this->fechahasta)); ?>" ></label>
            </div>
            <div class="col-sm-3">
                <input type="submit" class="btn btn-primary mt25" value="Filtrar" name="filtrar">
            </div>
            <div class="col-sm-3">
                <input type="submit" class="btn btn-primary mt25" value="Descargar" name="descargar">
            </div>
        </div>
    </form>
</div>

    <?php //echo JLayoutHelper::render('default_filter', array('view' => $this), dirname(__FILE__)); ?>
    <table class="table table-striped" id="reservaList">
        <thead>
        <tr>
            <th class=''>
                <?php echo JHtml::_('grid.sort',  'Pedido', 'a.nombre', $listDirn, $listOrder); ?>
            </th>
            <th class=''>
                <?php echo JHtml::_('grid.sort',  'Reserva', 'a.id', $listDirn, $listOrder); ?>
            </th>
            <th class=''>
                <?php echo JHtml::_('grid.sort',  'Fecha Pago', 'a.fecha_creacion', $listDirn, $listOrder); ?>
            </th>
            <th class=''>
                <?php echo JHtml::_('grid.sort',  'Fecha Visita', 'fecha_visita', $listDirn, $listOrder); ?>
            </th>
            <th class=''>
                <?php echo JHtml::_('grid.sort',  'COM_ERESERVAS_RESERVAS_NOMBRE', 'a.nombre', $listDirn, $listOrder); ?>
            </th>
            <th class=''>
                <?php echo JHtml::_('grid.sort',  'Tipo Pago', 'a.nombre', $listDirn, $listOrder); ?>
            </th>
            <th class=''>
                <?php echo JHtml::_('grid.sort',  'Factura DNI / NIF', 'a.nombre', $listDirn, $listOrder); ?>
            </th>
            <th class=''>
                <?php echo JHtml::_('grid.sort',  'Factura Direccion', 'a.nombre', $listDirn, $listOrder); ?>
            </th>




        </tr>
        </thead>

        <tbody>
        <?php foreach ($selectExpertis as $k => $selectExperti) : ?>

            <tr class="row<?php echo $k % 2; ?>">
                <td>
                    <?php echo $selectExperti['id_factura']; ?>
                </td>

                <td>
                    <?php echo $selectExperti['id']; ?>
                </td>
                <td>
                    <?php echo date('d/m/Y',strtotime($selectExperti['fecha_creacion'])); ?>
                </td>
                <td>
                    <?php echo date('d/m/Y',strtotime($selectExperti['fecha_visita'])).' '.substr($selectExperti['hora_visita'],0,5); ?>
                </td>
                <td>
                    <?php echo $selectExperti['nombre']; ?>
                </td>
                <td>
                    <?php echo $selectExperti['pagado']; ?>
                </td>

                <td>
                    <?php echo ($selectExperti['factura_nif'] == "")?"--":$selectExperti['factura_nif']; ?>
                </td>

                <td>
                    <?php echo ($selectExperti['factura_direccion'] == "")?"--":$selectExperti['factura_direccion']; ?>
                </td>

            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>


    <input type="hidden" name="task" value=""/>
    <input type="hidden" name="boxchecked" value="0"/>
    <input type="hidden" name="filter_order" value="<?php echo $listOrder; ?>"/>
    <input type="hidden" name="filter_order_Dir" value="<?php echo $listDirn; ?>"/>
    <?php echo HTMLHelper::_('form.token'); ?>
</form>

<?php if($canDelete) : ?>
    <script type="text/javascript">

        jQuery(document).ready(function () {
            jQuery('.delete-button').click(deleteItem);
        });

        function deleteItem() {

            if (!confirm("<?php echo Text::_('COM_ERESERVAS_DELETE_MESSAGE'); ?>")) {
                return false;
            }
        }
    </script>
<?php endif; ?>
