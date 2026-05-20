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
class EreservasControllerObtenerEventos extends EreservasController
{
/**
 * El objetivo de esta clase es obtener los campos de las visitas
 */

    /**
     * Funcion para mostrar todas las visitas
     */
    public function visitas(){

        $db= JFactory::getDbo();
        $query = $db->getQuery(true);
        $query->select('*');
        $query->from($db->quoteName('#__ereservas_visita'));
        $query->where($db->quoteName('state') . ' = ' . $db->quote('1'));
        $db->setQuery($query);
        $results = $db->loadAssocList();
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
     * Funcion AJAX donde mostramos todas las reservas que tengamos en una visita en concreto
     * Devuelve el resultado de esa select en JSON para el AJAX
     */
    public function reservas_recibir()
    {
        // Obtenemos el valor del AJAX
        $visita_id = JFactory::getApplication()->input->get('visitaid');

        $db2 = JFactory::getDbo();
        $query = $db2->getQuery(true);
        $query->select(array('a.id','a.state','a.nombre','a.email','a.personas','b.nombre as idioma','a.pagado','a.estado', 'a.asistencia','a.precio','a.observaciones','c.id as num_pedido','a.fecha_creacion as fecha_pedido'));
        $query->from($db2->quoteName('#__ereservas_reserva','a'));
        $query->leftJoin($db2->quoteName('#__ereservas_idioma','b').' ON (a.id_idioma = b.id)');
        $query->leftJoin($db2->quoteName('#__ereservas_factura','c').' ON (c.id_reserva = a.id)');
        $query->where($db2->quoteName('a.id_visita') . ' = ' . $db2->quote($visita_id));
        $query->where($db2->quoteName('a.estado') . ' <> ' . $db2->quote('cancelado'));
        $query->where($db2->quoteName('a.state') . ' = ' . $db2->quote('1'));
        $query->where('(('.$db2->quoteName('c.state') . ' = ' . $db2->quote('1').') OR ('.$db2->quoteName('c.state') . ' IS NULL) OR ('.$db2->quoteName('a.estado') . ' = ' . $db2->quote('pendiente-pago').'))');
        $query->order($db2->quoteName('a.nombre') . ' ASC');
        $db2->setQuery($query);

        $resultado = $db2->loadAssocList();

        //Quieren ver las canceladas también, pero debajo.
        $canceladas = $this->reservas_canceladas($visita_id);

        $combinados = array_merge($resultado, $canceladas);

        echo new JResponseJson($combinados);
    }


    protected function reservas_canceladas($visita_id){

        $db2 = JFactory::getDbo();
        $query = $db2->getQuery(true);
        $query->select(array('a.id','a.state','a.nombre','a.email','a.personas','b.nombre as idioma','a.pagado','a.estado', 'a.asistencia','a.precio','a.observaciones','c.id as num_pedido','a.fecha_creacion as fecha_pedido'));
        $query->from($db2->quoteName('#__ereservas_reserva','a'));
        $query->leftJoin($db2->quoteName('#__ereservas_idioma','b').' ON (a.id_idioma = b.id)');
        $query->leftJoin($db2->quoteName('#__ereservas_factura','c').' ON (c.id_reserva = a.id)');
        $query->where($db2->quoteName('a.id_visita') . ' = ' . $db2->quote($visita_id));
        $query->where('(('.$db2->quoteName('a.estado') . ' = ' . $db2->quote('cancelado').') OR ('.$db2->quoteName('a.state') . ' = ' . $db2->quote('0').'))');
        $query->order($db2->quoteName('a.nombre') . ' ASC');
        $db2->setQuery($query);

        $resultado = $db2->loadAssocList();

        return $resultado;
    }
    /**
     * Funcion AJAX para mostrar todas las visitas que hay en un dia en concreto
     * Devuelve el resultado de esa select en JSON para el AJAX
     */
    public function visitasdias(){
        $dia= JFactory::getApplication()->input->get('dia');

        EreservasRecalculo::recalcularAforosDia($dia);

        $db3 = JFactory::getDbo();
        $query = $db3->getQuery(true);
        $query->select(array('a.id','a.fecha','a.hora_inicio','b.nombre','a.aforo_web','a.aforo_privado','a.aforo_ocupado', 'c.nombre as idioma'));
        $query->from($db3->quoteName('#__ereservas_visita','a'));
        $query->leftJoin($db3->quoteName('#__ereservas_tipo_visita','b').' ON (a.id_tipo_visita = b.id)');
        $query->leftJoin($db3->quoteName('#__ereservas_idioma','c').' ON (a.idioma = c.id)');
        $query->where($db3->quoteName('a.state') . ' = ' . $db3->quote('1'));
        $query->where('date('.$db3->quoteName('a.fecha') . ') = ' . $db3->quote($dia));
        $query->order($db3->quoteName('a.hora_inicio').' ASC');
        $db3->setQuery($query);
        $resultado = $db3->loadAssocList();
        echo new JResponseJson($resultado);
    }

    /**
     * Funcion en el cual insertamos una visita rapida
     */
    public function visitasrapidas(){
        //Obtenemos los valores
        $fecha= JFactory::getApplication()->input->get('fecha');
        $hora= JFactory::getApplication()->input->get('hora',false,'html');
        $tipovisita= JFactory::getApplication()->input->get('tipovisita');
        $idioma = JFactory::getApplication()->input->get('idioma');
        $visitavalores= $this->getVisita($tipovisita);

        $aforoweb= $visitavalores[0]['aforo_web'];
        $aforoprivado= $visitavalores[0]['aforo_privado'];
        $comida=$visitavalores[0]['comida'];
        $cata= $visitavalores[0]['cata'];
        $precio= $visitavalores[0]['precio'];

        //Comprobamos que no estamos haciendo uan visita ya existente, deben de coincidir el tipo, la hora y la fecha, si coincide mandadmos un false
        $valor = EreservasUtiles::comprobarExistenciaVisita($tipovisita,$hora.':00',$fecha.' 00:00:00');
        if($valor){
            echo new JResponseJson(false);
            die();
        }

        //Si no coincide la visita con ninguna ya existente, hacemos el inserto
        $db4 = JFactory::getDbo();
        $insert_visitas=$db4->getQuery(true);

        $columnas=array('state','fecha', 'hora_inicio', 'id_tipo_visita','aforo_web', 'aforo_privado', 'comida','cata','precio','idioma');
        $valores=array($db4->quote(1),$db4->quote($fecha),$db4->quote($hora),$db4->quote($tipovisita),$db4->quote($aforoweb),$db4->quote($aforoprivado),$db4->quote($comida),$db4->quote($cata),$db4->quote($precio), $db4->quote($idioma));
            $insert_visitas
                ->insert($db4->quoteName('#__ereservas_visita'))
                ->columns($db4->quoteName($columnas))
                ->values(implode(',', $valores));
        $db4->setQuery($insert_visitas);

        $db4->execute();
        //Devolvemos true via JSON
        echo new JResponseJson(true);
    }

    /**
     * Funcion en el cual insertamos una visita rapida
     */
    public function reservasrapidas(){
        //Cogemos todos los valores que nos envian y los transformamos en variables
        $clave= "Midb18T7EMESA2011Bo1Qt79a";
        $user= JFactory::getUser();
        $userId= $user->get('id');
        $nombre= JFactory::getApplication()->input->get('nombre',false,'html');
        $email= JFactory::getApplication()->input->get('email',false,'html');
        $telefono=JFactory::getApplication()->input->get('telefono',false,'html');
        $tarjeta=JFactory::getApplication()->input->get('tarjeta',false,'html');
        $tarjetacod= EreservasTarjeta::cifrarinsert($tarjeta);
        $codigopostal=JFactory::getApplication()->input->get('codigopostal');
        $personas= JFactory::getApplication()->input->get('personas');
        $idioma=JFactory::getApplication()->input->get('idioma');
        $usuario=JFactory::getApplication()->input->get('usuario');
        $pais=JFactory::getApplication()->input->get('pais');
        $tipocliente=JFactory::getApplication()->input->get('tipocliente');
        $visitaid= JFactory::getApplication()->input->get('visitaid');
        $precioinput= JFactory::getApplication()->input->get('precio');
        $precio= ($precioinput == 0)? $this->calcularprecio($personas, $visitaid) : $precioinput;
        $pagado = JFactory::getApplication()->input->get('pagado');
        if($tipocliente == "4"){
            $pagado = 'agencia';
            $precio = '0';
        }
        if($pagado == "invitacion"){
            $precio="0";
        }
        elseif($pagado != "no"){
            $precio = '0';
        }
        $estado = JFactory::getApplication()->input->get('estado');
        $asistencia = JFactory::getApplication()->input->get('asistencia');
        $observaciones= JFactory::getApplication()->input->get('observaciones',false,'html');
        $enviarcorreo = JFactory::getApplication()->input->get('enviarcorreo', false, 'html');

        //iniciamos el insert
        $db5 = JFactory::getDbo();
        $insert_reservas=$db5->getQuery(true);
        $columnas=array('state','created_by','modified_by','nombre', 'email','telefono','tarjeta','cpostal','personas','id_idioma',
        'id_usuario','id_pais','id_tipo_cliente','id_visita','estado','fecha_creacion','fecha_modificacion','observaciones', 'precio', 'pagado', 'asistencia');
        $valores=array('1',$db5->quote($userId),$db5->quote($userId),$db5->quote($nombre),$db5->quote($email),$db5->quote($telefono),$tarjetacod,$db5->quote($codigopostal),$db5->quote($personas),$db5->quote($idioma),
        $db5->quote($usuario),$db5->quote($pais),$db5->quote($tipocliente),$db5->quote($visitaid),$db5->quote($estado),$db5->quote(date("Y-m-d H:i:s")),$db5->quote(date("Y-m-d H:i:s")),$db5->quote($observaciones), $db5->quote($precio), $db5->quote($pagado),
            $db5->quote($asistencia)  );
        $insert_reservas
            ->insert($db5->quoteName('#__ereservas_reserva'))
            ->columns($db5->quoteName($columnas))
            ->values(implode(',', $valores));
        $db5->setQuery($insert_reservas);
        $db5->execute();
        $id_insertado=$db5->insertid();

        //Recalculamos el aforo que tendra esa visita, ya que con la reserva que acabamos de hacer, habra mas personas
         EreservasRecalculo::recalcularaforo($visitaid);
         if($enviarcorreo === "pago"){
             EreservasCorreos::enviarMail($id_insertado,'pago');

             $db8= JFactory::getDbo();
             $query = $db8->getQuery(true);
             $campos = array($db8->quoteName('estado') . ' = ' . $db8->quote('pendiente-pago'));
             $condiciones = array($db8->quoteName('id') . ' = ' . $db8->quote($id_insertado));
             $query->update($db8->quoteName('#__ereservas_reserva'))->set($campos)->where($condiciones);
             $db8->setQuery($query);
             $results = $db8->execute();
         }
         elseif($enviarcorreo === "confirmacion"){
            /* Esto era cuando sólo había dos posibles funcionamientos, pago o confirmación por usuario
            EreservasCorreos::enviarMail($id_insertado,'confirmada-por-cliente');

            $db9= JFactory::getDbo();
            $query = $db9->getQuery(true);
            $campos = array($db9->quoteName('estado') . ' = ' . $db9->quote('pendiente-conf'));
            $condiciones = array($db9->quoteName('id') . ' = ' . $db9->quote($id_insertado));
            $query->update($db9->quoteName('#__ereservas_reserva'))->set($campos)->where($condiciones);
            $db9->setQuery($query);
            $results = $db9->execute();
            */

             /* Aquí notificaciones por ahora sólo confirmado */

            if($estado == 'confirmado') {
                EreservasCorreos::enviarMail($id_insertado,'confirmada-por-cliente');
            }

        }
    }

    /**
     * Funcion donde mostramos todos los valores de una visita en concreto
     * @param $idvisita
     * @return resultado de esa select a la BD
     */
    protected function getVisita($idvisita){
        $db6= JFactory::getDbo();
        $query = $db6->getQuery(true);
        $query->select('*');
        $query->from($db6->quoteName('#__ereservas_tipo_visita'));
        $query->where($db6->quoteName('id').' = ' .$db6->quote($idvisita) );
        $db6->setQuery($query);
        $results = $db6->loadAssocList();
        return $results;

    }

    /**
     * Funcion donde mostramos todos los valores de una visita en concreto
     * @return resultado de esa select a la BD
     */
    public function getVisitaFront(){
        $idvisita= JFactory::getApplication()->input->get('visitaid');
        $db6= JFactory::getDbo();
        $query = $db6->getQuery(true);
        $query->select('*');
        $query->from($db6->quoteName('#__ereservas_visita'));
        $query->where($db6->quoteName('id').' = ' .$db6->quote($idvisita) );
        $db6->setQuery($query);
        $results = $db6->loadAssoc();
        echo new JResponseJson($results);

    }

    /**
     * Funcion donde cogemos los valores de una reserva para mostrarla via JS en la modal
     */
    public function getreserva(){
        $reservaid= JFactory::getApplication()->input->get('reservaid');
        $db= JFactory::getDbo();
        $query = $db->getQuery(true);
        $query->select('*');
        $query->from($db->quoteName('#__ereservas_reserva'));
        $query->where($db->quoteName('id').' = ' .$db->quote($reservaid) );
        $db->setQuery($query);
        $results = $db->loadAssoc();
        unset($results['tarjeta']);
        echo new JResponseJson($results);
    }

    /**
     * Funcion para enviar el pago
     */
    public function enviarpago(){
        //Obtenemos los valores
        $id_reserva= JFactory::getApplication()->input->get('reservaid');
        $id_visita= JFactory::getApplication()->input->get('visitaid');

        //Llamamos a la libreria que manda el correo
        $check = EreservasCorreos::enviarMail($id_reserva,'pago');

        //Si se ha enviado el correo, hacemos un update del estado a "pendiente del pago" de esa reserva
        if($check){
            $db8= JFactory::getDbo();
            $query = $db8->getQuery(true);
            $campos = array($db8->quoteName('estado') . ' = ' . $db8->quote('pendiente-pago'));
            $condiciones = array($db8->quoteName('id') . ' = ' . $db8->quote($id_reserva));
            $query->update($db8->quoteName('#__ereservas_reserva'))->set($campos)->where($condiciones);
            $db8->setQuery($query);
            $results = $db8->execute();

            //Si se ha hecho correctamente el update, mostramos la alerta
            if($results) {
                $respuesta = 'El mail se ha enviado con éxito';
            } else {
                $respuesta = 'El mail no se ha podido enviar';
            }
        }
        //Si no se ha podido enviar el mail, mostramos un mensaje de error
        else {
            //TODO enviar alerta de error
            $respuesta = 'El mail no se ha podido enviar';
        }

        echo $respuesta;

        die();
    }

    /**
     * Funcion de enviar confirmacion por correo
     */
    public function enviarConfirmacion(){
        //Obtenemos los valores
        $id_reserva= JFactory::getApplication()->input->get('reservaid');
        $id_visita= JFactory::getApplication()->input->get('visitaid');

        //Llamamos a la libreria de mandar correos
        $check = EreservasCorreos::enviarMail($id_reserva,'confirmacion');

        //En caos de que se haya enviado, hacemos el update en la base de datos
        if($check){
            $db9= JFactory::getDbo();
            $query = $db9->getQuery(true);
            $campos = array($db9->quoteName('estado') . ' = ' . $db9->quote('pendiente-conf'));
            $condiciones = array($db9->quoteName('id') . ' = ' . $db9->quote($id_reserva));
            $query->update($db9->quoteName('#__ereservas_reserva'))->set($campos)->where($condiciones);
            $db9->setQuery($query);
            $results = $db9->execute();

            $respuesta = 'Email enviado con éxito';
        } else {
            $respuesta = 'Ha habido un problema con el envío del email';
        }

        echo $respuesta;

        die();

    }

    /**
     * Funcion AJAX que cancela una reserva, lo que hacemos es modificar el estado a "cancelado"
     * Una vez echo el update, tendremos que volver a calcular el aforo disponible para esa visita
     */
    public function cancelar(){
        $id_reserva= JFactory::getApplication()->input->get('reservaid',null,'int');
        $id_visita= JFactory::getApplication()->input->get('visitaid',null,'int');

        $db10= JFactory::getDbo();
        $query = $db10->getQuery(true);
        $campos = array(
            $db10->quoteName('estado') . ' = ' . $db10->quote('cancelado'),
            $db10->quoteName('state'). ' = ' . $db10->quote(0)
        );
        $condiciones = array(
            $db10->quoteName('id') . ' = ' . $db10->quote($id_reserva),
            $db10->quoteName('id_visita') . ' = ' . $db10->quote($id_visita)
        );
        $query->update($db10->quoteName('#__ereservas_reserva'))->set($campos)->where($condiciones);
        $db10->setQuery($query);

        $results = $db10->execute();

        EreservasRecalculo::recalcularaforo($id_visita);
        EreservasUtiles::obtenerPlazas($id_visita);

        if($results) {
            EreservasCorreos::enviarMail($id_reserva,'cancelacion');
        }

        echo new JResponseJson(array(
            'success' => true,
            'mensaje' => 'Cancelación completada ejecutando lista de espera'
        ));

        jexit();
    }

    /**
     * Funcion donde confirmamos manualmente la asisitencia de una reserva en concreto
     * Hacemos un update en el campo asistencia, marcandolo a "si"
     */
     public function confirmarasistencia(){
        $id_reserva= JFactory::getApplication()->input->get('reservaid');
        $db11= JFactory::getDbo();
        $query = $db11->getQuery(true);
        $campos = array($db11->quoteName('asistencia') . ' = ' . $db11->quote('si'));
        $condiciones = array($db11->quoteName('id') . ' = ' . $db11->quote($id_reserva));
        $query->update($db11->quoteName('#__ereservas_reserva'))->set($campos)->where($condiciones);
        $db11->setQuery($query);
        $results = $db11->execute();
        return $results;
    }

    /**
     * Funcion donde editamos la reserva
     */
    public function editarreserva(){
        $insertar_campos                = new stdClass();
        $insertar_campos->id            = JFactory::getApplication()->input->get('reservaid');
        $insertar_campos->nombre        = JFactory::getApplication()->input->get('nombre',false,'html');
        $insertar_campos->email         = JFactory::getApplication()->input->get('email',false,'html');
        $insertar_campos->telefono      = JFactory::getApplication()->input->get('telefono',false, 'html');
        $insertar_campos->cpostal       = JFactory::getApplication()->input->get('codigopostal',false, 'html');
        $insertar_campos->personas      = JFactory::getApplication()->input->get('personas',false,'html');
        $insertar_campos->id_pais       = JFactory::getApplication()->input->get('pais',false,'html');
        $insertar_campos->id_usuario    = JFactory::getApplication()->input->get('usuario',false,'html');
        $insertar_campos->id_idioma     = JFactory::getApplication()->input->get('idioma',false,'html');
        $insertar_campos->id_tipo_cliente = JFactory::getApplication()->input->get('tipocliente',false,'html');
        $insertar_campos->pagado        = JFactory::getApplication()->input->get('pagado',false,'html');
        $insertar_campos->estado        = JFactory::getApplication()->input->get('estado',false,'html');
        $insertar_campos->observaciones = JFactory::getApplication()->input->get('observaciones',false,'html');
        $insertar_campos->asistencia    = JFactory::getApplication()->input->get('asistencia', false, 'html');
        $id_visita                   = JFactory::getApplication()->input->get('visitaid');
        $precioinput= JFactory::getApplication()->input->get('precio',false,'html');
        $insertar_campos->precio= ($precioinput == 0)? $this->calcularprecio($insertar_campos->personas, $id_visita) : $precioinput;
        if($insertar_campos->pagado == "invitacion"){
            $insertar_campos->precio = "0";
        }
        $result = JFactory::getDbo()->updateObject('#__ereservas_reserva', $insertar_campos, 'id');
        EreservasRecalculo::recalcularaforo($id_visita);

        EreservasUtiles::obtenerPlazas($id_visita);

    }


    /**
     * Funcion para cambiar la visita de una reserva
     */

    public function cambiardiareserva(){
        // Create an object for the record we are going to update.
        $actualizar = new stdClass();

        // Must be a valid primary key value.
        $actualizar->id =  JFactory::getApplication()->input->get('id_reserva');;
        $actualizar->id_visita = JFactory::getApplication()->input->get('id_visita');

        // Update their details in the users table using id as the primary key.
        $result = JFactory::getDbo()->updateObject('#__ereservas_reserva', $actualizar, 'id');

        EreservasRecalculo::recalcularaforo($actualizar->id_visita);

        EreservasUtiles::obtenerPlazas($actualizar->id_visita);

    }

    /**
     * Funcion para cambiar el aforo de una visita
     */

    public function actualizarVisitaAforo(){
        // Create an object for the record we are going to update.
        $actualizar = new stdClass();

        // Must be a valid primary key value.
        $actualizar->id =  JFactory::getApplication()->input->get('id_visita');;
        $actualizar->aforo_web = JFactory::getApplication()->input->get('aforo_web');
        $actualizar->aforo_privado = JFactory::getApplication()->input->get('aforo_privado');

        // Update their details in the users table using id as the primary key.
        $result = JFactory::getDbo()->updateObject('#__ereservas_visita', $actualizar, 'id');

        EreservasRecalculo::recalcularaforo($actualizar->id);

        EreservasUtiles::obtenerPlazas($actualizar->id);

    }


    /**
     * Funcion para borrar una visita dado un id
     */
    public function borrarvisita(){
        $idvisita       = JFactory::getApplication()->input->get('visitaid');

        $actualizar_visita = new stdClass();

        $actualizar_visita->id = $idvisita;
        $actualizar_visita->state = 0;

        $result = JFactory::getDbo()->updateObject('#__ereservas_visita', $actualizar_visita, 'id');

    }

    /**
     * Funcion para calcular el precio total de la reserva a pagar, se hace antes de insertar la reserva por 1º vez
     * @param $personas -> numero de personas que se ha puesto en el input
     * @param $idvisita -> id de la visita de la cual se esta haciendo la reserva
     * @return $precio_total -> es el resultado de multiplicar las personas por el precio de la visita
     */

    protected function calcularprecio($personas, $idvisita){
        $db = JFactory::getDbo();

        $query = $db->getQuery(true);

        $query->select('(precio *'.$personas.') as total');
        $query->from($db->quoteName('#__ereservas_visita'));
        $query->where($db->quoteName('id') . ' LIKE ' . $db->quote($idvisita));
        $db->setQuery($query);

        $precio_total = $db->loadResult();
        return $precio_total;
    }
}