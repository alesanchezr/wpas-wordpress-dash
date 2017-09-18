

function GFSBToggleButton() {

	var isText = jQuery("#form_button_text").is(":checked"),
		isImage = jQuery("#form_button_image").is(":checked"),
		isButton = jQuery("#form_button_html").is(":checked"),
		textRow = jQuery("#form_button_text_setting"),
		imageRow = jQuery("#form_button_image_path_setting"),
		buttonRow = jQuery("#form_button_html_setting");

	textRow.add(imageRow).add(buttonRow).hide();

	if ( isText ) {
		textRow.fadeIn();
	}

	if ( isImage ) {
		imageRow.fadeIn();
	}

	if ( isButton ) {
		buttonRow.fadeIn();
	}

}

