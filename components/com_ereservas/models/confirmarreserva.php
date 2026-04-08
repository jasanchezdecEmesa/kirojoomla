<?php

/**
 * @version    CVS: 1.0.0
 * @package    Com_Ereservas
 * @author     Equipos Mecanizados,S.L. <rsainz@emesa.com>
 * @copyright  2019 Equipos Mecanizados,S.L.
 * @license    Licencia Pública General GNU versión 2 o posterior. Consulte LICENSE.txt
 */
// No direct access.
defined('_JEXEC') or die;

jimport('joomla.application.component.modelitem');
jimport('joomla.event.dispatcher');

use Joomla\CMS\Factory;
use Joomla\Utilities\ArrayHelper;

/**
 * Ereservas model.
 *
 * @since  1.6
 */
class EreservasModelConfirmarReserva extends JModelItem
{
    public $_item;

        
    
        
	/**
	 * Method to auto-populate the model state.
	 *
	 * Note. Calling getState in this method will result in recursion.
	 *
	 * @return void
	 *
	 * @since    1.6
	 *
	 */
	protected function populateState()
	{
		$app  = JFactory::getApplication('com_ereservas');
		$user = JFactory::getUser();

		// Check published state
		if ((!$user->authorise('core.edit.state', 'com_ereservas')) && (!$user->authorise('core.edit', 'com_ereservas')))
		{
			$this->setState('filter.published', 1);
			$this->setState('filter.archived', 2);
		}

		// Load state from the request userState on edit or from the passed variable on default
		if (Factory::getApplication()->input->get('layout') == 'edit')
		{
			$id = JFactory::getApplication()->getUserState('com_ereservas.edit.reserva.id');
		}
		else
		{
			$id = JFactory::getApplication()->input->get('id');
			Factory::getApplication()->setUserState('com_ereservas.edit.reserva.id', $id);
		}

		$this->setState('reserva.id', $id);

		// Load the parameters.
		$params       = $app->getParams();
		$params_array = $params->toArray();

		if (isset($params_array['item_id']))
		{
			$this->setState('reserva.id', $params_array['item_id']);
		}

		$this->setState('params', $params);
	}

