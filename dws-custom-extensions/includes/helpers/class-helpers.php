<?php

namespace Deep_Web_Solutions\Helpers;
use Deep_Web_Solutions\Front\DWS_Public;

if (!defined('ABSPATH')) { exit; }

/**
 * A collection of very useful helper functions to be used throughout the project.
 *
 * @since   1.0.0
 * @version 1.0.0
 * @author  Antonius Cezar Hegyes <a.hegyes@deep-web-solutions.de>
 */
final class DWS_Helper {
	/**
	 * Allows for the theme to overwrite templates from DWS modules.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @param   string  $slug   Template slug.
	 * @param   string  $name   Template name.
	 * @param   string  $path   Relative path inside the plugin's/theme's directory.
	 */
	public static function get_template_part($slug, $name, $path) {
		$path = trailingslashit($path);

		$template = locate_template($path . "{$slug}-{$name}.php");
		if (!$template) {
			$template = DWS_CUSTOM_EXTENSIONS_BASE_PATH . $path . "templates/{$slug}-{$name}.php";
		}

		load_template($template, false);
	}

	/**
	 * Extracts headers from overridable templates. It searches for "DWSComment:" and adds the following text as an
	 * instruction in the admin options for what the template is used for.
	 *
	 * @since       1.3.2
	 * @version     1.3.2
	 *
	 * @author      Dushan Terzikj <d.terzikj@deep-web-solutions.de>
	 *
	 * @param       string  $file   The file which is searched.
	 *
	 * @return      bool|string     The template instruction if exists, false otherwise.
	 */
	public static function extract_file_header($file){
		if ($file_content = fopen($file, 'r')) {
			while (!feof($file_content)) {
				$line = fgets($file_content);
				$comment_start_pos = strpos($line, 'DWSComment:');
				if ($comment_start_pos) {
					/* $comment_start_pos+11 is the start position of the text */
					$comment_start_pos = $comment_start_pos + 11;
					return trim(substr($line, $comment_start_pos));

				}
			}
		}

		return false;
	}

	/**
	 * Gets a list of all the files that exist in a certain directory recursively.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @param   string      $directory      The directory to search through.
	 * @param   int         $depth          Current recursion depth.
	 *
	 * @return  array   List of the relative paths of all the files inside the directory.
	 */
	public static function list_files($directory, $depth = 0) {
		$directory = trailingslashit($directory);
		if (!is_dir($directory)) {
			return array();
		}

		$template_files = scandir($directory);
		unset($template_files[array_search('.', $template_files, true)]);
		unset($template_files[array_search('..', $template_files, true)]);

		if (count($template_files) < 1) {
			return array();
		}

		$files = call_user_func_array(
			'array_merge',
			array_map(
				function ($template_file) use ($directory, $depth) {
					if ($template_file === 'index.php') {
						return array();
					}

					return is_dir(trailingslashit($directory) . $template_file)
						? DWS_Helper::list_files(trailingslashit($directory) . $template_file, $depth + 1)
						: array(trailingslashit($directory) . $template_file);
				},
				$template_files
			)
		);

		// before returning for good, get rid of the original file path
		if ($depth === 0) {
			$files = array_map(
				function ($file) use ($directory) {
					return str_replace($directory, '', $file);
				},
				$files
			);
		}

		return $files;
	}

	/**
	 * Iterates through all the files in a certain directory and loads all of them.
	 * On top of that, it loads also all '.php' files in sub-directories which have
	 * the same name as the directory itself.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @param   string  $directory  The path to the directory from which the files should be loaded from.
	 */
	public static function load_files($directory) {
		$directory = trailingslashit($directory);
		$files     = self::list_files($directory);

		foreach ($files as $file) {
			if ($file === basename($file) || ltrim(dirname($file), '/') === basename($file, '.php')) {
				/** @noinspection PhpIncludeInspection */
				require_once($directory . $file);
			}
		}
	}

