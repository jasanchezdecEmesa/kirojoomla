<?php

/**
 * @version    CVS: 1.0.0
 * @package    Com_Ereservas
 * @author     Equipos Mecanizados,S.L. <rsainz@emesa.com>
 * @copyright  2019 Equipos Mecanizados,S.L.
 * @license    Licencia Pública General GNU versión 2 o posterior. Consulte LICENSE.txt
 */

defined('_JEXEC') or die;

use \Joomla\CMS\Factory;
use \Joomla\CMS\Language\Text;

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
				'id', 'a.id',
				'ordering', 'a.ordering',
				'state', 'a.state',
				'created_by', 'a.created_by',
				'modified_by', 'a.modified_by',
				'nombre', 'a.nombre',
				'id_sala', 'a.id_sala',
				'aforo_web', 'a.aforo_web',
				'aforo_privado', 'a.aforo_privado',
				'comida', 'a.comida',
				'cata', 'a.cata',
				'observaciones', 'a.observaciones',
				'precio', 'a.precio',
				'fecha', 'a.fecha',
				'hora_inicio', 'a.hora_inicio',
				'hora_fin', 'a.hora_fin',
				'idioma', 'a.idioma',
				'id_tipo_visita', 'a.id_tipo_visita',
				'aforo_ocupado', 'a.aforo_ocupado',
				'guia', 'a.guia',
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

		$list['limit']     = $app->getUserStateFromRequest($this->context . '.list.limit', 'limit', $app->get('list_limit'), 'uint');
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

            $query->from('`#__ereservas_visita` AS a');

		// Join over the users for the checked out user.
		$query->select('uc.name AS uEditor');
		$query->join('LEFT', '#__users AS uc ON uc.id=a.checked_out');

		// Join over the created by field 'created_by'
		$query->join('LEFT', '#__users AS created_by ON created_by.id = a.created_by');

		// Join over the created by field 'modified_by'
		$query->join('LEFT', '#__users AS modified_by ON modified_by.id = a.modified_by');
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
					$query->where('( a.nombre LIKE ' . $search . '  OR #__ereservas_idioma_3206036.nombre LIKE ' . $search . '  OR #__ereservas_tipo_visita_3206044.nombre LIKE ' . $search . '  OR #__ereservas_guias_3224666.nombre LIKE ' . $search . ' )');
                }
            }


		// Filtering id_sala
		$filter_id_sala = $this->state->get("filter.id_sala");

		if ($filter_id_sala)
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

		if ($filter_idioma)
		{
			$query->where("a.`idioma` = '".$db->escape($filter_idioma)."'");
		}

		// Filtering id_tipo_visita
		$filter_id_tipo_visita = $this->state->get("filter.id_tipo_visita");

		if ($filter_id_tipo_visita)
		{
			$query->where("a.`id_tipo_visita` = '".$db->escape($filter_id_tipo_visita)."'");
		}

		// Filtering guia
		$filter_guia = $this->state->get("filter.guia");

		if ($filter_guia)
		{
			$query->where("a.`guia` = '".$db->escape($filter_guia)."'");
		}

		// Add the list ordering clause.
		$orderCol  = $this->state->get('list.ordering', 'fecha');
		$orderDirn = $this->state->get('list.direction', 'ASC');

		if ($orderCol && $orderDirn)
		{
			$query->order($db->escape($orderCol . ' ' . $orderDirn),$db->escape('fecha ASC'),$db->escape('hora_inicio ASC'));
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

			if (isset($item->id_sala))
			{

				$values    = explode(',', $item->id_sala);
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

				$item->id_sala = !empty($textValue) ? implode(', ', $textValue) : $item->id_sala;
			}


			$item->comida = JText::_('COM_ERESERVAS_VISITAS_COMIDA_OPTION_' . strtoupper($item->comida));

			$item->cata = JText::_('COM_ERESERVAS_VISITAS_CATA_OPTION_' . strtoupper($item->cata));

			if (isset($item->idioma))
			{

				$values    = explode(',', $item->idioma);
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

				$item->idioma = !empty($textValue) ? implode(', ', $textValue) : $item->idioma;
			}


			if (isset($item->id_tipo_visita))
			{

				$values    = explode(',', $item->id_tipo_visita);
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

				$item->id_tipo_visita = !empty($textValue) ? implode(', ', $textValue) : $item->id_tipo_visita;
			}


			if (isset($item->guia))
			{

				$values    = explode(',', $item->guia);
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

				$item->guia = !empty($textValue) ? implode(', ', $textValue) : $item->guia;
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
		$app              = Factory::getApplication();
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
			$app->enqueueMessage(Text::_("COM_ERESERVAS_SEARCH_FILTER_DATE_FORMAT"), "warning");
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
		return (date_create($date)) ? Factory::getDate($date)->format("Y-m-d") : null;
	}
}
