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
namespace Joomla\Plugin\Task\Deletecpnblogs\Extension;

use Joomla\CMS\Factory;
use Joomla\CMS\Plugin\CMSPlugin;
use Joomla\Component\Scheduler\Administrator\Event\ExecuteTaskEvent;
use Joomla\Component\Scheduler\Administrator\Task\Status;
use Joomla\Component\Scheduler\Administrator\Traits\TaskPluginTrait;
use Joomla\Database\DatabaseAwareTrait;
use Joomla\Event\SubscriberInterface;
use Joomla\Event\DispatcherInterface;

\defined('_JEXEC') or die;

/**
 * A task plugin. For Delete Cookies Policy Notification Bar Logs after x days
 * {@see ExecuteTaskEvent}.
 *
     * @since 5.1.0
 */
final class Deletecpnblogs extends CMSPlugin implements SubscriberInterface
{
    use TaskPluginTrait;
	use DatabaseAwareTrait;

    /**
     * @var string[]
     *
     * @since 5.1.0
     */
    protected const TASKS_MAP = [
        'Deletecpnblogs.deletecpnblogs' => [
            'langConstPrefix' => 'PLG_TASK_DELETECPNBLOGS_DELETECPNBLOGS',
            'method'          => 'deleteCpnbLogs',
            'form'            => 'deletecpnblogs',
        ],
    ];

    /**
     * @var boolean
     * @since 5.0.0
     */
    protected $autoloadLanguage = true;

    /**
     * Constructor.
     *
     * @param   DispatcherInterface  $dispatcher     The dispatcher
     * @param   array                $config         An optional associative array of configuration settings
     * @param   string               $rootDirectory  The root directory to look for images
     *
     * @since 5.1.0
     */
    public function __construct(DispatcherInterface $dispatcher, array $config)
    {
        parent::__construct($dispatcher, $config);
    }

     /**
     * @inheritDoc
     *
     * @return string[]
     *
     * @since 5.1.0
     */
    public static function getSubscribedEvents(): array
    {
        return [
            'onTaskOptionsList'    => 'advertiseRoutines',
            'onExecuteTask'        => 'standardRoutineHandler',
            'onContentPrepareForm' => 'enhanceTaskItemForm',
        ];
    }

    /**
     * @param   ExecuteTaskEvent  $event  The `onExecuteTask` event.
     *
     * @return integer  The routine exit code.
     *
     * @since 5.1.0
     * @throws \Exception
     */
    private function deleteCpnbLogs(ExecuteTaskEvent $event): int
    {
        $daysToDeleteAfter = (int) $event->getArgument('params')->cpnbLogDeletePeriod ?? 0;
        $this->logTask(sprintf('Delete Logs after %d days', $daysToDeleteAfter));
        $now               = Factory::getDate()->toSql();
        $db                = $this->getDatabase();
        $query             = $db->getQuery(true);

        if ($daysToDeleteAfter > 0) {
            $days = -1 * $daysToDeleteAfter;

            $query->clear()
                ->delete($db->quoteName('#__plg_system_cookiespolicynotificationbar_logs'))
                ->where($db->quoteName('datetime') . ' < ' . $query->dateAdd($db->quote($now), $days, 'DAY'));

            $db->setQuery($query);

            try {
                $db->execute();
                $deletedRows = $db->getAffectedRows(); // Get the count of deleted rows
                $this->logTask(sprintf('Deleted %d logs', $deletedRows)); // Log the count of deleted rows
            } catch (\RuntimeException $e) {
                // Log the error possibly and handle exceptions
                $this->logTask('Error deleting logs: ' . $e->getMessage());
                return Status::KNOCKOUT;
            }
        } else {
            $this->logTask('No logs deleted as the deletion period is set to 0 days.');
        }

        $this->logTask('Delete Logs end');

        return Status::OK;
    }

}