	/**
	 * Returns the content of a file inside CSS style tags.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @param   string  $path   The path to the CSS file with the content.
	 * @param   bool    $echo   True if the content should be echoed, false if returned.
	 *
	 * @return  bool|string     True if the content should be echoed, the content itself otherwise.
	 */
	public static function get_stylesheet($path, $echo = false) {
		ob_start();

		echo '<style type="text/css">';
		/** @noinspection PhpIncludeInspection */
		include($path);
		echo '</style>';

		if ($echo) {
			echo ob_get_clean();
			return true;
		} else {
			return ob_get_clean();
		}
	}

	/**
	 * Returns the content of a file, wrapped in CSS tags, and
	 * replaces some placeholders.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @param   string  $path           The path to the CSS file with the content.
	 * @param   array   $placeholders   The values with which the placeholders should be replaced: {placeholder} => {value}
	 * @param   bool    $echo           True if the content should be echoed, false if returned.
	 *
	 * @return  bool|string     True if the content should be echoed, the content itself otherwise.
	 */
	public static function get_stylesheet_with_variables($path, $placeholders, $echo = false) {
		$content = self::get_stylesheet($path);
		$result  = self::replace_placeholders($placeholders, $content);

		if ($echo) {
			echo $result;
			return true;
		} else {
			return $result;
		}
	}

	/**
	 * Returns a string wrapped in CSS tags.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @param   string  $css    The CSS content.
	 * @param   bool    $echo   True if the content should be echoed, false if returned.
	 *
	 * @return  bool|string     True if the content should be echoed, the content itself otherwise.
	 */
	public static function get_stylesheet_from_string($css, $echo = false) {
		ob_start();

		echo '<style type="text/css">';
		echo $css;
		echo '</style>';

		if ($echo) {
			echo ob_get_clean();
			return true;
		} else {
			return ob_get_clean();
		}
	}

	/**
	 * Sometimes we need to add inline CSS to handles which are not registered at all.
	 * This creates a dummy handle to allow for custom CSS output.
	 *
	 * @param   string  $handle     The fake handle to which the inline content should be added.
	 * @param   string  $css        The CSS content to be added inline.
	 */
	public static function add_inline_stylesheet_to_false_handle($handle, $css) {
		wp_register_style($handle, false); wp_enqueue_style($handle);
		wp_add_inline_style($handle, $css);
	}

	/**
	 * Returns the content of a file wrapped in JS tags.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @param   string  $path   The path to the JS file with the content.
	 * @param   bool    $echo   True if the content should be echoed, false if returned.
	 *
	 * @return  bool|string     True if the content should be echoed, the content itself otherwise.
	 */
	public static function get_javascript($path, $echo = false) {
		ob_start();

		echo '<script type="text/javascript">';
		/** @noinspection PhpIncludeInspection */
		include($path);
		echo '</script>';

		if ($echo) {
			echo ob_get_clean();
			return true;
		} else {
			return ob_get_clean();
		}
	}

	/**
	 * Returns the content of a JS file wrapped in JS tags,
	 * and replaces placeholders.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @param   string  $path           The path to the JS file with the content.
	 * @param   array   $placeholders   The values with which the placeholders should be replaced: {placeholder} => {value}
	 * @param   bool    $echo           True if the content should be echoed, false if returned.
	 *
	 * @return  bool|string     True if the content should be echoed, the content itself otherwise.
	 */
	public static function get_javascript_with_variables($path, $placeholders, $echo = false) {
		$content = self::get_javascript($path);
		$result  = self::replace_placeholders($placeholders, $content);

		if ($echo) {
			echo $result;
			return true;
		} else {
			return $result;
		}
	}

	/**
	 * Wraps a string in JS tags.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @param   string  $js     The JS content.
	 * @param   bool    $echo   True if the content should be echoed, false if returned.
	 *
	 * @return  bool|string     True if the content should be echoed, the content itself otherwise.
	 */
	public static function get_javascript_from_string($js, $echo = false) {
		ob_start();

		echo '<script type="text/javascript">';
		echo $js;
		echo '</script>';

		if ($echo) {
			echo ob_get_clean();
			return true;
		} else {
			return ob_get_clean();
		}
	}

	/**
	 * Sometimes we need to add inline JS to handles which are not registered at all.
	 * This creates a dummy handle to allow for custom JS output.
	 *
	 * @param   string  $handle     The fake handle to which the inline content should be added.
	 * @param   string  $js         The JS to be added inline.
	 */
	public static function add_inline_script_to_false_handle($handle, $js) {
		// todo: the trick from inline css doesn't work for JS, this is a workaround for now
		wp_add_inline_script(DWS_Public::get_asset_handle(), $js);
	}

