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

JLoader::registerPrefix('Ereservas', JPATH_SITE . '/components/com_ereservas/');

/**
 * Class EreservasRouter
 *
 * @since  3.3
 */
class EreservasRouter extends \Joomla\CMS\Component\Router\RouterBase
{
	/**
	 * Build method for URLs
	 * This method is meant to transform the query parameters into a more human
	 * readable form. It is only executed when SEF mode is switched on.
	 *
	 * @param   array  &$query  An array of URL arguments
	 *
	 * @return  array  The URL arguments to use to assemble the subsequent URL.
	 *
	 * @since   3.3
	 */
	public function build(&$query)
	{
		$segments = array();
		$view     = null;

    	if (isset($query['task']))
		{
			$taskParts  = explode('.', $query['task']);
			$segments[] = implode('/', $taskParts);
			$view       = $taskParts[0];
			unset($query['task']);
		}

		if (isset($query['view']))
		{
			$segments[] = $query['view'];
			$view = $query['view'];
			
			unset($query['view']);
		}

		if (isset($query['id']))
		{
			if ($view !== null)
			{
				$segments[] = $query['id'];
			}
			else
			{
				$segments[] = $query['id'];
			}

			unset($query['id']);
		}

		return $segments;
	}

	/**
	 * Parse method for URLs
	 * This method is meant to transform the human readable URL back into
	 * query parameters. It is only executed when SEF mode is switched on.
	 *
	 * @param   array  &$segments  The segments of the URL to parse.
	 *
	 * @return  array  The URL attributes to be used by the application.
	 *
	 * @since   3.3
	 */
	public function parse(&$segments)
	{
		$vars = array();

		//EMESA Si es la pantalla de pago,obtenemos el ID Reserva de la URL.

		$app = JFactory::getApplication();
		$menu = $app->getMenu();
		$active = $menu->getActive();

		if(@$active->query['view'] == 'pagoform') {

			$active->query['id_reserva'] = array_shift($segments);
			$vars['view'] = $active->query['view'];

		} else if(@$active->query['view'] == 'visitasPublicas') {

		    $active->query['alias'] = array_shift($segments);
            $vars['view'] = 'visita';

        } else {
			// View is always the first element of the array
			$vars['view'] = array_shift($segments);

			while (!empty($segments))
			{
				$segment = array_pop($segments);

				// If it's the ID, let's put on the request
				if (is_numeric($segment))
				{
					$vars['id'] = $segment;
				}
				else
				{
					$vars['task'] = $vars['view'] . '.' . $segment;
				}
			}
		}

		$model = EreservasHelpersEreservas::getModel($vars['view']);



		return $vars;
	}
}
