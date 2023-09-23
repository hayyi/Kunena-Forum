<?php

/**
 * Kunena Component
 *
 * @package         Kunena.Administrator.Template
 * @subpackage      Categories
 *
 * @copyright       Copyright (C) 2008 - 2023 Kunena Team. All rights reserved.
 * @license         https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link            https://www.kunena.org
 **/

// No direct access
defined('_JEXEC') or die;


use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Factory;
use Joomla\CMS\Uri\Uri;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Layout\LayoutHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Session\Session;
use Kunena\Forum\Libraries\Factory\KunenaFactory;

HTMLHelper::_('bootstrap.tooltip');
HTMLHelper::_('behavior.multiselect');

// Import CSS
$wa =  $this->document->getWebAssetManager();
$wa->useStyle('searchtools');

$user      = Factory::getApplication()->getIdentity();
$userId    = $user->get('id');
$listOrder = $this->state->get('list.ordering');
$listDirn  = $this->state->get('list.direction');
$canOrder  = $user->authorise('core.edit.state', 'com_kunena');
$saveOrder = $listOrder == 'a.ordering';



if (!empty($saveOrder)) {
    $saveOrderingUrl = 'index.php?option=com_kunena&task=categories.saveOrderAjax&tmpl=component&' . Session::getFormToken() . '=1';
    HTMLHelper::_('draggablelist.draggable');
}
?>

