<?php

/**
 * @version    CVS: 1.0.0
 * @package    Com_Ereservas
 * @author     Equipos Mecanizados,S.L. <rsainz@emesa.com>
 * @copyright  2019 Equipos Mecanizados,S.L.
 * @license    Licencia Pública General GNU versión 2 o posterior. Consulte LICENSE.txt
 */

defined('_JEXEC') or die;

use Joomla\CMS\Factory;

jimport('joomla.application.component.modellist');

/**
 * Methods supporting a list of Ereservas records.
 *
 * @since  1.6
 */
class EreservasModelCalendarioReservas extends JModelList
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
				'id', 'a.id',
				'ordering', 'a.ordering',
				'state', 'a.state',
				'created_by', 'a.created_by',
				'modified_by', 'a.modified_by',
				'nombre', 'a.nombre',
				'telefono', 'a.telefono',
				'email', 'a.email',
				'id_visita', 'a.id_visita',
				'id_idioma', 'a.id_idioma',
				'personas', 'a.personas',
				'tarjeta', 'a.tarjeta',
				'pagado', 'a.pagado',
				'estado', 'a.estado',
				'id_usuario', 'a.id_usuario',
				'id_pais', 'a.id_pais',
				'cpostal', 'a.cpostal',
				'observaciones', 'a.observaciones',
				'id_tipo_cliente', 'a.id_tipo_cliente',
				'referencia', 'a.referencia',
				'vinos', 'a.vinos',
				'tipo_cata', 'a.tipo_cata',
				'aperitivo', 'a.aperitivo',
				'alergenos', 'a.alergenos',
				'menu_especial', 'a.menu_especial',
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
	 *
	 * @since    1.6
	 */
	protected function populateState($ordering = null, $direction = null)
	{
            $app  = JFactory::getApplication();
		$list = $app->getUserState($this->context . '.list');

		$ordering  = isset($list['filter_order'])     ? $list['filter_order']     : null;
		$direction = isset($list['filter_order_Dir']) ? $list['filter_order_Dir'] : null;

		$list['limit']     = $app->input->getInt('limit', JFactory::getConfig()->get('list_limit', 20));
		$list['start']     = $app->input->getInt('start', 0);
		$list['ordering']  = $ordering;
		$list['direction'] = $direction;

		$app->setUserState($this->context . '.list', $list);
		$app->input->set('list', null);

        // List state information.
        parent::populateState('a.ordering', 'asc');

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
            
		// Join over the users for the checked out user.
		$query->select('uc.name AS uEditor');
		$query->join('LEFT', '#__users AS uc ON uc.id=a.checked_out');

		// Join over the created by field 'created_by'
		$query->join('LEFT', '#__users AS created_by ON created_by.id = a.created_by');

		// Join over the created by field 'modified_by'
		$query->join('LEFT', '#__users AS modified_by ON modified_by.id = a.modified_by');
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
            
		if (!Factory::getUser()->authorise('core.edit', 'com_ereservas'))
		{
			$query->where('a.state = 1');
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

		if ($filter_id_visita)
		{
			$query->where("a.`id_visita` = '".$db->escape($filter_id_visita)."'");
		}

		// Filtering id_idioma
		$filter_id_idioma = $this->state->get("filter.id_idioma");

		if ($filter_id_idioma)
		{
			$query->where("a.`id_idioma` = '".$db->escape($filter_id_idioma)."'");
		}

		// Filtering id_usuario
		$filter_id_usuario = $this->state->get("filter.id_usuario");

		if ($filter_id_usuario)
		{
			$query->where("a.`id_usuario` = '".$db->escape($filter_id_usuario)."'");
		}

		// Filtering id_pais
		$filter_id_pais = $this->state->get("filter.id_pais");

		if ($filter_id_pais)
		{
			$query->where("a.`id_pais` = '".$db->escape($filter_id_pais)."'");
		}

		// Filtering id_tipo_cliente
		$filter_id_tipo_cliente = $this->state->get("filter.id_tipo_cliente");

		if ($filter_id_tipo_cliente)
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
	 * Method to get an array of data items
	 *
	 * @return  mixed An array of data on success, false on failure.
	 */
	public function getItems()
	{
		$items = parent::getItems();
		
		foreach ($items as $item)
		{

			if (isset($item->id_visita))
			{

				$values    = explode(',', $item->id_visita);
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

				$item->id_visita = !empty($textValue) ? implode(', ', $textValue) : $item->id_visita;
			}


			if (isset($item->id_idioma))
			{

				$values    = explode(',', $item->id_idioma);
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

				$item->id_idioma = !empty($textValue) ? implode(', ', $textValue) : $item->id_idioma;
			}


			$item->pagado = JText::_('COM_ERESERVAS_RESERVAS_PAGADO_OPTION_' . strtoupper($item->pagado));

			$item->estado = JText::_('COM_ERESERVAS_RESERVAS_ESTADO_OPTION_' . strtoupper($item->estado));

			if (isset($item->id_usuario))
			{

				$values    = explode(',', $item->id_usuario);
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

				$item->id_usuario = !empty($textValue) ? implode(', ', $textValue) : $item->id_usuario;
			}


			if (isset($item->id_pais))
			{

				$values    = explode(',', $item->id_pais);
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

				$item->id_pais = !empty($textValue) ? implode(', ', $textValue) : $item->id_pais;
			}


			if (isset($item->id_tipo_cliente))
			{

				$values    = explode(',', $item->id_tipo_cliente);
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

				$item->id_tipo_cliente = !empty($textValue) ? implode(', ', $textValue) : $item->id_tipo_cliente;
			}

		}

		return $items;
	}

	/**
	 * Overrides the default function to check Date fields format, identified by
	 * "_dateformat" suffix, and erases the field if it's not correct.
	 *
	 * @return void
	 */
	protected function loadFormData()
	{
		$app              = JFactory::getApplication();
		$filters          = $app->getUserState($this->context . '.filter', array());
		$error_dateformat = false;

		foreach ($filters as $key => $value)
		{
			if (strpos($key, '_dateformat') && !empty($value) && $this->isValidDate($value) == null)
			{
				$filters[$key]    = '';
				$error_dateformat = true;
			}
		}

		if ($error_dateformat)
		{
			$app->enqueueMessage(JText::_("COM_ERESERVAS_SEARCH_FILTER_DATE_FORMAT"), "warning");
			$app->setUserState($this->context . '.filter', $filters);
		}

		return parent::loadFormData();
	}

	/**
	 * Checks if a given date is valid and in a specified format (YYYY-MM-DD)
	 *
	 * @param   string  $date  Date to be checked
	 *
	 * @return bool
	 */
	private function isValidDate($date)
	{
		$date = str_replace('/', '-', $date);
		return (date_create($date)) ? JFactory::getDate($date)->format("Y-m-d") : null;
	}
}
