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

use \Joomla\CMS\Factory;

/**
 * Ereservas helper.
 *
 * @since  1.6
 */
class EreservasHelper
{
	/**
	 * Configure the Linkbar.
	 *
	 * @param   string  $vName  string
	 *
	 * @return void
	 */
	public static function addSubmenu($vName = '')
	{
		JHtmlSidebar::addEntry(
			JText::_('COM_ERESERVAS_TITLE_VISITAS'),
			'index.php?option=com_ereservas&view=visitas',
			$vName == 'visitas'
		);

JHtmlSidebar::addEntry(
			JText::_('COM_ERESERVAS_TITLE_TIPOVISITAS'),
			'index.php?option=com_ereservas&view=tipovisitas',
			$vName == 'tipovisitas'
		);

JHtmlSidebar::addEntry(
			JText::_('COM_ERESERVAS_TITLE_SALAS'),
			'index.php?option=com_ereservas&view=salas',
			$vName == 'salas'
		);

JHtmlSidebar::addEntry(
			JText::_('COM_ERESERVAS_TITLE_IDIOMAS'),
			'index.php?option=com_ereservas&view=idiomas',
			$vName == 'idiomas'
		);

JHtmlSidebar::addEntry(
			JText::_('COM_ERESERVAS_TITLE_PAISS'),
			'index.php?option=com_ereservas&view=paiss',
			$vName == 'paiss'
		);

JHtmlSidebar::addEntry(
			JText::_('COM_ERESERVAS_TITLE_TIPOCLIENTES'),
			'index.php?option=com_ereservas&view=tipoclientes',
			$vName == 'tipoclientes'
		);

JHtmlSidebar::addEntry(
			JText::_('COM_ERESERVAS_TITLE_RESERVAS'),
			'index.php?option=com_ereservas&view=reservas',
			$vName == 'reservas'
		);

JHtmlSidebar::addEntry(
			JText::_('COM_ERESERVAS_TITLE_GUIAS'),
			'index.php?option=com_ereservas&view=guias',
			$vName == 'guias'
		);

JHtmlSidebar::addEntry(
			JText::_('COM_ERESERVAS_TITLE_CALENDARIOS'),
			'index.php?option=com_ereservas&view=calendarios',
			$vName == 'calendarios'
		);

JHtmlSidebar::addEntry(
			JText::_('COM_ERESERVAS_TITLE_FACTURAS'),
			'index.php?option=com_ereservas&view=facturas',
			$vName == 'facturas'
		);

	}

	/**
	 * Gets the files attached to an item
	 *
	 * @param   int     $pk     The item's id
	 *
	 * @param   string  $table  The table's name
	 *
	 * @param   string  $field  The field's name
	 *
	 * @return  array  The files
	 */
	public static function getFiles($pk, $table, $field)
	{
		$db = Factory::getDbo();
		$query = $db->getQuery(true);

		$query
			->select($field)
			->from($table)
			->where('id = ' . (int) $pk);

		$db->setQuery($query);

		return explode(',', $db->loadResult());
	}

	/**
	 * Gets a list of the actions that can be performed.
	 *
	 * @return    JObject
	 *
	 * @since    1.6
	 */
	public static function getActions()
	{
		$user   = Factory::getUser();
		$result = new JObject;

		$assetName = 'com_ereservas';

		$actions = array(
			'core.admin', 'core.manage', 'core.create', 'core.edit', 'core.edit.own', 'core.edit.state', 'core.delete'
		);

		foreach ($actions as $action)
		{
			$result->set($action, $user->authorise($action, $assetName));
		}

		return $result;
	}
}

