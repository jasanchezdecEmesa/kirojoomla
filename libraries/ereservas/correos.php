<?php
/**
 * @package     Joomla.Platform
 * @subpackage  Application
 *
 * @copyright   Copyright (C) 2005 - 2018 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

defined('JPATH_PLATFORM') or die;

class EreservasCorreos
{
    /**
     * Método para enviar Mails
     */
    public static function enviarMail($id,$tipo) {
        //Llamamos y asignamos a variables la propia configuracion de Joomla para enviar correos
        $mailer = JFactory::getMailer();
        $config = JFactory::getConfig();
        $sender = array(
            $config->get( 'mailfrom' ),
            $config->get( 'fromname' )
        );
        $mailer->setSender($sender);

        //Comporbamos el tipo de correo, si es de confirmacion o para enviar el pago, generamos el token
        if($tipo == 'confirmacion' || $tipo == 'pago') {
            EreservasCorreos::generarTokenReserva($id);
        }
        //Llamamos a una funcion dentro de esta libreria para obtener la reserva que nos han pasado
        $datos = EreservasCorreos::obtenerReserva($id);

        $datos['fecha'] = new JDate($datos['fecha']);

        /*if (empty($datos['fecha']) || $datos['fecha'] === '0000-00-00 00:00:00' || $datos['fecha'] === '0000-00-00') {
            $datos['fecha'] = null;
        } else {
            $datos['fecha'] = new JDate($datos['fecha']);
        }*/

        $plantilla = EreservasCorreos::cargarPlantilla($tipo,$datos);

        $mailer->addRecipient($datos['email']);

        $mailer->setSubject($plantilla['asunto']);

        $body = $plantilla['cuerpo'];
        $mailer->isHtml(true);
        $mailer->Encoding = 'base64';
        $mailer->setBody($body);



        $send = $mailer->Send();

        if ( $send !== true ) {
            $archivo_log_ko=JPATH_ADMINISTRATOR."/logs/correos/ko.log";
            $log_insertar_ko = $id." - ".$tipo . " - " . date("Y-m-d H:i:s") . " - Error: " . $send . "\r\n";

            file_put_contents($archivo_log_ko,$log_insertar_ko, FILE_APPEND);
            return $send;
        } else {
            $archivo_log_ok=JPATH_ADMINISTRATOR."/logs/correos/ok.log";
            $log_insertar_ok = $id." - ".$tipo . " - " . date("Y-m-d H:i:s") . "\r\n";

            file_put_contents($archivo_log_ok,$log_insertar_ok, FILE_APPEND);
            return $send;
        }
    }

    /**
     * Método para obtener el correo de la reserva
     */
    protected static function obtenerReserva($id){

        $db = JFactory::getDbo();

        $query = $db->getQuery(true);

        $query->select(array('a.id','a.email','a.token','a.nombre','a.telefono','b.fecha','b.hora_inicio as hora','c.nombre as visita','d.nombre as idioma','a.personas','a.pagado','f.id as factura'));
        $query->from($db->quoteName('#__ereservas_reserva', 'a'));
        $query->leftJoin($db->quoteName('#__ereservas_visita', 'b'). 'ON (a.id_visita = b.id)');
        $query->leftJoin($db->quoteName('#__ereservas_tipo_visita', 'c'). 'ON (b.id_tipo_visita = c.id)');
        $query->leftJoin($db->quoteName('#__ereservas_idioma', 'd'). 'ON (a.id_idioma = d.id)');
        $query->leftJoin($db->quoteName('#__ereservas_factura', 'f'). 'ON (a.id = f.id_reserva)');
        $query->where($db->quoteName('a.id') . ' = '. $db->quote($id));
        $query->order($db->quoteName('f.state') . '  desc');
        $query->order($db->quoteName('f.id') . '  desc');

        $db->setQuery($query,0,1);

        $result = $db->loadAssoc();

        return $result;

    }

    /**
     * Funcion donde segun el tipo de correo que queramos mandar, cargaremos una plantilla u otro
     * @param $tipo -> es el tipo de correo que se quiere enviar, segun el tipo, cargara una plantilla o no
     * @param $datos -> datos de la reserva para en un futuro usarlo en la plantilla
     * @return array
     */
    protected static function cargarPlantilla($tipo,$datos){

        $plantilla = array();

        switch($tipo){
            case 'pago':
                $plantilla['asunto'] = 'Correo de pago';
                $plantilla['cuerpo'] = EreservasCorreos::PlantillaPago($datos);
                break;
            case 'confirmacion':
                $plantilla['asunto'] = 'Correo de Confirmación';
                $plantilla['cuerpo'] = EreservasCorreos::PlantillaConfirmacion($datos);
                break;
            case 'confirmada-por-cliente':
                $plantilla['asunto'] = 'Reserva confirmada con éxito';
                $plantilla['cuerpo'] = EreservasCorreos::PlantillaConfirmadoPorCliente($datos);
                break;
            case 'pagook':
                $plantilla['asunto'] = 'Confirmación de pago';
                $plantilla['cuerpo'] = EreservasCorreos::PlantillaPagook($datos);
                break;
            case 'pagoko':
                $plantilla['asunto'] = 'Error en el pago';
                $plantilla['cuerpo'] = EreservasCorreos::PlantillaPagoko($datos);
                break;
            case 'cancelacion':
                $plantilla['asunto'] = 'Reserva cancelada';
                $plantilla['cuerpo'] = EreservasCorreos::PlantillaCancelacion($datos);
                break;

        }

        return $plantilla;

    }

    /**
     * Funcion donde generamos un id unico para seguidamente insertarlo en la base de datos y poder comprobar que el token sigue siendo el
     * mismo cuando acceda a confirmar su reserva, a la pasarela de pago...
     * @param $id
     * @return string
     */
    public static function generarTokenReserva($id){

        //generamos un id unico
        $token = uniqid();

        //Creamos un objeto, para asi simplificar la introduccion de datos en la BD
        $object = new stdClass();

        $object->id = (int) $id;
        $object->token = $token;
        $object->fecha_modificacion = date("Y-m-d H:i:s");

        //Hacemos un update via objeto
        JFactory::getDbo()->updateObject('#__ereservas_reserva', $object, 'id');
        return $token;
    }


    /**
     * Funcion para cargar la plantilla de pago
     * @param $datos -> datos de la reserva para poder rellenar en esta plantilla
     * @return string
     */
    protected static function PlantillaPago($datos){

        $mensaje = array();

        $mensaje['titulo'] = 'Pago de reserva:';

        $mensaje['mensaje'] = '<div>';
        $mensaje['mensaje'].= '<p>Acaba de solicitar la reserva de Kiro Sushi:</p>';
        $mensaje['mensaje'].= '<ul style="padding: 5px 10px;">';
        $mensaje['mensaje'].= '<li><strong>Nombre: </strong>'.$datos['nombre'].'</li>';
        $mensaje['mensaje'].= '<li><strong>Correo: </strong>'.$datos['email'].'</li>';
        $mensaje['mensaje'].= '<li><strong>Teléfono: </strong>'.$datos['telefono'].'</li>';
        $mensaje['mensaje'].= '<li><strong>Visita: </strong>'.$datos['visita'].'</li>';
        $mensaje['mensaje'].= '<li><strong>Día: </strong>'.JHtml::_('date', $datos['fecha'], JText::_('DATE_FORMAT_LC')).'</li>';
        $mensaje['mensaje'].= '<li><strong>Hora: </strong>'.substr($datos['hora'],0,5).'</li>';
        //$mensaje['mensaje'].= '<li><strong>Idioma: </strong>'.$datos['idioma'].'</li>';
        $mensaje['mensaje'].= '<li><strong>Plazas: </strong>'.$datos['personas'].'</li>';
        $mensaje['mensaje'].= '</ul>';
        $mensaje['mensaje'].= '<p>Por favor, para finalizar la reserva realice el pago en la siguiente URL:</p>';
        //Aqui ponemos un enlace donde el cliente tendra que escribir sus datos de facturacion, lo mandamos con el token que tenga asignado y hayamos generado anteriormente
        $mensaje['mensaje'].= '<p><a href="'.JRoute::_(JURi::base().'index.php?option=com_ereservas&view=pagoform').
            '&reservaidcorreo='.$datos["id"].'&token='.$datos["token"].'" style="color: #d1a447; text-decoration: none;">Realizar Pago</a></p>';
        $mensaje['mensaje'].= '</div>';

        $html = EreservasCorreos::plantillaComun($mensaje);

        return $html;
    }

    /**
     * Funcion para cargar la plantilla de confirmacion de reserva
     * @param $datos -> datos de la reserva para poder rellenar en esta plantilla
     * @return string
     */
    protected static function PlantillaConfirmacion($datos){

        $mensaje = array();

        $mensaje['titulo'] = 'Confirmación de reserva:';

        $mensaje['mensaje']  = '<div>';
        $mensaje['mensaje'].= '<p>Hola: '.$datos['nombre'].', ha realizado una reserva en Kiro Sushi:</p>';
        $mensaje['mensaje'].= '<ul style="padding: 5px 10px;">';
        $mensaje['mensaje'].= '<li><strong>Nombre: </strong>'.$datos['nombre'].'</li>';
        $mensaje['mensaje'].= '<li><strong>Correo: </strong>'.$datos['email'].'</li>';
        $mensaje['mensaje'].= '<li><strong>Teléfono: </strong>'.$datos['telefono'].'</li>';
        $mensaje['mensaje'].= '<li><strong>Visita: </strong>'.$datos['visita'].'</li>';
        $mensaje['mensaje'].= '<li><strong>Día: </strong>'.JHtml::_('date', $datos['fecha'], JText::_('DATE_FORMAT_LC')).'</li>';
        $mensaje['mensaje'].= '<li><strong>Hora: </strong>'.substr($datos['hora'],0,5).'</li>';
        //$mensaje['mensaje'].= '<li><strong>Idioma: </strong>'.$datos['idioma'].'</li>';
        $mensaje['mensaje'].= '<li><strong>Plazas: </strong>'.$datos['personas'].'</li>';
        $mensaje['mensaje'].= '</ul>';
        $mensaje['mensaje'].= '<p>Por favor, para confirmar la reserva pulse en la siguiente URL:</p>';
        $mensaje['mensaje'].= '<p><a href="'.JRoute::_(JURi::base().'index.php?option=com_ereservas&view=confirmarreserva').
                                '&reserva='.$datos['token'].'&codigo='.$datos['id'].'">Confirmar Reserva</a></p>';
        $mensaje['mensaje'].= '</div>';

        $html = EreservasCorreos::plantillaComun($mensaje);

        return $html;
    }

    /**
     * Funcion para cargar la plantilla de confirmacion Confirmado via cliente
     * @param $datos -> datos de la reserva para poder rellenar en esta plantilla
     * @return string
     */
    protected static function PlantillaConfirmadoPorCliente($datos){

        $mensaje = array();

        $mensaje['titulo'] = 'Confirmación de reserva:';

        $mensaje['mensaje']  = '<div>';
        $mensaje['mensaje'] .= '<p>¡Le confirmamos que su reserva se ha realizado con éxito!</p>';
        $mensaje['mensaje'] .= '<ul style="padding: 5px 10px;">';
        $mensaje['mensaje'] .= '<li><strong>Nombre: </strong>'.$datos['nombre'].'</li>';
        $mensaje['mensaje'] .= '<li><strong>Correo: </strong>'.$datos['email'].'</li>';
        $mensaje['mensaje'] .= '<li><strong>Teléfono: </strong>'.$datos['telefono'].'</li>';
        $mensaje['mensaje'] .= '<li><strong>Visita: </strong>'.$datos['visita'].'</li>';
        $mensaje['mensaje'] .= '<li><strong>Día: </strong>'.JHtml::_('date', $datos['fecha'], JText::_('DATE_FORMAT_LC')).'</li>';
        $mensaje['mensaje'] .= '<li><strong>Hora: </strong>'.substr($datos['hora'],0,5).'</li>';
        //$mensaje['mensaje'] .= '<li><strong>Idioma: </strong>'.$datos['idioma'].'</li>';
        $mensaje['mensaje'] .= '<li><strong>Plazas: </strong>'.$datos['personas'].'</li>';
        $mensaje['mensaje'] .= '</ul>';
        $mensaje['mensaje'] .= '<p>Cualquier duda que tenga puede ponerse en contacto con nosotros.</p>';
        $mensaje['mensaje'] .= '<b>Le informamos de la importancia en la puntualidad, dado que es un solo turno y misma hora para todos los clientes.
        En caso de llegar más tarde de la hora no podrá acceder al restaurante, y así perdiendo su reserva y fianza</b>';
        $mensaje['mensaje'] .= '</div>';

        $html = EreservasCorreos::plantillaComun($mensaje);

        return $html;
    }
    /**
     * Funcion para cargar la plantilla de confirmacion de pago
     * @param $datos -> datos de la reserva para poder rellenar en esta plantilla
     * @return string
     */
    protected static function PlantillaPagook($datos){

        $mensaje = array();

        $mensaje['titulo'] = 'Pago realizado con éxito:';

        $mensaje['mensaje']  = '<div>';
        $mensaje['mensaje'] .= '<p>Le escribimos este mail para confirmar que su pago se ha realizado correctamente</p>';
        $mensaje['mensaje'] .= '<ul style="padding: 5px 10px;">';
        $mensaje['mensaje'] .= '<li><strong>Nº Reserva: </strong>'.$datos['id'].'</li>';
        $mensaje['mensaje'] .= '<li><strong>Nº Pedido: </strong>'.$datos['factura'].'</li>';
        $mensaje['mensaje'] .= '<li><strong>Nombre: </strong>'.$datos['nombre'].'</li>';
        $mensaje['mensaje'] .= '<li><strong>Correo: </strong>'.$datos['email'].'</li>';
        $mensaje['mensaje'] .= '<li><strong>Teléfono: </strong>'.$datos['telefono'].'</li>';
        $mensaje['mensaje'] .= '<li><strong>Visita: </strong>'.$datos['visita'].'</li>';
        $mensaje['mensaje'] .= '<li><strong>Día: </strong>'.JHtml::_('date', $datos['fecha'], JText::_('DATE_FORMAT_LC')).'</li>';
        $mensaje['mensaje'] .= '<li><strong>Hora: </strong>'.substr($datos['hora'],0,5).'</li>';
        //$mensaje['mensaje'] .= '<li><strong>Idioma: </strong>'.$datos['idioma'].'</li>';
        $mensaje['mensaje'] .= '<li><strong>Plazas: </strong>'.$datos['personas'].'</li>';
        $mensaje['mensaje'] .= '</ul>';
        $mensaje['mensaje'] .= '<p>Si necesita modificar su reserva, póngase en contacto con nosotros</p>';
        $mensaje['mensaje'] .= '<b>Le informamos de la importancia en la puntualidad, dado que es un solo turno y misma hora para todos los clientes.
        En caso de llegar más tarde de la hora no podrá acceder al restaurante, y así perdiendo su reserva y fianza</b>';

        $mensaje['mensaje'] .= '</div>';

        $html = EreservasCorreos::plantillaComun($mensaje);

        return $html;
    }

    /**
     * Funcion para cargar la plantilla de error en el pago
     * @param $datos -> datos de la reserva para poder rellenar en esta plantilla
     * @return string
     */
    protected static function PlantillaPagoko($datos){

        $mensaje = array();

        $mensaje['titulo'] = 'Problema en el pago:';

        $mensaje['mensaje']  = '<div>';
        $mensaje['mensaje'].= '<p>Le escribimos este mail para comunicarle que ha habido un problema con el pago de esta reserva:</p>';
        $mensaje['mensaje'].= '<ul style="padding: 5px 10px;">';
        $mensaje['mensaje'] .= '<li><strong>Nº Reserva: </strong>'.$datos['id'].'</li>';
        $mensaje['mensaje'] .= '<li><strong>Nº Pedido: </strong>'.$datos['factura'].'</li>';
        $mensaje['mensaje'].= '<li><strong>Nombre: </strong>'.$datos['nombre'].'</li>';
        $mensaje['mensaje'].= '<li><strong>Correo: </strong>'.$datos['email'].'</li>';
        $mensaje['mensaje'].= '<li><strong>Teléfono: </strong>'.$datos['telefono'].'</li>';
        $mensaje['mensaje'].= '<li><strong>Visita: </strong>'.$datos['visita'].'</li>';
        $mensaje['mensaje'].= '<li><strong>Día: </strong>'.JHtml::_('date', $datos['fecha'], JText::_('DATE_FORMAT_LC')).'</li>';
        $mensaje['mensaje'].= '<li><strong>Hora: </strong>'.substr($datos['hora'],0,5).'</li>';
        //$mensaje['mensaje'].= '<li><strong>Idioma: </strong>'.$datos['idioma'].'</li>';
        $mensaje['mensaje'].= '<li><strong>Plazas: </strong>'.$datos['personas'].'</li>';
        $mensaje['mensaje'].= '</ul>';
        $mensaje['mensaje'].= '<p>Si todavia desea reservar su visita, inténtelo de nuevo en unos minutos.</p>';
        $mensaje['mensaje'] .= '</div>';

        $html = EreservasCorreos::plantillaComun($mensaje);

        return $html;
    }

    /**
     * Funcion para cargar la plantilla de cancelación
     * @param $datos -> datos de la reserva para poder rellenar en esta plantilla
     * @return string
     */
    protected static function PlantillaCancelacion($datos){

        $mensaje = array();

        $mensaje['titulo'] = 'Su reserva se ha cancelado:';

        $mensaje['mensaje']  = '<div>';
        $mensaje['mensaje'] .= '<p>La siguiente reserva se ha cancelado:</p>';
        $mensaje['mensaje'] .= '<ul style="padding: 5px 10px;">';
        $mensaje['mensaje'] .= '<li><strong>Nº Reserva: </strong>'.$datos['id'].'</li>';
        $mensaje['mensaje'] .= '<li><strong>Nº Pedido: </strong>'.$datos['factura'].'</li>';
        $mensaje['mensaje'] .= '<li><strong>Nombre: </strong>'.$datos['nombre'].'</li>';
        $mensaje['mensaje'] .= '<li><strong>Correo: </strong>'.$datos['email'].'</li>';
        $mensaje['mensaje'] .= '<li><strong>Teléfono: </strong>'.$datos['telefono'].'</li>';
        $mensaje['mensaje'] .= '<li><strong>Visita: </strong>'.$datos['visita'].'</li>';
        $mensaje['mensaje'] .= '<li><strong>Día: </strong>'.JHtml::_('date', $datos['fecha'], JText::_('DATE_FORMAT_LC')).'</li>';
        $mensaje['mensaje'] .= '<li><strong>Hora: </strong>'.substr($datos['hora'],0,5).'</li>';
        //$mensaje['mensaje'] .= '<li><strong>Idioma: </strong>'.$datos['idioma'].'</li>';
        $mensaje['mensaje'] .= '<li><strong>Plazas: </strong>'.$datos['personas'].'</li>';
        $mensaje['mensaje'] .= '</ul>';
//        $mensaje['mensaje'] .= '<p>Por favor, si no deseaba la cancelación, póngase en contacto con nosotros para solucionar la incidencia.</p>';
        $mensaje['mensaje'] .= '</div>';

        $html = EreservasCorreos::plantillaCancelada($mensaje);

        return $html;
    }


    /**
     * Plantilla común
     *
     * Revice un array con ['titulo'] y['mensaje']
     *
     * devuelve html
     *
     */
    protected static function plantillaComun($datos){

        $html = '<div class="moz-forward-container">
<div id="wrapper" dir="ltr" style="background-color: #f5f5f5; margin: 0; padding: 15px 0; width: 100%; -webkit-text-size-adjust: none;">
<table border="0" width="100%" cellspacing="0" cellpadding="0">
<tbody>
<tr>
<td align="center" valign="top">
<div id="template_header_image">
<p style="margin: 0;"><img style="border: none; display: inline-block; font-size: 14px; font-weight: bold; height: auto; outline: none; text-decoration: none; text-transform: capitalize; vertical-align: middle; margin-left: 0; margin-right: 0;" src="https://kirosushi.es/images/correo.jpg" alt="Logo Kiro" /></p>
</div>
<table id="template_container" style="background-color: #fdfdfd; border: 1px solid #dcdcdc; box-shadow: 0 1px 4px rgba(0, 0, 0, 0.1); border-radius: 3px;" border="0" width="600" cellspacing="0" cellpadding="0">
<tbody>
<tr>
<td align="center" valign="top"><!-- Header --></p>
<table id="template_header" style="color: #000; border-bottom: 0; font-weight: bold; line-height: 100%; vertical-align: middle; font-family: \'Helvetica Neue\',Helvetica, Roboto, Arial,sans-serif; border-radius: 3px 3px 0 0;" border="0" width="600" cellspacing="0" cellpadding="0">
<tbody>
<tr>
<td id="header_wrapper" style="padding: 36px 48px; display: block;">
<h1 style="font-family: \'Helvetica Neue\',Helvetica, Roboto, Arial,sans-serif; font-size: 30px; font-weight: 300; line-height: 150%; margin: 0; text-align: center; text-shadow: 0 1px 0 #7797b4; color: #000;text-decoration: underline;text-decoration-color: #cfa144;">'.$datos['titulo'].'</h1>
</td>
</tr>
</tbody>
</table>
<p><!-- End Header --></td>
</tr>
<tr>
<td align="center" valign="top"><!-- Body --></p>
<table id="template_body" border="0" width="600" cellspacing="0" cellpadding="0">
<tbody>
<tr>
<td id="body_content" style="background-color: #fdfdfd;" valign="top"><!-- Content --></p>
<table border="0" width="100%" cellspacing="0" cellpadding="20">
<tbody>
<tr>
<td style="padding: 48px 48px 32px;" valign="top">
<div id="body_content_inner" style="color: #737373; font-family: \'Helvetica Neue\',Helvetica, Roboto, Arial,sans-serif; font-size: 16px; line-height: 150%; text-align: left;">
'.$datos["mensaje"].'
<p>Quedamos a su disposición para cualquier consulta al respecto en el email <a style="color: #d1a447; text-decoration: none;" href="mailto:info@kirosushi.es">info@kirosushi.es</a></p>
<p style="margin: 0 0 16px;">Muchas gracias por la confianza depositada en Kiro Sushi.</p>
<p style="margin: 60px 0;text-align: center;"><b>Kiro Sushi.</b></p>
</div>
</td>
</tr>
</tbody>
</table>
<p><!-- End Content --></td>
</tr>
</tbody>
</table>
<p><!-- End Body --></td>
</tr>
</tbody>
</table>
</td>
</tr>
<tr>
<td align="center" valign="top"><!-- Footer --></p>
<table id="template_footer" border="0" width="600" cellspacing="0" cellpadding="10">
<tbody>
<tr>
<td style="padding: 0; border-radius: 6px;" valign="top">
<table border="0" width="100%" cellspacing="0" cellpadding="10">
<tbody>
<tr>
</td>
</tr>
</tbody>
</table>
</td>
</tr>
</tbody>
</table>
<p><!-- End Footer --></td>
</tr>
</tbody>
</table>
</div>
</div>
<p>&nbsp;</p>
';


        return $html;
    }

    /**
     * Plantilla común
     *
     * Revice un array con ['titulo'] y['mensaje']
     *
     * devuelve html
     *
     */
    protected static function plantillaCancelada($datos){

        $html = '<div class="moz-forward-container">
<div id="wrapper" dir="ltr" style="background-color: #f5f5f5; margin: 0; padding: 15px 0; width: 100%; -webkit-text-size-adjust: none;">
<table border="0" width="100%" cellspacing="0" cellpadding="0">
<tbody>
<tr>
<td align="center" valign="top">
<div id="template_header_image">
<p style="margin: 0;"><img style="border: none; display: inline-block; font-size: 14px; font-weight: bold; height: auto; outline: none; text-decoration: none; text-transform: capitalize; vertical-align: middle; margin-left: 0; margin-right: 0;" src="https://kirosushi.es/images/correo.jpg" alt="Logo Kiro" /></p>
</div>
<table id="template_container" style="background-color: #fdfdfd; border: 1px solid #dcdcdc; box-shadow: 0 1px 4px rgba(0, 0, 0, 0.1); border-radius: 3px;" border="0" width="600" cellspacing="0" cellpadding="0">
<tbody>
<tr>
<td align="center" valign="top"><!-- Header --></p>
<table id="template_header" style="color: #000; border-bottom: 0; font-weight: bold; line-height: 100%; vertical-align: middle; font-family: \'Helvetica Neue\',Helvetica, Roboto, Arial,sans-serif; border-radius: 3px 3px 0 0;" border="0" width="600" cellspacing="0" cellpadding="0">
<tbody>
<tr>
<td id="header_wrapper" style="padding: 36px 48px; display: block;">
<h1 style="font-family: \'Helvetica Neue\',Helvetica, Roboto, Arial,sans-serif; font-size: 30px; font-weight: 300; line-height: 150%; margin: 0; text-align: center; text-shadow: 0 1px 0 #7797b4; color: #000;text-decoration: underline;text-decoration-color: #cfa144;">'.$datos['titulo'].'</h1>
</td>
</tr>
</tbody>
</table>
<p><!-- End Header --></td>
</tr>
<tr>
<td align="center" valign="top"><!-- Body --></p>
<table id="template_body" border="0" width="600" cellspacing="0" cellpadding="0">
<tbody>
<tr>
<td id="body_content" style="background-color: #fdfdfd;" valign="top"><!-- Content --></p>
<table border="0" width="100%" cellspacing="0" cellpadding="20">
<tbody>
<tr>
<td style="padding: 48px 48px 32px;" valign="top">
<div id="body_content_inner" style="color: #737373; font-family: \'Helvetica Neue\',Helvetica, Roboto, Arial,sans-serif; font-size: 16px; line-height: 150%; text-align: left;">
'.$datos["mensaje"].'
<p>Quedamos a su disposición para cualquier consulta al respecto en el email <a style="color: #d1a447; text-decoration: none;" href="mailto:info@kirosushi.es">info@kirosushi.es</a></p>
<p style="margin: 0 0 16px;">Muchas gracias por la confianza depositada en Kiro Sushi.</p>
<p style="margin: 60px 0;text-align: center;"><b>Kiro Sushi.</b></p>
</div>
</td>
</tr>
</tbody>
</table>
<p><!-- End Content --></td>
</tr>
</tbody>
</table>
<p><!-- End Body --></td>
</tr>
</tbody>
</table>
</td>
</tr>
<tr>
<td align="center" valign="top"><!-- Footer --></p>
<table id="template_footer" border="0" width="600" cellspacing="0" cellpadding="10">
<tbody>
<tr>
<td style="padding: 0; border-radius: 6px;" valign="top">
<table border="0" width="100%" cellspacing="0" cellpadding="10">
<tbody>
<tr>
</td>
</tr>
</tbody>
</table>
</td>
</tr>
</tbody>
</table>
<p><!-- End Footer --></td>
</tr>
</tbody>
</table>
</div>
</div>
<p>&nbsp;</p>
';


        return $html;
    }

    public static function correoListaEspera($datos){

        $email = isset($datos['email']) ? trim($datos['email']) : '';
        if (empty($email) || !JMailHelper::isEmailAddress($email)) {
            return false;
        }

        $mensaje = array();
        $mensaje['titulo'] = 'Se han liberado plazas para su dia en kirosushi.es:';

        $mailer = JFactory::getMailer();
        $config = JFactory::getConfig();
        $sender = array(
            $config->get('mailfrom'),
            $config->get('fromname')
        );

        $mailer->setSender($sender);
        $mailer->addRecipient($email);
        $mailer->setSubject($mensaje['titulo']);

        $mensaje['mensaje']  = '<div>';
        $mensaje['mensaje'] .= '<p>Le escribimos este mail para comunicarle que han quedado plazas libres en su día:</p>';
        $mensaje['mensaje'] .= '<ul style="padding: 5px 10px;">';
        $mensaje['mensaje'] .= '<li><strong>Nº Plazas: </strong>'.$datos['plazas'].'</li>';
        $mensaje['mensaje'] .= '<li><strong>Fecha: </strong>'
            . JHtml::_('date', $datos['fecha_liberada'], JText::_('DATE_FORMAT_LC3'))
            . '</li>';
        $mensaje['mensaje'] .= '</ul>';
        $mensaje['mensaje'] .= '<p>Para realizar la reserva pulse <a href="https://kirosushi.es/reservas">aquí</a>. Tenga en cuenta que la lista de espera es amplia y puede que otra persona haya realizado la reserva si tarda en realizar la suya.</p>';
        $mensaje['mensaje'] .= '</div>';

        $plantilla = EreservasCorreos::plantillaComun($mensaje);

        $mailer->isHtml(true);
        $mailer->Encoding = 'base64';
        $mailer->setBody($plantilla);

        $send = $mailer->Send();

        if ( $send !== true ) {
            $archivo_log_ko=JPATH_ADMINISTRATOR."/logs/correos/ko.log";
            $log_insertar_ko = "listaespera - ". date("Y-m-d H:i:s") . " - Error: " . $send . "\r\n";

            file_put_contents($archivo_log_ko,$log_insertar_ko, FILE_APPEND);
            return $send;
        } else {
            $archivo_log_ok=JPATH_ADMINISTRATOR."/logs/correos/ok.log";
            $log_insertar_ok = "listaespera - " . date("Y-m-d H:i:s") . "\r\n";

            file_put_contents($archivo_log_ok,$log_insertar_ok, FILE_APPEND);
            return $send;
        }
    }

}








