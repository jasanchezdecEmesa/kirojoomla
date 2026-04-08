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
JLoader::registerPrefix('Ereservas', JPATH_LIBRARIES . '/ereservas');

/**
 * View to edit
 *
 * @since  1.6
 */
class EreservasViewConfirmarReserva extends JViewLegacy
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
		$app  = JFactory::getApplication();
		$user = JFactory::getUser();

		$this->state  = $this->get('State');
		$this->item   = $this->get('Item');
		$this->params = $app->getParams('com_ereservas');

		// Check for errors.
		if (count($errors = $this->get('Errors')))
		{
			throw new Exception(implode("\n", $errors));
		}

		$jinput = JFactory::getApplication()->input;
		$token = $jinput->get('reserva', false, 'almun');
		$id = $jinput->get('codigo', false, 'int');

		$this->exito = $this->confirmarReserva($token,$id);

		if($this->exito > 0) {
			EreservasCorreos::enviarMail($id,'confirmada-por-cliente');
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
		// We need to get it from the menu item itself
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
     * Funcion para poner la reserva en confirmado
     * @param $token -> Token que nos han mandado y deberemos comprobar ne la base de datos
     * @param $id -> id de la reserva
     * @return int -> numero de lineas afectadas, es mayor que 0 es que hemos modificado alguna
     */
	protected function confirmarReserva($token,$id) {

		$db = JFactory::getDbo();

		$query = $db->getQuery(true);

		$fields = array(
			$db->quoteName('estado') . ' = ' . $db->quote('confirmado')
		);

		$conditions = array(
			$db->quoteName('token') . ' = ' .$db->quote($token),
			$db->quoteName('id') . ' = ' .$db->quote($id)
		);

		$query->update($db->quoteName('#__ereservas_reserva'))->set($fields)->where($conditions);

		$db->setQuery($query);

		$result = $db->execute();

		return $db->getAffectedRows();

	}
}
