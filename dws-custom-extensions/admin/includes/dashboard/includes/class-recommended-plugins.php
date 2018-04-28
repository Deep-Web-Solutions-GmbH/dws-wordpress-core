<?php

namespace Deep_Web_Solutions\Admin\Dashboard;
if (!defined('ABSPATH')) { exit; }

if (!class_exists('WP_List_Table')) {
	require_once(ABSPATH . 'wp-admin/includes/screen.php');
	require_once(ABSPATH . 'wp-admin/includes/class-wp-list-table.php');
}

/**
 * Provides the logic behind the content and the actions of the recommended plugins DWS dashboard page.
 *
 * @since   1.2.0
 * @version 1.2.0
 *
 * @author  Antonius Cezar Hegyes <a.hegyes@deep-web-solutions.de>
 */
final class DWS_Plugins_List_Table extends \WP_List_Table {
	/**
	 * DWS_Plugins_List_Table constructor.
	 *
	 * @since   1.2.0
	 * @version 1.2.0
	 *
	 * @see     \WP_List_Table::__construct()
	 */
	public function __construct() {
		parent::__construct(
			array(
				'singular'  => 'plugin',
				'plural'    => 'plugins',
				'ajax'      => false
            )
		);
	}

	/**
	 * Get a list of columns.
	 *
	 * @since   1.2.0
	 * @version 1.2.0
	 *
	 * @see     \WP_List_Table::get_columns()
	 * @see     \WP_List_Table::single_row_columns()
	 *
	 * @return  array   An associative array containing column information.
	 */
	public function get_columns() {
		return array(
			'cb'    		        => '<input type="checkbox" />', // render a checkbox instead of text
			'dws_plugin_name'       => _x('Name', 'Recommended plugins', DWS_CUSTOM_EXTENSIONS_LANG_DOMAIN),
			'dws_plugin_source'     => _x('Source', 'Recommended plugins', DWS_CUSTOM_EXTENSIONS_LANG_DOMAIN),
			'dws_plugin_version'    => _x('Version', 'Recommended plugins', DWS_CUSTOM_EXTENSIONS_LANG_DOMAIN),
			'dws_plugin_status'     => _x('Description', 'Recommended plugins', DWS_CUSTOM_EXTENSIONS_LANG_DOMAIN)
		);
	}

	/**
	 * Get a list of sortable columns.
	 *
	 * @since   1.2.0
	 * @version 1.2.0
	 *
	 * @see     \WP_List_Table::get_sortable_columns()
	 *
	 * @return  array   An associative array containing all the columns that should be sortable.
	 */
	public function get_sortable_columns() {
		return array(
			'dws_plugin_name'   => array('dws_plugin_name', false),
			'dws_plugin_source' => array('dws_plugin_source', false),
			'dws_plugin_status' => array('dws_plugin_status', false)
		);
	}

	/**
	 * Get default column value.
	 *
	 * @since   1.2.0
	 * @version 1.2.0
	 *
	 * @see     \WP_List_Table::column_default()
	 *
	 * @param   object  $item           A singular item (one full row's worth of data).
	 * @param   string  $column_name    The name/slug of the column to be processed.
	 *
	 * @return  string  Text or HTML to be placed inside the column <td>.
	 */
	protected function column_default($item, $column_name) {
		switch($column_name) {
			case 'dws_plugin_source':
			case 'dws_plugin_status':
				return $item[$column_name];
			default:
				return print_r($item, true); // show the whole array for troubleshooting purposes
		}
	}

	/**
	 * Get value for checkbox column.
	 *
	 * @since   1.2.0
	 * @version 1.2.0
	 *
	 * @see     \WP_List_Table::column_cb()
	 *
	 * @param   object  $item   A singular item (one full row's worth of data).
	 *
	 * @return  string  Text to be placed inside the column <td>.
	 */
	protected function column_cb($item) {
		return sprintf(
			'<input type="checkbox" name="%1$s[]" value="%2$s" />',
			$this->_args['singular'],  // let's simply repurpose the table's singular label ("plugin")
			$item['ID']                // the value of the checkbox should be the record's ID
		);
	}

