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

use \Joomla\CMS\Language\Text;

JHtml::addIncludePath(JPATH_COMPONENT . '/helpers/html');
JHtml::_('bootstrap.tooltip');
JHtml::_('behavior.multiselect');
JHtml::_('formbehavior.chosen', 'select');
JHtml::_('jquery.framework');
$document = JFactory::getDocument();
$document->addScript('media/com_ereservas/js/locales/es.js?v=10');
$document->addScript('media/com_ereservas/js/corecalendar.js?v=11');
$document->addScript('media/com_ereservas/js/maindaygrid.js?v=10');
$document->addScript('media/com_ereservas/js/interaction.js?v=10');
$document->addStyleSheet('media/com_ereservas/css/maindaygrid.css');
$document->addStyleSheet('media/com_ereservas/css/maincore.css');
$document->addScript('media/com_ereservas/js/bootstrap-touchspin.js');
$document->addStyleSheet('media/com_ereservas/css/bootstrap-touchspin.css');
$document->addScript('media/com_ereservas/js/calendariovisitas.js?v=9');
$document->addStyleSheet('media/com_ereservas/css/calendariovisitas.css?v=9');

$canEdit = JFactory::getUser()->authorise('core.edit', 'com_ereservas');

if (!$canEdit && JFactory::getUser()->authorise('core.edit.own', 'com_ereservas'))
{
	$canEdit = JFactory::getUser()->id == $this->item->created_by;
}
//PARA MARIO, con este método sabemos el idioma, para traducir a inglés o castellano cuando sea:
//Deremos elegir las traducciones nombre_en, presentacion_en y descripcion_en dependiendo de esta variable. El resto se traducirán por constantes.
$idioma = JFactory::getLanguage()->getTag();
$paises= $this->getpais();
$nombre = ($idioma == 'es-ES')?'nombre':'nombre_en';
$presentacion = ($idioma == 'es-ES')?'presentacion':'presentacion_en';
$descripcion = ($idioma == 'es-ES')?'descripcion':'descripcion_en';

?>
<div class="row">
    <input type="hidden" value="<?php echo $this->item->id; ?>" id="idvisita">
    <div class="row visita">
        <div class="col-sm-5 seccion-texto">
            <p><img width="600" height="629" src="images/kiro_tradicional_reservas.jpg" class="wp-post-image" alt="" title="kiro_tradicional_reservas"></p>
            <p><?= Text::_('COM_ERESERVAS_RESERVA_TEXTO'); ?></p>
            <p style="margin-bottom: 0;"><strong><?= Text::_('COM_ERESERVAS_RESERVA_PRECIO_TITULO'); ?></strong></p>
            <p><strong><?= Text::_('COM_ERESERVAS_RESERVA_PRECIO_CANTIDAD'); ?></strong></p>
            <p><strong><?= Text::_('COM_ERESERVAS_RESERVA_CELIACOS'); ?></strong></p>
        </div>
        <div class="col-sm-7 texto">
            <h2 class="reserva-visita"><?= Text::_('COM_ERESERVAS_RESERVA_TITULO'); ?></h2>
            <p><?= Text::_('COM_ERESERVAS_RESERVA_PARRAFO1'); ?></p>
            <p><?= Text::_('COM_ERESERVAS_RESERVA_PARRAFO2'); ?></p>
            <div id="seccion-calendario"></div>
            <div class="row reservas-calendario">

                <div id="eventos" class="col-sm-8"></div>
                <div id="formularios" class="col-sm-4">
                    <div  id="adultos"  style="display: none;">
                        <h3><?= Text::_('COM_ERESERVAS_RESERVA_PERSONAS'); ?></h3>
                        <input type="hidden" id="jform_id_tipo_visita" value="">
                        <input type="hidden" id="jform_precio" value="<?php echo $this->item->precio ?>">
                        <label for="adultos" class="label-adultos" class="control-label"><?= Text::_('COM_ERESERVAS_RESERVA_ADULTOS'); ?> (<?php echo $this->item->precio ?> €)</label>
                        <input id="jform_adultos" type="text" value="--" name="adultos" class="form-control">
                        <a id="enviar-info" class="btn btn-primary"><?= Text::_('COM_ERESERVAS_RESERVA_RESERVAR'); ?> <span class="fa fa-calendar-check-o"></span></a>
                    </div>
                </div>
                <div class="col-sm-12" id="campos-reserva"  style="display: none;">
                    <h3 style="width: 100%"><?= Text::_('COM_ERESERVAS_RESERVA_RESERVAR'); ?></h3>
                    <div class="col-sm-6 inputs-reservas">
                        <label for="nombre" class="control-label"><?= Text::_('COM_ERESERVAS_RESERVA_NOMBRE'); ?> <span class="star">&nbsp;*</span></label>
                        <input id="jform_nombre_reserva" type="text" placeholder="Nombre..." class="form-control">
                    </div>
                    <div class="col-sm-6 inputs-reservas">
                        <label for="email" class="control-label"><?= Text::_('COM_ERESERVAS_RESERVA_CORREO'); ?> <span class="star">&nbsp;*</span></label>
                        <input id="jform_email_reserva" type="email" placeholder="Email..." class="form-control">
                    </div>
                    <div class="col-sm-6 inputs-reservas">
                        <label for="telefono" class="control-label"><?= Text::_('COM_ERESERVAS_RESERVA_TELEFONO'); ?> <span class="star">&nbsp;*</span></label>
                        <input id="jform_telefono_reserva" type="text" placeholder="Teléfono..." class="form-control">
                    </div>
                    <div class="col-sm-6 inputs-reservas">
                        <label for="pais"><?= Text::_('COM_ERESERVAS_RESERVA_PAIS'); ?> </label>
                        <select class="form-control" name="jform[pais]" id="jform_pais_reserva">
                            <?php foreach($paises as $key => $pais):?>
                                <option value="<?php echo $pais['id'];?>"><?php echo $pais["nombre"]; ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-sm-6 inputs-reservas">
                        <label for="cpostal" class="control-label"><?= Text::_('COM_ERESERVAS_RESERVA_CODIGO_POSTAL'); ?> <span class="star">&nbsp;*</span></label>
                        <input id="jform_cpostal_reserva" type="text" placeholder="Código Postal..." class="form-control">
                    </div>
                    <div class="col-sm-6 inputs-reservas">
                        <label for="municipio" class="control-label"><?= Text::_('COM_ERESERVAS_RESERVA_MUNICIPIO'); ?> </label>
                        <input id="jform_municipio_reserva" type="text" placeholder="Municipio..." class="form-control">
                    </div>
                    <div class="col-sm-12 inputs-reservas">
                        <label for="observaciones" class="control-label"><?= Text::_('COM_ERESERVAS_RESERVA_OBSERVACIONES'); ?> </label>
                        <textarea id="jform_observaciones_reserva" placeholder="¿Algo más que necesitamos saber?" class="form-control"></textarea>
                    </div>
                    <a href="#" id="enviar-reserva" class="btn btn-primary"><?= Text::_('COM_ERESERVAS_RESERVA_CONFIRMAR'); ?> </a>
                </div>
            </div>
        </div>
    </div>


    <div class="row seleccion-personas">
        <div class="col-sm-4"></div>
        <div class="col-sm-8">

        </div>
    </div>
</div>
