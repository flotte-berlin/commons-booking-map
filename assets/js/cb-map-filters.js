function CB_Map_Filters($, cb_map) {

  this.init = function($) {
    var that = this;

    var $filter_container = $('<div class="cb-map-filters"></div>');

    var show_item_availability_filter =  cb_map.settings.show_item_availability_filter;
    var show_cb_item_categories_filter = Object.keys(cb_map.settings.filter_cb_item_categories).length > 0

    if(show_item_availability_filter || show_cb_item_categories_filter) {
      var $form = $('<form></form');
      var $filter_options = $('<div class="cb-filter-options"></div>');

      if(show_item_availability_filter) {
        this.init_availability_filter($, $filter_options);
      }

      if(show_cb_item_categories_filter) {
        this.init_category_filter($, $filter_options);
      }

      $form.append($filter_options);

      var $button = $('<button>' + cb_map.translation['FILTER'] + '</button>');

      $button.click(function(event) {
        event.preventDefault();

        var filters = {
          cb_item_categories: []
        };
        var data = $form.serializeArray();
        data.forEach(function(obj) {
          if(obj.name.indexOf('cb_item_categories') > -1) {
            filters.cb_item_categories.push(obj.value);
          }
          else {
            filters[obj.name] = obj.value;
          }
        });

        console.log('filters: ', filters);

        //set default values
        if(that.do_availability_check(filters)) {
          if(!filters.date_start) {
            filters.date_start = $('input[name="date_start"]').attr('min');
          }

          if(!filters.date_end) {
            filters.date_end = $('input[name="date_end"]').attr('max');
          }

          if(!filters.day_count) {
            filters.day_count = $('select[name="day_count"]').lastChild('option').val();
          }
        }

        //TODO: ensure date_start < date_end

        var location_data = JSON.parse(JSON.stringify(cb_map.location_data)); //TODO: use a more efficient way of object cloning
        location_data = that.apply_filters(location_data, filters);

        if(cb_map.markers) {
          cb_map.markers.clearLayers();
        }

        cb_map.render_locations(location_data);
      });

      $button_wrapper = $('<div class="cb-map-button-wrapper"></div>');
      $button_wrapper.append($button);
      $form.append($button_wrapper);

      $filter_container.append($form);
      $filter_container.insertAfter($('#cb-map-' + cb_map.settings.cb_map_id));
    }
  }

  this.do_availability_check = function(filters) {
    return filters.date_start.length > 0 || filters.date_end.length > 0 || filters.day_count > 0;
  }

  this.init_availability_filter = function($, $filter_options) {
    if(cb_map.settings.show_item_availability_filter) {

      var $container = $('<div><div class="cb-map-filter-group-label">' + cb_map.translation['AVAILABILITY'] + '</div></div>');
      var $wrapper = $('<div class="cb-map-filter-group"></div>');
      $container.append($wrapper);

      var $date_start_input = $('<input type="date" name="date_start" min="' + cb_map.settings.filter_availability.date_min + '" max="' + cb_map.settings.filter_availability.date_max + '">');
      var $date_end_input = $('<input type="date" name="date_end" min="' + cb_map.settings.filter_availability.date_min + '" max="' + cb_map.settings.filter_availability.date_max + '">');
      var $day_count_select = $('<select name="day_count"></select>')
      for(var d = 0; d <= cb_map.settings.filter_availability.day_count_max; d++) {
        var show_value = d == 0 ? '-' : d;
        $day_count_select.append('<option value="' + d + '">' + show_value + '</option>')
      }

      $wrapper.append('<label>' + cb_map.translation['FROM'] + '</label>'); //TODO: translate label texts
      $wrapper.append($date_start_input);
      $wrapper.append('<label>' + cb_map.translation['UNTIL'] + '</label>');
      $wrapper.append($date_end_input);
      $wrapper.append('<label>' + cb_map.translation['AT_LEAST'] + '</label>');
      $wrapper.append($day_count_select);
      $wrapper.append('<label>' + cb_map.translation['DAYS'] + '</label>');
    }

    $filter_options.append($container);
  },

  this.init_category_filter = function($, $filter_options) {
    var $container = $('<div><div class="cb-map-filter-group-label">' + cb_map.translation['CATEGORIES'] + '</div></div>');
    var $wrapper = $('<div class="cb-map-filter-group"></div>');
    $container.append($wrapper);

    $.each(cb_map.settings.filter_cb_item_categories, function(group_index, group) {
      var $fieldset = $('<fieldset></fieldset>');
      if(group.name.length > 0) {
        $fieldset.append('<legend>' + group.name + '</legend>');
      }

      $.each(group.elements, function(element_index, category) {
        var $input = $('<input type="checkbox" name="cb_item_categories[]" value="' + category.cat_id + '">')
        var $label = $('<label></label>');
        $label.html(category.markup);
        $fieldset.append($input);
        $fieldset.append($label);
      });

      $wrapper.append($fieldset);
    });

    $filter_options.append($container);
  }

  this.apply_filters = function(location_data, filters) {
    var that = this;
    console.log('apply_filters: ', filters)

    //availability filters
    if(this.do_availability_check(filters)) {
      location_data = this.apply_item_availability_filters(location_data, filters);
    }

    //item category filters
    location_data = this.apply_item_category_filters(location_data, filters);

    console.log(location_data);

    return location_data;
  }

  this.apply_item_category_filters = function(location_data, filters) {
    console.log('apply_item_category_filters');

    var user_categories = filters.cb_item_categories;
    var filtered_locations = [];
    var that = this;

    //prepare category groups array
    var category_groups = [];
    var filter_cb_item_categories = cb_map.settings.filter_cb_item_categories;
    Object.keys(filter_cb_item_categories).forEach(function(groupId) {
      var group_elements = filter_cb_item_categories[groupId].elements;
      var group = [];

      group_elements.forEach(function(group_element) {
        group.push(group_element.cat_id);
      });

      category_groups.push(group);
    });

    //console.log('category_groups: ', category_groups);
    //console.log('user_categories: ', user_categories);

    //filter out category groups that are not present in user categories (because these have to be ignored)
    var filtered_category_groups = [];
    category_groups.forEach(function(category_group) {
      var filtered_group = [];

      category_group.forEach(function(category) {
        if(user_categories.includes(category.toString())) {
          filtered_group.push(category);
        }
      });

      if(filtered_group.length > 0) {
        filtered_category_groups.push(filtered_group);
      }

    });

    //filter the items
    location_data.forEach(function(location) {
      var items = location.items;
      location.items = [];
      items.forEach(function(item, item_index) {
        var is_valid = that.check_item_terms_against_categories(item.terms, filtered_category_groups);
        if(is_valid) {
          location.items.push(item);
        }
      });
    });

    location_data.forEach(function(location) {
      if(location.items.length > 0) {
        filtered_locations.push(location);
      }
    });

    return filtered_locations;

  }

  this.check_item_terms_against_categories = function(item_terms, category_groups) {
    var valid_groups_count = 0;

    category_groups.forEach(function(group) {
      for(var i = 0; i < item_terms.length; i++) {
        var term = item_terms[i];

        if(group.includes(term)) {
          valid_groups_count++;
          break;
        }
      };
    });

    return valid_groups_count == Object.keys(category_groups).length;
  }

  this.apply_item_availability_filters = function(location_data, filters) {
    console.log('apply_item_availability_filters');

    var that = this;
    var filtered_locations = [];

    location_data.forEach(function(location) {
      var items = location.items;
      location.items = [];
      items.forEach(function(item) {
        item.availability = that.reduce_availability(item.availability, filters.date_start, filters.date_end);
        var max_free_days_in_row = that.get_max_free_days_in_row(item.availability);

        if(max_free_days_in_row >= filters.day_count) {
          location.items.push(item);
        }
      });
    });

    //only show locations with items
    location_data.forEach(function(location) {
      if(location.items.length > 0) {
        filtered_locations.push(location);
      }
    });

    return filtered_locations;
  }

  this.reduce_availability = function(availability, date_start, date_end) {
    var inside = false;
    var updated_availability = [];

    for(var d = 0; d < availability.length; d++) {
      var day = availability[d];

      if(day.date === date_start) {
        inside = true;
      }

      if(inside) {
        updated_availability.push(day)
      }

      if(day.date === date_end) {
        inside = false;
      }
    }

    return updated_availability;
  }

  /**
  convert availability to sequences to ease up counting of free days in a row
  */
  this.calc_availability_sequences = function(availability) {
    var availability_sequences = [];

    availability.forEach(function(day) {
      if(availability_sequences.length == 0) {
        availability_sequences.push({
          status: day.status,
          count: 1
        });
      }
      else {
        if(availability_sequences[availability_sequences.length - 1].status == day.status) {
          availability_sequences[availability_sequences.length - 1].count++;
        }
        else {
          availability_sequences.push({
            status: day.status,
            count: 1
          });
        }
      }
    });

    return availability_sequences;
  }

  this.get_max_free_days_in_row = function(availability) {

    var availability_sequences = this.calc_availability_sequences(availability);

    var max_free_days_in_row = 0;
    var current_free_days_in_row = 0;

    availability_sequences.forEach(function(availability_sequence, seq_id) {
      if(availability_sequence.status == 0) {
        current_free_days_in_row += availability_sequence.count;
      }

      //closing days (value == 1) count only if days before & after are free (value == 0)
      if(availability_sequence.status == 1) {
        if(seq_id > 0 && availability_sequences[seq_id - 1].status == 0 && availability_sequences[seq_id + 1] && availability_sequences[seq_id + 1].status == 0) {
          current_free_days_in_row += availability_sequence.count;
        }
      }

      //a row of free days end with a sequence status > LOCATION_CLOSED or end of $availability_sequences
      if(availability_sequence.status > 1 || seq_id == availability_sequences.length - 1) {
        if(max_free_days_in_row < current_free_days_in_row) {
          max_free_days_in_row = current_free_days_in_row
        }
        current_free_days_in_row = 0;
      }
    });

    return max_free_days_in_row;
  }

  this.init($, cb_map);
}
