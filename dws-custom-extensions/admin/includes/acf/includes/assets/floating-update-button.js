/**
 * @author  Dushan Terzikj  <d.terzikj@deep-web-solutions.de>
 *
 * @since   1.3.2
 * @version 1.0.0
 *
 * Sticks another fixed update button which stays on the screen as you scroll up and down.
 */
jQuery(document).ready(function ($) {
    $('#publishing-action').append(
        '<input type="submit" accesskey="p" value="Update" class="dws_floating-update-button button button-large button-primary" id="publish" name="publish">'
    );
});