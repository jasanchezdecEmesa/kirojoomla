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
JLoader::registerPrefix('Ereservas', JPATH_LIBRARIES . '/ereservas');
/**
 * View class for a list of Ereservas.
 *
 * @since  1.6
 */
class EreservasViewExpertis extends \Joomla\CMS\MVC\View\HtmlView
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
		$app = Factory::getApplication();
        $input = $app->input;
        $base = JURI::root();
		$this->state = $this->get('State');
		//$this->items = $this->get('Items');
		//$this->pagination = $this->get('Pagination');
        $this->items = [];
		$this->params = $app->getParams('com_ereservas');
		$this->filterForm = $this->get('FilterForm');
		$this->activeFilters = $this->get('ActiveFilters');

        //Aqui obtenemos los valores del "formulario" de la parte superior de esta pagina
        $this->fechadesde = $input->get('fecha-desde',false);
        $this->fechahasta = $input->get('fecha-hasta', false);


        //Si no se ha hecho el formulario correctamente (no se ha rellenado alguna fecha / no se ha pulsado el boton), llamamos a la vista normal,
        //pero la variable se debe de llamar igual para el futuro manejo de datos en la vista
        if($this->fechadesde ||$this->fechahasta){

            if(($this->fechadesde))$this->fechadesde.= ' 00:00:00';
            if(($this->fechahasta))$this->fechahasta.= ' 23:59:59';

            $this->selectExpertis = $this->SelectExpertis($this->fechadesde, $this->fechahasta);
        }else{
            //$this->selectExpertis= $this->SelectExpertis();
            $this->selectExpertis = array();
        }



        if(JFactory::getApplication()->input->get('descargar')){

            if(isset($this->selectExpertis)){
                $this->PrepararInforme($this->selectExpertis);

                $ruta = $base.'exportaciones/'.$this->archivo_generado.'.xlsx';
                $archivo = $ruta;

                header("Location:".@$archivo."");
            }

        }


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
		$app   = Factory::getApplication();
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

	protected function SelectExpertis($fechadesde=null, $fechahasta=null){

        $db = JFactory::getDbo();
        $query = $db->getQuery(true);

        $query->select(array('a.id', 'a.fecha_creacion', 'a.nombre', 'a.pagado','b.id as id_factura', 'b.nif as factura_nif', 'b.direccion as factura_direccion','c.fecha as fecha_visita', 'c.hora_inicio as hora_visita'));
        $query->from($db->quoteName('#__ereservas_reserva', 'a'));
        $query->join('INNER', $db->quoteName('#__ereservas_factura', 'b') . ' ON ' . $db->quoteName('b.id_reserva') . ' = ' . $db->quoteName('a.id'));
        $query->join('LEFT', $db->quoteName('#__ereservas_visita', 'c') . ' ON ' . $db->quoteName('c.id') . ' = ' . $db->quoteName('a.id_visita'));
        $query->where($db->quoteName('a.estado') . ' LIKE ' . $db->quote('confirmado'));
        $query->where($db->quoteName('b.state') . ' = ' . $db->quote('1'));

        //Aqui tenemos que calcular las fechas que nos han marcado en el formulario
        //si nos han llegado vacia la fecha hasta, cogeremos desde esa fecha en adelante
        if($fechadesde){
            $query->where($db->quoteName('a.fecha_creacion') . ' >= ' . $db->quote($fechadesde));
        }
        if($fechahasta){
            $query->where($db->quoteName('a.fecha_creacion') . ' <= ' . $db->quote($fechahasta));
        }

        $query->order($db->quoteName('b.id') . ' ASC ');

        $db->setQuery($query);

        $resultado = $db->loadAssocList();
        return $resultado;

    }


    protected function PrepararInforme($datos){

        $datos = json_decode( json_encode( $datos ), true );

        $this->archivo_generado = EreservasExportacionexcel::GenerarInforme($datos, 'Expertis');


    }


}