	/**
	 * Outputs an empty element which will "clear" the content.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 */
	public static function echo_clear() {
		echo '<div class="dws_clear"></div>';
	}

	/**
	 * Takes a text and expands the shortcodes inside. By default, it echoes the result.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @param   string  $text   The original text to be processed.
	 * @param   bool    $echo   True if the result should be echoed, false if returned.
	 *
	 * @return  bool|string     True if the content is echoed, the content itself otherwise.
	 */
	public static function echo_processed_shortcodes($text, $echo = true) {
		$result = do_shortcode($text);

		if ($echo) {
			echo $result;
			return true;
		} else {
			return $result;
		}
	}

	/**
	 * Takes an associate array $placeholder -> $replacement
	 * and replaces all instances of $placeholder with $replacement
	 * inside the second paramter, which is a string.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @param   array   $placeholders   The values with which the placeholders must be replaced: {placeholder} => {value}
	 * @param   string  $string         The string containing the placeholders.
	 *
	 * @return  string  Processed string with all the placeholders replaced.
	 */
	public static function replace_placeholders($placeholders, $string) {
		return str_replace(array_keys($placeholders), array_values($placeholders), $string);
	}

	/**
	 * Removes an element from an array by its value.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @param   array   $array      The original array passed by reference.
	 * @param   mixed   $value      The value that should be unset.
	 */
	public static function unset_array_element_by_value(&$array, $value) {
		if (($key = array_search($value, $array)) !== false) {
			unset($array[$key]);
		}
	}

	/**
	 * Useful class for calls to functional programming constructs,
	 * such as 'map_reduce'. Returns the logical or result of the
	 * two parameters.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @param   bool    $v1     The first boolean value.
	 * @param   bool    $v2     The second boolean value.
	 *
	 * @return  bool    The result of "or-ing" the two boolean parameters.
	 */
	public static function logical_or($v1, $v2) {
		return $v1 || $v2;
	}

	/**
	 * Useful class for calls to functional programming constructs,
	 * such as 'map_reduce'. Returns the logical and result of the
	 * two parameters.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @param   bool    $v1     The first boolean value.
	 * @param   bool    $v2     The second boolean value.
	 *
	 * @return  bool    The result of "and-ing" the two boolean parameters.
	 */
	public static function logical_and($v1, $v2) {
		return $v1 && $v2;
	}

	/**
	 * Removes an anonymous object filter.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @param   string   $tag        Hook name.
	 * @param   string   $class      Class name.
	 * @param   string   $method     Method name.
	 */
	public static function remove_anonymous_object_filter($tag, $class, $method) {
		$filters = $GLOBALS['wp_filter'][$tag];
		if (empty($filters)) {
			return;
		}

		foreach ($filters as $priority => $filter) {
			foreach ($filter as $identifier => $function) {
				if (is_array($function) and is_a($function['function'][0], $class) and $method === $function['function'][1]) {
					remove_filter($tag, array($function['function'][0], $method), $priority);
				}
			}
		}
	}

