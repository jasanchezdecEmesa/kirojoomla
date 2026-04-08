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
$paises_agrupados= $this->paisesagrupados;
?>

<h1>Informe Reserva País/Municipio</h1>
<!--Aqui generamos el formulario que filtra todos los registros por fecha para posteriormente agruparlo -->
<div class="row">
    <div class="col-sm-8">
        <form action="<?php echo htmlspecialchars(Uri::getInstance()->toString()); ?>" method="post"
              name="adminFiltros" id="filtros">
            <label for="fecha-desde">Fecha desde <input type="date" name="fecha-desde" required></label>
            <label for="fecha-desde">Fecha hasta <input type="date" name="fecha-hasta"></label>
            <input type="submit" class="btn btn-primary" name="enviar-fechas">

        </form>
    </div>
    <div class="col-sm-4">
        <form action="<?php echo htmlspecialchars(Uri::getInstance()->toString()); ?>" method="post"
              name="descargarForm" id="DescargarForm">
            <input type="submit" class="btn btn-primary" value="Descargar" name="descargar">
        </form>
    </div>
</div>

<table id="conteopaises"  class="table table-striped">
    <thead>
        <th class=''><p>País</p></th>
        <th class=''><p>Código postal</p></th>
        <th><p>Total</p></th>
    </thead>
    <tbody>
        <?php foreach ($paises_agrupados as $pais){

            echo "<tr>";
            echo "<td>" . $pais['nombre'] . "</td>";
            if($pais['cpostal'] == ""){
                if($pais['nombre'] === "España"){
                    echo "<td><em> Sin código postal </em></td>";
                }
                else{
                    echo "<td><em> Extranjero </em></td>";
                }
            }else{
                echo "<td><em>". $pais['cpostal'] ."</em></td>";
            }
            echo "<td>" . $pais['personas'] . "</td>";
            echo "</tr>";
        }
?>
    </tbody>
</table>