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
 * *************IMPORTANTE***************************************
 * Para evitar que ocurran cosas malas en esta plataforma lo que hacemos es crear funciones publicas que unicamente
 * recogen los datos y las mandan a las funciones protegidas, para asi evitar que alguien se nos meta de por medio
 *
 * @since  1.6
 */
class EreservasControllerObtenerCampos extends EreservasController
{
	/**
	 * Funcion para comprobar que nos llega algun parametro a la hora de obtener la visita.
	 */
	public function recibir_visita()
	{
		// Obtenemos el valor del AJAX
		$anyParam = JFactory::getApplication()->input->get('opcion');
		try
		{
			//lo imprimimos (no hace falta la funcion)
			echo new JResponseJson($this->consulta_visita($anyParam));
		}
			//metemos el catch para saber
		catch(Exception $e)
		{
			echo new JResponseJson($e);
		}
		//importante el die
		die();
	}

    /**
     * Funcion para obtener los campos del id visita en funcion de los ids mandados
     * @param $param -> ids del tipo de visita que queremos mostrar
     * @return mixed -> todos los campos del tipo visita
     */
	protected function consulta_visita($param){
		$count= sizeof((array) $param);
		$db= JFactory::getDbo();
		$query = $db->getQuery(true);
		$query->select('*');
		$query->from($db->quoteName('#__ereservas_tipo_visita'));
		for($i=0; $i<$count; $i++){
			if($i==0){
				$query->where($db->quoteName('id') . ' LIKE ' . $param[$i]);
			}
			$query->orWhere($db->quoteName('id') . ' LIKE ' . $param[$i]);
		}

		$db->setQuery($query);
		$results = $db->loadAssocList();
		return $results;
	}

    /**
     * Funcion para comprobar que nos llega algun parametro a la hora de obtener la reserva.
     */
	public function recibir_reservas(){
		// Obtenemos el valor del AJAX
		  $reserva = JFactory::getApplication()->input->get('opcion');
	try{
	  //lo imprimimos (no hace falta la funcion)
      echo new JResponseJson($this->consulta_reserva($reserva));
	}
	//metemos el catch para saber
    catch(Exception $e){
      echo new JResponseJson($e);
	}
        //importante el die
        die();
    }

    /**
     * Funcion para mostrar la visita segun el id reserva que le pasemos como parametro
     * @param $reserva -> id reserva
     * @return mixed -> resultado de la consulta de la base de datos
     */
	protected function consulta_reserva($reserva){
		$db= JFactory::getDbo();
		$query = $db->getQuery(true);
		$query->select('*');
		$query->from($db->quoteName('#__ereservas_visita'));
		$query->where($db->quoteName('id') . ' = ' . $reserva);
		$db->setQuery($query);
		$results = $db->loadAssocList();
		return $results;
	}

    /**
     * Funcion para traer la tarjeta cifrada y mandarla a la liberia que se encarga de descifrarla
     * @throws Exception
     */
	public function desencriptar_recibir(){
		// Obtenemos el valor del AJAX
		$anyParam = JFactory::getApplication()->input->get('id_reserva');
		try{
			//lo imprimimos (no hace falta la funcion)
			echo new JResponseJson(EreservasTarjeta::descifrar($anyParam));
		}
			//metemos el catch para saber
		catch(Exception $e){
			echo new JResponseJson($e);
		}
		//importante el die
		die();
	}
	/**
	 * Funcion para recibir el precio por persona
	 */
	 public function recibirprecio(){
         $idvisita =  JFactory::getApplication()->input->get('idvisita');
         $personas = JFactory::getApplication()->input->get('personas');
         try{
                echo new JResponseJson($this->consultaprecio($idvisita, $personas));;
             }
                //metemos el catch para saber
            catch(Exception $e)
            {
                echo new JResponseJson($e);
            }
            //importante el die
            die();
	 }

    /**
     * Funcion AJAX para consultar el precio de una visita segun las personas que se pongan en el formulario
     * @param $idvisita -> es el id visita que ha seleccionado en el formulario
     * @param $personas -> es el numero de personas que se han puesto en el formulario
     * @return mixed -> El precio total de las personas
     */
	 protected function consultaprecio($idvisita,$personas){
	 $db= JFactory::getDbo();
	 $consulta_precio = $db->getQuery(true);
	 $consulta_precio -> select($personas . '* precio as total');
	 $consulta_precio->from($db->quoteName('#__ereservas_visita'));
	 $consulta_precio->where($db->quoteName('id') . 'LIKE ' . $db->quote($idvisita));
	 $db->setQuery($consulta_precio);
     $precio = $db->loadAssocList();
     return $precio;
	 }

    /**
     * Funcion para recibir el precio
     */
	 public function recibirpreciopersona(){
	 $idvisita =  JFactory::getApplication()->input->get('idvisita');
         try{
                echo new JResponseJson($this->consulta_preciopp($idvisita));;
             }
                //metemos el catch para saber
            catch(Exception $e)
            {
                echo new JResponseJson($e);
            }
            //importante el die
            die();
	 }
    /**
     * Funcion AJAX para consultar el precio de una visita
     * @param $idvisita -> es el id visita que ha seleccionado en el formulario
     * @return mixed -> El precio total de las personas
     */
	 protected function consulta_preciopp($idvisita){
	  $db= JFactory::getDbo();
	 $consulta_preciopp = $db->getQuery(true);
	 $consulta_preciopp -> select('precio');
	 $consulta_preciopp->from($db->quoteName('#__ereservas_visita'));
	 $consulta_preciopp->where($db->quoteName('id') . 'LIKE ' . $db->quote($idvisita));
	 $db->setQuery($consulta_preciopp);
     $preciopp = $db->loadAssocList();
     return $preciopp;
	 }
}
