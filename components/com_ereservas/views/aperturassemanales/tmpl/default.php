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
$document->addScript(Uri::root() . 'media/com_ereservas/js/aperturassemanales.js?v=2');

?>
<div>
	<h1>Crear Servicios en el Calendario</h1>
    <p><em>En esta pantalla se crearán servicios según las aperturas semanales entre las fechas indicadas (fecha de inicio y de fin incluidas).</em></p>
    <p><em>Por favor, determina antes de crear estas visitas el calendario de fechas y festivos.</em></p>
	<div class="boton-crear"><button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#formulario">Crear</button></div>
	<table class="table table-stripped table-hover" id="tabla_aperturas">
		<thead>
		<tr>
			<th>Fecha Inicio</th>
			<th>Fecha Fin</th>
            <th>Tipo de Visita</th>
			<th>Acción</th>
		</tr>

		</thead>
		<tbody>

		<?php foreach($datos as $dato):?>
			<tr>
		<td><?php echo  JHtml::_('date', $dato['fecha_inicio'], 'DATE_FORMAT_LC1'); ?></td>
		<td><?php echo  JHtml::_('date', $dato['fecha_fin'], 'DATE_FORMAT_LC1'); ?></td>
        <td><?php echo $dato['visita']; ?></td>
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
				<h5 class="modal-title" id="formularioTitulo">Crear Visitas</h5>
				<button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<div class="modal-body">
				<div class="row">
					<div class="col-sm-6">
						<div class="form-group">
							<label for="fecha_inicio">Día Inicio</label>
							<input class="form-control" id="fecha_inicio" name="fecha_inicio" type="date" required>
						</div>
					</div>
					<div class="col-sm-6">
						<div class="form-group">
							<label for="fecha_fin">Dia Fin</label>
							<input class="form-control" type="date" name="fecha_fin" id="fecha_fin" required>
						</div>
					</div>
				</div>
                <div class="form-group">
                    <label for="id_tipo_visita">Tipo de Visita</label>
                    <select name="id_tipo_visita" id="id_tipo_visita">
                        <?php
                            foreach ($this->visitas as $visita) {
                        ?>
                            <option value="<?= $visita['id']; ?>"><?= $visita['nombre']; ?></option>
                        <?php
                            }
                        ?>
                    </select>
                </div>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
				<button type="button" class="btn btn-primary" id="guardar" onclick="guardarApertura(0)">Guardar</button>
			</div>
		</div>
	</div>
</div>

<!--* Modal para cuando se borra-->

<div class="modal fade" id="modal-eliminar" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="formularioTituloBorrar">Eliminar Visita</h5>
				<button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<div class="modal-body">
				<p>¿Estas seguro que quieres borrar las visitas <span id="borrar-nombre-visita"></span> del <span id="borrar-dia-inicio"></span> al <span id="borrar-dia-fin"></span>?</p>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
				<button type="button" class="btn btn-danger" id="#borrar" onclick="borrarApertura(0)">Borrar</button>
			</div>
		</div>
	</div>
</div>
