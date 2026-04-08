<?php
class EreservasRecalculo{

    /**
     * Funcion que calcula el aforo disponible en una visita
     * @param $idvisita -> es el id del cual queremos reaculcar el aforo
     */
    public static function recalcularaforo($idvisita){

        $db = JFactory::getDbo();
        $subQuery = $db->getQuery(true);
        $update = $db->getQuery(true);
        //sacamos hacemos la suma mediante bases de datos
        $subQuery->select("SUM(personas)as suma")
            ->from($db->quoteName('#__ereservas_reserva'))
            ->where($db->quoteName('id_visita').'='. $db->quote($idvisita))
            ->where($db->quoteName('estado').'<> '.$db->quote('cancelado') )
            ->where($db->quoteName('state').' = '.$db->quote('1') );

        $db->setQuery($subQuery);

        //extraemos el resultado
        $valor_sumar= $db->loadResult();


        //lo convertimos a INT
        $valor_int= (int)$valor_sumar;

        $db2 = JFactory::getDbo();

        //hacemos el update, y le establecemos el valor que hemos obtenido en la variable anterior
        $update->update($db2->quoteName('#__ereservas_visita', 'a'))
            ->set($db2->quoteName('a.aforo_ocupado')."=".$db2->quote($valor_int))
            ->where($db2->quoteName('a.id').'='. $db2->quote($idvisita));
        $db2->setQuery($update);
        $db2->execute();
    }

    /*
     * Funcion para recalcular los aforos de varios dias
     */
    public static function recalcularAforosDia($dia) {
        //Consulta para sacar todos las visitas que hay en un dia en concreto
        $db3 = JFactory::getDbo();
        $query = $db3->getQuery(true);
        $query->select('a.id');
        $query->from($db3->quoteName('#__ereservas_visita','a'));
        $query->leftJoin($db3->quoteName('#__ereservas_tipo_visita','b').' ON (a.id_tipo_visita = b.id)');
        $query->where('date('.$db3->quoteName('a.fecha') . ') = ' . $db3->quote($dia));
        $db3->setQuery($query);

        $dias = $db3->loadAssocList();

        //Por cada dia que haya, llamamos a la funcion de arriba para que nos recalcule el aforo
        foreach ($dias as $dia){
            EreservasRecalculo::recalcularaforo($dia['id']);
        }

    }

}