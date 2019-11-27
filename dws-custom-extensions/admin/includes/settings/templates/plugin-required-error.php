<?php

if (!defined('ABSPATH')) { exit; }

/**
 * A very early error message displayed if no custom fields plugin was chosen.
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
        <?php
            echo sprintf(
                __('%s error: Please choose a custom fields plugin in the DWS Settings page. Do not forget to install and activate it.', DWS_CUSTOM_EXTENSIONS_LANG_DOMAIN),
                DWS_CUSTOM_EXTENSIONS_NAME);
        ?>
    </p>
</div>