	/**
	 * Get name column value.
	 *
	 * @since   1.2.0
	 * @version 1.2.0
	 *
	 * @param   object  $item   A singular item (one full row's worth of data).
	 *
	 * @return  string  Text to be placed inside the column <td>.
	 */
	protected function column_dws_plugin_name($item) {
		$actions = array();
		$page    = wp_unslash( $_REQUEST['page'] ); // WPCS: Input var ok.

		// build delete row action
//		$delete_query_args = array(
//			'page'   => $page,
//			'action' => 'delete',
//			'movie'  => $item['ID'],
//		);
//		$actions['delete'] = sprintf(
//			'<a href="%1$s">%2$s</a>',
//			esc_url( wp_nonce_url( add_query_arg( $delete_query_args, 'admin.php' ), 'deletemovie_' . $item['ID'] ) ),
//			_x( 'Delete', 'List table row action', 'wp-list-table-example' )
//		);

		// return the title contents.
		return sprintf( '%1$s <span style="color:silver;">(id:%2$s)</span>%3$s',
		                $item['dws_plugin_name'],
		                $item['ID'],
		                $this->row_actions( $actions )
		);
	}

	/**
	 * Get an associative array ( option_name => option_title ) with the list
	 * of bulk actions available on this table.
	 *
	 * @since   1.2.0
	 * @version 1.2.0
	 *
	 * @see     \WP_List_Table::get_bulk_actions()
	 *
	 * @return  array   An associative array containing all the bulk actions.
	 */
	protected function get_bulk_actions() {
		return array(
			'install'   => _x('Install', 'Recommended plugins', DWS_CUSTOM_EXTENSIONS_LANG_DOMAIN),
			'update'    => _x('Update', 'Recommended plugins', DWS_CUSTOM_EXTENSIONS_LANG_DOMAIN),
			'delete'    => _x('Delete', 'Recommended plugins', DWS_CUSTOM_EXTENSIONS_LANG_DOMAIN)
		);
	}

	/**
	 * Handle bulk actions.
	 *
	 * @since   1.2.0
	 * @version 1.2.0
	 *
	 * @see     DWS_Plugins_List_Table::process_bulk_action()
	 */
	protected function process_bulk_action() {
		switch($this->current_action()) {
			case 'install':
			case 'update':
			case 'delete':
		}
	}

	/**
	 * Prepares the list of items for displaying.
	 *
	 * @since   1.2.0
	 * @version 1.2.0
	 *
	 * @see     \WP_List_Table::prepare_items()
	 * @see     DWS_Plugins_List_Table::$_column_headers
	 * @see     DWS_Plugins_List_Table::$items
	 * @see     DWS_Plugins_List_Table::get_columns()
	 * @see     DWS_Plugins_List_Table::get_sortable_columns()
	 * @see     DWS_Plugins_List_Table::get_pagenum()
	 * @see     DWS_Plugins_List_Table::set_pagination_args()
	 */
	public function prepare_items() {
		// process any bulk actions
		$this->process_bulk_action();

		// output regular table
		$this->_column_headers = array($this->get_columns(), array(), $this->get_sortable_columns());

		$data = file_get_contents('https://config.deep-web-solutions.de/wp-plugins.json');
		var_dump($data);
	}

	/**
	 * Callback to allow sorting of plugins.
	 *
	 * @since   1.2.0
	 * @version 1.2.0
	 *
	 * @param   string  $a  First value.
	 * @param   string  $b  Second value.
	 *
	 * @return  int     Less than 0 if first value smaller than second, 0 if equal, and greater than 0 otherwise.
	 */
	protected function usort_reorder( $a, $b ) {
		// If no sort, default to title.
		$orderby = ! empty( $_REQUEST['orderby'] ) ? wp_unslash( $_REQUEST['orderby'] ) : 'dws_plugin_name'; // WPCS: Input var ok.

		// If no order, default to asc.
		$order = ! empty( $_REQUEST['order'] ) ? wp_unslash( $_REQUEST['order'] ) : 'asc'; // WPCS: Input var ok.

		// Determine sort order.
		$result = strcmp( $a[ $orderby ], $b[ $orderby ] );

		return ( 'asc' === $order ) ? $result : - $result;
	}
}