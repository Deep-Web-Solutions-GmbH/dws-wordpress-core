<?php if (!defined('ABSPATH')) { exit; }

/**
 * A very early error message displayed if environment requirements are not met.
 *
 * @since   1.0.0
 * @version 1.0.0
 *
 * @see     dws_requirements_error
 */

?>

<div class="error">
    <p>
		<?php echo DWS_CUSTOM_EXTENSIONS_NAME; ?> error: Your environment doesn't meet all of the system requirements
        listed below.
    </p>

    <ul class="ul-disc">
        <li>
            <strong>PHP <?php echo DWS_CUSTOM_EXTENSIONS_MIN_PHP; ?>+</strong>
            <em>(You're running version <?php echo PHP_VERSION; ?>)</em>
        </li>

        <li>
            <strong>WordPress <?php echo DWS_CUSTOM_EXTENSIONS_MIN_WP; ?>+</strong>
            <em>(You're running version <?php echo esc_html($GLOBALS['wp_version']); ?>)</em>
        </li>
    </ul>

    <p>
        If you need to upgrade your version of PHP you can ask your hosting company for assistance,
        and if you need help upgrading WordPress you can refer to <a
                href="http://codex.wordpress.org/Upgrading_WordPress">the Codex</a>.
    </p>
</div>