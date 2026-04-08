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

/**
 * Visita controller class.
 *
 * @since  1.6
 */
class EreservasControllerGestionarVisitas extends EreservasController
{
/**
 * El objetivo de esta clase es obtener los campos de las visitas
 */

    /**
     * Funcion para mostrar todas las visitas
     */
    public function visitas(){

        $idtipovisita= JFactory::getApplication()->input->get('idtipovisita');
        $frontal= JFactory::getApplication()->input->get('frontal',false,'uint');
        $paraweb= JFactory::getApplication()->input->get('web', 0,'uint');
        $fecha_inicio= substr(JFactory::getApplication()->input->get('start'),0,-7). ' 00:00:00';
        $fecha_fin = substr(JFactory::getApplication()->input->get('end'),0,-7) . ' 00:00:00';

        if($frontal) {
            $fecha_inicio = date('Y-m-d 00:00:00', strtotime('+30 minutes'));
        }

        //TODO esto debería ser un parámetro
        $periodo_apertura = true;
        if($periodo_apertura && $frontal) {
            $fecha_limite = $this->consultarFechaLimite();
        }


        $db= JFactory::getDbo();
        $query = $db->getQuery(true);
        $query->select('*');
        $query->from($db->quoteName('#__ereservas_visita'));
        $query->where($db->quoteName('state') . ' = ' . $db->quote('1'));
        if($paraweb == 1) $query->where($db->quoteName('aforo_web') . ' > ' . $db->quote('0'));
        $query->where($db->quoteName('id_tipo_visita') . ' = ' . $db->quote($idtipovisita));
        $query->where($db->quoteName('fecha') . ' BETWEEN ' . $db->quote($fecha_inicio) . 'AND' . $db->quote($fecha_fin));
        if($periodo_apertura && $frontal) {
            $query->where($db->quoteName('fecha') . ' <= ' . $db->quote($fecha_limite.' 23:59:59'));
        }
        $query->order($db->quoteName('fecha'));
        $db->setQuery($query);
        $results = $db->loadAssocList();

        header("Access-Control-Allow-Origin: *");

        echo new JResponseJson($results);
    }

    /**
     * Funcion para mostrar todos los cierres que hay
     */
    public function cierres(){

        $db= JFactory::getDbo();
        $query = $db->getQuery(true);
        $query->select('*');
        $query->from($db->quoteName('#__ereservas_cierres'));
        $query->where($db->quoteName('state') . ' LIKE ' . $db->quote('1'));
        $db->setQuery($query);
        $results = $db->loadAssocList();
        echo new JResponseJson($results);
    }


    /**
     * Funcion AJAX para mostrar todas las visitas que hay en un dia en concreto
     * Devuelve el resultado de esa select en JSON para el AJAX
     */
    public function visitasdias(){
        $dia= JFactory::getApplication()->input->get('dia');

        $tipovisitaid= JFactory::getApplication()->input->get('idtipovisita');

        $paraweb= JFactory::getApplication()->input->get('web', 0,'uint');


        EreservasRecalculo::recalcularAforosDia($dia);

        $db3 = JFactory::getDbo();
        $query = $db3->getQuery(true);
        $query->select(array('a.id','a.fecha','a.hora_inicio','b.nombre','a.aforo_web','a.aforo_privado','a.aforo_ocupado', 'c.nombre as idioma'));
        $query->from($db3->quoteName('#__ereservas_visita','a'));
        $query->leftJoin($db3->quoteName('#__ereservas_tipo_visita','b').' ON (a.id_tipo_visita = b.id)');
        $query->leftJoin($db3->quoteName('#__ereservas_idioma','c').' ON (a.idioma = c.id)');
        $query->where($db3->quoteName('a.state') . ' = ' . $db3->quote('1'));
        $query->where($db3->quoteName('a.id_tipo_visita') . ' = ' . $db3->quote($tipovisitaid));
        $query->where('date('.$db3->quoteName('a.fecha') . ') = ' . $db3->quote($dia));
        $query->order($db3->quoteName('a.hora_inicio').' ASC');
        $db3->setQuery($query);
        $resultado = $db3->loadAssocList();

        header("Access-Control-Allow-Origin: *");

        echo new JResponseJson($resultado);
    }

    public function insertarvisita(){
        $visitaid = JFactory::getApplication()->input->get('visitaid');
        $personas = JFactory::getApplication()->input->get('personas');



        $validarPersonas = $this->validarPersonas($visitaid);
        $error= true;

        if($validarPersonas >= $personas){
           $error = false;
        }
    }

