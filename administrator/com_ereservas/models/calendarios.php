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
class EreservasModelCalendarios extends \Joomla\CMS\MVC\Model\ListModel
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
				'fecha_inicio', 'a.`fecha_inicio`',
				'fecha_fin', 'a.`fecha_fin`',
				'id_tipo_visita', 'a.`id_tipo_visita`',
				'hora_inicio', 'a.`hora_inicio`',
				'hora_final', 'a.`hora_final`',
				'frecuencia', 'a.`frecuencia`',
				'dias_semana', 'a.`dias_semana`',
				'dias_mes', 'a.`dias_mes`',
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
		$query->from('`#__ereservas_calendario` AS a');
                
		// Join over the users for the checked out user
		$query->select("uc.name AS uEditor");
		$query->join("LEFT", "#__users AS uc ON uc.id=a.checked_out");

		// Join over the user field 'created_by'
		$query->select('`created_by`.name AS `created_by`');
		$query->join('LEFT', '#__users AS `created_by` ON `created_by`.id = a.`created_by`');

		// Join over the user field 'modified_by'
		$query->select('`modified_by`.name AS `modified_by`');
		$query->join('LEFT', '#__users AS `modified_by` ON `modified_by`.id = a.`modified_by`');
		// Join over the foreign key 'id_tipo_visita'
		$query->select('`#__ereservas_tipo_visita_3251380`.`nombre` AS tipovisitas_fk_value_3251380');
		$query->join('LEFT', '#__ereservas_tipo_visita AS #__ereservas_tipo_visita_3251380 ON #__ereservas_tipo_visita_3251380.`id` = a.`id_tipo_visita`');
                

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
				
			}
		}
                

		// Filtering fecha_inicio
		$filter_fecha_inicio_from = $this->state->get("filter.fecha_inicio.from");

		if ($filter_fecha_inicio_from !== null && !empty($filter_fecha_inicio_from))
		{
			$query->where("a.`fecha_inicio` >= '".$db->escape($filter_fecha_inicio_from)."'");
		}
		$filter_fecha_inicio_to = $this->state->get("filter.fecha_inicio.to");

		if ($filter_fecha_inicio_to !== null  && !empty($filter_fecha_inicio_to))
		{
			$query->where("a.`fecha_inicio` <= '".$db->escape($filter_fecha_inicio_to)."'");
		}

		// Filtering fecha_fin
		$filter_fecha_fin_from = $this->state->get("filter.fecha_fin.from");

		if ($filter_fecha_fin_from !== null && !empty($filter_fecha_fin_from))
		{
			$query->where("a.`fecha_fin` >= '".$db->escape($filter_fecha_fin_from)."'");
		}
		$filter_fecha_fin_to = $this->state->get("filter.fecha_fin.to");

		if ($filter_fecha_fin_to !== null  && !empty($filter_fecha_fin_to))
		{
			$query->where("a.`fecha_fin` <= '".$db->escape($filter_fecha_fin_to)."'");
		}

		// Filtering id_tipo_visita
		$filter_id_tipo_visita = $this->state->get("filter.id_tipo_visita");

		if ($filter_id_tipo_visita !== null && !empty($filter_id_tipo_visita))
		{
			$query->where("a.`id_tipo_visita` = '".$db->escape($filter_id_tipo_visita)."'");
		}

		// Filtering frecuencia
		$filter_frecuencia = $this->state->get("filter.frecuencia");

		if ($filter_frecuencia !== null && (is_numeric($filter_frecuencia) || !empty($filter_frecuencia)))
		{
			$query->where("a.`frecuencia` = '".$db->escape($filter_frecuencia)."'");
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

			if (isset($oneItem->id_tipo_visita))
			{
				$values    = explode(',', $oneItem->id_tipo_visita);
				$textValue = array();

				foreach ($values as $value)
				{
					$db    = JFactory::getDbo();
					$query = $db->getQuery(true);
					$query
						->select('`#__ereservas_tipo_visita_3251380`.`nombre`')
						->from($db->quoteName('#__ereservas_tipo_visita', '#__ereservas_tipo_visita_3251380'))
						->where($db->quoteName('#__ereservas_tipo_visita_3251380.id') . ' = '. $db->quote($db->escape($value)));

					$db->setQuery($query);
					$results = $db->loadObject();

					if ($results)
					{
						$textValue[] = $results->nombre;
					}
				}

				$oneItem->id_tipo_visita = !empty($textValue) ? implode(', ', $textValue) : $oneItem->id_tipo_visita;
			}
					$oneItem->frecuencia = JText::_('COM_ERESERVAS_CALENDARIOS_FRECUENCIA_OPTION_' . strtoupper($oneItem->frecuencia));

				// Get the title of every option selected.

				$options = explode(',', $oneItem->dias_semana);

				$options_text = array();

				foreach ((array) $options as $option)
				{
					$options_text[] = JText::_('COM_ERESERVAS_CALENDARIOS_DIAS_SEMANA_OPTION_' . strtoupper($option));
				}

				$oneItem->dias_semana = !empty($options_text) ? implode(',', $options_text) : $oneItem->dias_semana;
		}

		return $items;
	}
}
