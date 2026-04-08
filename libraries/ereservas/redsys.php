<?php
use Joomla\CMS\Factory;
use \Joomla\CMS\Language\Text;
// Se incluye la librería
include 'redsys/apiRedsys.php';
class EreservasRedsys
{
    /**
     * Funcion donde generamos los parametros para mandarselos a Redsys mediante el formulario
     * @param $idfactura -> id factura que acaba de generar el cliente cuando ha rellenado el formulario
     * @param $cantidad -> Cantidad a pagar
     * @param $idreserva -> la reserva que se esta haciendo
     * @return string
     */
    public static function pago($idfactura, $cantidad, $idreserva, $idioma_web){
        $precio= @$cantidad[0]->total;
        //El precio lo tenemos que multiplicar por 100 para que Redys muestre el correcto
        $precio= $precio*100;
        // $precio = 1;
        $pago = new RedsysAPI();
        $valores = EreservasRedsys::mirarvalores($idreserva);

        // Se Rellenan los campos
        $pago->setParameter("DS_MERCHANT_AMOUNT",$precio);
        $pago->setParameter("DS_MERCHANT_ORDER",$idfactura);
        $pago->setParameter("DS_MERCHANT_MERCHANTCODE", $valores['fuc']);
        $pago->setParameter("DS_MERCHANT_CURRENCY",$valores['moneda']);
        $pago->setParameter("DS_MERCHANT_TRANSACTIONTYPE",$valores['trans']);
        $pago->setParameter("DS_MERCHANT_TERMINAL",$valores['terminal']);
        $pago->setParameter("DS_MERCHANT_MERCHANTURL",$valores['url']);
        $pago->setParameter("DS_MERCHANT_CONSUMERLANGUAGE",$idioma_web);
        $pago->setParameter("DS_MERCHANT_URLOK",$valores['urlOK']);
        $pago->setParameter("DS_MERCHANT_URLKO",$valores['urlKO']);
        $parametros= $pago->createMerchantParameters();
        $firma= $pago->createMerchantSignature($valores['clave']);
        $codigo= '<form name="frm" id="form-redsys" action="'.$valores['servidor'].'" method="POST">
Ds_Merchant_SignatureVersion <input type="text" name="Ds_SignatureVersion" value="'.$valores['version'].'"/></br>
Ds_Merchant_MerchantParameters <input type="text" name="Ds_MerchantParameters" value="'.$parametros.'"/></br>
Ds_Merchant_Signature <input type="text" name="Ds_Signature" value="'.$firma.'"/></br>
<input type="submit" value="Enviar" >';
        return $codigo;
    }

    /**
     * Funcion donde obtenemos los valores fijos para Redsys y tambien creamos las urls de pagook, pagoko...
     * Estos valores son los genericos y si hay cambio de cliente o de la pasarela de pago deberiamos solo cambiar estos datos
     * @param $idreserva
     * @return array
     */
    protected static function mirarvalores($idreserva){

        $token = EreservasRedsys::obtenerToken($idreserva);

        $valores= array();
        //Entorno de Pruebas
        //$valores['servidor'] = 'https://sis-t.redsys.es:25443/sis/realizarPago';
        //$valores ['clave']= 'asq7HjrUOBfKmC576ILgskD5srU870gJ7';
        //Entorno real Real
        $valores['servidor'] = 'https://sis.redsys.es/sis/realizarPago';
        $valores ['clave']= 'aXCxHVS9lLpSsfwnZu/OD1O2RkGSFL3z';
        
        
        $valores['fuc'] = '124167875';
        $valores ['terminal']="1";
        $valores['moneda']="978";
        $valores ['trans']="0";
        $valores ['url']= JUri::base()."index.php?option=com_ereservas&task=recibirredsys.pago&format=json&idreservapago=".$idreserva."&token=".$token;
        $valores ['urlOK']= JUri::base()."index.php?option=com_ereservas&view=pagoform&pago=true&idreservapago=".$idreserva;
        $valores ['urlKO']= JUri::base()."index.php?option=com_ereservas&view=pagoform&pago=false&idreservapago=".$idreserva;
        $valores ['version'] = 'HMAC_SHA256_V1';
        
        return $valores;
    }

