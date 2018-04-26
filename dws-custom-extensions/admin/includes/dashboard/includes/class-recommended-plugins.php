<?php

namespace Deep_Web_Solutions\Admin\Dashboard;
if (!defined('ABSPATH')) { exit; }

if (!class_exists('WP_List_Table')) {
	require_once(ABSPATH . 'wp-admin/includes/screen.php');
	require_once(ABSPATH . 'wp-admin/includes/class-wp-list-table.php');
}

final class DWS_Plugins_List_Table extends \WP_List_Table {

}