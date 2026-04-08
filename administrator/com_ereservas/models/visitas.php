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
class EreservasModelVisitas extends \Joomla\CMS\MVC\Model\ListModel
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
				'id_sala', 'a.`id_sala`',
				'aforo_web', 'a.`aforo_web`',
				'aforo_privado', 'a.`aforo_privado`',
				'comida', 'a.`comida`',
				'cata', 'a.`cata`',
				'observaciones', 'a.`observaciones`',
				'precio', 'a.`precio`',
				'fecha', 'a.`fecha`',
				'hora_inicio', 'a.`hora_inicio`',
				'hora_fin', 'a.`hora_fin`',
				'idioma', 'a.`idioma`',
				'id_tipo_visita', 'a.`id_tipo_visita`',
				'aforo_ocupado', 'a.`aforo_ocupado`',
				'guia', 'a.`guia`',
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
        parent::populateState('id', 'ASC');

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
		$query->from('`#__ereservas_visita` AS a');
                
		// Join over the users for the checked out user
		$query->select("uc.name AS uEditor");
		$query->join("LEFT", "#__users AS uc ON uc.id=a.checked_out");

		// Join over the user field 'created_by'
		$query->select('`created_by`.name AS `created_by`');
		$query->join('LEFT', '#__users AS `created_by` ON `created_by`.id = a.`created_by`');

		// Join over the user field 'modified_by'
		$query->select('`modified_by`.name AS `modified_by`');
		$query->join('LEFT', '#__users AS `modified_by` ON `modified_by`.id = a.`modified_by`');
		// Join over the foreign key 'id_sala'
		$query->select('`#__ereservas_sala_3206140`.`nombre` AS salas_fk_value_3206140');
		$query->join('LEFT', '#__ereservas_sala AS #__ereservas_sala_3206140 ON #__ereservas_sala_3206140.`id` = a.`id_sala`');
		// Join over the foreign key 'idioma'
		$query->select('`#__ereservas_idioma_3206036`.`nombre` AS idiomas_fk_value_3206036');
		$query->join('LEFT', '#__ereservas_idioma AS #__ereservas_idioma_3206036 ON #__ereservas_idioma_3206036.`id` = a.`idioma`');
		// Join over the foreign key 'id_tipo_visita'
		$query->select('`#__ereservas_tipo_visita_3206044`.`nombre` AS tipovisitas_fk_value_3206044');
		$query->join('LEFT', '#__ereservas_tipo_visita AS #__ereservas_tipo_visita_3206044 ON #__ereservas_tipo_visita_3206044.`id` = a.`id_tipo_visita`');
		// Join over the foreign key 'guia'
		$query->select('`#__ereservas_guias_3224666`.`nombre` AS guias_fk_value_3224666');
		$query->join('LEFT', '#__ereservas_guias AS #__ereservas_guias_3224666 ON #__ereservas_guias_3224666.`id` = a.`guia`');
                

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
				$query->where('( a.nombre LIKE ' . $search . '  OR #__ereservas_idioma_3206036.nombre LIKE ' . $search . '  OR #__ereservas_tipo_visita_3206044.nombre LIKE ' . $search . '  OR #__ereservas_guias_3224666.nombre LIKE ' . $search . ' )');
			}
		}
                

		// Filtering id_sala
		$filter_id_sala = $this->state->get("filter.id_sala");

		if ($filter_id_sala !== null && !empty($filter_id_sala))
		{
			$query->where("FIND_IN_SET('" . $db->escape($filter_id_sala) . "',a.id_sala)");
		}

		// Filtering fecha
		$filter_fecha_from = $this->state->get("filter.fecha.from");

		if ($filter_fecha_from !== null && !empty($filter_fecha_from))
		{
			$query->where("a.`fecha` >= '".$db->escape($filter_fecha_from)."'");
		}
		$filter_fecha_to = $this->state->get("filter.fecha.to");

		if ($filter_fecha_to !== null  && !empty($filter_fecha_to))
		{
			$query->where("a.`fecha` <= '".$db->escape($filter_fecha_to)."'");
		}

		// Filtering idioma
		$filter_idioma = $this->state->get("filter.idioma");

		if ($filter_idioma !== null && !empty($filter_idioma))
		{
			$query->where("a.`idioma` = '".$db->escape($filter_idioma)."'");
		}

		// Filtering id_tipo_visita
		$filter_id_tipo_visita = $this->state->get("filter.id_tipo_visita");

		if ($filter_id_tipo_visita !== null && !empty($filter_id_tipo_visita))
		{
			$query->where("a.`id_tipo_visita` = '".$db->escape($filter_id_tipo_visita)."'");
		}

		// Filtering guia
		$filter_guia = $this->state->get("filter.guia");

		if ($filter_guia !== null && !empty($filter_guia))
		{
			$query->where("a.`guia` = '".$db->escape($filter_guia)."'");
		}
		// Add the list ordering clause.
		$orderCol  = $this->state->get('list.ordering', 'id');
		$orderDirn = $this->state->get('list.direction', 'ASC');

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

			if (isset($oneItem->id_sala))
			{
				$values    = explode(',', $oneItem->id_sala);
				$textValue = array();

				foreach ($values as $value)
				{
					$db    = JFactory::getDbo();
					$query = $db->getQuery(true);
					$query
						->select('`#__ereservas_sala_3206140`.`nombre`')
						->from($db->quoteName('#__ereservas_sala', '#__ereservas_sala_3206140'))
						->where($db->quoteName('#__ereservas_sala_3206140.id') . ' = '. $db->quote($db->escape($value)));

					$db->setQuery($query);
					$results = $db->loadObject();

					if ($results)
					{
						$textValue[] = $results->nombre;
					}
				}

				$oneItem->id_sala = !empty($textValue) ? implode(', ', $textValue) : $oneItem->id_sala;
			}
					$oneItem->comida = JText::_('COM_ERESERVAS_VISITAS_COMIDA_OPTION_' . strtoupper($oneItem->comida));
					$oneItem->cata = JText::_('COM_ERESERVAS_VISITAS_CATA_OPTION_' . strtoupper($oneItem->cata));

			if (isset($oneItem->idioma))
			{
				$values    = explode(',', $oneItem->idioma);
				$textValue = array();

				foreach ($values as $value)
				{
					$db    = JFactory::getDbo();
					$query = $db->getQuery(true);
					$query
						->select('`#__ereservas_idioma_3206036`.`nombre`')
						->from($db->quoteName('#__ereservas_idioma', '#__ereservas_idioma_3206036'))
						->where($db->quoteName('#__ereservas_idioma_3206036.id') . ' = '. $db->quote($db->escape($value)));

					$db->setQuery($query);
					$results = $db->loadObject();

					if ($results)
					{
						$textValue[] = $results->nombre;
					}
				}

				$oneItem->idioma = !empty($textValue) ? implode(', ', $textValue) : $oneItem->idioma;
			}

			if (isset($oneItem->id_tipo_visita))
			{
				$values    = explode(',', $oneItem->id_tipo_visita);
				$textValue = array();

				foreach ($values as $value)
				{
					$db    = JFactory::getDbo();
					$query = $db->getQuery(true);
					$query
						->select('`#__ereservas_tipo_visita_3206044`.`nombre`')
						->from($db->quoteName('#__ereservas_tipo_visita', '#__ereservas_tipo_visita_3206044'))
						->where($db->quoteName('#__ereservas_tipo_visita_3206044.id') . ' = '. $db->quote($db->escape($value)));

					$db->setQuery($query);
					$results = $db->loadObject();

					if ($results)
					{
						$textValue[] = $results->nombre;
					}
				}

				$oneItem->id_tipo_visita = !empty($textValue) ? implode(', ', $textValue) : $oneItem->id_tipo_visita;
			}

			if (isset($oneItem->guia))
			{
				$values    = explode(',', $oneItem->guia);
				$textValue = array();

				foreach ($values as $value)
				{
					$db    = JFactory::getDbo();
					$query = $db->getQuery(true);
					$query
						->select('`#__ereservas_guias_3224666`.`nombre`')
						->from($db->quoteName('#__ereservas_guias', '#__ereservas_guias_3224666'))
						->where($db->quoteName('#__ereservas_guias_3224666.id') . ' = '. $db->quote($db->escape($value)));

					$db->setQuery($query);
					$results = $db->loadObject();

					if ($results)
					{
						$textValue[] = $results->nombre;
					}
				}

				$oneItem->guia = !empty($textValue) ? implode(', ', $textValue) : $oneItem->guia;
			}
		}

		return $items;
	}
}