<form action="<?php echo Route::_('index.php?option=com_kunena&view=categories'); ?>" method="post"
      name="adminForm" id="adminForm">
    <div class="row">
        <div class="col-md-12">
            <div id="j-main-container" class="j-main-container">
            <?php echo LayoutHelper::render('joomla.searchtools.default', array('view' => $this)); ?>

                <div class="clearfix"></div>
                <?php if (empty($this->items)) : ?>
                    <div class="alert alert-info">
                        <span class="icon-info-circle" aria-hidden="true"></span><span class="visually-hidden"><?php echo Text::_('INFO'); ?></span>
                        <?php echo Text::_('JGLOBAL_NO_MATCHING_RESULTS'); ?>
                    </div>
                <?php else : ?>
                <table class="table table-striped" id="categoryList">
                    <thead>
                    <tr>
                        <th class="w-1 text-center">
                            <input type="checkbox" autocomplete="off" class="form-check-input" name="checkall-toggle" value=""
                                   title="<?php echo Text::_('JGLOBAL_CHECK_ALL'); ?>" onclick="Joomla.checkAll(this)"/>
                        </th>

                    <?php if (isset($this->items[0]->ordering)) : ?>
                    <th scope="col" class="w-1 text-center d-none d-md-table-cell">

                        <?php echo HTMLHelper::_('searchtools.sort', '', 'a.ordering', $listDirn, $listOrder, null, 'asc', 'JGRID_HEADING_ORDERING', 'icon-menu-2'); ?>

                    </th>
                    <?php endif; ?>
                        <th class='left w-1'>
                            <?php echo HTMLHelper::_('searchtools.sort', 'Published', 'a.published', $listDirn, $listOrder); ?>
                        </th>
                        <th class='left w-9'>
                            <?php echo HTMLHelper::_('searchtools.sort', 'Title', 'a.name', $listDirn, $listOrder); ?>
                        </th>
                        <th class='left w-1'>
                            <?php echo HTMLHelper::_('searchtools.sort', 'ACCESS', 'a.access', $listDirn, $listOrder); ?>
                        </th>
                        <th class='left w-1'>
                            <?php echo HTMLHelper::_('searchtools.sort', 'LOCKED', 'a.locked', $listDirn, $listOrder); ?>
                        </th>
                        <th class='left w-1'>
                            <?php echo HTMLHelper::_('searchtools.sort', 'REVIEW', 'a.review', $listDirn, $listOrder); ?>
                        </th>
                        <th class='left w-1'>
                            <?php echo HTMLHelper::_('searchtools.sort', 'POLL', 'a.allowPolls', $listDirn, $listOrder); ?>
                        </th>
                        <th class='left w-1'>
                            <?php echo HTMLHelper::_('searchtools.sort', 'ANONYMOUS', 'a.anonymous', $listDirn, $listOrder); ?>
                        </th>
                        <th scope="col" class="w-3 d-none d-lg-table-cell" >
                            <?php echo HTMLHelper::_('searchtools.sort', 'JGRID_HEADING_ID', 'a.id', $listDirn, $listOrder); ?>
                        </th>
                    </tr>
                    </thead>
                    <tbody <?php if (!empty($saveOrder)) :
                        ?> class="js-draggable" data-url="<?php echo $saveOrderingUrl; ?>" data-direction="<?php echo strtolower($listDirn); ?>" <?php
                           endif; ?>>
                    <?php foreach ($this->items as $i => $item) :
                        $ordering   = ($listOrder == 'a.ordering');
                        $canCreate  = $user->authorise('core.create', 'com_kunena');
                        $canEdit    = $user->authorise('core.edit', 'com_kunena');
                        $canCheckin = $user->authorise('core.manage', 'com_kunena');
                        $canChange  = $user->authorise('core.edit.state', 'com_kunena');
                        $img_no             = '<i class="icon-cancel"></i>';
                        $img_yes            = '<i class="icon-checkmark"></i>';
                        $i                  = 0;
                        ?>
                        <tr class="row<?php echo $i % 2; ?>" data-draggable-group='1' data-transition>
                            <td class="text-center">
                                <?php echo HTMLHelper::_('grid.id', $i, $item->id); ?>
                            </td>

                            <?php if (isset($this->items[0]->ordering)) : ?>
                            <td class="text-center d-none d-md-table-cell">

                                <?php

                                $iconClass = '';

                                if (!$canChange) {
                                    $iconClass = ' inactive';
                                } elseif (!$saveOrder) {
                                    $iconClass = ' inactive" title="' . Text::_('JORDERINGDISABLED');
                                }                           ?>                          <span class="sortable-handler<?php echo $iconClass ?>">
                            <span class="icon-ellipsis-v" aria-hidden="true"></span>
                            </span>
                                <?php if ($canChange && $saveOrder) : ?>
                            <input type="text" name="order[]" size="5" value="<?php echo $item->ordering; ?>" class="width-20 text-area-order hidden">
                                <?php endif; ?>
                            </td>
                            <?php endif; ?>
                            <td class="text-center">
                                <?php echo HTMLHelper::_('jgrid.published', $item->published, $i, 'categories.', $canChange); ?>
                            </td>

                            <th scope="row">
                                <?php if ($canEdit) : ?>
                                    <a href="<?php echo Route::_('index.php?option=com_kunena&view=category&layout=edit&catid=' . (int) $item->id); ?>">
                                        <?php echo $this->escape($item->name); ?></a>
                                <?php else : ?>
                                    <?php echo $this->escape($item->name); ?>
                                <?php endif; ?>
                                <div>
                                    <span class="small">
                                                 <?php echo Text::sprintf('JGLOBAL_LIST_ALIAS', $this->escape($item->alias)); ?>
                                            </span>
                                </div>
                            </th>
                            <td class="center d-none d-md-table-cell">
                                <span><?php echo $item->access; ?></span>
                                <small>
                                    <?php echo Text::sprintf('(Access: %s)', $this->escape($item->accesstype)); ?>
                                </small>
                            </td>
                            <td class="center d-none d-md-table-cell">
                                <a class="btn btn-micro <?php echo $item->locked ? 'active' : ''; ?>"
                                   href="javascript: void(0);"
                                   onclick="return Joomla.listItemTask('cb<?php echo $i; ?>','<?php echo($item->locked ? 'un' : '') . 'lock'; ?>')">
                                    <?php echo $item->locked == 1 ? $img_yes : $img_no; ?>
                                </a>
                            </td>

                                <td class="center d-none d-md-table-cell">
                                    <a class="btn btn-micro <?php echo $item->review ? 'active' : ''; ?>"
                                       href="javascript: void(0);"
                                       onclick="return Joomla.listItemTask('cb<?php echo $i; ?>','<?php echo($item->review ? 'un' : '') . 'review'; ?>')">
                                        <?php echo $item->review == 1 ? $img_yes : $img_no; ?>
                                    </a>
                                </td>
                                <td class="center d-none d-md-table-cell">
                                    <a class="btn btn-micro <?php echo $item->allowPolls ? 'active' : ''; ?>"
                                       href="javascript: void(0);"
                                       onclick="return Joomla.listItemTask('cb<?php echo $i; ?>','<?php echo($item->allowPolls ? 'deny' : 'allow') . '_polls'; ?>')">
                                        <?php echo $item->allowPolls == 1 ? $img_yes : $img_no; ?>
                                    </a>
                                </td>
                                <td class="center d-none d-md-table-cell">
                                    <a class="btn btn-micro <?php echo $item->allowAnonymous ? 'active' : ''; ?>"
                                       href="javascript: void(0);"
                                       onclick="return Joomla.listItemTask('cb<?php echo $i; ?>','<?php echo($item->allowAnonymous ? 'deny' : 'allow') . '_anonymous'; ?>')">
                                        <?php echo $item->allowAnonymous == 1 ? $img_yes : $img_no; ?>
                                    </a>
                                </td>

                            <td class="center d-none d-md-table-cell">
                                <?php echo (int) $item->id; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
                <?php endif; ?>

                <?php echo $this->pagination->getListFooter(); ?>

                <input type="hidden" name="task" value=""/>
                <input type="hidden" name="boxchecked" value="0"/>
                <input type="hidden" name="list[fullorder]" value="<?php echo $listOrder; ?> <?php echo $listDirn; ?>"/>
                <?php echo HTMLHelper::_('form.token'); ?>
            </div>
        </div>
    </div>
</form>