    /**
     * Funcion donde creamos lo necesario para el pago
     * @param $version -> version de la firma
     * @param $datos -> datos que hemos recibido de Redsys
     * @param $signatureRecibida -> firma que meos recibido
     * @param $reserva -> id reserva
     * @param $token -> token que hemos generado
     * @throws Exception
     */
    public static function pagoJSON($version, $datos, $signatureRecibida, $reserva,$token){
        $app  = Factory::getApplication();
        $miObj = new RedsysAPI();
        $decodec = $miObj->decodeMerchantParameters($datos);
        $valores= EreservasRedsys::mirarvalores($reserva);
        $kc = $valores['clave'];
        $firma = $miObj->createMerchantSignatureNotif($kc,$datos);

        //comprobamos que la firma que hemos generado correctamente aqui es la misma que la recibida para comprobar
        //que hemos sido nosotros
        if ($firma === $signatureRecibida){

            //llamamos a la funcion donde tambien comprobamos si el token es el mismo que hemos generado antes en la reserva
            $check = EreservasRedsys::comprobarToken($reserva,$token);
            //Si es correcta las 2 verificaciones, podemos mirar ahora la respuesta del pago
            if($check > 0) {
                $respuesta=json_decode($decodec);
                $codigo_respuesta= $respuesta->Ds_Response;
                //Si el codigo respuesta es ok (0000), tendremos que hacer la funcionalidad
                if($codigo_respuesta==='0000'){
                    EreservasRedsys::activarFactura((int)$respuesta->Ds_Order, (int)$reserva);
                    EreservasRedsys::pagook($reserva);
                    EreservasCorreos::enviarMail($reserva, 'pagook');
                }
                //Si el pago no se ha hecho correctamente, hacemos la funcionalidad de error
                else{
                    EreservasRedsys::pagoko($reserva);
                    $visita = EreservasRedsys::obtenerVisita($reserva);
                    EreservasRecalculo::recalcularaforo($visita);
                    EreservasCorreos::enviarMail($reserva, 'pagoko');
                    echo 'No se ha podido completar el pago, codigo de error'.$codigo_respuesta, 'error';
                }
            }
            //si el Token no es valido
            else {
                echo 'Token Invalido';
            }
        }
        //si la firma no es valida
        else {
            //EreservasRedsys::pagoko($reserva);
            //EreservasCorreos::enviarMail($reserva, 'pagoko');
            //$app->enqueueMessage('Firma no valida', 'error');
            echo 'Firma no válida';
        }
    }

    /**
     * Funcion que se ejecuta cuando el pago ha sido correcto, donde cambiamos
     * algunos de los campos de la tabla reserva
     * @param $idreserva
     */
    protected static function pagook($idreserva){
        $db3 = JFactory::getDbo();
        $query3 = $db3->getQuery(true);
        $campos = array(
            $db3->quoteName('pagado') . ' = ' . $db3->quote('online'),
            $db3->quoteName('estado') . ' = ' . $db3->quote('confirmado'),
            $db3->quoteName('state') . ' = ' . $db3->quote(1)
        );
        $condiciones = array(
            $db3->quoteName('id') . '=' . $db3->quote($idreserva),
        );
        $query3->update($db3->quoteName('#__ereservas_reserva'))->set($campos)->where($condiciones);
        $db3->setQuery($query3);

        $result = $db3->execute();
    }

