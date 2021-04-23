<?php
/**
 * Kunena Component
 *
 * @package       Kunena.Framework
 * @subpackage    Integration
 *
 * @copyright     Copyright (C) 2008 - 2021 Kunena Team. All rights reserved.
 * @license       https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link          https://www.kunena.org
 **/

namespace Kunena\Forum\Libraries\Integration;

defined('_JEXEC') or die();

use Exception;
use Joomla\CMS\Factory;
use Joomla\CMS\Plugin\PluginHelper;
use Kunena\Forum\Libraries\Factory\KunenaFactory;
use Kunena\Forum\Libraries\Layout\KunenaLayout;
use Kunena\Forum\Libraries\Route\KunenaRoute;
use Kunena\Forum\Libraries\User\KunenaUser;
use function defined;

/**
 * Class KunenaProfile
 *
 * @since   Kunena 6.0
 */
class KunenaProfile
{
	/**
	 * @var     boolean
	 * @since   Kunena 6.0
	 */
	protected static $instance = false;

	/**
	 * @param   null  $integration  integration
	 *
	 * @return  boolean|KunenaProfile
	 *
	 * @since   Kunena 6.0
	 *
	 * @throws  Exception
	 */
	public static function getInstance($integration = null)
	{
		if (self::$instance === false)
		{
			PluginHelper::importPlugin('kunena');

			$classes = Factory::getApplication()->triggerEvent('onKunenaGetProfile');

			foreach ($classes as $class)
			{
				if (!is_object($class))
				{
					continue;
				}

				self::$instance = $class;
				break;
			}

			if (!self::$instance)
			{
				self::$instance = new self;
			}
		}

		return self::$instance;
	}

	/**
	 * @param   int  $limit  limit
	 *
	 * @return  array
	 *
	 * @since   Kunena 6.0
	 *
	 * @throws  Exception
	 */
	public function getTopHits($limit = 0): array
	{
		if (!$limit)
		{
			$limit = KunenaFactory::getConfig()->popUserCount;
		}

		return (array) $this->getTopHitsArray($limit);
	}

	/**
	 * @param   int  $limit  limit
	 *
	 * @return  array
	 *
	 * @since   Kunena 6.0
	 */
	protected function getTopHitsArray($limit = 0): array
	{
		return [];
	}

	/**
	 * @param   string  $action  action
	 * @param   bool    $xhtml   xhtml
	 *
	 * @return  boolean
	 *
	 * @since   Kunena 6.0
	 *
	 * @throws  Exception
	 * @throws  null
	 */
	public function getStatisticsURL($action = '', $xhtml = true): bool
	{
		$config = KunenaFactory::getConfig();
		$my     = Factory::getApplication()->getIdentity();

		if ($config->statsLinkAllowed == 0 && $my->id == 0)
		{
			return false;
		}

		return KunenaRoute::_('index.php?option=com_kunena&view=statistics' . $action, $xhtml);
	}

	/**
	 * @param   string  $action  action
	 * @param   bool    $xhtml   xhtml
	 *
	 * @return  string|void
	 *
	 * @since   Kunena 6.0
	 */
	public function getUserListURL($action = '', $xhtml = true): void
	{
	}

	/**
	 * @param   KunenaUser  $user   user
	 * @param   string      $task   task
	 * @param   bool        $xhtml  xhtml
	 *
	 * @return boolean|void
	 *
	 * @since   Kunena 6.0
	 */
	public function getProfileURL($user, $task = '', $xhtml = true): void
	{
	}

	/**
	 * @param   KunenaLayout  $view    view
	 * @param   object        $params  params
	 *
	 * @return  void
	 *
	 * @since   Kunena 6.0
	 */
	public function showProfile(KunenaLayout $view, object $params): void
	{
	}

	/**
	 * @param   integer  $userid  userid
	 * @param   bool     $xhtml   xhtml
	 *
	 * @return boolean|void
	 *
	 * @since   Kunena 6.0
	 */
	public function getEditProfileURL(int $userid, $xhtml = true): void
	{
	}
}
