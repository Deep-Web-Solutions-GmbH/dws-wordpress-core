<?php

if (!defined('ABSPATH')) { exit; }

/**
 * A very early error message displayed if the required custom fields plugin is not active.
 *
 * @since   2.0.0
 * @version 2.0.0
 * @author  Fatine Tazi <f.tazi@deep-web-solutions.de>
 *
 * @see     \Deep_Web_Solutions\Core\DWS_Installation
 */

?>

<div class="error">
    <p>
        <?php echo DWS_CUSTOM_EXTENSIONS_NAME; ?> Please install and activate  <?php echo get_option('dws-core_settings-framework', false); ?> plugin(s).
    </p>
</div>