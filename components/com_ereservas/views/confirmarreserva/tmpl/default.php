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
?>

<div style="min-height: 400px; padding-top: 100px;">
	<?php
	if($this->exito > 0) {
		echo '<p style="font-size: 21px;">Su reserva se ha <strong>confirmado con éxito</strong>, en breve le llegará un correo confirmando los datos de la misma.</p>';
	} else {
		echo '<p style="font-size: 21px;">Ha habido un problema con la confirmación de su reserva, por favor, <strong>póngase en contacto con nosotros.</strong></p>';
	}
	?>
</div>




