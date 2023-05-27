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

defined('_JEXEC') or die();

use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Layout\LayoutHelper;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Session\Session;
use Joomla\CMS\Uri\Uri;
use Kunena\Forum\Libraries\Factory\KunenaFactory;
use Kunena\Forum\Libraries\Route\KunenaRoute;
use Kunena\Forum\Libraries\Version\KunenaVersion;

$listOrder = $this->escape($this->state->get('list.ordering'));
$listDirn  = $this->escape($this->state->get('list.direction'));
$saveOrder = ($listOrder == 'a.order' && strtolower($listDirn) == 'asc');
$extension = $this->escape($this->state->get('filter.extension'));
$filterItem = $this->escape($this->state->get('item.id'));
$saveOrderingUrl = 'index.php?option=com_kunena&task=categories.saveOrderAjax&tmpl=component&' . Session::getFormToken() . '=1';

/** @var Joomla\CMS\WebAsset\WebAssetManager $wa */
$wa = $this->document->getWebAssetManager();
$wa->useScript('jquery');

$this->document->addScript(Uri::root() . 'media/kunena/core/js/multiselect-uncompressed.js');

$this->document->addScriptDeclaration(
	"jQuery(document).ready(function() {
        Joomla.JMultiSelect('adminForm');
    });"
);

HTMLHelper::_('kunenaforum.sortablelist', 'categoryList', 'adminForm', $this->listDirection, $this->saveOrderingUrl, false, true);

