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

jimport('joomla.application.component.view');

/**
 * View class for a list of Ereservas.
 *
 * @since  1.6
 */
class EreservasViewAperturaCalendario extends JViewLegacy
{
	protected $items;

	protected $pagination;

	protected $state;

	protected $params;

	/**
	 * Display the view
	 *
	 * @param   string  $tpl  Template name
	 *
	 * @return void
	 *
	 * @throws Exception
	 */
	public function display($tpl = null)
	{
		$app = JFactory::getApplication();

		$this->state = $this->get('State');
		$this->items = $this->get('Items');
		$this->pagination = $this->get('Pagination');
		$this->params = $app->getParams('com_ereservas');
		$this->filterForm = $this->get('FilterForm');
		$this->activeFilters = $this->get('ActiveFilters');
		$this->datosinicio = $this->datosinicio();
		// Check for errors.
		if (@count($errors = $this->get('Errors')))
		{
			throw new Exception(implode("\n", $errors));
		}

		$this->_prepareDocument();
		parent::display($tpl);
	}

	/**
	 * Prepares the document
	 *
	 * @return void
	 *
	 * @throws Exception
	 */
	protected function _prepareDocument()
	{
		$app   = JFactory::getApplication();
		$menus = $app->getMenu();
		$title = null;

		// Because the application sets a default page title,
		// we need to get it from the menu item itself
		$menu = $menus->getActive();

		if ($menu)
		{
			$this->params->def('page_heading', $this->params->get('page_title', $menu->title));
		}
		else
		{
			$this->params->def('page_heading', JText::_('COM_ERESERVAS_DEFAULT_PAGE_TITLE'));
		}

		$title = $this->params->get('page_title', '');

		if (empty($title))
		{
			$title = $app->get('sitename');
		}
		elseif ($app->get('sitename_pagetitles', 0) == 1)
		{
			$title = JText::sprintf('JPAGETITLE', $app->get('sitename'), $title);
		}
		elseif ($app->get('sitename_pagetitles', 0) == 2)
		{
			$title = JText::sprintf('JPAGETITLE', $title, $app->get('sitename'));
		}

		$this->document->setTitle($title);

		if ($this->params->get('menu-meta_description'))
		{
			$this->document->setDescription($this->params->get('menu-meta_description'));
		}

		if ($this->params->get('menu-meta_keywords'))
		{
			$this->document->setMetadata('keywords', $this->params->get('menu-meta_keywords'));
		}

		if ($this->params->get('robots'))
		{
			$this->document->setMetadata('robots', $this->params->get('robots'));
		}
	}


	/**
	 * Consulta para sacar los tipos de visita para el formulario
	 * @return todos los tipos de visita -> $results
	 */
	protected function datosinicio(){
		$db= JFactory::getDbo();
		$query = $db->getQuery(true);
		$query->select('*');
		$query->from($db->quoteName('#__ereservas_fechas_apertura'));
		$query->where($db->quoteName('state') . ' = '. $db->quote('1'));
		$db->setQuery($query);
		$results = $db->loadAssocList();
		return $results;
	}


	/**
	 * Check if state is set
	 *
	 * @param   mixed  $state  State
	 *
	 * @return bool
	 */
	public function getState($state)
	{
		return isset($this->state->{$state}) ? $this->state->{$state} : false;
	}

	/**
	 * Consulta para sacar los tipos de visita para el formulario
	 * @return todos los tipos de visita -> $results
	 */
	protected function gettipovisita(){
		$db= JFactory::getDbo();
		$query = $db->getQuery(true);
		$query->select('*');
		$query->from($db->quoteName('#__ereservas_tipo_visita'));
		$db->setQuery($query);
		$results = $db->loadAssocList();
		return $results;
	}
	/**
	 * Consulta para sacar los idiomas para el formulario de las reservas, situado en la parte mas inferior
	 * @return todos los idomas que hay en la bdo y en la tabla ereservas_idioma -> $results
	 */
	protected function getidiomas(){
		$db= JFactory::getDbo();
		$query = $db->getQuery(true);
		$query->select('*');
		$query->from($db->quoteName('#__ereservas_idioma'));
		$db->setQuery($query);
		$results = $db->loadAssocList();
		return $results;
	}
	/**
	 * Consulta para sacar los usuarios para el formulario de las reservas, situado en la parte mas inferior
	 * @return todos los usuarios que hay en la bdo y en la tabla #__users, siempre y cuando pertenezcan al grupo 3 -> $results
	 */
	protected function getusers(){
		$db= JFactory::getDbo();
		$query = $db->getQuery(true);
		$query->select('a.*');
		$query->from($db->quoteName('#__users','a'));
		$query->join('INNER',$db->quoteName('#__user_usergroup_map', 'b') . ' ON (' . $db->quoteName('a.id') . ' = ' . $db->quoteName('b.user_id') . ')');
		$query->where($db->quoteName('b.group_id'). ' =  3' );
		$db->setQuery($query);
		$results = $db->loadAssocList();
		return $results;
	}
	protected function getpais(){
		$db= JFactory::getDbo();
		$query = $db->getQuery(true);
		$query->select('*');
		$query->from($db->quoteName('#__ereservas_pais'));
		$db->setQuery($query);
		$results = $db->loadAssocList();
		return $results;
	}
	protected function tipocliente(){
		$db= JFactory::getDbo();
		$query = $db->getQuery(true);
		$query->select('*');
		$query->from($db->quoteName('#__ereservas_tipo_cliente'));
		$db->setQuery($query);
		$results = $db->loadAssocList();
		return $results;
	}
}
