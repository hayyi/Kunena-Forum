<?php

/**
 * Kunena Component
 *
 * @package         Kunena.Administrator
 * @subpackage      Views
 *
 * @copyright       Copyright (C) 2008 - 2023 Kunena Team. All rights reserved.
 * @license         https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link            https://www.kunena.org
 **/

namespace Kunena\Forum\Administrator\View\Categories;

\defined('_JEXEC') or die();

use Exception;
use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Multilanguage;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\View\GenericDataException;
use Joomla\CMS\MVC\View\HtmlView as BaseHtmlView;
use Joomla\CMS\Object\CMSObject;
use Joomla\CMS\Toolbar\Toolbar;
use Joomla\CMS\Toolbar\ToolbarHelper;
use Kunena\Forum\Libraries\Forum\Category\KunenaCategory;
use Kunena\Forum\Libraries\User\KunenaUserHelper;

/**
 * About view for Kunena backend
 *
 * @since   Kunena 6.0
 */
class HtmlView extends BaseHtmlView
{
    /**
     * @var     array|KunenaCategory[]
     * @since   Kunena 6.0
     */
    public $categories = [];

    public $sortFields;

    public $ordering;

    public $saveOrder;

    /**
     * @var     array|KunenaCategory[]
     * @since   Kunena 6.0
     */
    public $batchCategories;

    /**
     * The model state
     *
     * @var    CMSObject
     * @since  Kunena 6.0
     */
    protected $state;

    /**
     * @var mixed
     * @since version
     */
    protected $pagination;

    /**
     * @var mixed
     * @since version
     */
    protected $filterActive;

    /**
     * @param   null  $tpl  tpl
     *
     * @return  void
     *
     * @since   Kunena 6.0
     *
     * @throws Exception
     */
    public function display($tpl = null)
    {
        $this->state         = $this->get('State');
        $this->categories    = $this->get('AdminCategories');
        $this->pagination    = $this->get('AdminNavigation');
        $this->filterForm    = $this->get('FilterForm');
        $this->activeFilters = $this->get('ActiveFilters');

        // Preprocess the list of items to find ordering divisions.
        $this->ordering = [];

        foreach ($this->categories as $item) {
            $this->ordering[$item->parentid][] = $item->id;
        }

        // Written this way because we only want to call IsEmptyState if no items, to prevent always calling it when not needed.
        /*if (!count($this->items) && $this->isEmptyState = $this->get('IsEmptyState')) {
            $this->setLayout('emptystate');
        }*/

        // Check for errors.
        if (count($errors = $this->get('Errors'))) {
            throw new GenericDataException(implode("\n", $errors), 500);
        }

        $this->user = Factory::getApplication()->getIdentity();
        $this->me   = KunenaUserHelper::getMyself();

        // We don't need toolbar in the modal window.
        if ($this->getLayout() !== 'modal') {
            $this->addToolbar();

            // We do not need to filter by language when multilingual is disabled
            if (!Multilanguage::isEnabled()) {
                unset($this->activeFilters['language']);
                $this->filterForm->removeField('language', 'filter');
            }
        } else {
            // In article associations modal we need to remove language filter if forcing a language.
            if ($forcedLanguage = Factory::getApplication()->getInput()->get('forcedLanguage', '', 'CMD')) {
                // If the language is forced we can't allow to select the language, so transform the language selector filter into a hidden field.
                $languageXml = new \SimpleXMLElement('<field name="language" type="hidden" default="' . $forcedLanguage . '" />');
                $this->filterForm->setField($languageXml, 'filter', true);
                
                // Also, unset the active language filter so the search tools is not open by default with this filter.
                unset($this->activeFilters['language']);
            }
        }

        $this->filter              = new \stdClass();
        $this->filter->Item        = $this->escape($this->state->get('item.id'));
        $this->filter->Search      = $this->escape($this->state->get('filter.search'));
        $this->filter->Published   = $this->escape($this->state->get('filter.published'));
        $this->filter->Title       = $this->escape($this->state->get('filter.title'));
        $this->filter->Type        = $this->escape($this->state->get('filter.type'));
        $this->filter->Access      = $this->escape($this->state->get('filter.access'));
        $this->filter->Locked      = $this->escape($this->state->get('filter.locked'));
        $this->filter->Review      = $this->escape($this->state->get('filter.review'));
        $this->filter->Allow_polls = $this->escape($this->state->get('filter.allowPolls'));
        $this->filter->Anonymous   = $this->escape($this->state->get('filter.anonymous'));
        $this->filter->Active      = $this->escape($this->state->get('filter.active'));
        $this->filter->Levels      = $this->escape($this->state->get('filter.levels'));

        $this->listOrdering      = $this->escape($this->state->get('list.ordering'));
        $this->listDirection     = $this->escape($this->state->get('list.direction'));
        $this->saveOrder       = ($this->listOrdering == 'a.ordering' && $this->listDirection == 'asc');
        $this->saveOrderingUrl = 'index.php?option=com_kunena&view=categories&task=categories.saveorderajax';

        return parent::display($tpl);
    }