$this->document->addScriptDeclaration(
	"Joomla.orderTable = function () {
        var table = document.getElementById(\"sortTable\");
        var direction = document.getElementById(\"directionTable\");
        var order = table.options[table.selectedIndex].value;
        if (order != '" . $listOrder . "') {
            dirn = 'asc';
        } else {
            dirn = direction.options[direction.selectedIndex].value;
        }
        Joomla.tableOrdering(order, dirn, '');
    }"
);
?>
<form action="<?php echo KunenaRoute::_('administrator/index.php?option=com_kunena&view=categories'); ?>"
      method="post" name="adminForm"
      id="adminForm">
    <div class="row">
        <div class="col-md-12">
            <div id="j-main-container" class="j-main-container">
                <?php
                // Search tools bar
                echo LayoutHelper::render('joomla.searchtools.default', ['view' => $this]);
                ?>
                <?php if (empty($this->categories)) : ?>
                    <div class="alert alert-info">
                        <span class="icon-info-circle" aria-hidden="true"></span><span class="visually-hidden"><?php echo Text::_('INFO'); ?></span>
                        <?php echo Text::_('JGLOBAL_NO_MATCHING_RESULTS'); ?>
                    </div>

                <?php var_dump($this->categories);?>
                <?php else : ?>
                <table class="table table-striped" id="categoryList">
                    <caption class="visually-hidden">
                        <?php echo Text::_('COM_CATEGORIES_TABLE_CAPTION'); ?>,
                        <span id="orderedBy"><?php echo Text::_('JGLOBAL_SORTED_BY'); ?> </span>,
                        <span id="filteredBy"><?php echo Text::_('JGLOBAL_FILTERED_BY'); ?></span>
                    </caption>
                    <thead>
                    <tr>
                        <td class="w-1 text-center">
                            <?php echo HTMLHelper::_('grid.checkall'); ?>
                        </td>
                        <th scope="col" class="w-1 d-none d-md-table-cell">
                            <?php echo HTMLHelper::_('grid.sort', '<i class="icon-menu-2"></i>', 'a.ordering', 'asc', '', null, 'asc', 'JGRID_HEADING_ORDERING'); ?>
                        </th>
                        <th scope="col" class="w-1 text-center">
                        </th>
                        <th scope="col" class="w-1">
                            <?php echo Text::_('COM_KUNENA_GO'); ?>
                        </th>
                        <th scope="col" class="w-20 d-none d-md-table-cell">
                            <?php echo HTMLHelper::_('grid.sort', 'JGLOBAL_TITLE', 'p.title', $this->listDirection, $this->listOrdering); ?>
                        </th>
                        <th scope="col" class="w-10 d-none d-md-table-cell">
                            <?php echo HTMLHelper::_('grid.sort', 'COM_KUNENA_CATEGORIES_LABEL_ACCESS', 'p.access', $this->listDirection, $this->listOrdering); ?>
                        </th>
                        <th scope="col" class="w-1">
                            <?php echo HTMLHelper::_('grid.sort', 'COM_KUNENA_LOCKED', 'p.locked', $this->listDirection, $this->listOrdering); ?>
                        </th>
                        <th scope="col" class="w-1">
                            <?php echo HTMLHelper::_('grid.sort', 'COM_KUNENA_REVIEW', 'p.review', $this->listDirection, $this->listOrdering); ?>
                        </th>
                        <th scope="col" class="w-1">
                            <?php echo HTMLHelper::_('grid.sort', 'COM_KUNENA_CATEGORIES_LABEL_POLL', 'p.allowPolls', $this->listDirection, $this->listOrdering); ?>
                        </th>
                        <th scope="col" class="w-1">
                            <?php echo HTMLHelper::_('grid.sort', 'COM_KUNENA_CATEGORY_ANONYMOUS', 'p.anonymous', $this->listDirection, $this->listOrdering); ?>
                        </th>
                        <th scope="col" class="w-1 d-none d-md-table-cell">
                            <?php echo HTMLHelper::_('grid.sort', 'JGRID_HEADING_ID', 'p.id', $this->listDirection, $this->listOrdering); ?>
                        </th>
                    </tr>
                    </thead>
                    <?php if ($this->pagination->pagesTotal > 1) : ?>
                    <tfoot>
                    <tr>
                        <td colspan="10">
                            <?php echo $this->pagination->getListFooter(); ?>
                            <?php // Load the batch processing form. ?>
                            <?php echo $this->loadTemplate('batch'); ?>
                            <?php // Load the modal to confirm delete. ?>
                            <?php echo $this->loadTemplate('confirmdelete'); ?>
                        </td>
                    </tr>
                    </tfoot>
                    <?php endif;?>
                    <tbody <?php if ($saveOrder) :
                        ?> class="js-draggable" data-url="<?php echo $saveOrderingUrl; ?>" data-direction="<?php echo strtolower($listDirn); ?>" data-nested="true"<?php
                    endif; ?>>
                    <?php
                    $img_no             = '<i class="icon-cancel"></i>';
                    $img_yes            = '<i class="icon-checkmark"></i>';
                    $i                  = 0;

                    if ($this->pagination->total >= 0) :
                        foreach ($this->categories as $item) :
                            $orderkey = array_search($item->id, $this->ordering[$item->parentid]);
                            $canEdit = $this->me->isAdmin($item);
                            $canCheckin = $this->user->authorise('core.admin', 'com_checkIn') || $item->checked_out == $this->user->id || $item->checked_out == 0;
                            $canEditOwn = $canEdit;
                            $canChange  = $canEdit && $canCheckin;

                            // Get the parents of item for sorting
                            if ($item->level > 0)
                            {
                                $parentsStr       = "";
                                $_currentParentId = $item->parentid;
                                $parentsStr       = " " . $_currentParentId;

                                for ($i2 = 0; $i2 < $item->level; $i2++)
                                {
                                    foreach ($this->ordering as $k => $v)
                                    {
                                        $v = implode("-", $v);
                                        $v = "-" . $v . "-";

                                        if (strpos($v, "-" . $_currentParentId . "-") !== false)
                                        {
                                            $parentsStr       .= " " . $k;
                                            $_currentParentId = $k;
                                            break;
                                        }
                                    }
                                }
                            }
                            else
                            {
                                $parentsStr = "";
                            }
                            ?>
                            <tr sortable-group-id="<?php echo $item->parentid; ?>" item-id="<?php echo $item->id ?>"
                                parents="<?php echo $parentsStr ?>" level="<?php echo $item->level ?>">
                                <td class="text-center">
                                    <?php echo HTMLHelper::_('grid.id', $i, $item->id, false, 'cid', 'cb', $item->name); ?>
                                </td>
                                <td class="order nowrap center hidden-phone">
                                    <?php if ($canChange)
                                        :
                                        $disableClassName = '';
                                        $disabledLabel = '';

                                        if (!$this->saveOrder)
                                            :
                                            $disabledLabel    = Text::_('JORDERINGDISABLED');
                                            $disableClassName = 'inactive tip-top';
                                        endif; ?>
                                        <span class="sortable-handler<?php echo $disableClassName; ?>" title="<?php echo $disabledLabel; ?>">
                                                <span class="icon-ellipsis-v"></span>
                                            </span>
                                        <input type="text" style="display:none;" name="order[]" size="5"
                                               value="<?php echo $orderkey; ?>"/>
                                    <?php else:
                                        ?>
                                        <span class="sortable-handler inactive">
                                            <i class="icon-menu"></i>
                                        </span>
                                    <?php endif; ?>
                                </td>
                                <td class="center">
                                    <?php echo HTMLHelper::_('jgrid.published', $item->published, $i, 'category.'); ?>
                                </td>
                                <td class="center">
                                    <?php if (!$this->filter->Item || ($this->filter->Item != $item->id && $item->parentid)) :
                                        ?>
                                        <button class="btn btn-micro"
                                                title="Display only this item and its children"
                                                onclick="jQuery('input[name=catid]').val(<?php echo $item->id ?>);this.form.submit()">
                                            <i class="icon-location"></i>
                                        </button>
                                    <?php else :
                                        ?>
                                        <button class="btn btn-micro"
                                                title="Display only this item and its children"
                                                onclick="jQuery('input[name=catid]').val(<?php echo $item->parentid ?>);this.form.submit()">
                                            <i class="icon-arrow-up"></i>
                                        </button>
                                    <?php endif; ?>
                                </td>
                                <td class="has-context">
                                    <?php
                                    echo str_repeat('<span class="gi">&mdash;</span>', $item->level);

                                    if ($item->checked_out) {
                                        $canCheckin = $item->checked_out == 0 || $item->checked_out == $this->user->id || $this->user->authorise('core.admin', 'com_checkIn');
                                        $editor     = KunenaFactory::getUser($item->editor)->getName();
                                        echo HTMLHelper::_('jgrid.checkedout', $i, $editor, $item->checked_out_time, 'category.', $canCheckin);
                                    }
                                    ?>
                                    <a href="<?php echo Route::_('index.php?option=com_kunena&view=category&layout=edit&catid=' . (int) $item->id); ?>">
                                        <?php echo $this->escape($item->name); ?>
                                    </a>
                                    <small>
                                        <?php echo Text::sprintf('JGLOBAL_LIST_ALIAS', $this->escape($item->alias)); ?>
                                    </small>
                                </td>
                                <td class="center d-none d-md-table-cell">
                                    <span><?php echo $item->accessname; ?></span>
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
                                <?php if ($item->isSection()) :
                                    ?>
                                    <td class="center d-none d-md-table-cell" colspan="3">
                                        <?php echo Text::_('COM_KUNENA_SECTION'); ?>
                                    </td>
                                <?php else :
                                    ?>
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
                                <?php endif; ?>

                                <td class="center d-none d-md-table-cell">
                                    <?php echo (int) $item->id; ?>
                                </td>
                            </tr>
                            <?php
                            $i++;
                        endforeach;
                    else :
                        ?>
                        <tr>
                            <td colspan="10">
                                <div class="card card-block bg-faded p-2 center filter-state">
                                    <span><?php echo Text::_('COM_KUNENA_FILTERACTIVE'); ?>
                                        <?php
                                        if ($this->filter->Active) :
                                            ?>
                                            <button class="btn btn-outline-primary" type="button"
                                                    onclick="document.getElements('.filter').set('value', '');this.form.submit();"><?php echo Text::_('COM_KUNENA_FIELD_LABEL_FILTERCLEAR'); ?></button>
                                        <?php else :
                                            ?>
                                            <button class="btn btn-outline-success" type="button"
                                                    onclick="Joomla.submitbutton('add');"><?php echo Text::_('COM_KUNENA_NEW_CATEGORY'); ?></button>
                                        <?php endif; ?>
                                    </span>
                                </div>
                            </td>
                        </tr>
                    <?php endif; ?>
                    </tbody>
                </table>
                <?php endif; ?>
            </div>
        </div>
        <div class="pull-right small">
            <?php echo KunenaVersion::getLongVersionHTML(); ?>
        </div>
    </div>
    <input type="hidden" name="task" value=""/>
    <input type="hidden" name="catid" value="<?php echo $this->filter->Item; ?>"/>
    <input type="hidden" name="boxchecked" value="0"/>
    <input type="hidden" name="filter_order" value="<?php echo $this->listOrdering; ?>"/>
    <input type="hidden" name="filter_order_Dir" value="<?php echo $this->listDirection; ?>"/>
    <?php echo HTMLHelper::_('form.token'); ?>
</form>
