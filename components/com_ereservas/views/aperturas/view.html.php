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
class EreservasViewAperturas extends JViewLegacy
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
        $this->idiomas = $this->idiomas();
        $this->visitas = $this->visitas();

		// Check for errors.
		if (count($errors = $this->get('Errors')))
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
	 * Consulta para sacar las visitas para el formulario
	 * @return todas las visitas -> $results
	 */
	protected function datosinicio(){
		$db= JFactory::getDbo();
		$query = $db->getQuery(true);
		$query->select(array('a.id','a.id_tipo_visita','a.fecha_inicio','a.fecha_fin','a.hora_inicio','a.hora_final','a.idioma','a.dias_semana','b.nombre as visita','c.nombre as nombre_idioma'));
		$query->from($db->quoteName('#__ereservas_calendario','a'));
        $query->leftJoin($db->quoteName('#__ereservas_tipo_visita','b'). ' ON '.$db->quoteName('a.id_tipo_visita').' = '.$db->quoteName('b.id'));
        $query->leftJoin($db->quoteName('#__ereservas_idioma','c'). ' ON '.$db->quoteName('a.idioma').' = '.$db->quoteName('c.id'));
		$query->where($db->quoteName('a.state') . ' = '. $db->quote('1'));
        $query->where($db->quoteName('a.frecuencia') . ' = '. $db->quote('diaria'));
        $query->order($db->quoteName('a.fecha_inicio') . ' DESC ');
		$db->setQuery($query);
		$results = $db->loadAssocList();
		return $results;
	}

    /**
     * Consulta para sacar los idiomas
     * @return todas los tipos de visita -> $results
     */
    protected function idiomas(){
        $db= JFactory::getDbo();
        $query = $db->getQuery(true);
        $query->select(array('id','nombre'));
        $query->from($db->quoteName('#__ereservas_idioma'));
        $query->where($db->quoteName('state') . ' = '. $db->quote('1'));
        $db->setQuery($query);
        $results = $db->loadAssocList();
        return $results;
    }

    /**
     * Consulta para sacar los tipos de visita
     * @return todos los tipos de visita -> $results
     */
    protected function visitas(){
        $db= JFactory::getDbo();
        $query = $db->getQuery(true);
        $query->select(array('id','nombre'));
        $query->from($db->quoteName('#__ereservas_tipo_visita'));
        $query->where($db->quoteName('state') . ' = '. $db->quote('1'));
        $db->setQuery($query);
        $results = $db->loadAssocList();
        return $results;
    }


}
