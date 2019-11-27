<?php if (!defined('ABSPATH')) { exit; }

/**
 * A very early error message displayed if the required custom fields plugin is not active.
 *
 * @since   2.0.0
 * @version 2.0.0
 * @author  Fatine Tazi <f.tazi@deep-web-solutions.de>
 *
 * @see     \Deep_Web_Solutions\Core\DWS_Installation
 *
 * @var     string  $html
 */

?>

<div class="error">
    <p>
        <strong><?php echo DWS_CUSTOM_EXTENSIONS_NAME; ?></strong> <br/>
        <?php _e('Please install and activate the following plugin(s):', DWS_CUSTOM_EXTENSIONS_LANG_DOMAIN); ?>
        <?php echo $html; ?>
    </p>
</div>