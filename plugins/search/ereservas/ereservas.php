<?php

/**
 * @package     Joomla.Plugin
 * @subpackage  Search.content
 *
 * @copyright   Copyright (C) 2005 - 2014 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */
defined('_JEXEC') or die;

require_once JPATH_SITE . '/components/com_ereservas/router.php';

use \Joomla\CMS\Factory;

/**
 * Content search plugin.
 *
 * @package     Joomla.Plugin
 * @subpackage  Search.content
 * @since       1.6
 */
class PlgSearchEreservas extends \Joomla\CMS\Plugin\CMSPlugin
{
	/**
	 * Determine areas searchable by this plugin.
	 *
	 * @return  array  An array of search areas.
	 *
	 * @since   1.6
	 */
	public function onContentSearchAreas()
	{
		static $areas = array(
			'ereservas' => 'Ereservas'
		);

		return $areas;
	}

	/**
	 * Search content (articles).
	 * The SQL must return the following fields that are used in a common display
	 * routine: href, title, section, created, text, browsernav.
	 *
	 * @param   string  $text      Target search string.
	 * @param   string  $phrase    Matching option (possible values: exact|any|all).  Default is "any".
	 * @param   string  $ordering  Ordering option (possible values: newest|oldest|popular|alpha|category).  Default is "newest".
	 * @param   mixed   $areas     An array if the search it to be restricted to areas or null to search all areas.
	 *
	 * @return  array  Search results.
	 *
	 * @since   1.6
	 */
	public function onContentSearch($text, $phrase = '', $ordering = '', $areas = null)
	{
		$db = Factory::getDbo();

		if (is_array($areas))
		{
			if (!array_intersect($areas, array_keys($this->onContentSearchAreas())))
			{
				return array();
			}
		}

		$limit = $this->params->def('search_limit', 50);

		$text = trim($text);

		if ($text == '')
		{
			return array();
		}

		$rows = array();

		
//Search Visitas.
if ($limit > 0) {
    switch ($phrase) {
        case 'exact':
            $text = $db->quote('%' . $db->escape($text, true) . '%', false);
            $wheres2 = array();
            $wheres2[] = 'a.nombre LIKE ' . $text;
$wheres2[] = '`ereservas_idioma`.`nombre` LIKE ' . $text;
$wheres2[] = '`ereservas_tipo_visita`.`nombre` LIKE ' . $text;
$wheres2[] = '`ereservas_guias`.`nombre` LIKE ' . $text;
            $where = '(' . implode(') OR (', $wheres2) . ')';
            break;

        case 'all':
        case 'any':
        default:
            $words = explode(' ', $text);
            $wheres = array();

            foreach ($words as $word) {
                $word = $db->quote('%' . $db->escape($word, true) . '%', false);
                $wheres2 = array();
                $wheres2[] = 'a.nombre LIKE ' . $word;
$wheres2[] = '`ereservas_idioma`.`nombre` LIKE ' . $word;
$wheres2[] = '`ereservas_tipo_visita`.`nombre` LIKE ' . $word;
$wheres2[] = '`ereservas_guias`.`nombre` LIKE ' . $word;
                $wheres[] = implode(' OR ', $wheres2);
            }

            $where = '(' . implode(($phrase == 'all' ? ') AND (' : ') OR ('), $wheres) . ')';
            break;
    }

    switch ($ordering) {
        default:
            $order = 'a.id DESC';
            break;
    }

    $query = $db->getQuery(true);

    $query
            ->clear()
            ->select(
                    array(
                        'a.id',
                        'a.nombre AS title',
                        '"" AS created',
                        'a.nombre AS text',
                        '"Visita" AS section',
                        '1 AS browsernav'
                    )
            )
            ->from('#__ereservas_visita AS a')
            ->innerJoin('`#__ereservas_idioma` AS ereservas_idioma ON ereservas_idioma.id = a.idioma')
->innerJoin('`#__ereservas_tipo_visita` AS ereservas_tipo_visita ON ereservas_tipo_visita.id = a.id_tipo_visita')
->innerJoin('`#__ereservas_guias` AS ereservas_guias ON ereservas_guias.id = a.guia')
            ->where('(' . $where . ')')
            ->group('a.id')
            ->order($order);

    $db->setQuery($query, 0, $limit);
    $list = $db->loadObjectList();
    $limit -= count($list);

    if (isset($list)) {
        foreach ($list as $key => $item) {
            $list[$key]->href = JRoute::_('index.php?option=com_ereservas&view=visita&id=' . $item->id, false, 2);
        }
    }

    $rows = array_merge($list, $rows);
}



//Search Tipo visitas.
if ($limit > 0) {
    switch ($phrase) {
        case 'exact':
            $text = $db->quote('%' . $db->escape($text, true) . '%', false);
            $wheres2 = array();
            $wheres2[] = 'a.nombre LIKE ' . $text;
            $where = '(' . implode(') OR (', $wheres2) . ')';
            break;

        case 'all':
        case 'any':
        default:
            $words = explode(' ', $text);
            $wheres = array();

            foreach ($words as $word) {
                $word = $db->quote('%' . $db->escape($word, true) . '%', false);
                $wheres2 = array();
                $wheres2[] = 'a.nombre LIKE ' . $word;
                $wheres[] = implode(' OR ', $wheres2);
            }

            $where = '(' . implode(($phrase == 'all' ? ') AND (' : ') OR ('), $wheres) . ')';
            break;
    }

    switch ($ordering) {
        default:
            $order = 'a.id DESC';
            break;
    }

    $query = $db->getQuery(true);

    $query
            ->clear()
            ->select(
                    array(
                        'a.id',
                        'a.nombre AS title',
                        '"" AS created',
                        'a.nombre AS text',
                        '"Tipo visita" AS section',
                        '1 AS browsernav'
                    )
            )
            ->from('#__ereservas_tipo_visita AS a')
            
            ->where('(' . $where . ')')
            ->group('a.id')
            ->order($order);

    $db->setQuery($query, 0, $limit);
    $list = $db->loadObjectList();
    $limit -= count($list);

    if (isset($list)) {
        foreach ($list as $key => $item) {
            $list[$key]->href = JRoute::_('index.php?option=com_ereservas&view=tipovisita&id=' . $item->id, false, 2);
        }
    }

    $rows = array_merge($list, $rows);
}



//Search Salas.
if ($limit > 0) {
    switch ($phrase) {
        case 'exact':
            $text = $db->quote('%' . $db->escape($text, true) . '%', false);
            $wheres2 = array();
            $wheres2[] = 'a.nombre LIKE ' . $text;
            $where = '(' . implode(') OR (', $wheres2) . ')';
            break;

        case 'all':
        case 'any':
        default:
            $words = explode(' ', $text);
            $wheres = array();

            foreach ($words as $word) {
                $word = $db->quote('%' . $db->escape($word, true) . '%', false);
                $wheres2 = array();
                $wheres2[] = 'a.nombre LIKE ' . $word;
                $wheres[] = implode(' OR ', $wheres2);
            }

            $where = '(' . implode(($phrase == 'all' ? ') AND (' : ') OR ('), $wheres) . ')';
            break;
    }

    switch ($ordering) {
        default:
            $order = 'a.id DESC';
            break;
    }

    $query = $db->getQuery(true);

    $query
            ->clear()
            ->select(
                    array(
                        'a.id',
                        'a.nombre AS title',
                        '"" AS created',
                        'a.nombre AS text',
                        '"Sala" AS section',
                        '1 AS browsernav'
                    )
            )
            ->from('#__ereservas_sala AS a')
            
            ->where('(' . $where . ')')
            ->group('a.id')
            ->order($order);

    $db->setQuery($query, 0, $limit);
    $list = $db->loadObjectList();
    $limit -= count($list);

    if (isset($list)) {
        foreach ($list as $key => $item) {
            $list[$key]->href = JRoute::_('index.php?option=com_ereservas&view=sala&id=' . $item->id, false, 2);
        }
    }

    $rows = array_merge($list, $rows);
}



//Search Idiomas.
if ($limit > 0) {
    switch ($phrase) {
        case 'exact':
            $text = $db->quote('%' . $db->escape($text, true) . '%', false);
            $wheres2 = array();
            $wheres2[] = 'a.nombre LIKE ' . $text;
            $where = '(' . implode(') OR (', $wheres2) . ')';
            break;

        case 'all':
        case 'any':
        default:
            $words = explode(' ', $text);
            $wheres = array();

            foreach ($words as $word) {
                $word = $db->quote('%' . $db->escape($word, true) . '%', false);
                $wheres2 = array();
                $wheres2[] = 'a.nombre LIKE ' . $word;
                $wheres[] = implode(' OR ', $wheres2);
            }

            $where = '(' . implode(($phrase == 'all' ? ') AND (' : ') OR ('), $wheres) . ')';
            break;
    }

    switch ($ordering) {
        default:
            $order = 'a.id DESC';
            break;
    }

    $query = $db->getQuery(true);

    $query
            ->clear()
            ->select(
                    array(
                        'a.id',
                        'a.nombre AS title',
                        '"" AS created',
                        'a.nombre AS text',
                        '"Idioma" AS section',
                        '1 AS browsernav'
                    )
            )
            ->from('#__ereservas_idioma AS a')
            
            ->where('(' . $where . ')')
            ->group('a.id')
            ->order($order);

    $db->setQuery($query, 0, $limit);
    $list = $db->loadObjectList();
    $limit -= count($list);

    if (isset($list)) {
        foreach ($list as $key => $item) {
            $list[$key]->href = JRoute::_('index.php?option=com_ereservas&view=idioma&id=' . $item->id, false, 2);
        }
    }

    $rows = array_merge($list, $rows);
}



//Search Pais.
if ($limit > 0) {
    switch ($phrase) {
        case 'exact':
            $text = $db->quote('%' . $db->escape($text, true) . '%', false);
            $wheres2 = array();
            $wheres2[] = 'a.nombre LIKE ' . $text;
            $where = '(' . implode(') OR (', $wheres2) . ')';
            break;

        case 'all':
        case 'any':
        default:
            $words = explode(' ', $text);
            $wheres = array();

            foreach ($words as $word) {
                $word = $db->quote('%' . $db->escape($word, true) . '%', false);
                $wheres2 = array();
                $wheres2[] = 'a.nombre LIKE ' . $word;
                $wheres[] = implode(' OR ', $wheres2);
            }

            $where = '(' . implode(($phrase == 'all' ? ') AND (' : ') OR ('), $wheres) . ')';
            break;
    }

    switch ($ordering) {
        default:
            $order = 'a.id DESC';
            break;
    }

    $query = $db->getQuery(true);

    $query
            ->clear()
            ->select(
                    array(
                        'a.id',
                        'a.nombre AS title',
                        '"" AS created',
                        'a.nombre AS text',
                        '"Pais" AS section',
                        '1 AS browsernav'
                    )
            )
            ->from('#__ereservas_pais AS a')
            
            ->where('(' . $where . ')')
            ->group('a.id')
            ->order($order);

    $db->setQuery($query, 0, $limit);
    $list = $db->loadObjectList();
    $limit -= count($list);

    if (isset($list)) {
        foreach ($list as $key => $item) {
            $list[$key]->href = JRoute::_('index.php?option=com_ereservas&view=pais&id=' . $item->id, false, 2);
        }
    }

    $rows = array_merge($list, $rows);
}



//Search Tipo clientes.
if ($limit > 0) {
    switch ($phrase) {
        case 'exact':
            $text = $db->quote('%' . $db->escape($text, true) . '%', false);
            $wheres2 = array();
            $wheres2[] = 'a.nombre LIKE ' . $text;
            $where = '(' . implode(') OR (', $wheres2) . ')';
            break;

        case 'all':
        case 'any':
        default:
            $words = explode(' ', $text);
            $wheres = array();

            foreach ($words as $word) {
                $word = $db->quote('%' . $db->escape($word, true) . '%', false);
                $wheres2 = array();
                $wheres2[] = 'a.nombre LIKE ' . $word;
                $wheres[] = implode(' OR ', $wheres2);
            }

            $where = '(' . implode(($phrase == 'all' ? ') AND (' : ') OR ('), $wheres) . ')';
            break;
    }

    switch ($ordering) {
        default:
            $order = 'a.id DESC';
            break;
    }

    $query = $db->getQuery(true);

    $query
            ->clear()
            ->select(
                    array(
                        'a.id',
                        'a.nombre AS title',
                        '"" AS created',
                        'a.nombre AS text',
                        '"Tipo cliente" AS section',
                        '1 AS browsernav'
                    )
            )
            ->from('#__ereservas_tipo_cliente AS a')
            
            ->where('(' . $where . ')')
            ->group('a.id')
            ->order($order);

    $db->setQuery($query, 0, $limit);
    $list = $db->loadObjectList();
    $limit -= count($list);

    if (isset($list)) {
        foreach ($list as $key => $item) {
            $list[$key]->href = JRoute::_('index.php?option=com_ereservas&view=tipocliente&id=' . $item->id, false, 2);
        }
    }

    $rows = array_merge($list, $rows);
}



//Search Reservas.
if ($limit > 0) {
    switch ($phrase) {
        case 'exact':
            $text = $db->quote('%' . $db->escape($text, true) . '%', false);
            $wheres2 = array();
            $wheres2[] = 'a.nombre LIKE ' . $text;
$wheres2[] = 'a.telefono LIKE ' . $text;
$wheres2[] = 'a.email LIKE ' . $text;
$wheres2[] = 'a.cpostal LIKE ' . $text;
            $where = '(' . implode(') OR (', $wheres2) . ')';
            break;

        case 'all':
        case 'any':
        default:
            $words = explode(' ', $text);
            $wheres = array();

            foreach ($words as $word) {
                $word = $db->quote('%' . $db->escape($word, true) . '%', false);
                $wheres2 = array();
                $wheres2[] = 'a.nombre LIKE ' . $word;
$wheres2[] = 'a.telefono LIKE ' . $word;
$wheres2[] = 'a.email LIKE ' . $word;
$wheres2[] = 'a.cpostal LIKE ' . $word;
                $wheres[] = implode(' OR ', $wheres2);
            }

            $where = '(' . implode(($phrase == 'all' ? ') AND (' : ') OR ('), $wheres) . ')';
            break;
    }

    switch ($ordering) {
        default:
            $order = 'a.id DESC';
            break;
    }

    $query = $db->getQuery(true);

    $query
            ->clear()
            ->select(
                    array(
                        'a.id',
                        'a.nombre AS title',
                        '"" AS created',
                        'a.nombre AS text',
                        '"Reserva" AS section',
                        '1 AS browsernav'
                    )
            )
            ->from('#__ereservas_reserva AS a')
            
            ->where('(' . $where . ')')
            ->group('a.id')
            ->order($order);

    $db->setQuery($query, 0, $limit);
    $list = $db->loadObjectList();
    $limit -= count($list);

    if (isset($list)) {
        foreach ($list as $key => $item) {
            $list[$key]->href = JRoute::_('index.php?option=com_ereservas&view=reserva&id=' . $item->id, false, 2);
        }
    }

    $rows = array_merge($list, $rows);
}

		return $rows;
	}
}
