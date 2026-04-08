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

use \Joomla\CMS\Factory;
use \Joomla\CMS\Uri\Uri;

JHtml::addIncludePath(JPATH_COMPONENT . '/helpers/html');
JHtml::_('bootstrap.tooltip');
JHtml::_('behavior.multiselect');
JHtml::_('formbehavior.chosen', 'select');
$document = JFactory::getDocument();
$lang = JFactory::getLanguage();
$datos= $this->datosinicio;
$document->addScript(Uri::root() . 'media/com_ereservas/js/aperturacalendario.js');

date_default_timezone_set('Europe/Madrid');
$fecha_actual = date('Y-m-d H:i:s');
echo $fecha_actual;
?>



<div>
	<h1>Aperturas de Calendarios</h1>
	<div class="boton-crear"><button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#formulario">Crear</button></div>
	<table class="table table-stripped table-hover" id="tabla_apertura">
		<thead>
		<tr>
			<th>Fecha Apertura</th>
			<th>Fecha final</th>
			<th>Acción</th>
		</tr>
		</thead>
		<tbody>

		<?php foreach($datos as $dato):?>
		<td><?php echo JHtml::_('date', $dato['fecha_apertura'], 'd-m-Y H:i');  ?></td>
        <td><?php echo JHtml::_('date', $dato['fecha_final'], 'd-m-Y'); ?></td>

			<td><button class="btn btn-success editarservicio" onclick="modal_editar(<?php echo $dato['id'];?>)" data-bs-toggle="modal" data-bs-target="#formulario" type="button">Editar</button>
				<button class="btn btn-danger borrarservicio" onclick="modal_eliminar(<?php echo $dato['id'];?>)" data-bs-toggle="modal" data-bs-target="#modal-eliminar" type="button">Borrar</button>
			</td>
			</tr>

		<?php endforeach;?>
		</tbody>
	</table>
</div>
<div class="modal fade" id="formulario" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="formularioTitulo">Crear Fecha Apertura</h5>
				<button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<div class="modal-body">
				<div class="row">
					<div class="col-sm-6">
						<div class="form-group">
							<label for="fecha_apertura">Fecha Apertura</label>
                            <input type="datetime-local" name="fecha_apertura" id="fecha_apertura">
						</div>
					</div>
					<div class="col-sm-6">
						<div class="form-group">
							<label for="fecha_final">Fecha Final</label>
                            <input type="date" name="fecha_final" id="fecha_final">
						</div>
					</div>
				</div>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
				<button type="button" class="btn btn-primary" id="guardar" onclick="guardarapertura(0)">Guardar</button>
			</div>
		</div>
	</div>
</div>
<div class="modal fade" id="modal-eliminar" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="formularioTituloBorrar">Eliminar Reserva</h5>
				<button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<div class="modal-body">
				<p>¿Estas seguro que quieres borrar la siguiente apertura?
					<ul>
					<li><strong>Fecha Apertura: </strong><span id="apertura-span"></span></li>
					<li><strong>Fecha Final: </strong><span id="final-span"></span></li>
				</ul>
				</p>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
				<button type="button" class="btn btn-danger" id="#borrar" onclick="borrarservicio(0)">Borrar</button>
			</div>
		</div>
	</div>
</div>