	/**
	 * Method to get an object.
	 *
	 * @param   integer $id The id of the object to get.
	 *
	 * @return  mixed    Object on success, false on failure.
     *
     * @throws Exception
	 */
	public function getItem($id = null)
	{
            if ($this->_item === null)
            {
                $this->_item = false;

                if (empty($id))
                {
                    $id = $this->getState('reserva.id');
                }

                // Get a level row instance.
                $table = $this->getTable();

                // Attempt to load the row.
                if ($table->load($id))
                {
                    

                    // Check published state.
                    if ($published = $this->getState('filter.published'))
                    {
                        if (isset($table->state) && $table->state != $published)
                        {
                            throw new Exception(JText::_('COM_ERESERVAS_ITEM_NOT_LOADED'), 403);
                        }
                    }

                    // Convert the JTable to a clean JObject.
                    $properties  = $table->getProperties(1);
                    $this->_item = ArrayHelper::toObject($properties, 'JObject');

                    
                } 
            }
        
            

		if (isset($this->_item->created_by))
		{
			$this->_item->created_by_name = JFactory::getUser($this->_item->created_by)->name;
		}

		if (isset($this->_item->modified_by))
		{
			$this->_item->modified_by_name = JFactory::getUser($this->_item->modified_by)->name;
		}

		if (isset($this->_item->id_visita) && $this->_item->id_visita != '')
		{
			if (is_object($this->_item->id_visita))
			{
				$this->_item->id_visita = ArrayHelper::fromObject($this->_item->id_visita);
			}

			$values = (is_array($this->_item->id_visita)) ? $this->_item->id_visita : explode(',',$this->_item->id_visita);

			$textValue = array();

			foreach ($values as $value)
			{
				$db    = JFactory::getDbo();
				$query = $db->getQuery(true);

				$query
					->select('CONCAT(`#__ereservas_visita_3206109`.`nombre`, \'  - \', `#__ereservas_visita_3206109`.`fecha`, \'  - \', `#__ereservas_visita_3206109`.`hora_inicio`) AS `fk_value`')
					->from($db->quoteName('#__ereservas_visita', '#__ereservas_visita_3206109'))
					->where($db->quoteName('id') . ' = ' . $db->quote($value));

				$db->setQuery($query);
				$results = $db->loadObject();

				if ($results)
				{
					$textValue[] = $results->fk_value;
				}
			}

			$this->_item->id_visita = !empty($textValue) ? implode(', ', $textValue) : $this->_item->id_visita;

		}

		if (isset($this->_item->id_idioma) && $this->_item->id_idioma != '')
		{
			if (is_object($this->_item->id_idioma))
			{
				$this->_item->id_idioma = ArrayHelper::fromObject($this->_item->id_idioma);
			}

			$values = (is_array($this->_item->id_idioma)) ? $this->_item->id_idioma : explode(',',$this->_item->id_idioma);

			$textValue = array();

			foreach ($values as $value)
			{
				$db    = JFactory::getDbo();
				$query = $db->getQuery(true);

				$query
					->select('`#__ereservas_idioma_3206117`.`nombre`')
					->from($db->quoteName('#__ereservas_idioma', '#__ereservas_idioma_3206117'))
					->where($db->quoteName('id') . ' = ' . $db->quote($value));

				$db->setQuery($query);
				$results = $db->loadObject();

				if ($results)
				{
					$textValue[] = $results->nombre;
				}
			}

			$this->_item->id_idioma = !empty($textValue) ? implode(', ', $textValue) : $this->_item->id_idioma;

		}

		if (!empty($this->_item->pagado))
		{
			$this->_item->pagado = JText::_('COM_ERESERVAS_RESERVAS_PAGADO_OPTION_' . $this->_item->pagado);
		}

		if (!empty($this->_item->estado))
		{
			$this->_item->estado = JText::_('COM_ERESERVAS_RESERVAS_ESTADO_OPTION_' . $this->_item->estado);
		}

		if (isset($this->_item->id_usuario) && $this->_item->id_usuario != '')
		{
			if (is_object($this->_item->id_usuario))
			{
				$this->_item->id_usuario = ArrayHelper::fromObject($this->_item->id_usuario);
			}

			$values = (is_array($this->_item->id_usuario)) ? $this->_item->id_usuario : explode(',',$this->_item->id_usuario);

			$textValue = array();

			foreach ($values as $value)
			{
				$db    = JFactory::getDbo();
				$query = $db->getQuery(true);

				$query
					->select('`#__users_3206126`.`name`')
					->from($db->quoteName('#__users', '#__users_3206126'))
					->where($db->quoteName('id') . ' = ' . $db->quote($value));

				$db->setQuery($query);
				$results = $db->loadObject();

				if ($results)
				{
					$textValue[] = $results->name;
				}
			}

			$this->_item->id_usuario = !empty($textValue) ? implode(', ', $textValue) : $this->_item->id_usuario;

		}

		if (isset($this->_item->id_pais) && $this->_item->id_pais != '')
		{
			if (is_object($this->_item->id_pais))
			{
				$this->_item->id_pais = ArrayHelper::fromObject($this->_item->id_pais);
			}

			$values = (is_array($this->_item->id_pais)) ? $this->_item->id_pais : explode(',',$this->_item->id_pais);

			$textValue = array();

			foreach ($values as $value)
			{
				$db    = JFactory::getDbo();
				$query = $db->getQuery(true);

				$query
					->select('`#__ereservas_pais_3206127`.`nombre`')
					->from($db->quoteName('#__ereservas_pais', '#__ereservas_pais_3206127'))
					->where($db->quoteName('id') . ' = ' . $db->quote($value));

				$db->setQuery($query);
				$results = $db->loadObject();

				if ($results)
				{
					$textValue[] = $results->nombre;
				}
			}

			$this->_item->id_pais = !empty($textValue) ? implode(', ', $textValue) : $this->_item->id_pais;

		}

		if (isset($this->_item->id_tipo_cliente) && $this->_item->id_tipo_cliente != '')
		{
			if (is_object($this->_item->id_tipo_cliente))
			{
				$this->_item->id_tipo_cliente = ArrayHelper::fromObject($this->_item->id_tipo_cliente);
			}

			$values = (is_array($this->_item->id_tipo_cliente)) ? $this->_item->id_tipo_cliente : explode(',',$this->_item->id_tipo_cliente);

			$textValue = array();

			foreach ($values as $value)
			{
				$db    = JFactory::getDbo();
				$query = $db->getQuery(true);

				$query
					->select('`#__ereservas_tipo_cliente_3206132`.`nombre`')
					->from($db->quoteName('#__ereservas_tipo_cliente', '#__ereservas_tipo_cliente_3206132'))
					->where($db->quoteName('id') . ' = ' . $db->quote($value));

				$db->setQuery($query);
				$results = $db->loadObject();

				if ($results)
				{
					$textValue[] = $results->nombre;
				}
			}

			$this->_item->id_tipo_cliente = !empty($textValue) ? implode(', ', $textValue) : $this->_item->id_tipo_cliente;

		}

            return $this->_item;
        }

