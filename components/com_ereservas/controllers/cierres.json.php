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
 * Cierre y aperturas controller class.
 *
 * @since  1.6
 */
class EreservasControllerCierres extends EreservasController{

    /**
* Comprobamos el id que nos ha llegado del JS, si es 0, asumiremos que es un nuevo elemento, sino estaremos editando
*/
    public function comprobarid(){
        $jinput = JFactory::getApplication()->input;
        $id = $jinput->get('id',false,'int');
        $dia_inicio = $jinput->get('dia_inicio',false,'html');
        $dia_fin = $jinput->get('dia_fin',false,'html');
        $concepto= $jinput->get('concepto',false,'html');
        $tipo_cierre= $jinput->get('tipo_cierre', false, 'html');
        if($id == 0){
        $devuelta= $this->crearcierre($id,$dia_inicio, $dia_fin,$concepto, $tipo_cierre);
        echo new JResponseJson( $devuelta);
        }else{
            $devuelta= $this->actualizarcierre($id,$dia_inicio, $dia_fin,$concepto, $tipo_cierre);
        echo new JResponseJson( $devuelta);
        }
    }

 /**
* Funcion que nos sirve para insertar los datos en la base de datos, llamamos a la libreria de utiles para una mayor
* agilidad de insertar los datos que no estan en el formulario
* @param $id
* @param $dia_inicio
* @param $dia_fin
* @param $concepto
 */

    protected function crearcierre($id,$dia_inicio, $dia_fin,$concepto, $tipo_cierre){
    $valores = EreservasUtiles::camposInsertGenericos();
    $db = JFactory::getDbo();
    $query = $db->getQuery(true);
    $columns = array('fecha_inicio', 'fecha_fin', 'concepto', 'tipo_cierre');
    $values = array($db->quote($dia_inicio), $db->quote($dia_fin),$db->quote($concepto), $db->quote($tipo_cierre) );

    foreach($valores as $clave => $valor) {
        array_push($columns,$clave);
        array_push($values,$db->quote($valor));
    }

    $query
        ->insert($db->quoteName('#__ereservas_cierres'))
        ->columns($db->quoteName($columns))
        ->values(implode(',', $values));
    $db->setQuery($query);
    $db->execute();
    }

 /**
* Funcion que se usa para traer de nuevo los datos de la BDO e imprimirlos por JS
 */

    public function reset(){
        $db = JFactory::getDbo();
        $query = $db->getQuery(true);
        $query->select('*');
        $query->from($db->quoteName('#__ereservas_cierres'));
        $query->where($db->quoteName('state') . ' = '. $db->quote('1'));
        $db->setQuery($query);
        $resultado = $db->loadAssocList();

        echo new JResponseJson($resultado);
    }

/**
* Funcion que sirve para coger los datos de la base de datos dado un ID concreto
 */

    public function editar(){
     $jinput = JFactory::getApplication()->input;
        $id = $jinput->get('id');
         $db = JFactory::getDbo();
        $query = $db->getQuery(true);
        $query->select('*');
        $query->from($db->quoteName('#__ereservas_cierres'));
        $query->where($db->quoteName('id') . ' = '. $db->quote($id));
        $db->setQuery($query);
        $resultado = $db->loadAssoc();
        echo new JResponseJson($resultado);
    }

/**
 *  Funcion para actualizar un campo de la BDO dado su ID
* @param $id
* @param $dia_inicio
* @param $dia_fin
* @param $concepto
 */

    protected function actualizarcierre($id,$dia_inicio, $dia_fin,$concepto, $tipo_cierre){
    $db = JFactory::getDbo();
    $query = $db->getQuery(true);
    $campos = array(
        $db->quoteName('fecha_inicio') . ' = ' . $db->quote($dia_inicio),
        $db->quoteName('fecha_fin') . ' = ' . $db->quote($dia_fin),
        $db->quoteName('concepto') . ' = ' . $db->quote($concepto),
        $db->quoteName('tipo_cierre'). ' = ' . $db->quote($tipo_cierre)
    );

    $condiciones = array(
     $db->quoteName('id') . ' = '.$id,
    );

    $query->update($db->quoteName('#__ereservas_cierres'))->set($campos)->where($condiciones);

    $db->setQuery($query);

    $result = $db->execute();
    }

        /**
* Funcion que hace que se borre un campo en la BDO (lo que hacemos es actualizar el campo estado a 0)
 */

    public function borrarcierre(){
        $jinput = JFactory::getApplication()->input;
        $id = $jinput->get('id');
        $db = JFactory::getDbo();
    $query = $db->getQuery(true);
    $campos = array(
        $db->quoteName('state') . ' = ' . $db->quote('0'),
    );

    $condiciones = array(
     $db->quoteName('id') . ' = '.$id,
    );
    $query->update($db->quoteName('#__ereservas_cierres'))->set($campos)->where($condiciones);

    $db->setQuery($query);

    $result = $db->execute();

    }
}