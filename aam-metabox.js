/**
 * Script handles author selection on the post edit page.
 */
jQuery(document).ready(function($) {

	$('#post_author_override_label').autocomplete({
		source:    author_metabox_params.ajax_url + '?action=author_search&nonce=' + author_metabox_params.nonce,
		delay:     500,
		minLength: 3,
		open: function() {
			$(this).addClass( 'open' );
		},
		close: function() {
			$(this).removeClass( 'open' );
		},
		select: function (event, ui) {
			// Set autocomplete element to display the label
			this.value = ui.item.label;

			// Store value in hidden field
			$('#post_author_override').val(ui.item.value);

			// Prevent default behaviour
			return false;
		}
	});

});
