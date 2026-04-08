<?php
/* ======================================================
# Cookies Policy Notification Bar for Joomla! - v4.3.5 (pro version)
# -------------------------------------------------------
# For Joomla! CMS (v3.x)
# Author: Web357 (Yiannis Christodoulou)
# Copyright (©) 2014-2024 Web357. All rights reserved.
# License: GNU/GPLv3, http://www.gnu.org/licenses/gpl-3.0.html
# Website: https:/www.web357.com
# Demo: https://demo.web357.com/joomla/browse/cookies-policy-notification-bar
# Support: support@web357.com
# Last modified: Monday 27 May 2024, 01:57:48 PM
========================================================= */
// No direct access
defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Layout\LayoutHelper;
use Joomla\CMS\Router\Route;
use Joomla\CMS\User\UserFactoryInterface;

HTMLHelper::addIncludePath(JPATH_COMPONENT . '/helpers/');
HTMLHelper::_('bootstrap.tooltip');
HTMLHelper::_('behavior.multiselect');

if (version_compare(JVERSION, '4.0', 'lt')) {
    HTMLHelper::_('formbehavior.chosen', 'select');
}

$user = (version_compare(JVERSION, "4.0", ">=")) ? Factory::getApplication()->getIdentity() : Factory::getUser();
$userId = $user->get('id');
$listOrder = $this->state->get('list.ordering');
$listDirn = $this->state->get('list.direction');

$sortFields = $this->getSortFields();
?>

<?php
if ($this->plugin_params && !$this->plugin_params->get('store_acceptance_logs_into_db', '1')): ?>
	<div class="alert alert-danger" role="alert">
		<?php echo Text::_('COM_COOKIESPOLICYNOTIFICATIONBAR_STORE_LOGS_IS_DISABLE'); ?>
	</div>
<?php endif;?>

<script type="text/javascript">
Joomla.submitbutton = function(task)
{
	if (task == 'logs.deleteAllLogs')
	{
		var confirmTask = confirm("<?php echo Text::_('COM_COOKIESPOLICYNOTIFICATIONBAR_DELETE_ALL_CONFIRM_ALERT_MSG'); ?>")
		if (confirmTask)
		{
			Joomla.submitform(task);
		}
		else
		{
			return false;
		}
	}
	else
	{
		Joomla.submitform(task);
	}
}
</script>

<form action="<?php echo Route::_('index.php?option=com_cookiespolicynotificationbar&view=logs'); ?>" method="post" name="adminForm" id="adminForm">
	<?php if (!empty($this->sidebar)): ?>
	<div id="j-sidebar-container" class="span2">
		<?php echo $this->sidebar; ?>
	</div>
	<div id="j-main-container" class="span10">
		<?php else: ?>
		<div id="j-main-container">
			<?php endif;?>

			<?php echo LayoutHelper::render('joomla.searchtools.default', array('view' => $this)); ?>

			<div class="clearfix"></div>
			<table class="table table-striped" id="logsList">
				<thead>
					<tr>
						<th width="1%">
							<input type="checkbox" name="checkall-toggle" value=""
								title="<?php echo Text::_('JGLOBAL_CHECK_ALL'); ?>" onclick="Joomla.checkAll(this)" />
						</th>
						<th class='left'>
							<?php echo HTMLHelper::_('searchtools.sort', 'JGLOBAL_FIELD_ID_LABEL', 'a.`id`', $listDirn, $listOrder); ?>
						</th>
						<th class='left'>
							<?php echo HTMLHelper::_('searchtools.sort', Text::_('COM_COOKIESPOLICYNOTIFICATIONBAR_STATUS'), 'a.`status`', $listDirn, $listOrder); ?>
						</th>
						<th class='left'>
							<?php echo HTMLHelper::_('searchtools.sort', Text::_('COM_COOKIESPOLICYNOTIFICATIONBAR_DATETIME'), 'a.`datetime`', $listDirn, $listOrder); ?>
						</th>
						<th class='left'>
							<?php echo Text::_('COM_COOKIESPOLICYNOTIFICATIONBAR_USER'); ?>
						</th>
						<th class='left'>
							<?php echo Text::_('COM_COOKIESPOLICYNOTIFICATIONBAR_IP_ADDRESS'); ?>
						</th>
						<th class='left'>
							<?php echo Text::_('COM_COOKIESPOLICYNOTIFICATIONBAR_COOKIES_INFO'); ?>
						</th>
					</tr>
				</thead>
				<tfoot>
					<tr>
						<td
							colspan="7">
							<?php echo $this->pagination->getListFooter(); ?>
						</td>
					</tr>
				</tfoot>
				<tbody>
					<?php
