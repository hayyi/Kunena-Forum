<?php

/**
 * Kunena Component
 *
 * @package         Kunena.Administrator
 * @subpackage      Extension
 *
 * @copyright       Copyright (C) 2008 - 2023 Kunena Team. All rights reserved.
 * @license         https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link            https://www.kunena.org
 **/

namespace Kunena\Forum\Administrator\Extension;

\defined('JPATH_PLATFORM') or die;

use Joomla\CMS\Application\SiteApplication;
use Joomla\CMS\Association\AssociationServiceTrait;
use Joomla\CMS\Categories\CategoryServiceTrait;
use Joomla\CMS\Component\Router\RouterServiceInterface;
use Joomla\CMS\Component\Router\RouterServiceTrait;
use Joomla\CMS\Extension\BootableExtensionInterface;
use Joomla\CMS\Extension\MVCComponent;
use Joomla\CMS\HTML\HTMLRegistryAwareTrait;
use Joomla\CMS\Tag\TagServiceTrait;
use Kunena\Component\Kunena\Administrator\Service\Html\KUNENA;
use Psr\Container\ContainerInterface;
use Kunena\Forum\Site\Service\Html\Kunenagrid;
use Kunena\Forum\Site\Service\Html\Kunenaforum;

/**
 * Component class for com_kunena
 *
 * @since   Kunena 6.0
 */
class ForumComponent extends MVCComponent implements BootableExtensionInterface, RouterServiceInterface
{
    use AssociationServiceTrait;
    use RouterServiceTrait;
    use HTMLRegistryAwareTrait;
    use CategoryServiceTrait, TagServiceTrait {
        CategoryServiceTrait::getTableNameForSection insteadof TagServiceTrait;
        CategoryServiceTrait::getStateColumnForSection insteadof TagServiceTrait;
    }

    /**
     * Booting the extension. This is the function to set up the environment of the extension like
     * registering new class loaders, etc.
     *
     * If required, some initial set up can be done from services of the container, eg.
     * registering HTML services.
     *
     * @param   ContainerInterface  $container  The container
     *
     * @return  void
     *
     * @since   Kunena 6.0
     */
    public function boot(ContainerInterface $container)
    {
        $this->getRegistry()->register('kunenagrid', new Kunenagrid($container->get(SiteApplication::class)));
        $this->getRegistry()->register('kunenaforum', new Kunenaforum($container->get(SiteApplication::class)));
    }


    /**
     * Returns the table for the count items functions for the given section.
     *
     * @param   string    The section
     *
     * * @return  string|null
     *
     * @since   4.0.0
     */
    protected function getTableNameForSection(string $section = null)
    {
    }

    /**
     * Adds Count Items for Category Manager.
     *
     * @param   \stdClass[]  $items    The category objects
     * @param   string       $section  The section
     *
     * @return  void
     *
     * @since   4.0.0
     */
    public function countItems(array $items, string $section)
    {
    }
}