	/**
	 * Get an instance of JTable class
	 *
	 * @param   string $type   Name of the JTable class to get an instance of.
	 * @param   string $prefix Prefix for the table class name. Optional.
	 * @param   array  $config Array of configuration values for the JTable object. Optional.
	 *
	 * @return  JTable|bool JTable if success, false on failure.
	 */
	public function getTable($type = 'Reserva', $prefix = 'EreservasTable', $config = array())
	{
		$this->addTablePath(JPATH_ADMINISTRATOR . '/components/com_ereservas/tables');

		return JTable::getInstance($type, $prefix, $config);
	}

	/**
	 * Get the id of an item by alias
	 *
	 * @param   string $alias Item alias
	 *
	 * @return  mixed
	 */
	public function getItemIdByAlias($alias)
	{
            $table      = $this->getTable();
            $properties = $table->getProperties();
            $result     = null;

            if (key_exists('alias', $properties))
            {
                $table->load(array('alias' => $alias));
                $result = $table->id;
            }
            
                return $result;
            
	}

	/**
	 * Method to check in an item.
	 *
	 * @param   integer $id The id of the row to check out.
	 *
	 * @return  boolean True on success, false on failure.
	 *
	 * @since    1.6
	 */
	public function checkin($id = null)
	{
		// Get the id.
		$id = (!empty($id)) ? $id : (int) $this->getState('reserva.id');
                
		if ($id)
		{
			// Initialise the table
			$table = $this->getTable();

			// Attempt to check the row in.
			if (method_exists($table, 'checkin'))
			{
				if (!$table->checkin($id))
				{
					return false;
				}
			}
		}

		return true;
                
	}

	/**
	 * Method to check out an item for editing.
	 *
	 * @param   integer $id The id of the row to check out.
	 *
	 * @return  boolean True on success, false on failure.
	 *
	 * @since    1.6
	 */
	public function checkout($id = null)
	{
		// Get the user id.
		$id = (!empty($id)) ? $id : (int) $this->getState('reserva.id');

                
		if ($id)
		{
			// Initialise the table
			$table = $this->getTable();

			// Get the current user object.
			$user = JFactory::getUser();

			// Attempt to check the row out.
			if (method_exists($table, 'checkout'))
			{
				if (!$table->checkout($user->get('id'), $id))
				{
					return false;
				}
			}
		}

		return true;
                
	}

	/**
	 * Publish the element
	 *
	 * @param   int $id    Item id
	 * @param   int $state Publish state
	 *
	 * @return  boolean
	 */
	public function publish($id, $state)
	{
		$table = $this->getTable();
                
		$table->load($id);
		$table->state = $state;

		return $table->store();
                
	}

	/**
	 * Method to delete an item
	 *
	 * @param   int $id Element id
	 *
	 * @return  bool
	 */
	public function delete($id)
	{
		$table = $this->getTable();

                
                    return $table->delete($id);
                
	}

	
}