$cookies_info = [];
foreach ($this->items as $i => $item): $cookies_info = json_decode($item->cookiesinfo, true);?>
						<tr class="row<?php echo $i % 2; ?>">

							<td>
								<?php echo HTMLHelper::_('grid.id', $i, $item->id); ?>
							</td>

							<td>
								<?php echo $this->escape($item->id); ?>
							</td>

							<td>
								<?php
								if (is_countable($cookies_info) && count($cookies_info) === array_sum($cookies_info)): ?>

									<?php if ($item->status === 'declined'): ?>
										<span class="label label-important badge bg-danger text-white">
											<?php echo Text::_('COM_COOKIESPOLICYNOTIFICATIONBAR_DECLINED'); ?>
										</span>
									<?php else: ?>
										<span class="label label-success badge bg-success text-white">
											<?php echo Text::_('COM_COOKIESPOLICYNOTIFICATIONBAR_ACCEPTED'); ?>
										</span>
									<?php endif; ?>
									
								<?php elseif (is_countable($cookies_info) && array_sum($cookies_info) === 0): ?>
									<span class="label label-important badge bg-danger text-white">
										<?php echo Text::_('COM_COOKIESPOLICYNOTIFICATIONBAR_DECLINED'); ?>
									</span>
							<?php elseif (is_countable($cookies_info) && count($cookies_info) > array_sum($cookies_info)): ?>
								<span class="label label-warning badge bg-warning text-white">
									<?php echo Text::_('COM_COOKIESPOLICYNOTIFICATIONBAR_PARTIAL_CONCENT'); ?>
								</span>
							<?php else: ?>
								<span class="label label-success badge bg-success text-white">
									<?php echo Text::_('COM_COOKIESPOLICYNOTIFICATIONBAR_ACCEPTED'); ?>
								</span>
							<?php endif;?>
						</td>
						<td>
							<?php echo HTMLHelper::_("date", $item->datetime, "Y-m-d H:i:s"); ?>
						</td>
						<td>
							<?php if ($item->user_id == 0): ?>
								<?php echo Text::_('COM_COOKIESPOLICYNOTIFICATIONBAR_GUEST'); ?>
							<?php elseif ($user = Factory::getContainer()->get(UserFactoryInterface::class)->loadUserById($item->user_id)): ?>
								<a href="index.php?option=com_users&task=user.edit&id=<?php echo $user->id; ?>">
									<?php echo $user->username; ?>
								</a>
							<?php endif;?>
						</td>
						<td>
							<?php
							$geoip = $this->getDataFromGeoIP($item->ip_address);
							?>
							<span style="margin-right:5px;">
								<?php echo $this->escape($geoip['country_name']); ?>
							</span>
							<?php echo $item->ip_address; ?>
						</td>
						<td>
							<?php
$cookies_consent_html = [];
if (!empty($cookies_info)) {
    foreach ($cookies_info as $cookie_category_name => $cookie_category_decicion) {
        $cookies_consent_html[] = $cookie_category_name . ': ' . ($cookie_category_decicion || $cookie_category_name === 'required-cookies' ? '<span class="icon-publish"></span>' : '<span class="icon-cancel-2" style="color:red;"></span>');
    }
    echo implode('&nbsp;&nbsp;&nbsp;', $cookies_consent_html);
} else {
	if ($item->status === 'declined')
	{
    	echo '<span class="icon-cancel-2" style="color:red;"></span> ' . Text::_('COM_COOKIESPOLICYNOTIFICATIONBAR_USER_DECLINED_THE_COOKIES_POLICY');
	}
	else
	{
    	echo '<span class="icon-publish"></span> ' . Text::_('COM_COOKIESPOLICYNOTIFICATIONBAR_USER_ACCEPTED_THE_COOKIES_POLICY');
	}
}
?>
						</td>
					</tr>
					<?php endforeach;?>
				</tbody>
			</table>

			<input type="hidden" name="task" value="" />
			<input type="hidden" name="boxchecked" value="0" />
			<input type="hidden" name="list[fullorder]" value="<?php echo $listOrder; ?> <?php echo $listDirn; ?>" />
			<?php echo HTMLHelper::_('form.token'); ?>

			<?php echo Web357Framework\Functions::showFooter("com_cookiespolicynotificationbar", Text::_('COM_COOKIESPOLICYNOTIFICATIONBAR_CLEAN')); ?>

		</div>
</form>
<script>
	window.toggleField = function (id, task, field) {

		var f = document.adminForm,
			i = 0,
			cbx, cb = f[id];

		if (!cb) return false;

		while (true) {
			cbx = f['cb' + i];

			if (!cbx) break;

			cbx.checked = false;
			i++;
		}

		var inputField = document.createElement('input');

		inputField.type = 'hidden';
		inputField.name = 'field';
		inputField.value = field;
		f.appendChild(inputField);

		cb.checked = true;
		f.boxchecked.value = 1;
		Joomla.submitform(task);

		return false;
	};
</script>