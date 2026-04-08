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
$document->addScript(Uri::root() . 'media/com_ereservas/js/serviciossemalanes.js');
?>
<div>
	<h1>Servicios Semanales</h1>
	<div class="boton-crear"><button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#formulario">Crear</button></div>
	<table class="table table-stripped table-hover" id="tabla_servicios">
		<thead>
		<tr>
			<th>Día de la semana</th>
			<th>Hora</th>
			<th>Aforo</th>
			<th>Acción</th>
		</tr>
		</thead>
		<tbody>

		<?php foreach($datos as $dato):?>
			<tr>
			<?php switch ($dato['dia_semana']) {
				case "1":
					echo "<td>Lunes</td>";
					break;
				case "2":
					echo "<td>Martes</td>";
					break;
				case "3":
					echo "<td>Miércoles</td>";
					break;
				case "4":
					echo "<td>Jueves</td>";
					break;
				case "5":
					echo "<td>Viernes</td>";
					break;
				case "6":
					echo "<td>Sábado</td>";
					break;
				case "7":
					echo "<td>Domingo</td>";
					break;
			}
			?>

			<td><?php echo $dato['hora']; ?></td>
			<td><?php echo $dato['aforo']; ?></td>
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
				<h5 class="modal-title" id="formularioTitulo">Crear Reserva</h5>
				<button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<div class="modal-body">
					<div class="form-group">
						<label for="dia_semana">Día de la semana *</label>
						<select class="form-control" name="dia_semana" id="dia_semana" required>
							<option value="1">Lunes</option>
							<option value="2">Martes</option>
							<option value="3">Miércoles</option>
							<option value="4">Jueves</option>
							<option value="5">Viernes</option>
							<option value="6">Sábado</option>
							<option value="7">Domingo</option>
						</select>
					</div>
					<div class="row">
						<div class="col-sm-6">
							<div class="form-group">
								<label for="time">Hora *</label>
								<input class="form-control" type="time" name="hora" id="hora" required>
							</div>
						</div>
						<div class="col-sm-6">
							<div class="form-group">
								<label for="aforo">Plazas *</label>
								<input class="form-control" type="number" name="aforo" id="aforo" required>
							</div>
						</div>
					</div>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
				<button type="button" class="btn btn-primary" id="guardar" onclick="guardarServicio(0)">Guardar</button>
			</div>
		</div>
	</div>
</div>
<!--* Modal para cuando se borra-->

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
				<p>¿Estas seguro que quieres borrar la reserva del <span id="semana-span"></span> a las <span id="hora-span"></span> ?</p>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
				<button type="button" class="btn btn-danger" id="#borrar" onclick="borrarservicio(0)">Borrar</button>
			</div>
		</div>
	</div>
</div>