	/**
	 * Returns a GUIDv4 string.
	 *
	 * Uses the best cryptographically secure method for all supported platforms with fallback to an older,
	 * less secure version.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * Code originally written by Dave Pearson.
	 * @link    http://php.net/manual/de/function.com-create-guid.php
	 *
	 * @param   bool    $trim   Whether to remove encapsulating braces or not.
	 *
	 * @return  string  A valid GUIDv4.
	 */
	public static function generate_guid_v4($trim = true) {
		// Windows
		if (function_exists('com_create_guid') === true) {
			if ($trim === true) {
				return trim(com_create_guid(), '{}');
			} else {
				return com_create_guid();
			}
		}

		// OSX/Linux
		if (function_exists('openssl_random_pseudo_bytes') === true) {
			$data    = openssl_random_pseudo_bytes(16);
			$data[6] = chr(ord($data[6]) & 0x0f | 0x40);    // set version to 0100
			$data[8] = chr(ord($data[8]) & 0x3f | 0x80);    // set bits 6-7 to 10
			return vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($data), 4));
		}

		// Fallback (PHP 4.2+)
		mt_srand((double)microtime() * 10000);
		$charid = strtolower(md5(uniqid(rand(), true)));
		$hyphen = chr(45);                  // "-"
		$lbrace = $trim ? "" : chr(123);    // "{"
		$rbrace = $trim ? "" : chr(125);    // "}"
		$guidv4 = $lbrace .
			substr($charid, 0, 8) . $hyphen .
			substr($charid, 8, 4) . $hyphen .
			substr($charid, 12, 4) . $hyphen .
			substr($charid, 16, 4) . $hyphen .
			substr($charid, 20, 12) .
			$rbrace;

		return $guidv4;
	}

	/**
	 * Returns the number of days difference between now and a timestamp,
	 * counting midnight as when the date changes.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @param   int     $timestamp          The timestamp to get the difference to.
	 * @param   int     $timestamp_against  The timestamp to compare to.
	 *
	 * @return  int     The number of days between the two timestamps, always absolute.
	 */
	public static function days_difference_at_midnight($timestamp, $timestamp_against = null) {
		$timestamp_against = ($timestamp_against === null) ? current_time('timestamp') : $timestamp_against;

		$date1 = new \DateTime(date('Y-m-d', $timestamp_against));
		$date2 = new \DateTime(date('Y-m-d', $timestamp));
		return $date1->diff($date2, true)->days;
	}

	/**
	 * Sends an email with a CSV file as attachment.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @param   string  $csv_string     The actual CSV content in string format.
	 * @param   string  $body           The body of the email.
	 * @param   string  $to             The recipient of the email.
	 * @param   string  $subject        The subject of the email.
	 * @param   string  $from           The sender of the email.
	 *
	 * @return  bool    True if the mail was successfully accepted for delivery, false otherwise.
	 */
	public static function send_csv_email($csv_string, $body, $to, $subject, $from) {
		// this will provide plenty adequate entropy
		$multipartSep = '-----' . md5(time()) . '-----';

		// arrays are much more readable
		$headers = array(
			"From: $from",
			"Reply-To: $from",
			"Content-Type: multipart/mixed; boundary=\"$multipartSep\""
		);

		// make the attachment
		$attachment = chunk_split(base64_encode($csv_string));

		// make the body of the message
		$body = "--$multipartSep\r\n"
			. "Content-Type: text/plain; charset=ISO-8859-1; format=flowed\r\n"
			. "Content-Transfer-Encoding: 7bit\r\n"
			. "\r\n"
			. "$body\r\n"
			. "--$multipartSep\r\n"
			. "Content-Type: text/csv\r\n"
			. "Content-Transfer-Encoding: base64\r\n"
			. "Content-Disposition: attachment; filename=\"" . date('Y-m-d') . ".csv\"\r\n"
			. "\r\n"
			. "$attachment\r\n"
			. "--$multipartSep--";

		// send the email, return the result
		return @mail($to, $subject, $body, implode("\r\n", $headers));
	}

	/**
	 * Modifies the query segment of a given URL.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @param   string  $url    The URL to be modified.
	 * @param   array   $mod    The parameters that should be modified/added.
	 *
	 * @return  string  The resulting URL.
	 */
	public static function modify_url_query($url, $mod) {
		$purl = parse_url($url);

		$params = array();

		if (($query_str = $purl['query'])) {
			parse_str($query_str, $params);

			foreach ($params as $name => $value) {
				if (isset($mod[$name])) {
					$params[$name] = $mod[$name];
					unset($mod[$name]);
				}
			}
		}

		$params = array_merge($params, $mod);

		$ret = "";

		if ($purl['scheme']) {
			$ret = $purl['scheme'] . "://";
		}
		if ($purl['host']) {
			$ret .= $purl['host'];
		}
		if ($purl['path']) {
			$ret .= $purl['path'];
		}
		if ($params) {
			$ret .= '?' . http_build_query($params);
		}
		if ($purl['fragment']) {
			$ret .= "#" . $purl['fragment'];
		}

		return $ret;
	}
}