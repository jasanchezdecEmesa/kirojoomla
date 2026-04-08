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
use Joomla\CMS\Factory;
/**
 * Lista de espera controller class.
 *
 * @since  1.6
 */
class EreservasControllerListaEspera extends EreservasController{

   

    /**
     *  Funcion para crear un registro en la BDO dado su ID
     * @return void
     */

    public function crear_registro(){
        $input = JFactory::getApplication()->input;
		$nombre = $input->get('nombre',null,'html');
        $email = $input->get('email',null,'html');
        $comensales = $input->get('comensales',null,'int');
        $fecha = $input->get('fecha',null,'html');

        $db = JFactory::getDbo();

        $query = $db->getQuery(true);

        $columns = array('nombre', 'email', 'plazas', 'fecha');

        $values = array($db->quote($nombre), $db->quote($email), $db->quote($comensales), $db->quote($fecha));

        $query
            ->insert($db->quoteName('#__ereservas_lista_espera'))
            ->columns($db->quoteName($columns))
            ->values(implode(',', $values));

        $db->setQuery($query);

        $db->execute();

        echo new JResponseJson("Registro creado correctamente");
        die();
    }
}