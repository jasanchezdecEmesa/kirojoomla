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
class EreservasViewInformeReservaProvincia extends \Joomla\CMS\MVC\View\HtmlView
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
		$this->items = $this->get('Items');
		$this->pagination = $this->get('Pagination');
		$this->params = $app->getParams('com_ereservas');
		$this->filterForm = $this->get('FilterForm');
		$this->activeFilters = $this->get('ActiveFilters');

        //Aqui obtenemos los valores del "formulario" de la parte superior de esta pagina
        $fechadesde = $input->get('fecha-desde');
        $fechahasta = $input->get('fecha-hasta');
        //Tambien obtenemos el valor del boton para asegurarnos de que se ha pulsado
        $boton      = $input->get('enviar-fechas');

        //Si no se ha hecho el formulario correctamente (no se ha rellenado alguna fecha / no se ha pulsado el boton), llamamos a la vista normal,
        //pero la variable se debe de llamar igual para el futuro manejo de datos en la vista
        if(isset($fechadesde) and isset($fechahasta) and $boton === "Enviar"){
            $fechadesde.= ' 00:00:00';
            ($fechahasta != '')?$fechahasta.=' 00:00:00':$fechahasta;
            $this->paisesagrupados = $this->AgruparPaisesFecha($fechadesde, $fechahasta);
        }else{
            $this->paisesagrupados= $this->agruparpaises();
        }

        if(@$_POST["descargar"] == 'Descargar'){
            if(JFactory::getApplication()->input->get('descargar')) $this->PrepararInforme($this->paisesagrupados);

            $ruta = $base.'exportaciones/'.$this->archivo_generado.'.xlsx';
            $archivo = $ruta;
            header("Location:".@$archivo."");
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


	/**
     * Funcion para sacar el informe, sacamos el nombre del pais, la suma de las personas ya agrupadas y el codigo postal solo el de españa (id=1)
     * @return $result -> Es el resultado de la consulta
     */

    protected function agruparpaises(){
        $db = JFactory::getDbo();
        $query = $db->getQuery(true);

        $query->select(array('b.nombre', 'SUM(a.personas) as personas', 'if(a.id_pais = 1, a.cpostal, "") as cpostal'));
        $query->from($db->quoteName('#__ereservas_reserva', 'a'));
        $query->join('INNER', $db->quoteName('#__ereservas_pais', 'b') . ' ON ' . $db->quoteName('a.id_pais') . ' = ' . $db->quoteName('b.id'));
        $query->where($db->quoteName('estado') . ' LIKE ' . $db->quote('confirmado'));
        $query->group($db->quoteName('b.nombre'));
        $query->group($db->quoteName('a.cpostal'));
        $query->group($db->quoteName('a.id_pais'));
        $db->setQuery($query);

        $result = $db->loadAssocList();
        return $result;
    }

    /**
     * Funcion para sacar el informe, sacamos el nombre del pais, la suma de las personas ya agrupadas y el codigo postal solo el de españa (id=1) segun las fechas que nos pasen
     * @param $fechadesde -> fecha (datetime) inicio
     * @param $fechahasta -> fecha (datetime) fin
     * @return $resultado -> resultado de la consulta
     */
    protected function AgruparPaisesFecha($fechadesde, $fechahasta){
        $db = JFactory::getDbo();
        $query = $db->getQuery(true);

        $query->select(array('b.nombre', 'SUM(a.personas) as personas', 'if(a.id_pais = 1, a.cpostal, "") as cpostal'));
        $query->from($db->quoteName('#__ereservas_reserva', 'a'));
        $query->join('INNER', $db->quoteName('#__ereservas_pais', 'b') . ' ON ' . $db->quoteName('a.id_pais') . ' = ' . $db->quoteName('b.id'));
        $query->join('INNER', $db->quoteName('#__ereservas_visita', 'c') . ' ON ' . $db->quoteName('a.id_visita') . ' = ' . $db->quoteName('c.id'));
        $query->where($db->quoteName('estado') . ' LIKE ' . $db->quote('confirmado'));

        //Aqui tenemos que calcular las fechas que nos han marcado en el formulario
        //si nos han llegado vacia la fecha hasta, cogeremos desde esa fecha en adelante
        if($fechahasta === ''){
            $query->where($db->quoteName('c.fecha') . ' >= ' . $db->quote($fechadesde));
        }
        //sino tendremos que sacar todas las que hay de por medio
        else {
            $query->where($db->quoteName('c.fecha') . ' BETWEEN ' . $db->quote($fechadesde) . ' AND ' . $db->quote($fechahasta));
        }
        $query->order($db->quoteName('b.nombre') . ' ASC');
        $db->setQuery($query);
        $resultado = $db->loadAssocList();
        return $resultado;
    }

    protected function PrepararInforme($datos){
        foreach ( $datos as $k=>$v )
        {
            $datos[$k] ['Pais'] = $datos[$k] ['nombre'];
            $datos[$k] ['Total'] = $datos[$k] ['personas'];
            $datos[$k] ['Código Postal'] = $datos[$k] ['cpostal'];
            unset($datos[$k]['nombre']);
            unset($datos[$k]['personas']);
            unset($datos[$k]['cpostal']);
        }

        $this->archivo_generado = EreservasExportacionexcel::GenerarInforme($datos, 'Provincias');


    }

}
