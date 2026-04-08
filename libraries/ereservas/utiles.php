<?php
/**
 * @package     Joomla.Platform
 * @subpackage  Application
 *
 * @copyright   Copyright (C) 2005 - 2018 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

defined('JPATH_PLATFORM') or die;

class EreservasUtiles
{
    /**
     * Método para comprobar la duplicidad de una visita
     * @param $id -> id visita
     * @param $hora -> hora a la que se ha establecido la visita
     * @param $fecha -> fecha a la que se ha establecido la visita
     * @return mixed devolvemos null en caso de que no exista o el resultado de esa visita en caso que tenga los mismos datos
     */
    public static function comprobarExistenciaVisita($id,$hora,$fecha){

        //Convertimos la hora, puede venir en formato H:i y el where necesita H:i:s
        $horaOK = strtotime($hora);
        $hora = date('H:i:s', $horaOK);

        $db = JFactory::getDbo();

        $query = $db->getQuery(true);

        $query->select($db->quoteName('id'));
        $query->from($db->quoteName('#__ereservas_visita'));
        $query->where($db->quoteName('id_tipo_visita') . ' LIKE '. $db->quote($id));
        $query->where($db->quoteName('fecha') . ' LIKE '. $db->quote($fecha));
        $query->where($db->quoteName('hora_inicio') . ' LIKE '. $db->quote($hora));
        $query->where($db->quoteName('state') . ' = 1');

        $db->setQuery($query);

        $result = $db->loadResult();

        return $result;
    }
    /**
     * Método para comprobar si una fecha el local está cerrado
     * @param $fecha -> fecha a la que se ha establecido la visita
     * @return mixed devolvemos null en caso de que no exista o el resultado de esa visita en caso que tenga los mismos datos
     */
    public static function comprobarFechaCierre($fecha){

        $db = JFactory::getDbo();

        $query = $db->getQuery(true);

        $query->select($db->quoteName('id'));
        $query->from($db->quoteName('#__ereservas_cierres'));
        $query->where($db->quoteName('fecha_inicio') . ' <= '. $db->quote($fecha));
        $query->where($db->quoteName('fecha_fin') . ' >= '. $db->quote($fecha));
        $query->where($db->quoteName('tipo_cierre') . ' IN ( '. $db->quote('cierre').', '. $db->quote('sin-visita').')');
        $query->where($db->quoteName('state') . ' = '. $db->quote('1'));

        $db->setQuery($query);

        $result = $db->loadResult();

        return $result;
    }


    public static function camposInsertGenericos(){

        $usuario = JFactory::getUser()->id;

        $valores = array();

        $valores['ordering'] = '1';
        $valores['state'] = '1';
        $valores['checked_out'] = '0';
        $valores['checked_out_time'] = '0000-00-00 00:00:00';
        $valores['created_by'] = $usuario;
        $valores['modified_by'] = $usuario;

        return $valores;
    }

    public static function obtenerPlazas($idvisita){
        $db= JFactory::getDbo();
        $query = $db->getQuery(true);
        $query->select($db->quoteName(array('aforo_web', 'aforo_ocupado', 'fecha')));
        $query->from($db->quoteName('#__ereservas_visita'));
        $query->where($db->quoteName('id') . ' LIKE ' . $db->quote($idvisita));

        $db->setQuery($query);
        $results = $db->loadAssoc();



        $aforo_web = $results['aforo_web'];
        $aforo_ocupado = $results['aforo_ocupado'];

        $disponibles = $aforo_web - $aforo_ocupado;


        if($disponibles > 0){
          EreservasUtiles::obtenerListaEspera($results['fecha'], $disponibles);
        }
    }

    public static function obtenerListaEspera($fecha, $plazas){

        $db= JFactory::getDbo();
        $query = $db->getQuery(true);

        $query->select($db->quoteName(array('nombre', 'email', 'plazas', 'fecha')));
        $query->from($db->quoteName('#__ereservas_lista_espera'));

        $condicionFecha =
        '(' .
        $db->quoteName('fecha') . ' = ' . $db->quote($fecha) .
        ' OR ' .
        $db->quoteName('fecha') . ' = ' . $db->quote('0000-00-00 00:00:00') .
        ')';

        $query->where($condicionFecha);
        $query->where($db->quoteName('plazas').' <= '.$db->quote($plazas));

        $db->setQuery($query);
        $results = $db->loadAssocList();


        if(count($results) >= 0){
            foreach ($results as $clienteEspera) {
            $clienteEspera['fecha_liberada'] = $fecha;
            EreservasCorreos::correoListaEspera($clienteEspera);
        }
        }


    }
}











