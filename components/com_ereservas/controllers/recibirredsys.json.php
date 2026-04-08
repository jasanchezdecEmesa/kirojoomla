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
JLoader::registerPrefix('Ereservas', JPATH_LIBRARIES . '/ereservas');

class EreservasControllerRecibirredsys extends EreservasController
{
/**
 * Esta clase se ejecuta cuando mandamos el formulario oculto en el pagoForm, aqui recibimos los datos y los mandamos
 * De nuevo a la libreria donde preparamos el pago
 */
    public function pago(){
        $jinput = JFactory::getApplication()->input;
        $version = $jinput->get('Ds_SignatureVersion', false, 'html');
        $datos = $jinput->get('Ds_MerchantParameters', false, 'html');
        $signatureRecibida = $jinput->get('Ds_Signature', false, 'html');
        $reserva= $jinput->get('idreservapago', false, 'html');
        $token= $jinput->get('token', false, 'html');
        
        EreservasRedsys::pagoJSON($version, $datos, $signatureRecibida, $reserva,$token);
    }

}