    /**
     * Returns an array of review filter options.
     *
     * @return  array
     *
     * @since   Kunena 6.0
     */
    protected function getSortFields(): array
    {
        $sortFields   = [];
        $sortFields[] = HTMLHelper::_('select.option', 'ordering', Text::_('COM_KUNENA_REORDER'));
        $sortFields[] = HTMLHelper::_('select.option', 'p.published', Text::_('JSTATUS'));
        $sortFields[] = HTMLHelper::_('select.option', 'p.title', Text::_('JGLOBAL_TITLE'));
        $sortFields[] = HTMLHelper::_('select.option', 'p.access', Text::_('COM_KUNENA_CATEGORIES_LABEL_ACCESS'));
        $sortFields[] = HTMLHelper::_('select.option', 'p.locked', Text::_('COM_KUNENA_LOCKED'));
        $sortFields[] = HTMLHelper::_('select.option', 'p.review', Text::_('COM_KUNENA_REVIEW'));
        $sortFields[] = HTMLHelper::_('select.option', 'p.allowPolls', Text::_('COM_KUNENA_CATEGORIES_LABEL_POLL'));
        $sortFields[] = HTMLHelper::_('select.option', 'p.anonymous', Text::_('COM_KUNENA_CATEGORY_ANONYMOUS'));
        $sortFields[] = HTMLHelper::_('select.option', 'p.id', Text::_('JGRID_HEADING_ID'));

        return $sortFields;
    }

    /**
     * Returns an array of review filter options.
     *
     * @return  array
     *
     * @since   Kunena 6.0
     */
    protected function getSortDirectionFields(): array
    {
        $sortDirection   = [];
        $sortDirection[] = HTMLHelper::_('select.option', 'asc', Text::_('JGLOBAL_ORDER_ASCENDING'));
        $sortDirection[] = HTMLHelper::_('select.option', 'desc', Text::_('JGLOBAL_ORDER_DESCENDING'));

        return $sortDirection;
    }

    /**
     * Add the page title and toolbar.
     *
     * @return  void
     *
     * @since   Kunena 6.0
     */
    protected function addToolbar(): void
    {
        $this->filterActive = $this->escape($this->state->get('filter.active'));
        $this->pagination   = $this->get('AdminNavigation');

        // Get the toolbar object instance
        $toolbar = Toolbar::getInstance();

        ToolbarHelper::title(Text::_('COM_KUNENA') . ': ' . Text::_('COM_KUNENA_CATEGORY_MANAGER'), 'list-view');
        ToolbarHelper::spacer();
        ToolbarHelper::addNew('categories.add', 'COM_KUNENA_NEW_CATEGORY');

        ToolbarHelper::editList('categories.edit');
        ToolbarHelper::divider();
        ToolbarHelper::publish('categories.publish');
        ToolbarHelper::unpublish('categories.unpublish');
        ToolbarHelper::divider();

         $dropdown = $toolbar->dropdownButton('status-group')
           ->text('JTOOLBAR_CHANGE_STATUS')
           ->toggleSplit(false)
           ->icon('icon-ellipsis-h')
           ->buttonClass('btn btn-action')
           ->listCheck(true);

        $childBar = $dropdown->getChildToolbar();

        $childBar->delete('categories.delete')->listCheck(true);

        $childBar->popupButton('batch')
           ->text('JTOOLBAR_BATCH')
           ->selector('batchCategories')
           ->listCheck(true);

        ToolbarHelper::spacer();
        $helpUrl = 'https://docs.kunena.org/en/setup/sections-categories';
        ToolbarHelper::help('COM_KUNENA', false, $helpUrl);
    }

    /**
     * Returns an array of standard published state filter options.
     *
     * @return  array The HTML code for the select tag
     *
     * @since   Kunena 6.0
     */
    public function publishedOptions(): array
    {
        // Build the active state filter options.
        $options   = [];
        $options[] = HTMLHelper::_('select.option', '1', Text::_('COM_KUNENA_FIELD_LABEL_ON'));
        $options[] = HTMLHelper::_('select.option', '0', Text::_('COM_KUNENA_FIELD_LABEL_OFF'));

        return $options;
    }

    /**
     * Returns an array of locked filter options.
     *
     * @return  array  The HTML code for the select tag
     *
     * @since   Kunena 6.0
     */
    public function lockOptions(): array
    {
        // Build the active state filter options.
        $options   = [];
        $options[] = HTMLHelper::_('select.option', '1', Text::_('COM_KUNENA_FIELD_LABEL_ON'));
        $options[] = HTMLHelper::_('select.option', '0', Text::_('COM_KUNENA_FIELD_LABEL_OFF'));

        return $options;
    }

    /**
     * Returns an array of review filter options.
     *
     * @return  array The HTML code for the select tag
     *
     * @since   Kunena 6.0
     */
    public function reviewOptions(): array
    {
        // Build the active state filter options.
        $options   = [];
        $options[] = HTMLHelper::_('select.option', '1', Text::_('COM_KUNENA_FIELD_LABEL_ON'));
        $options[] = HTMLHelper::_('select.option', '0', Text::_('COM_KUNENA_FIELD_LABEL_OFF'));

        return $options;
    }

    /**
     * Returns an array of review filter options.
     *
     * @return  array
     *
     * @since   Kunena 6.0
     */
    public function allowPollsOptions(): array
    {
        // Build the active state filter options.
        $options   = [];
        $options[] = HTMLHelper::_('select.option', '1', Text::_('COM_KUNENA_FIELD_LABEL_ON'));
        $options[] = HTMLHelper::_('select.option', '0', Text::_('COM_KUNENA_FIELD_LABEL_OFF'));

        return $options;
    }

    /**
     * Returns an array of type filter options.
     *
     * @return  array  The HTML code for the select tag
     * @since   Kunena 6.0
     */
    public function anonymousOptions(): array
    {
        // Build the active state filter options.
        $options   = [];
        $options[] = HTMLHelper::_('select.option', '1', Text::_('COM_KUNENA_FIELD_LABEL_ON'));
        $options[] = HTMLHelper::_('select.option', '0', Text::_('COM_KUNENA_FIELD_LABEL_OFF'));

        return $options;
    }
}