    protected function validarPersonas($visitaid){
        $db4 = JFactory::getDbo();
        $query = $db4->getQuery(true);

        $query->select("GREATEST((aforo_privado),(aforo_web)) - aforo_ocupado as Resultado");
        $query->from($db4->quoteName('#__ereservas_visita'));
        $query->where($db4->quoteName('id') . ' = ' . $db4->quote($visitaid));
        $db4->setQuery($query);
        $results = $db4->loadResult();

        echo  $results;
        return $results;
    }

    public function insertarreserva(){
        $pagado= 'no';
        $estado= 'pendiente-pago';
        $precio= JFactory::getApplication()->input->get('preciototal',false,'iunt');
        $id_visita = JFactory::getApplication()->input->get('visitaid',false,'uint');
        $personas = JFactory::getApplication()->input->get('personas',0,'uint');
        $nombre = JFactory::getApplication()->input->get('nombre',false,'html');
        $email = JFactory::getApplication()->input->get('correo',false,'html');
        $telefono = JFactory::getApplication()->input->get('telefono',false,'html');
        $id_pais = JFactory::getApplication()->input->get('pais',false,'html');
        $id_idioma = JFactory::getApplication()->input->get('idioma',$this->traerIdiomaVisita($id_visita),'uint');
        $id_usuario = JFactory::getApplication()->input->get('id_usuario',220,'uint');
        $cpostal  = JFactory::getApplication()->input->get('cpostal',false,'html');
        $municipio = JFactory::getApplication()->input->get('municipio',false,'html');
        $observaciones = JFactory::getApplication()->input->get('observaciones',false,'html');
        $comercial = JFactory::getApplication()->input->get('info_comercial',false,'uint');
        $fecha_creacion = date("Y-m-d H:i:s");
        $idioma_web = JFactory::getApplication()->input->get('idioma_web',1,'uint');

        $precio_unidad = $this->traerPrecio($id_visita);
        $precio = $personas * $precio_unidad;

        if($id_visita < 1) {
            echo new JResponseJson($id_visita,'el campo id_visita es obligatorio','true');
            die();
        }


        $db = JFactory::getDbo();

        $query = $db->getQuery(true);

        $columns = array('ordering', 'state', 'checked_out', 'checked_out_time', 'created_by', 'modified_by',
        'nombre', 'telefono', 'email', 'id_visita', 'id_idioma', 'personas', 'tarjeta', 'pagado', 'estado',
        'id_usuario', 'id_pais', 'cpostal', 'observaciones', 'id_tipo_cliente', 'referencia', 'vinos', 'tipo_cata', 'aperitivo', 'alergenos', 'menu_especial',
        'token', 'fecha_creacion', 'fecha_modificacion', 'asistencia', 'precio', 'municipio','info_comercial');

        $values = array('0','1','0',$db->quote('0010-01-01 01:01:01'),'0','0',
        $db->quote($nombre),$db->quote($telefono),$db->quote($email),$db->quote($id_visita),$db->quote($id_idioma),$db->quote($personas),$db->quote(''),$db->quote($pagado),$db->quote($estado),
        $db->quote($id_usuario), $db->quote($id_pais),$db->quote($cpostal), $db->quote($observaciones), $db->quote(' '), $db->quote(' '), $db->quote(' '), $db->quote(' '),$db->quote(' '),$db->quote(' '),$db->quote(' '),
        $db->quote(' '),$db->quote($fecha_creacion),$db->quote($fecha_creacion), $db->quote(' '),$db->quote($precio),$db->quote($municipio),$db->quote($comercial));

        $query
            ->insert($db->quoteName('#__ereservas_reserva'))
            ->columns($db->quoteName($columns))
            ->values(implode(',', $values));

        $db->setQuery($query);

        $db->execute();

        $idinsertado = $db->insertid();
       $token= EreservasCorreos::generarTokenReserva($idinsertado);
       $devolver = ['id_insertado' => $idinsertado, 'token' => $token, 'idioma_web' => $idioma_web];


       header("Access-Control-Allow-Origin: *");

       echo new JResponseJson($devolver);

    }

    protected function traerPrecio($id_visita){
        $db4 = JFactory::getDbo();
        $query = $db4->getQuery(true);

        $query->select("precio");
        $query->from($db4->quoteName('#__ereservas_visita'));
        $query->where($db4->quoteName('id') . ' = ' . $db4->quote($id_visita));
        $db4->setQuery($query);
        $results = $db4->loadResult();

        return $results;
    }

