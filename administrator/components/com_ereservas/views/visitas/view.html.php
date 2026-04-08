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

use \Joomla\CMS\Language\Text;

/**
 * View class for a list of Ereservas.
 *
 * @since  1.6
 */
class EreservasViewVisitas extends \Joomla\CMS\MVC\View\HtmlView
{
	protected $items;

	protected $pagination;

	protected $state;

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
		$this->state = $this->get('State');
		$this->items = $this->get('Items');
		$this->pagination = $this->get('Pagination');
        $this->filterForm = $this->get('FilterForm');
        $this->activeFilters = $this->get('ActiveFilters');

		// Check for errors.
		if (count($errors = $this->get('Errors')))
		{
			throw new Exception(implode("\n", $errors));
		}

		EreservasHelper::addSubmenu('visitas');

		$this->addToolbar();

		$this->sidebar = JHtmlSidebar::render();
		parent::display($tpl);
	}

	/**
	 * Add the page title and toolbar.
	 *
	 * @return void
	 *
	 * @since    1.6
	 */
	protected function addToolbar()
	{
		$state = $this->get('State');
		$canDo = EreservasHelper::getActions();

		JToolBarHelper::title(Text::_('COM_ERESERVAS_TITLE_VISITAS'), 'visitas.png');

		// Check if the form exists before showing the add/edit buttons
		$formPath = JPATH_COMPONENT_ADMINISTRATOR . '/views/visita';

		if (file_exists($formPath))
		{
			if ($canDo->get('core.create'))
			{
				JToolBarHelper::addNew('visita.add', 'JTOOLBAR_NEW');

				if (isset($this->items[0]))
				{
					JToolbarHelper::custom('visitas.duplicate', 'copy.png', 'copy_f2.png', 'JTOOLBAR_DUPLICATE', true);
				}
			}

			if ($canDo->get('core.edit') && isset($this->items[0]))
			{
				JToolBarHelper::editList('visita.edit', 'JTOOLBAR_EDIT');
			}
		}

		if ($canDo->get('core.edit.state'))
		{
			if (isset($this->items[0]->state))
			{
				JToolBarHelper::divider();
				JToolBarHelper::custom('visitas.publish', 'publish.png', 'publish_f2.png', 'JTOOLBAR_PUBLISH', true);
				JToolBarHelper::custom('visitas.unpublish', 'unpublish.png', 'unpublish_f2.png', 'JTOOLBAR_UNPUBLISH', true);
			}
			elseif (isset($this->items[0]))
			{
				// If this component does not use state then show a direct delete button as we can not trash
				JToolBarHelper::deleteList('', 'visitas.delete', 'JTOOLBAR_DELETE');
			}

			if (isset($this->items[0]->state))
			{
				JToolBarHelper::divider();
				JToolBarHelper::archiveList('visitas.archive', 'JTOOLBAR_ARCHIVE');
			}

			if (isset($this->items[0]->checked_out))
			{
				JToolBarHelper::custom('visitas.checkin', 'checkin.png', 'checkin_f2.png', 'JTOOLBAR_CHECKIN', true);
			}
		}

		// Show trash and delete for components that uses the state field
		if (isset($this->items[0]->state))
		{
			if ($state->get('filter.state') == -2 && $canDo->get('core.delete'))
			{
				JToolBarHelper::deleteList('', 'visitas.delete', 'JTOOLBAR_EMPTY_TRASH');
				JToolBarHelper::divider();
			}
			elseif ($canDo->get('core.edit.state'))
			{
				JToolBarHelper::trash('visitas.trash', 'JTOOLBAR_TRASH');
				JToolBarHelper::divider();
			}
		}

		if ($canDo->get('core.admin'))
		{
			JToolBarHelper::preferences('com_ereservas');
		}

		// Set sidebar action - New in 3.0
		JHtmlSidebar::setAction('index.php?option=com_ereservas&view=visitas');
	}

	/**
	 * Method to order fields 
	 *
	 * @return void 
	 */
	protected function getSortFields()
	{
		return array(
			'a.`id`' => JText::_('JGRID_HEADING_ID'),
			'a.`ordering`' => JText::_('JGRID_HEADING_ORDERING'),
			'a.`state`' => JText::_('JSTATUS'),
			'a.`nombre`' => JText::_('COM_ERESERVAS_VISITAS_NOMBRE'),
			'a.`id_sala`' => JText::_('COM_ERESERVAS_VISITAS_ID_SALA'),
			'a.`aforo_web`' => JText::_('COM_ERESERVAS_VISITAS_AFORO_WEB'),
			'a.`aforo_privado`' => JText::_('COM_ERESERVAS_VISITAS_AFORO_PRIVADO'),
			'a.`comida`' => JText::_('COM_ERESERVAS_VISITAS_COMIDA'),
			'a.`cata`' => JText::_('COM_ERESERVAS_VISITAS_CATA'),
			'a.`observaciones`' => JText::_('COM_ERESERVAS_VISITAS_OBSERVACIONES'),
			'a.`precio`' => JText::_('COM_ERESERVAS_VISITAS_PRECIO'),
			'a.`fecha`' => JText::_('COM_ERESERVAS_VISITAS_FECHA'),
			'a.`hora_inicio`' => JText::_('COM_ERESERVAS_VISITAS_HORA_INICIO'),
			'a.`hora_fin`' => JText::_('COM_ERESERVAS_VISITAS_HORA_FIN'),
			'a.`idioma`' => JText::_('COM_ERESERVAS_VISITAS_IDIOMA'),
			'a.`id_tipo_visita`' => JText::_('COM_ERESERVAS_VISITAS_ID_TIPO_VISITA'),
			'a.`aforo_ocupado`' => JText::_('COM_ERESERVAS_VISITAS_AFORO_OCUPADO'),
			'a.`guia`' => JText::_('COM_ERESERVAS_VISITAS_GUIA'),
		);
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
}
