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
$document->addScript(Uri::root() . 'media/com_ereservas/js/cierres.js?v=2');
?>
<div>
	<h1>Cierres y Festivos</h1>
	<div class="boton-crear"><button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#formulario">Crear</button></div>
	<table class="table table-stripped table-hover" id="tabla_cierres">
		<thead>
		<tr>
			<th>Fecha Inicio</th>
			<th>Fecha Fin</th>
            <th>Tipo de Cierre</th>
			<th>Asunto</th>
			<th>Acción</th>
		</tr>

		</thead>
		<tbody>

		<?php foreach($datos as $dato):?>
			<tr>
		<td><?php echo  JHtml::_('date', $dato['fecha_inicio'], 'DATE_FORMAT_LC1'); ?></td>
		<td><?php echo  JHtml::_('date', $dato['fecha_fin'], 'DATE_FORMAT_LC1'); ?></td>
        <td><?php echo $dato['tipo_cierre'];?></td>
		<td><?php echo $dato['concepto']; ?></td>
		<td><button class="btn btn-success editarcierre" onclick="modal_editar(<?php echo $dato['id'];?>)" data-bs-toggle="modal" data-bs-target="#formulario" type="button">Editar</button>
			<button class="btn btn-danger borrarcierre" onclick="modal_eliminar(<?php echo $dato['id'];?>)" data-bs-toggle="modal" data-bs-target="#modal-eliminar" type="button">Borrar</button>
		</td>
			</tr>
		<?php endforeach?>
		</tbody>
	</table>
</div>
<div class="modal fade" id="formulario" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="formularioTitulo">Crear Cierre</h5>
				<button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<div class="modal-body">
				<div class="row">
					<div class="col-sm-6">
						<div class="form-group">
							<label for="dia_inicio">Día Inicio</label>
							<input class="form-control" id="dia_inicio" name="dia_inicio" type="date" required>
						</div>
					</div>
					<div class="col-sm-6">
						<div class="form-group">
							<label for="dia_fin">Dia Fin</label>
							<input class="form-control" type="date" name="dia_fin" id="dia_fin" required>
						</div>
					</div>
				</div>
                <div class="form-group">
                    <label for="TipoCierre">Tipo de cierre</label>
                    <select name="select" id="tipo_cierre">
                        <option value="cierre" selected>Cierre</option>
                        <option value="festivo-laboral">Festivo Laboral</option>
                        <option value="sin-visita">Sin visita</option>
                    </select>
                </div>
				<div class="form-group">
						<label for="concepto">Concepto</label>
					<input class="form-control" id="concepto" name="concepto" type="text" required>
				</div>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
				<button type="button" class="btn btn-primary" id="guardar" onclick="guardarCierre(0)">Guardar</button>
			</div>
		</div>
	</div>
</div>

<!--* Modal para cuando se borra-->

<div class="modal fade" id="modal-eliminar" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="formularioTituloBorrar">Eliminar Cierre</h5>
				<button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<div class="modal-body">
				<p>¿Estas seguro que quieres borrar el cierre del <span id="dia-inicio"></span> al <span id="dia-fin"></span> ?</p>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
				<button type="button" class="btn btn-danger" id="#borrar" onclick="borrarcierre(0)">Borrar</button>
			</div>
		</div>
	</div>
</div>