    protected function traerReserva($id_reserva,$token){
        $db4 = JFactory::getDbo();
        $query = $db4->getQuery(true);

        $query->select(array("precio","id"));
        $query->from($db4->quoteName('#__ereservas_reserva'));
        $query->where($db4->quoteName('id') . ' = ' . $db4->quote($id_reserva));
        $query->where($db4->quoteName('token') . ' = ' . $db4->quote($token));
        $db4->setQuery($query);
        $results = $db4->loadAssoc();

        return $results;
    }

    protected function consultarFactura($id_reserva){
        $db4 = JFactory::getDbo();
        $query = $db4->getQuery(true);

        $query->select("id");
        $query->from($db4->quoteName('#__ereservas_factura'));
        $query->where($db4->quoteName('id_reserva') . ' = ' . $db4->quote($id_reserva));
        $db4->setQuery($query);
        $results = $db4->loadResult();

        return $results;
    }

    /**
     * Funcion para mostrar todos los cierres que hay
     */
    public function facturacion(){

        $nombre   = JFactory::getApplication()->input->get('nombre',false,'html');
        $nif   = JFactory::getApplication()->input->get('nif',false,'html');
        $cpostal  = JFactory::getApplication()->input->get('cpostal',false,'html');
        $direccion  = JFactory::getApplication()->input->get('direccion',false,'html');
        $municipio = JFactory::getApplication()->input->get('municipio',false,'html');
        $id_reserva = JFactory::getApplication()->input->get('id_reserva',false,'uint');
        $factura = JFactory::getApplication()->input->get('factura',false,'uint');
        $token = JFactory::getApplication()->input->get('token',false,'html');


        header("Access-Control-Allow-Origin: *");

        //TODO si no hay nombre, sacar error.
        //echo new JResponseJson($results);

        $reserva = $this->traerReserva($id_reserva,$token);

        if(!@$reserva['id']) {
            echo new JResponseJson($reserva,'No se ha encontrado la reserva solicitada para facturación.','true');
            die();
        }

        if(!$nombre) {
            echo new JResponseJson($reserva,'Al menos necesitamos el campo nombre.','true');
            die();
        }

        //Consultamos factura, solo puede haber una
        $id_factura = $this->consultarFactura($id_reserva);

        //TODO Si existe factura, habrá que comprobar que no se haya pagado ya la reserva y emitir error.

        if($id_factura < 1) {
            $db = JFactory::getDbo();

            $query = $db->getQuery(true);

            $columns = array('ordering', 'state', 'checked_out', 'checked_out_time', 'created_by', 'modified_by', 'nombre',
                'nif', 'cpostal', 'direccion', 'municipio', 'precio', 'id_reserva', 'factura');

            $values = array('0','1','0',$db->quote('0010-01-01 01:01:01'),'0','0',$db->quote($nombre),
                $db->quote($nif),$db->quote($cpostal),$db->quote($direccion),$db->quote($municipio),$db->quote($reserva['precio']),
                $db->quote($id_reserva),$db->quote($factura));

            $query
                ->insert($db->quoteName('#__ereservas_factura'))
                ->columns($db->quoteName($columns))
                ->values(implode(',', $values));

            $db->setQuery($query);

            $db->execute();

            $id_factura = $db->insertid();

        }

        $datos['idreserva'] = $id_reserva;
        $datos['idfactura'] = $id_factura;

        echo new JResponseJson($datos);
    }

    protected function traerIdiomaVisita($id_visita){
        $db4 = JFactory::getDbo();
        $query = $db4->getQuery(true);

        $query->select("idioma");
        $query->from($db4->quoteName('#__ereservas_visita'));
        $query->where($db4->quoteName('id') . ' = ' . $db4->quote($id_visita));
        $db4->setQuery($query);
        $results = $db4->loadResult();

        return (int) $results;
    }

    protected function consultarFechaLimite(){
        $db4 = JFactory::getDbo();
        $query = $db4->getQuery(true);


        //COMPROBAR SIEMPRE QUE MIGREMOS SERVIDOR
        date_default_timezone_set('Europe/Madrid');
        $fecha_actual = date('Y-m-d H:i:s');

        $query->select("fecha_final");
        $query->from($db4->quoteName('#__ereservas_fechas_apertura'));
        $query->where($db4->quoteName('fecha_apertura') . ' < ' . $db4->quote($fecha_actual));
        $query->where($db4->quoteName('state') . ' = 1 ');
        $query->order($db4->quoteName('fecha_apertura') . ' DESC');
        $db4->setQuery($query);

        $results = $db4->loadResult();

        return $results;
    }

}