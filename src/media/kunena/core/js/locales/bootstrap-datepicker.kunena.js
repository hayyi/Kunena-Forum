/**
 * Kunena Component
 * @package Kunena.Media
 *
 * @copyright     Copyright (C) 2008 - 2024 Kunena Team. All rights reserved.
 * @license https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link https://www.kunena.org
 **/
;(function ($) {
    const Joomla = window.Joomla;
    if (Joomla !== undefined) {
        $.fn.datepicker.dates['kunena'] = {
            days: [Joomla.Text._('COM_KUNENA_BOOTSTRAP_DATEPICKER_DAYS_SUNDAY'), Joomla.Text._('COM_KUNENA_BOOTSTRAP_DATEPICKER_DAYS_MONDAY'), Joomla.Text._('COM_KUNENA_BOOTSTRAP_DATEPICKER_DAYS_TUESDAY'), Joomla.Text._('COM_KUNENA_BOOTSTRAP_DATEPICKER_DAYS_WEDNESDAY'), Joomla.Text._('COM_KUNENA_BOOTSTRAP_DATEPICKER_DAYS_THURSDAY'), Joomla.Text._('COM_KUNENA_BOOTSTRAP_DATEPICKER_DAYS_FRIDAY'), Joomla.Text._('COM_KUNENA_BOOTSTRAP_DATEPICKER_DAYS_SATURDAY')],
            daysShort: [Joomla.Text._('COM_KUNENA_BOOTSTRAP_DATEPICKER_DAYSSHORT_SUNDAY'), Joomla.Text._('COM_KUNENA_BOOTSTRAP_DATEPICKER_DAYSSHORT_MONDAY'), Joomla.Text._('COM_KUNENA_BOOTSTRAP_DATEPICKER_DAYSSHORT_TUESDAY'), Joomla.Text._('COM_KUNENA_BOOTSTRAP_DATEPICKER_DAYSSHORT_WEDNESDAY'), Joomla.Text._('COM_KUNENA_BOOTSTRAP_DATEPICKER_DAYSSHORT_THURSDAY'), Joomla.Text._('COM_KUNENA_BOOTSTRAP_DATEPICKER_DAYSSHORT_FRIDAY'), Joomla.Text._('COM_KUNENA_BOOTSTRAP_DATEPICKER_DAYSSHORT_SATURDAY')],
            daysMin: [Joomla.Text._('COM_KUNENA_BOOTSTRAP_DATEPICKER_DAYSMIN_SUNDAY'), Joomla.Text._('COM_KUNENA_BOOTSTRAP_DATEPICKER_DAYSMIN_MONDAY'), Joomla.Text._('COM_KUNENA_BOOTSTRAP_DATEPICKER_DAYSMIN_TUESDAY'), Joomla.Text._('COM_KUNENA_BOOTSTRAP_DATEPICKER_DAYSMIN_WEDNESDAY'), Joomla.Text._('COM_KUNENA_BOOTSTRAP_DATEPICKER_DAYSMIN_THURSDAY'), Joomla.Text._('COM_KUNENA_BOOTSTRAP_DATEPICKER_DAYSMIN_FRIDAY'), Joomla.Text._('COM_KUNENA_BOOTSTRAP_DATEPICKER_DAYSMIN_SATURDAY')],
            months: [Joomla.Text._('COM_KUNENA_BOOTSTRAP_DATEPICKER_MONTHS_JANUARY'), Joomla.Text._('COM_KUNENA_BOOTSTRAP_DATEPICKER_MONTHS_FEBRUARY'), Joomla.Text._('COM_KUNENA_BOOTSTRAP_DATEPICKER_MONTHS_MARCH'), Joomla.Text._('COM_KUNENA_BOOTSTRAP_DATEPICKER_MONTHS_APRIL'), Joomla.Text._('COM_KUNENA_BOOTSTRAP_DATEPICKER_MONTHS_MAY'), Joomla.Text._('COM_KUNENA_BOOTSTRAP_DATEPICKER_MONTHS_JUNE'), Joomla.Text._('COM_KUNENA_BOOTSTRAP_DATEPICKER_MONTHS_JULY'), Joomla.Text._('COM_KUNENA_BOOTSTRAP_DATEPICKER_MONTHS_AUGUST'), Joomla.Text._('COM_KUNENA_BOOTSTRAP_DATEPICKER_MONTHS_SEPTEMBER'), Joomla.Text._('COM_KUNENA_BOOTSTRAP_DATEPICKER_MONTHS_OCTOBER'), Joomla.Text._('COM_KUNENA_BOOTSTRAP_DATEPICKER_MONTHS_NOVEMBER'), Joomla.Text._('COM_KUNENA_BOOTSTRAP_DATEPICKER_MONTHS_DECEMBER')],
            monthsShort: [Joomla.Text._('COM_KUNENA_BOOTSTRAP_DATEPICKER_MONTH_SHORT_JANUARY'), Joomla.Text._('COM_KUNENA_BOOTSTRAP_DATEPICKER_MONTH_SHORT_FEBRUARY'), Joomla.Text._('COM_KUNENA_BOOTSTRAP_DATEPICKER_MONTH_SHORT_MARCH'), Joomla.Text._('COM_KUNENA_BOOTSTRAP_DATEPICKER_MONTH_SHORT_APRIL'), Joomla.Text._('COM_KUNENA_BOOTSTRAP_DATEPICKER_MONTH_SHORT_MAY'), Joomla.Text._('COM_KUNENA_BOOTSTRAP_DATEPICKER_MONTH_SHORT_JUNE'), Joomla.Text._('COM_KUNENA_BOOTSTRAP_DATEPICKER_MONTH_SHORT_JULY'), Joomla.Text._('COM_KUNENA_BOOTSTRAP_DATEPICKER_MONTH_SHORT_AUGUST'), Joomla.Text._('COM_KUNENA_BOOTSTRAP_DATEPICKER_MONTH_SHORT_SEPTEMBER'), Joomla.Text._('COM_KUNENA_BOOTSTRAP_DATEPICKER_MONTH_SHORT_OCTOBER'), Joomla.Text._('COM_KUNENA_BOOTSTRAP_DATEPICKER_MONTH_SHORT_NOVEMBER'), Joomla.Text._('COM_KUNENA_BOOTSTRAP_DATEPICKER_MONTH_SHORT_DECEMBER')],
            today: Joomla.Text._('COM_KUNENA_BOOTSTRAP_DATEPICKER_TODAY'),
            monthsTitle: Joomla.Text._('COM_KUNENA_BOOTSTRAP_DATEPICKER_MONTHS_TITLE'),
            clear: Joomla.Text._('COM_KUNENA_BOOTSTRAP_DATEPICKER_CLEAR'),
            weekStart: 1,
            format: "dd/mm/yyyy"
        };
    }
}(jQuery));
