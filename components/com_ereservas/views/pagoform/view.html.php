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

use Joomla\CMS\Factory;
use \Joomla\CMS\Language\Text;

jimport('joomla.application.component.view');
JLoader::registerPrefix('Ereservas', JPATH_LIBRARIES . '/ereservas');
/**
 * View to edit
 *
 * @since  1.6
 */
class EreservasViewPagoForm extends \Joomla\CMS\MVC\View\HtmlView
{
	protected $state;

	protected $item;

	protected $form;

	protected $params;

	protected $canSave;

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
		$jinput = JFactory::getApplication()->input;

		$this->state   = $this->get('State');
		$this->item    = $this->get('Item');
		$this->params  = $app->getParams('com_ereservas');
		$this->canSave = $this->get('CanSave');
		$this->form		= $this->get('Form');
		$this->id_reserva = @$app->getMenu()->getActive()->query['id_reserva'];

		// Check for errors.
		if (count($errors = $this->get('Errors')))
		{
			throw new Exception(implode("\n", $errors));
		}

		//comprobamos si pasamos por parametro la variable 'redsys'
		if($jinput->get('redsys',false,'alnum') == 'true'){

		    //en caso afirmativo, cargamos la plantilla redsys con los parametros idreserva e idfactura
			$tpl = 'redsys';
			$reserva=  $jinput->get('idreserva',false,'int');
			$factura = $jinput->get('idfactura',false,'int');
            $idioma_web = $jinput->get('idioma_web',1,'int');

           $idvisita = EreservasRedsys::obtenerVisita($reserva);
            EreservasRecalculo::recalcularaforo($idvisita);
            $aforovisita= $this->getAforoVisita($idvisita);

            $tpl=($aforovisita->aforo_ocupado > $aforovisita->aforo_web)?"redsyslleno":"redsys";

			//Si la longitud de idfactura es menos de 5 caracteres, añadimos 0 hasta completar
            //y llamamos a la funcion pago pasandole los idfactura y la idreserva
			$longitud= strlen($factura);
			if($longitud <= 5) {
				$numerosrelleno = 5;
				$facturallena = str_pad($factura,$numerosrelleno,'0' ,STR_PAD_LEFT);
				$this->redsys = $this->pago($facturallena, $reserva, $idioma_web);
			}else {
				$this->redsys = $this->pago($reserva, $reserva, $idioma_web);
			}
		}

		//Sino, si la variable que nos llega desde la url coincide con reservaidcorreo:
		elseif($jinput->get('reservaidcorreo',false,'int')){

			$idreserva=$jinput->get('reservaidcorreo',false,'int');
			$token=$jinput->get('token',false,'alnum');
			$this->reserva_get= $idreserva;

			//traemos los datos de la reserva
			$this->consulta_reserva = $this->traerreserva($idreserva);

			//Calculamos el total de la reserva, llamandoa a la funcion pago total con el idreserva como parametro
			$this->total = $this->pagototal($idreserva);

			
			//comprobar si ya existe un registro con este id, significara que ha intentado hacer un pago, ya sea ok o ko, ha entrado a redsysok
			$this->existe= $this->comprobarReservaFactura($idreserva);
			if($this->existe >= 1){
				$app->enqueueMessage("Ya existe un proceso de pago para esta reserva, póngase en contacto con nosotros", 'error');
				$tpl = 'errorpago';
			}


			//por ultimo, comprobamos que el token es el correcto, en caso negativo, lanzamos un mensaje de error y reedirigimos
			$this->token_consulta = $this->comprobartoken($idreserva);
			if($this->token_consulta->token !== $token){
				$app->enqueueMessage('Token no valido', 'error');
				$app->redirect(JRoute::_('index.php?option=com_ereservas&view=pagoform'));
			}
		}
		//Aqui miramos si la variable que nos llega desde la URL contiene pago, y dependiendo si es true o false, cargamos una plantilla u otra
		// En caso afirmativo, traer los datos de la reserva. mostrarlos antes del formulario.
		elseif($jinput->get('pago',false,'html') == 'true'){
		    $tpl ='redsysok';
		}
		elseif($jinput->get('pago',false,'html')=='false'){
			$tpl ='redsysko';
		}

		//si nos llega el id reserva vacio, lanzamos la plantilla de error
		elseif($this->id_reserva == "") {
			$tpl = 'error';
		}

