<?php
class EreservasTarjeta{
    /**
     * Funcion para ahorrar linea de codigo y llamarla desde la libreria, metodo mas seguro
     * @return string
     *
     */
    protected static function getclave(){
        $clave= "Midb18T7EMESA2011Bo1Qt79a";
        return $clave;
    }

    /**
     * Funcion para cifrar la tarjeta que nos hayan insertado en el formulario
     * @param $tarjeta
     * @return string Con la tarjeta cifrada y encriptada
     */
    public static function cifrarinsert($tarjeta){


        $clave = EreservasTarjeta::getclave();
        $db = JFactory::getDbo();
        $return= "AES_ENCRYPT(".$db->quote($tarjeta).", ".$db->quote($clave).")";
        return $return;

    }

    /**
     * Funcion que desencripta el valor que hay almacenado en la base de datos de la tarjeta
     * @param $id
     * @return mixed valor de la tarjeta desencriptada
     */
    public static function descifrar($id){

    $clave= EreservasTarjeta::getclave();
        $db= JFactory::getDbo();
        $desencriptar_query = $db->getQuery(true);
        $desencriptar_query->select("AES_DECRYPT(".$db->quoteName('tarjeta').", ".$db->quote($clave).")"."AS tarjeta");
        $desencriptar_query->from($db->quoteName('#__ereservas_reserva'));
        $desencriptar_query->where($db->quoteName('id') . ' LIKE ' . $id);
        $db->setQuery($desencriptar_query);
        $results = $db->loadAssocList();
        return $results;
    }

    /**
     * Funcion que se ejecuta cuando hemos editado una reserva y hay que volver a cifrar la tarjeta antes de insertarla en la base de datos
     * @param $id -> es el id reserva
     * @param $tarjeta -> es el valor que hemos obtenido del formulario a la hora de insertar, vendra en texto plano y es aqui donde la
     *                    tendremos que encriptar
     */
    public static function cifrareditar($id,$tarjeta){
        $db = JFactory::getDbo();
        $update = $db->getQuery(true);
        // Prepare the insert query.
        $clave = EreservasTarjeta::getclave();
        $datos= $db->quoteName('tarjeta') .  "=  ". "AES_ENCRYPT(".$db->quote($tarjeta).", ".$db->quote($clave).")";
        $update
            ->update($db->quoteName('#__ereservas_reserva'))
            ->set($datos)
            ->where($db->quoteName('id').'='. ($id));
        //ejecutar la query.
        $db->setQuery($update);
        $db->execute();
    }

}