    /**
     * Funcion que se ejecuta cuando el pago ha sido fallido, donde cambiamos
     * algunos de los campos de la tabla reserva
     * @param $idreserva
     */
    protected static function pagoko($idreserva){
        $db4 = JFactory::getDbo();
        $query4 = $db4->getQuery(true);
        $campos = array(
            $db4->quoteName('pagado') . ' = ' . $db4->quote('no'),
            $db4->quoteName('estado') . ' = ' . $db4->quote('cancelado'),
            $db4->quoteName('state') . ' = ' . $db4->quote(0)
        );
        $condiciones = array(
            $db4->quoteName('id') . '=' . $db4->quote($idreserva),
        );
        $query4->update($db4->quoteName('#__ereservas_reserva'))->set($campos)->where($condiciones);
        $db4->setQuery($query4);

        $result = $db4->execute();
    }

    /**
     * Funcion para obtener el token de la reserva
     * @param $id -> id reserva
     * @return mixed
     */
    protected static function obtenerToken($id){

        $db = JFactory::getDbo();
        $query = $db->getQuery(true);

        $query->select($db->quoteName('token'));
        $query->from($db->quoteName('#__ereservas_reserva'));
        $query->where($db->quoteName('id') . ' = '. $db->quote($id));

        $db->setQuery($query);

        $db->setQuery($query,0,1);

        $results = $db->loadResult();

        return $results;
    }

    /**
     * Funcion para obtner datos de la visita
     * @param $id -> idvisita
     * @return mixed
     */
    public static function obtenerVisita($id){

        $db = JFactory::getDbo();
        $query = $db->getQuery(true);

        $query->select($db->quoteName('id_visita'));
        $query->from($db->quoteName('#__ereservas_reserva'));
        $query->where($db->quoteName('id') . ' = '. $db->quote($id));

        $db->setQuery($query);

        $db->setQuery($query,0,1);

        $results = $db->loadResult();

        return $results;
    }

    /**
     * Funcion para checkear el token es correcto o no con la base de datos
     * @param $id -> id reserva
     * @param $token -> token que vamos a comparar con el que hay en la BD
     * @return mixed
     */
    protected static function comprobarToken($id,$token){

        $db = JFactory::getDbo();
        $query = $db->getQuery(true);

        $query->select($db->quoteName('id'));
        $query->from($db->quoteName('#__ereservas_reserva'));
        $query->where($db->quoteName('id') . ' = '. $db->quote($id));
        $query->where($db->quoteName('token') . ' = '. $db->quote($token));

        $db->setQuery($query);

        $db->setQuery($query,0,1);

        $results = $db->loadResult();

        return $results;
    }

    /**
     * Funcion para activar la factura una vez se ha hecho la reserva correctamente
     * @param $id -> id factura
     */
    protected static function activarFactura($id,$id_reserva){

        //validacion para que si queda alguna factura con state a 1 anterior, se cancele
        EreservasRedsys::desactivarFacturasAnteriores($id,$id_reserva);

        $db4 = JFactory::getDbo();
        $query4 = $db4->getQuery(true);
        $campos = array(
            $db4->quoteName('state') . ' = ' . $db4->quote('1')
        );
        $condiciones = array(
            $db4->quoteName('id') . '=' . $db4->quote($id),
        );
        $query4->update($db4->quoteName('#__ereservas_factura'))->set($campos)->where($condiciones);
        $db4->setQuery($query4);

        $result = $db4->execute();
    }

    /**
     * Desactivamos las facturas anteriores de la reserva
     * @param $id -> id factura
     * @param $id_reserva -> id reserva
     */
    protected static function desactivarFacturasAnteriores($id,$id_reserva){

        $db4 = JFactory::getDbo();
        $query4 = $db4->getQuery(true);
        $campos = array(
            $db4->quoteName('state') . ' = ' . $db4->quote('0')
        );
        $condiciones = array(
            $db4->quoteName('id') . '<' . $db4->quote($id),
            $db4->quoteName('id_reserva') . '=' . $db4->quote($id_reserva)
        );
        $query4->update($db4->quoteName('#__ereservas_factura'))->set($campos)->where($condiciones);
        $db4->setQuery($query4);

        $result = $db4->execute();
    }
}