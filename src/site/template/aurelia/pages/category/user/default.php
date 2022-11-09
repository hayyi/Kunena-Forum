<?php

/**
 * Kunena Component
 *
 * @package         Kunena.Template.Aurelia
 * @subpackage      Pages.Category
 *
 * @copyright       Copyright (C) 2008 - 2022 Kunena Team. All rights reserved.
 * @license         https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link            https://www.kunena.org
 **/

namespace Kunena\Forum\Site;

\defined('_JEXEC') or die();

use Joomla\CMS\Language\Text;

$content = $this->execute('Category/Subscriptions');

$this->addBreadcrumb(
    Text::_('COM_KUNENA_VIEW_CATEGORIES_USER'),
    'index.php?option=com_kunena&view=category&layout=user'
);

echo $content;
