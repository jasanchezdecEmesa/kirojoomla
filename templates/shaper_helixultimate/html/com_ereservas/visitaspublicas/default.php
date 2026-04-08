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

JHtml::addIncludePath(JPATH_COMPONENT . '/helpers/html');
JHtml::_('bootstrap.tooltip');
JHtml::_('behavior.multiselect');
JHtml::_('formbehavior.chosen', 'select');

$user       = JFactory::getUser();
$userId     = $user->get('id');
/*
$listOrder  = $this->state->get('list.ordering');
$listDirn   = $this->state->get('list.direction');
$canCreate  = $user->authorise('core.create', 'com_ereservas') && file_exists(JPATH_COMPONENT . DIRECTORY_SEPARATOR . 'models' . DIRECTORY_SEPARATOR . 'forms' . DIRECTORY_SEPARATOR . 'visitaform.xml');
$canEdit    = $user->authorise('core.edit', 'com_ereservas') && file_exists(JPATH_COMPONENT . DIRECTORY_SEPARATOR . 'models' . DIRECTORY_SEPARATOR . 'forms' . DIRECTORY_SEPARATOR . 'visitaform.xml');
$canCheckin = $user->authorise('core.manage', 'com_ereservas');
$canChange  = $user->authorise('core.edit.state', 'com_ereservas');
$canDelete  = $user->authorise('core.delete', 'com_ereservas');
*/

$alias = (JFactory::getLanguage()->getTag() == 'es-ES')?'alias':'alias_en';


?>
<div class="fondo-enoturismo">
    <div class="interior">
        <h1>Actividades y visitas Muga</h1>
    </div>
</div>
<div class="interior selector-enoturismo">
    <h2>Visitas, cursos, catas... todo un mundo de sensaciones</h2>
    <p class="fs18">Disfruta de actividades de enoturismo en Bodegas Muga. Elige el tipo de experiencia que deseas realizar:</p>

        <div class="row cartas cartas-publicas">

            <?php foreach ($this->items as $i => $item) : ?>
                <div class="carta">
                    <a href="<?php echo JUri::getInstance().$item->alias; ?>">
                        <img class="card-img-top" src="<?php echo $item->imagen ?>" alt="Card image cap">
                        <div class="card-body">
                            <h3 class="card-title"><?php echo $item->nombre ?></h3>
                            <div class="card-text"><?php echo $item->presentacion ?></div>
                            <div class="seccion-precio">
                                <p class="precio"><?php echo $item->precio ?> €</p>
                                <p class="btn btn-info">Reservar</p>
                            </div>
                        </div>
                    </a>
                </div>
            <?php endforeach; ?>
            </div>

    </form>
</div>
