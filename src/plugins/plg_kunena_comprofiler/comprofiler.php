<?php
/**
 * Kunena Plugin
 *
 * @package         Kunena.Plugins
 * @subpackage      Comprofiler
 *
 * @copyright       Copyright (C) 2008 - 2021 Kunena Team. All rights reserved.
 * @license         https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link            https://www.kunena.org
 **/

defined('_JEXEC') or die();

use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Plugin\CMSPlugin;
use Joomla\CMS\Plugin\PluginHelper;
use Kunena\Forum\Libraries\Factory\KunenaFactory;
use Kunena\Forum\Libraries\Forum\KunenaForum;
use Kunena\Forum\Libraries\Integration\KunenaActivity;
use Kunena\Forum\Libraries\Integration\KunenaAvatar;
use Kunena\Forum\Libraries\Integration\KunenaPrivate;
use Kunena\Forum\Libraries\Integration\KunenaProfile;

/**
 * Class plgKunenaComprofiler
 *
 * @since   Kunena 6.0
 */
class plgKunenaComprofiler extends CMSPlugin
{
	/**
	 * @var     string
	 * @since   Kunena 6.0
	 */
	public $minCBVersion = '2.0.0';

	/**
	 * plgKunenaComprofiler constructor.
	 *
	 * @param   object  $subject  subject
	 * @param   object  $config   config
	 *
	 * @since   Kunena 6.0
	 *
	 * @throws Exception
	 */
	public function __construct(object &$subject, object $config)
	{
		global $ueConfig;

		// Do not load if Kunena version is not supported or Kunena is offline
		if (!(class_exists('Kunena\Forum\Libraries\Forum\KunenaForum') && KunenaForum::isCompatible('6.0') && KunenaForum::installed()))
		{
			return;
		}

		$app = Factory::getApplication();

		// Do not load if CommunityBuilder is not installed
		if ((!file_exists(JPATH_SITE . '/libraries/CBLib/CBLib/Core/CBLib.php')) || (!file_exists(JPATH_ADMINISTRATOR . '/components/com_comprofiler/plugin.foundation.php')))
		{
			if (PluginHelper::isEnabled('kunena', 'comprofiler'))
			{
				$db    = Factory::getDBO();
				$query = $db->getQuery(true);
				$query->update($db->quoteName('#__extensions'));
				$query->where($db->quoteName('element') . ' = ' . $db->quote('comprofiler'));
				$query->where($db->quoteName('type') . ' = ' . $db->quote('plugin'));
				$query->where($db->quoteName('folder') . ' = ' . $db->quote('kunena'));
				$query->set($db->quoteName('enabled') . ' = 0');
				$db->setQuery($query);
				$db->execute();
			}

			return;
		}

		require_once JPATH_ADMINISTRATOR . '/components/com_comprofiler/plugin.foundation.php';

		cbimport('cb.html');
		cbimport('language.front');

		parent::__construct($subject, $config);

		$this->loadLanguage('plg_kunena_comprofiler.sys', JPATH_ADMINISTRATOR) || $this->loadLanguage('plg_kunena_comprofiler.sys', KPATH_ADMIN);

		if ($app->isClient('administrator') && (!isset($ueConfig ['version']) || version_compare($ueConfig ['version'], $this->minCBVersion) < 0))
		{
			$app->enqueueMessage(Text::sprintf('PLG_KUNENA_COMPROFILER_WARN_VERSION', $this->minCBVersion), 'notice');
		}
	}

	/**
	 * @param   string  $type    type
	 * @param   null    $view    view
	 * @param   null    $params  params
	 *
	 * @return  void
	 *
	 * @since   Kunena 6.0
	 *
	 * @throws Exception
	 */
	public function onKunenaDisplay(string $type, $view = null, $params = null): void
	{
		$integration = KunenaFactory::getProfile();

		if (!$integration instanceof KunenaProfileComprofiler)
		{
			return;
		}

		switch ($type)
		{
			case 'start':
				$integration->open();
				break;
			case 'end':
				$integration->close();
		}
	}

	/**
	 * @param   string  $context  context
	 * @param   int     $item     items
	 * @param   object  $params   params
	 * @param   int     $page     page
	 *
	 * @return  void
	 *
	 * @since   Kunena 6.0
	 *
	 * @throws Exception
	 */
	public function onKunenaPrepare(string $context, int &$item, object $params, $page = 0): void
	{
		if ($context == 'kunena.user')
		{
			$triggerParams = ['userid' => $item->userid, 'userinfo' => &$item];
			$integration   = KunenaFactory::getProfile();

			if ($integration instanceof KunenaProfileComprofiler)
			{
				KunenaProfileComprofiler::trigger('profileIntegration', $triggerParams);
			}
		}
	}

	/**
	 * Get Kunena access control object.
	 *
	 * @return  KunenaAccessComprofiler|void
	 *
	 * @since   Kunena 6.0
	 */
	public function onKunenaGetAccessControl()
	{
		if (!$this->params->get('access', 1))
		{
			return;
		}

		return new KunenaAccessComprofiler($this->params);
	}

	/**
	 * Get Kunena login integration object.
	 *
	 * @return  KunenaLoginComprofiler|void
	 *
	 * @since   Kunena 6.0
	 */
	public function onKunenaGetLogin()
	{
		if (!$this->params->get('login', 1))
		{
			return;
		}

		return new KunenaLoginComprofiler($this->params);
	}

	/**
	 * Get Kunena avatar integration object.
	 *
	 * @return  KunenaAvatar|void
	 *
	 * @since   Kunena 6.0
	 */
	public function onKunenaGetAvatar()
	{
		if (!$this->params->get('avatar', 1))
		{
			return;
		}

		return new AvatarComprofiler($this->params);
	}

	/**
	 * Get Kunena profile integration object.
	 *
	 * @return  KunenaProfile|void
	 *
	 * @since   Kunena 6.0
	 */
	public function onKunenaGetProfile()
	{
		if (!$this->params->get('profile', 1))
		{
			return;
		}

		return new KunenaProfileComprofiler($this->params);
	}

	/**
	 * Get Kunena private message integration object.
	 *
	 * @return  KunenaPrivate|void
	 *
	 * @since   Kunena 6.0
	 */
	public function onKunenaGetPrivate()
	{
		if (!$this->params->get('private', 1))
		{
			return;
		}

		return new KunenaPrivateComprofiler($this->params);
	}

	/**
	 * Get Kunena activity stream integration object.
	 *
	 * @return  KunenaActivity|void
	 *
	 * @since   Kunena 6.0
	 * @throws Exception
	 */
	public function onKunenaGetActivity()
	{
		if (!$this->params->get('activity', 1))
		{
			return;
		}

		return new KunenaActivityComprofiler($this->params);
	}
}