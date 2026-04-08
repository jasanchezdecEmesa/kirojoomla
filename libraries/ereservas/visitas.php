<?php
/**
 * @package     Joomla.Platform
 * @subpackage  Application
 *
 * @copyright   Copyright (C) 2005 - 2018 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

defined('JPATH_PLATFORM') or die;

class EreservasVisitas
{

    /**
     * Funcion para guardar las visitas con las fechas indicadas
     * @param $data
     */
    public static function generarVisitas($data,$agendasemanal = false)
{

    $fecha = $data['fecha_inicio'];
    $visita = EreservasVisitas::obtenerVisita($data['id_tipo_visita']);


    if ($agendasemanal == true) {

        $periodos = EreservasVisitas::aperturasSemanales();

        while (strtotime($fecha) <= strtotime($data['fecha_fin'])) {

                $diasemana = date('N', strtotime($fecha));

                foreach ($periodos as $periodo) {
                    if($periodo['dia_semana'] == $diasemana) {
                        $datos = array();
                        $datos['hora_inicio'] = $periodo['hora'];
                        $datos['hora_final'] = false;
                        $datos['aforo'] = $periodo['aforo'];
                        $datos['idioma'] = 1;
                        $datos['id_tipo_visita'] = $data['id_tipo_visita'];
                        $datos['id_calendario'] = $data['id_calendario'];

                        EreservasVisitas::guardarVisita($datos, $visita, $fecha);
                    }
                }

            //siguiente día
            $fecha = date("Y-m-d", strtotime("+1 day", strtotime($fecha)));
        }
        die();

    } else if ($data['frecuencia'] == 'mensual') {

        $dias = explode(',', $data['diaspuntuales']);
        foreach ($dias as $dia) {
            EreservasVisitas::guardarVisita($data, $visita, $dia);
        }


    } else {
        while (strtotime($fecha) <= strtotime($data['fecha_fin'])) {

            if ($data['frecuencia'] == 'diaria') {

                $diasemana = date('N', strtotime($fecha));

                if (in_array($diasemana, explode(',',$data['dias_semana']))) {
                    EreservasVisitas::guardarVisita($data, $visita, $fecha);
                }
            } else if ($data['frecuencia'] == 'mensual') {
                $diames = date('d', strtotime($fecha));
                if (in_array($diames, $data['dias_mes'])) {
                    EreservasVisitas::guardarVisita($data, $visita, $fecha);
                }
            }

//siguiente día
            $fecha = date("Y-m-d", strtotime("+1 day", strtotime($fecha)));
        }
    }
}

    /**
     * Obtenemos los datos de la visita a traves de su id
     * @param $id -> id visita
     * @return mixed
     */
    protected static function obtenerVisita($id)
    {

        $db = JFactory::getDbo();

        $query = $db->getQuery(true);

        $query->select($db->quoteName(array('aforo_web', 'aforo_privado', 'comida', 'cata', 'observaciones', 'precio', 'id_sala')));
        $query->from($db->quoteName('#__ereservas_tipo_visita'));
        $query->where($db->quoteName('id') . ' LIKE ' . $db->quote($id));

        $db->setQuery($query);

        $result = $db->loadAssoc();

        return $result;
    }

    /**
     * Funcion para guardar las visitas
     * @param $datos -> datos a insertar
     * @param $visita -> campos de la visita generada
     * @param $fecha -> fecha de la visita
     * @return bool
     */
    protected static function guardarVisita($datos, $visita, $fecha)
    {
//Procesamos la fecha para que siempre sea consistente
        $fecha = substr($fecha, 0, 10) . ' 00:00:00';

//Comprobamos que la visita no exista
        $existe = EreservasUtiles::comprobarExistenciaVisita($datos['id_tipo_visita'], $datos['hora_inicio'], $fecha);

//Comprobamos cierres
        $cierre = EreservasUtiles::comprobarFechaCierre($fecha);

//si ya existe el tipo de reserva para la misma fecha y hora, o es un día de cierre. La fecha no se crea
        if ($existe || $cierre) {
            return false;
        }

//mapeamos la base de datos

        $campos = [];
        $campos['state'] = 1;
        $campos['checked_out'] = 0;
        $campos['checked_out_time'] = '0000-00-00 00:00:00';
        $campos['created_by'] = JFactory::getUser()->id;
        $campos['modified_by'] = $campos['created_by'];
        $campos['aforo_web'] = (isset($datos['aforo']))?$datos['aforo']:$visita['aforo_web'];
        $campos['aforo_privado'] = $visita['aforo_privado'];
        $campos['comida'] = $visita['comida'];
        $campos['cata'] = $visita['cata'];
        $campos['observaciones'] = $visita['observaciones'];
        $campos['precio'] = $visita['precio'];
        $campos['fecha'] = $fecha;
        $campos['hora_inicio'] = $datos['hora_inicio'];
        $campos['hora_fin'] = $datos['hora_final'];
        $campos['idioma'] = $datos['idioma'];
        $campos['id_tipo_visita'] = $datos['id_tipo_visita'];
        $campos['id_sala'] = $visita['id_sala'];
        $campos['id_calendario'] = $datos['id_calendario'];
//$campos['guia'] = 0;


//realizamos la inserción

        $db = JFactory::getDbo();

        $query = $db->getQuery(true);

        $columns = array();
        $values = array();

        foreach ($campos as $clave => $valor) {
            array_push($columns, $clave);
            array_push($values, $db->quote($valor));
        }

        $query
            ->insert($db->quoteName('#__ereservas_visita'))
            ->columns($db->quoteName($columns))
            ->values(implode(',', $values));

        $db->setQuery($query);

        $resultado = $db->execute();

        return $resultado;

    }

    /**
     * Anulamos las Visitas asociadas a un calendario si no tienen visita asociada
     *
     * @param $id id del calendario
     */
    public static function anularCalendario($id)
    {
        $ids_visita = EreservasVisitas::obtenerIdsVisita($id);

        foreach ($ids_visita as $visita) {
            //Si hay id visita, comprobamos si se puede anular
            $anulable = EreservasVisitas::visitaAnulable($visita['id']);

            //Si se puede anular, la cerramos
            if($anulable) EreservasVisitas::cerrarVisita($visita['id']);
        }


        //Una vez limpio el calendario de reservas, cerramos esta línea
        $resultado = EreservasVisitas::cerrarCalendario($id);

        return $resultado;

    }



    /**
     * Obtenemos los datos originales del calendario
     * @param $id
     * @return array id de la visita a anular
     */

    protected static function obtenerIdsVisita($id){
        $db = JFactory::getDbo();

        $query = $db->getQuery(true);

        $query->select($db->quoteName('id'));
        $query->from($db->quoteName('#__ereservas_visita'));
        $query->where($db->quoteName('id_calendario') . ' = ' . $db->quote($id));

        $db->setQuery($query);

        $result = $db->loadAssocList();

        return $result;
    }

    /**
     * Comprobamos si una visita es anulable (si no tiene reservas, se puede anular)
     * @param $id
     * @return bool si es true, se puede anular
     */

    protected static function visitaAnulable($id){
        $db = JFactory::getDbo();

        $query = $db->getQuery(true);

        $query->select($db->quoteName('id'));
        $query->from($db->quoteName('#__ereservas_reserva'));
        $query->where($db->quoteName('id_visita') . ' LIKE ' . $db->quote($id));

        $db->setQuery($query, 0,1);

        $result = $db->loadResult();

        if($result > 0) {
            return false;
        } else {
            return true;
        }


    }

    /**
     * Comprobamos si una visita es anulable (si no tiene reservas, se puede anular)
     * @param $id
     * @return bool si es true, se puede anular
     */

    protected static function cerrarVisita($id){
        // Create an object for the record we are going to update.
        $object = new stdClass();

        // Must be a valid primary key value.
        $object->id = $id;
        $object->state = 0;

        // Update their details in the users table using id as the primary key.
        $result = JFactory::getDbo()->updateObject('#__ereservas_visita', $object, 'id');

        return $result;
    }

    /**
     * Comprobamos si una visita es anulable (si no tiene reservas, se puede anular)
     * @param $id
     * @return bool si es true, se puede anular
     */

    protected static function cerrarCalendario($id){
        // Create an object for the record we are going to update.
        $object = new stdClass();

        // Must be a valid primary key value.
        $object->id = $id;
        $object->state = 0;

        // Update their details in the users table using id as the primary key.
        $result = JFactory::getDbo()->updateObject('#__ereservas_calendario', $object, 'id');

        return $result;
    }


    /**
     * Creamos la línea de calendario
     * @param $datos del calendario
     */

    public static function guardarCalendario($datos){

        $valores = EreservasUtiles::camposInsertGenericos();
        $db = JFactory::getDbo();
        $query = $db->getQuery(true);

        $columns = array();
        $values = array();
        foreach($valores as $clave => $valor) {
            array_push($columns,$clave);
            array_push($values,$db->quote($valor));
        }

        foreach($datos as $clave => $valor) {
            array_push($columns,$clave);
            array_push($values,$db->quote($valor));
        }

        $query
            ->insert($db->quoteName('#__ereservas_calendario'))
            ->columns($db->quoteName($columns))
            ->values(implode(',', $values));
        $db->setQuery($query);
        $db->execute();

        return $db->insertid();;
    }

    /**
     * Obtenemos las aperturas semanales del cliente
     * @return array aperturas
     */

    protected static function aperturasSemanales(){
        $db = JFactory::getDbo();

        $query = $db->getQuery(true);

        $query->select($db->quoteName(array('dia_semana','hora','aforo')));
        $query->from($db->quoteName('#__ereservas_servicio_semanal'));
        $query->where($db->quoteName('state') . ' = ' . $db->quote('1'));
        $query->order($db->quoteName('dia_semana') . ' ASC');
        $query->order($db->quoteName('hora') . ' ASC');

        $db->setQuery($query);

        $result = $db->loadAssocList();

        return $result;
    }
}