<?php
/**
 * @package    Módulo calendario
 *
 * @author     Emesa Software webmaster@emesa.com
 * @copyright  [COPYRIGHT]
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 * @link       https://emesasoftware.es
 */

defined('_JEXEC') or die;

// Access to module parameters

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
$document->addScript('media/com_ereservas/js/calendariovisitas.js?v=10');
$document->addStyleSheet('media/com_ereservas/css/calendariovisitas.css?v=12');

$canEdit = JFactory::getUser()->authorise('core.edit', 'com_ereservas');
$idioma = JFactory::getLanguage()->getTag();
$helper = new ModCalendarioHelper();
$paises = $helper->traerPaises();
$nombre = ($idioma == 'es-ES')?'nombre':'nombre_en';
$presentacion = ($idioma == 'es-ES')?'presentacion':'presentacion_en';
$descripcion = ($idioma == 'es-ES')?'descripcion':'descripcion_en';

?>
    <input type="hidden" value="1" id="idvisita">
    <div class="row visita">
        <div class="col-sm-4">
            <div id="seccion-calendario"></div>
        </div>
        <div class="col-sm-8">
            <div class="row reservas-calendario">
                <div id="eventos" class="col-sm-8"></div>
                <div id="formularios" class="col-sm-4">
                    <div  id="adultos"  style="display: none;">
                        <h3 style="text-transform: uppercase"><?= Text::_('COM_ERESERVAS_RESERVA_PERSONAS'); ?></h3>
                        <input type="hidden" id="jform_id_tipo_visita" value="">
                        <input type="hidden" id="jform_precio" value="80">
                        <label for="adultos" class="label-adultos" class="control-label"><?= Text::_('COM_ERESERVAS_RESERVA_ADULTOS'); ?> (80 €)</label>
                        <input id="jform_adultos" type="text" value="--" name="adultos" class="form-control">
                        <a id="enviar-info" class="btn btn-primary"><?= Text::_('COM_ERESERVAS_RESERVA_RESERVAR'); ?></a>
                    </div>
                </div>
                <div class="col-sm-12" id="campos-reserva"  style="display: none;">
                    <h3 style="width: 100%; text-transform: uppercase"><?= Text::_('COM_ERESERVAS_RESERVA_RESERVAR'); ?></h3>
                    <div class="col-sm-12 inputs-reservas">
                        <label for="nombre" class="control-label"><?= Text::_('COM_ERESERVAS_RESERVA_NOMBRE'); ?> <span class="star">&nbsp;*</span></label>
                        <input id="jform_nombre_reserva" type="text" placeholder="Nombre..." class="form-control">
                    </div>
                    <div class="col-sm-6 inputs-reservas">
                        <label for="email" class="control-label"><?= Text::_('COM_ERESERVAS_RESERVA_CORREO'); ?> <span class="star">&nbsp;*</span></label>
                        <input id="jform_email_reserva" type="email" placeholder="Email..." class="form-control">
                    </div>
                    <div class="col-sm-6 inputs-reservas">
                        <label for="email" class="control-label"><?= Text::_('COM_ERESERVAS_RESERVA_CORREO'); ?> <span class="star">&nbsp;*</span></label>
                        <input id="jform_email_reserva-2" type="email" placeholder="Escribe de nuevo tu correo" class="form-control">
                    </div>
                    <p class="text-danger" style="display: none" id="mensaje_error_correo_validacion">El correo no coincide</p>
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
                    <a tabindex="0" id="enviar-reserva" class="btn btn-primary"><?= Text::_('COM_ERESERVAS_RESERVA_CONFIRMAR'); ?> </a>
                </div>
            </div>
        </div>
    </div>
    <div class="row seleccion-personas">
        <div class="col-sm-4"></div>
        <div class="col-sm-8"></div>
</div>