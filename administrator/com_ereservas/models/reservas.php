<?php

/**
 * @version    CVS: 1.0.0
 * @package    Com_Ereservas
 * @author     Equipos Mecanizados,S.L. <rsainz@emesa.com>
 * @copyright  2019 Equipos Mecanizados,S.L.
 * @license    Licencia Pública General GNU versión 2 o posterior. Consulte LICENSE.txt
 */
defined('_JEXEC') or die;

jimport('joomla.application.component.modellist');

/**
 * Methods supporting a list of Ereservas records.
 *
 * @since  1.6
 */
class EreservasModelReservas extends \Joomla\CMS\MVC\Model\ListModel
{
    
        
/**
	* Constructor.
	*
	* @param   array  $config  An optional associative array of configuration settings.
	*
	* @see        JController
	* @since      1.6
	*/
	public function __construct($config = array())
	{
		if (empty($config['filter_fields']))
		{
			$config['filter_fields'] = array(
				'id', 'a.`id`',
				'ordering', 'a.`ordering`',
				'state', 'a.`state`',
				'created_by', 'a.`created_by`',
				'modified_by', 'a.`modified_by`',
				'nombre', 'a.`nombre`',
				'telefono', 'a.`telefono`',
				'email', 'a.`email`',
				'id_visita', 'a.`id_visita`',
				'id_idioma', 'a.`id_idioma`',
				'personas', 'a.`personas`',
				'tarjeta', 'a.`tarjeta`',
				'pagado', 'a.`pagado`',
				'estado', 'a.`estado`',
				'id_usuario', 'a.`id_usuario`',
				'id_pais', 'a.`id_pais`',
				'cpostal', 'a.`cpostal`',
				'observaciones', 'a.`observaciones`',
				'id_tipo_cliente', 'a.`id_tipo_cliente`',
				'referencia', 'a.`referencia`',
				'vinos', 'a.`vinos`',
				'tipo_cata', 'a.`tipo_cata`',
				'aperitivo', 'a.`aperitivo`',
				'alergenos', 'a.`alergenos`',
				'menu_especial', 'a.`menu_especial`',
				'token', 'a.`token`',
				'fecha_creacion', 'a.`fecha_creacion`',
				'fecha_modificacion', 'a.`fecha_modificacion`',
			);
		}

		parent::__construct($config);
	}

    
        
    
        
	/**
	 * Method to auto-populate the model state.
	 *
	 * Note. Calling getState in this method will result in recursion.
	 *
	 * @param   string  $ordering   Elements order
	 * @param   string  $direction  Order direction
	 *
	 * @return void
	 *
	 * @throws Exception
	 */
	protected function populateState($ordering = null, $direction = null)
	{
        // List state information.
        parent::populateState("a.id", "ASC");

        $context = $this->getUserStateFromRequest($this->context . '.context', 'context', 'com_content.article', 'CMD');
        $this->setState('filter.context', $context);

        // Split context into component and optional section
        $parts = FieldsHelper::extract($context);

        if ($parts)
        {
            $this->setState('filter.component', $parts[0]);
            $this->setState('filter.section', $parts[1]);
        }
	}

	/**
	 * Method to get a store id based on model configuration state.
	 *
	 * This is necessary because the model is used by the component and
	 * different modules that might need different sets of data or different
	 * ordering requirements.
	 *
	 * @param   string  $id  A prefix for the store id.
	 *
	 * @return   string A store id.
	 *
	 * @since    1.6
	 */
	protected function getStoreId($id = '')
	{
		// Compile the store id.
		$id .= ':' . $this->getState('filter.search');
		$id .= ':' . $this->getState('filter.state');

                
                    return parent::getStoreId($id);
                
	}

