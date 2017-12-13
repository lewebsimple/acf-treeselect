(function ($) {

  function initialize_field ($el) {
    $el.find('select').each(function () {

      // Store previous value
      $(this).data('previous', this.value);

      // Monitor for selection changes
      $(this).on('change', function (e) {
        let index = this.name.lastIndexOf('[value]');

        // Hide and reset value previous selection's child input
        let toHide = this.name.substr(0, index) + '[' + $(this).data('previous') + ']' + this.name.substr(index);
        $el.find('select[name="' + toHide + '"]').each(function () {
          $(this).val('');
          $(this).hide();
          $(this).prop('disabled', true);
        });

        // Show latest selection
        if (this.value !== '') {
          let toShow = this.name.substr(0, index) + '[' + this.value + ']' + this.name.substr(index);
          $el.find('select[name="' + toShow + '"]').each(function () {
            $(this).show();
            $(this).prop('disabled', false);
          });
        }

        // Update previous value
        $(this).data('previous', this.value);

      });

    });
  }

  if (typeof acf.add_action !== 'undefined') {
    // ACF 5
    acf.add_action('ready append', function ($el) {
      acf.get_fields({type: 'treeselect'}, $el).each(function () {
        initialize_field($(this));
      });
    });
  } else {
    // ACF 4
    $(document).on('acf/setup_fields', function (e, postbox) {
      $(postbox).find('.field[data-field_type="treeselect"]').each(function () {
        initialize_field($(this));
      });
    });
  }

})(jQuery);
