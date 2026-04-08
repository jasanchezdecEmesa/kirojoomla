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

use \Joomla\CMS\Factory;
use \Joomla\CMS\Language\Text;

/**
 * View to edit
 *
 * @since  1.6
 */
class EreservasViewVisita extends \Joomla\CMS\MVC\View\HtmlView
{
	protected $state;

	protected $item;

	protected $form;

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
		$app  = Factory::getApplication();
		$user = Factory::getUser();

		$this->state  = $this->get('State');
		$this->item   = $this->obtenerVisita();
		$this->params = $app->getParams('com_ereservas');

		if (!empty($this->item))
		{
			$this->form = $this->get('Form');
		} else {
            $app->enqueueMessage('Las reserva buscada no está disponible', 'error');
            $app->redirect(JRoute::_('index.php'));
        }

		// Check for errors.
		if (count($errors = $this->get('Errors')))
		{
			throw new Exception(implode("\n", $errors));
		}

		

		if ($this->_layout == 'edit')
		{
			$authorised = $user->authorise('core.create', 'com_ereservas');

			if ($authorised !== true)
			{
				throw new Exception(Text::_('JERROR_ALERTNOAUTHOR'));
			}
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
		$app   = Factory::getApplication();
		$menus = $app->getMenu();
		$title = null;

		// Because the application sets a default page title,
		// We need to get it from the menu item itself
		$menu = $menus->getActive();

		if ($menu)
		{
			$this->params->def('page_heading', $this->params->get('page_title', $menu->title));
		}
		else
		{
			$this->params->def('page_heading', Text::_('COM_ERESERVAS_DEFAULT_PAGE_TITLE'));
		}

		$title = $this->params->get('page_title', '');

		if (empty($title))
		{
			$title = $app->get('sitename');
		}
		elseif ($app->get('sitename_pagetitles', 0) == 1)
		{
			$title = Text::sprintf('JPAGETITLE', $app->get('sitename'), $title);
		}
		elseif ($app->get('sitename_pagetitles', 0) == 2)
		{
			$title = Text::sprintf('JPAGETITLE', $title, $app->get('sitename'));
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

	protected function obtenerVisita(){

        $app = Factory::getApplication();   // equivalent of $app = JFactory::getApplication();
        $alias = @$app->getMenu()->getActive()->query['alias'];

        $db = JFactory::getDbo();

        $query = $db->getQuery(true);

        $query->select($db->quoteName(array('id','imagen','nombre','precio','presentacion','descripcion','nombre_en','presentacion_en','descripcion_en')));
        $query->from($db->quoteName('#__ereservas_tipo_visita'));
        $query->where('('.$db->quoteName('alias') . ' LIKE ' . $db->quote($alias).') OR ('.$db->quoteName('alias_en') . ' LIKE ' . $db->quote($alias).')');
        $query->where($db->quoteName('imagen') . ' IS NOT NULL');
        $query->where($db->quoteName('state') . ' = 1');
        $query->where($db->quoteName('aforo_web') . ' > 0');

        $db->setQuery($query);

        $results = $db->loadObject();

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
}
