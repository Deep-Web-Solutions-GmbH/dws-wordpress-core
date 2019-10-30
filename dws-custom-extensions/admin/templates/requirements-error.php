<?php if (!defined('ABSPATH')) { exit; }

/**
 * A very early error message displayed if environment requirements are not met.
 *
 * @since   1.0.0
 * @version 2.0.0
 *
 * @see     dws_requirements_error
 */

?>

<div class="error">
    <p>
        <?php
            echo sprintf(
                __('%s error: Your environment doesn\'t meet all of the system requirements listed below.', DWS_CUSTOM_EXTENSIONS_LANG_DOMAIN),
                DWS_CUSTOM_EXTENSIONS_NAME);
        ?>
    </p>

    <ul class="ul-disc">
        <li>
            <strong>PHP <?php echo DWS_CUSTOM_EXTENSIONS_MIN_PHP; ?>+</strong>
            <em><?php echo sprintf(__('You\'re running version %s', DWS_CUSTOM_EXTENSIONS_LANG_DOMAIN), PHP_VERSION); ?></em>
        </li>

        <li>
            <strong>WordPress <?php echo DWS_CUSTOM_EXTENSIONS_MIN_WP; ?>+</strong>
            <em><?php echo sprintf(__('You\'re running version %s', DWS_CUSTOM_EXTENSIONS_LANG_DOMAIN), esc_html($GLOBALS['wp_version'])); ?></em>
        </li>
    </ul>

    <p>
        <?php
            _e('If you need to upgrade your version of PHP you can ask your hosting company for assistance, and if you need help upgrading WordPress you can refer to <a href="http://codex.wordpress.org/Upgrading_WordPress">the Codex</a>.', DWS_CUSTOM_EXTENSIONS_LANG_DOMAIN);
        ?>
    </p>
</div>