		//en caso de que no tengamos ningun parametro coincidente desde la url, llamamos a las funciones de traer reserva y el pago total
		else{
			$this->consulta_reserva = $this->traerreserva($this->id_reserva);
			$this->total = $this->pagototal($this->id_reserva);
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
     * Funcion que nos trae todos los datos de la reserva
     * @param $idreserva
     * @return mixed
     */
	private function traerreserva($idreserva){
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);
		$query->select(array('a.nombre, a.email, a.telefono','a.cpostal ','c.nombre as visita' ,'b.precio', 'b.fecha','b.hora_inicio', 'a.personas','a.municipio'));
		$query->from($db->quoteName('#__ereservas_reserva','a'));
		$query->join('INNER', $db->quoteName('#__ereservas_visita', 'b') . ' ON (' . $db->quoteName('a.id_visita') . ' = ' . $db->quoteName('b.id') .')');
		$query->join('INNER', $db->quoteName('#__ereservas_tipo_visita', 'c') . ' ON (' . $db->quoteName('b.id_tipo_visita') . ' = ' . $db->quoteName('c.id') .')');
		$query->where($db->quoteName('a.id') . ' LIKE '. $db->quote($idreserva));
		$db->setQuery($query);

		$results = $db->loadObjectList();


		return $results;
	}

    /**
     * Funcion la cual llamamos a la libreria para preparar el formulario de pago
     * @param $idreserva
     * @param $factura -> todos los datos de la factura, para luego en la libreria usar dichos datos
     * @return string
     */
	private function pago($idreserva, $factura, $idioma_web){
	    //primero calculamos el precio total de la reserva/factura
		$precio = $this->pagototal($factura);
		$formredsys = EreservasRedsys::pago($idreserva,$precio, $factura, $idioma_web);
		return $formredsys;
	}

    /**
     * Calculamos el precio total de las personas que estan dentro de la reserva
     * @param $idreserva
     * @return mixed
     */
	private function pagototal($idreserva){
		$db2 = JFactory::getDbo();
		$query2 = $db2->getQuery(true);
		//$query2->select('a.personas*b.precio as total');
        //El precio puede ir en la reserva modificado en el backend, así que es mejor no calcularlo de nuevo
        $query2->select('a.precio as total');
		$query2->from($db2->quoteName('#__ereservas_reserva','a'));
		$query2->join('INNER', $db2->quoteName('#__ereservas_visita', 'b') . ' ON (' . $db2->quoteName('a.id_visita') . ' = ' . $db2->quoteName('b.id') .')');
		$query2->where($db2->quoteName('a.id') . ' LIKE '. $db2->quote($idreserva));
		$db2->setQuery($query2);
		$total = $db2->loadObjectList();
		return $total;
	}

    /**
     * Funcion en la cual comprobamos que el token que nos ha llegado coincide con el que se ha guardado en la base de datos
     * @param $reserva
     * @return mixed
     */
	private function comprobartoken($reserva){
		$db3 = JFactory::getDbo();
		$query3 = $db3->getQuery(true);
		$query3->select($db3->quoteName(array('token','estado','pagado')));
		$query3->from($db3->quoteName('#__ereservas_reserva','a'));
		$query3->where($db3->quoteName('id') . ' LIKE '. $db3->quote($reserva));
		$db3->setQuery($query3);
		$token_consulta = $db3->loadObject();
		return $token_consulta;
	}

	protected function getAforoVisita($id){
        $db4 = JFactory::getDbo();
        $query3 = $db4->getQuery(true);
        $query3->select($db4->quoteName(array('aforo_ocupado', 'aforo_web')));
        $query3->from($db4->quoteName('#__ereservas_visita'));
        $query3->where($db4->quoteName('id') . ' LIKE '. $db4->quote($id));
        $db4->setQuery($query3);
        $aforos = $db4->loadObject();
        return $aforos;
    }

	protected function comprobarReservaFactura($idreserva){
		$db4 = JFactory::getDbo();
        $query3 = $db4->getQuery(true);
        $query3->select($db4->quoteName(array('id_reserva', 'id')));
        $query3->from($db4->quoteName('#__ereservas_factura'));
        $query3->where($db4->quoteName('id_reserva') . ' LIKE '. $db4->quote($idreserva));
        $db4->setQuery($query3);
		$db4->execute();
		$num_rows = $db4->getNumRows();
		return $num_rows;
		$result = $db4->loadRowList();
	}



}