	/**
	 * Build an SQL query to load the list data.
	 *
	 * @return   JDatabaseQuery
	 *
	 * @since    1.6
	 */
	protected function getListQuery()
	{
		// Create a new query object.
		$db    = $this->getDbo();
		$query = $db->getQuery(true);

		// Select the required fields from the table.
		$query->select(
			$this->getState(
				'list.select', 'DISTINCT a.*'
			)
		);
		$query->from('`#__ereservas_reserva` AS a');
                
		// Join over the users for the checked out user
		$query->select("uc.name AS uEditor");
		$query->join("LEFT", "#__users AS uc ON uc.id=a.checked_out");

		// Join over the user field 'created_by'
		$query->select('`created_by`.name AS `created_by`');
		$query->join('LEFT', '#__users AS `created_by` ON `created_by`.id = a.`created_by`');

		// Join over the user field 'modified_by'
		$query->select('`modified_by`.name AS `modified_by`');
		$query->join('LEFT', '#__users AS `modified_by` ON `modified_by`.id = a.`modified_by`');
		// Join over the foreign key 'id_visita'
		$query->select('CONCAT(`#__ereservas_visita_3206109`.`nombre`, \' \', `#__ereservas_visita_3206109`.`fecha`, \' \', `#__ereservas_visita_3206109`.`hora_inicio`) AS visitas_fk_value_3206109');
		$query->join('LEFT', '#__ereservas_visita AS #__ereservas_visita_3206109 ON #__ereservas_visita_3206109.`id` = a.`id_visita`');
		// Join over the foreign key 'id_idioma'
		$query->select('`#__ereservas_idioma_3206117`.`nombre` AS idiomas_fk_value_3206117');
		$query->join('LEFT', '#__ereservas_idioma AS #__ereservas_idioma_3206117 ON #__ereservas_idioma_3206117.`id` = a.`id_idioma`');
		// Join over the foreign key 'id_usuario'
		$query->select('`#__users_3206126`.`name` AS users_fk_value_3206126');
		$query->join('LEFT', '#__users AS #__users_3206126 ON #__users_3206126.`id` = a.`id_usuario`');
		// Join over the foreign key 'id_pais'
		$query->select('`#__ereservas_pais_3206127`.`nombre` AS paiss_fk_value_3206127');
		$query->join('LEFT', '#__ereservas_pais AS #__ereservas_pais_3206127 ON #__ereservas_pais_3206127.`id` = a.`id_pais`');
		// Join over the foreign key 'id_tipo_cliente'
		$query->select('`#__ereservas_tipo_cliente_3206132`.`nombre` AS tipoclientes_fk_value_3206132');
		$query->join('LEFT', '#__ereservas_tipo_cliente AS #__ereservas_tipo_cliente_3206132 ON #__ereservas_tipo_cliente_3206132.`id` = a.`id_tipo_cliente`');
                

		// Filter by published state
		$published = $this->getState('filter.state');

		if (is_numeric($published))
		{
			$query->where('a.state = ' . (int) $published);
		}
		elseif ($published === '')
		{
			$query->where('(a.state IN (0, 1))');
		}

		// Filter by search in title
		$search = $this->getState('filter.search');

		if (!empty($search))
		{
			if (stripos($search, 'id:') === 0)
			{
				$query->where('a.id = ' . (int) substr($search, 3));
			}
			else
			{
				$search = $db->Quote('%' . $db->escape($search, true) . '%');
				$query->where('( a.nombre LIKE ' . $search . '  OR  a.telefono LIKE ' . $search . '  OR  a.email LIKE ' . $search . '  OR  a.cpostal LIKE ' . $search . ' )');
			}
		}
                

		// Filtering id_visita
		$filter_id_visita = $this->state->get("filter.id_visita");

		if ($filter_id_visita !== null && !empty($filter_id_visita))
		{
			$query->where("a.`id_visita` = '".$db->escape($filter_id_visita)."'");
		}

		// Filtering id_idioma
		$filter_id_idioma = $this->state->get("filter.id_idioma");

		if ($filter_id_idioma !== null && !empty($filter_id_idioma))
		{
			$query->where("a.`id_idioma` = '".$db->escape($filter_id_idioma)."'");
		}

		// Filtering id_usuario
		$filter_id_usuario = $this->state->get("filter.id_usuario");

		if ($filter_id_usuario !== null && !empty($filter_id_usuario))
		{
			$query->where("a.`id_usuario` = '".$db->escape($filter_id_usuario)."'");
		}

		// Filtering id_pais
		$filter_id_pais = $this->state->get("filter.id_pais");

		if ($filter_id_pais !== null && !empty($filter_id_pais))
		{
			$query->where("a.`id_pais` = '".$db->escape($filter_id_pais)."'");
		}

		// Filtering id_tipo_cliente
		$filter_id_tipo_cliente = $this->state->get("filter.id_tipo_cliente");

		if ($filter_id_tipo_cliente !== null && !empty($filter_id_tipo_cliente))
		{
			$query->where("a.`id_tipo_cliente` = '".$db->escape($filter_id_tipo_cliente)."'");
		}
		// Add the list ordering clause.
		$orderCol  = $this->state->get('list.ordering', "a.id");
		$orderDirn = $this->state->get('list.direction', "ASC");

		if ($orderCol && $orderDirn)
		{
			$query->order($db->escape($orderCol . ' ' . $orderDirn));
		}

		return $query;
	}

