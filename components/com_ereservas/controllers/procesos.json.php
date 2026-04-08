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
class EreservasControllerProcesos extends EreservasController
{
    /**
     * Funcion para comprobar que nos llega algun parametro a la hora de obtener la visita.
     */
    public function limpiarPagosIncompletos()
    {

        $reservas = $this->cargarPagosIncompletos();

        $recalculos = array();
        foreach ($reservas as $reserva) {
            $this->anularReserva($reserva['id']);
            if(!in_array($reserva['id_visita'],$recalculos)) {
                array_push($recalculos,$reserva['id_visita']);
            }
        }

        foreach ($recalculos as $recalculo) {
            EreservasRecalculo::recalcularaforo($recalculo);
        }

        echo new JResponseJson(true);
    }

    protected function cargarPagosIncompletos(){

        $db = JFactory::getDbo();

        $query = $db->getQuery(true);

        $query->select($db->quoteName(array('id','id_visita')));
        $query->from($db->quoteName('#__ereservas_reserva'));
        $query->where($db->quoteName('estado') . ' IN ( ' . $db->quote('cancelado').','.$db->quote('pendiente-pago').')');
        $query->where($db->quoteName('pagado') . ' = ' . $db->quote('no'));
        $query->where($db->quoteName('state') . ' = ' . $db->quote('1'));
        $query->where($db->quoteName('fecha_modificacion') . ' < DATE_SUB(NOW(), INTERVAL 30 MINUTE)');
        $query->order('id_visita ASC');

        $db->setQuery($query);

        $results = $db->loadAssocList();

        return $results;
    }

    protected function anularReserva($id) {
        $object = new stdClass();
        $object->id = (int) $id;
        $object->state = 0;

        $result = JFactory::getDbo()->updateObject('#__ereservas_reserva', $object, 'id');

        return $result;
    }

}
