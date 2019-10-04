(function ($) {
  'use strict';

  /**
   * Add weekday selection based on date range.
   */
  Drupal.behaviors.recurring_events_weekday_selection = {
    attach: function(context, settings) {
      var weekdays = new Array(7);
      weekdays[0] = "sunday";
      weekdays[1] = "monday";
      weekdays[2] = "tuesday";
      weekdays[3] = "wednesday";
      weekdays[4] = "thursday";
      weekdays[5] = "friday";
      weekdays[6] = "saturday";

      // When the weekly occurrence start date is changed.
      $('#edit-weekly-recurring-date-0-value-date').on('change', function(event) {
        var value = $(this).val();
        var date_parts = value.split('-');
        if (date_parts.length > 0) {
          var date = new Date(date_parts[0], date_parts[1] - 1, date_parts[2]);
          var weekday = weekdays[date.getDay()];

          // Remove all the weekday recurrence options.
          $('#edit-weekly-recurring-date-0-days').find('input').each(function(key, item) {
            $(item).prop('checked', false);
          });

          // Set the event to recur on the same day of the week as the start
          // date.
          $('#edit-weekly-recurring-date-0-days-' + weekday).prop('checked', true);
        }
      });

      // When the monthly occurrence start date is changed.
      $('#edit-monthly-recurring-date-0-value-date').on('change', function(event) {
        var value = $(this).val();
        var date_parts = value.split('-');
        if (date_parts.length > 0) {
          var date = new Date(date_parts[0], date_parts[1] - 1, date_parts[2]);
          var weekday = weekdays[date.getDay()];

          // Remove all the monthly recurrence options.
          $('#edit-monthly-recurring-date-0-days').find('input').each(function(key, item) {
            $(item).prop('checked', false);
          });

          // Set the event to recur on the same day of the week as the start
          // date.
          $('#edit-monthly-recurring-date-0-days-' + weekday).prop('checked', true);
        }
      });
    }
  };

  /**
   * Set end date for excluded and included dates to be the same as the start.
   */
  Drupal.behaviors.recurring_events_excluded_included_dates = {
    attach: function(context, settings) {
      $('#edit-excluded-dates-wrapper, #edit-included-dates-wrapper').find('input.form-date').once().on('change', function(e) {
        if ($(this).attr('name').includes('[value][date]')) {
          var start_date = this;
          var parent = $(this).closest('.form-wrapper');
          $(parent).find('input.form-date').each(function(index, item) {
            if (index == 1) {
              if ($(item).val() == '') {
                $(item).val($(start_date).val());
              }
            }
          });
        }
      });
    }
  };

}(jQuery));