	/**
	 * Get an array of data items
	 *
	 * @return mixed Array of data items on success, false on failure.
	 */
	public function getItems()
	{
		$items = parent::getItems();
                
		foreach ($items as $oneItem)
		{

			if (isset($oneItem->id_visita))
			{
				$values    = explode(',', $oneItem->id_visita);
				$textValue = array();

				foreach ($values as $value)
				{
					$db    = JFactory::getDbo();
					$query = $db->getQuery(true);
					$query
						->select('CONCAT(`#__ereservas_visita_3206109`.`nombre`, \'  - \', `#__ereservas_visita_3206109`.`fecha`, \'  - \', `#__ereservas_visita_3206109`.`hora_inicio`) AS `fk_value`')
						->from($db->quoteName('#__ereservas_visita', '#__ereservas_visita_3206109'))
						->where($db->quoteName('#__ereservas_visita_3206109.id') . ' = '. $db->quote($db->escape($value)));

					$db->setQuery($query);
					$results = $db->loadObject();

					if ($results)
					{
						$textValue[] = $results->fk_value;
					}
				}

				$oneItem->id_visita = !empty($textValue) ? implode(', ', $textValue) : $oneItem->id_visita;
			}

			if (isset($oneItem->id_idioma))
			{
				$values    = explode(',', $oneItem->id_idioma);
				$textValue = array();

				foreach ($values as $value)
				{
					$db    = JFactory::getDbo();
					$query = $db->getQuery(true);
					$query
						->select('`#__ereservas_idioma_3206117`.`nombre`')
						->from($db->quoteName('#__ereservas_idioma', '#__ereservas_idioma_3206117'))
						->where($db->quoteName('#__ereservas_idioma_3206117.id') . ' = '. $db->quote($db->escape($value)));

					$db->setQuery($query);
					$results = $db->loadObject();

					if ($results)
					{
						$textValue[] = $results->nombre;
					}
				}

				$oneItem->id_idioma = !empty($textValue) ? implode(', ', $textValue) : $oneItem->id_idioma;
			}
					$oneItem->pagado = JText::_('COM_ERESERVAS_RESERVAS_PAGADO_OPTION_' . strtoupper($oneItem->pagado));
					$oneItem->estado = JText::_('COM_ERESERVAS_RESERVAS_ESTADO_OPTION_' . strtoupper($oneItem->estado));

			if (isset($oneItem->id_usuario))
			{
				$values    = explode(',', $oneItem->id_usuario);
				$textValue = array();

				foreach ($values as $value)
				{
					$db    = JFactory::getDbo();
					$query = $db->getQuery(true);
					$query
						->select('`#__users_3206126`.`name`')
						->from($db->quoteName('#__users', '#__users_3206126'))
						->where($db->quoteName('#__users_3206126.id') . ' = '. $db->quote($db->escape($value)));

					$db->setQuery($query);
					$results = $db->loadObject();

					if ($results)
					{
						$textValue[] = $results->name;
					}
				}

				$oneItem->id_usuario = !empty($textValue) ? implode(', ', $textValue) : $oneItem->id_usuario;
			}

			if (isset($oneItem->id_pais))
			{
				$values    = explode(',', $oneItem->id_pais);
				$textValue = array();

				foreach ($values as $value)
				{
					$db    = JFactory::getDbo();
					$query = $db->getQuery(true);
					$query
						->select('`#__ereservas_pais_3206127`.`nombre`')
						->from($db->quoteName('#__ereservas_pais', '#__ereservas_pais_3206127'))
						->where($db->quoteName('#__ereservas_pais_3206127.id') . ' = '. $db->quote($db->escape($value)));

					$db->setQuery($query);
					$results = $db->loadObject();

					if ($results)
					{
						$textValue[] = $results->nombre;
					}
				}

				$oneItem->id_pais = !empty($textValue) ? implode(', ', $textValue) : $oneItem->id_pais;
			}

			if (isset($oneItem->id_tipo_cliente))
			{
				$values    = explode(',', $oneItem->id_tipo_cliente);
				$textValue = array();

				foreach ($values as $value)
				{
					$db    = JFactory::getDbo();
					$query = $db->getQuery(true);
					$query
						->select('`#__ereservas_tipo_cliente_3206132`.`nombre`')
						->from($db->quoteName('#__ereservas_tipo_cliente', '#__ereservas_tipo_cliente_3206132'))
						->where($db->quoteName('#__ereservas_tipo_cliente_3206132.id') . ' = '. $db->quote($db->escape($value)));

					$db->setQuery($query);
					$results = $db->loadObject();

					if ($results)
					{
						$textValue[] = $results->nombre;
					}
				}

				$oneItem->id_tipo_cliente = !empty($textValue) ? implode(', ', $textValue) : $oneItem->id_tipo_cliente;
			}
		}

		return $items;
	}
}
