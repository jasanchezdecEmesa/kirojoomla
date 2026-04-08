<?php
/**
 * @package    Módulo calendario
 *
 * @author     Emesa Software webmaster@emesa.com
 * @copyright  [COPYRIGHT]
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 * @link       https://emesasoftware.es
 */

use Joomla\CMS\Helper\ModuleHelper;
use JFactory;

defined('_JEXEC') or die;

$moduleclass_sfx = htmlspecialchars($params->get('moduleclass_sfx'));

require ModuleHelper::getLayoutPath('mod_calendario', $params->get('layout', 'default'));

class ModCalendarioHelper{
	public function traerPaises(){
		$db= JFactory::getDbo();
		$query = $db->getQuery(true);
		$query->select('*');
		$query->from($db->quoteName('#__ereservas_pais'));
		$db->setQuery($query);
		$paises = $db->loadAssocList();
    	return $paises;
	}
}
    