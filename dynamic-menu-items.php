<?php
/**
 * Dynamic Menu Items
 *
 * @package       DYNAMICMEN
 * @author        Bright Bridge Web
 * @license       gplv2
 * @version       1.0.3
 *
 * @wordpress-plugin
 * Plugin Name:   Dynamic Menu Items
 * Plugin URI:    https://brightbridgeweb.com/custom-plugins/dynamic-menu-items
 * Description:   Add posts, pages, or custom post types specific to a category, tag, or custom taxonomy dynamically to any menu.
 * Version:       1.0.3
 * Author:        Bright Bridge Web
 * Author URI:    https://brightbridgeweb.com
 * Text Domain:   dynamic-menu-items
 * Domain Path:   /languages
 * License:       GPLv2
 * License URI:   https://www.gnu.org/licenses/gpl-2.0.html
 */

if ( ! defined( 'ABSPATH' ) ) exit;

define('BBWDDYNOMNUITM_PATH', plugin_dir_path(__FILE__));
define('BBWDDYNOMNUITM_URL', plugin_dir_url(__FILE__));
define('BBWDDYNOMNUITM_VERSION', '1.0.3');
define('BBWDDYNOMNUITM_P_SLUG', dirname( plugin_basename( __FILE__ ) ) );
define('BBWDDYNOMNUITM_NONCE', 'B@14B23@#W34532D');

require_once(BBWDDYNOMNUITM_PATH.'includes/registerStuff.php');
function BBWDDYNOMENUITM_p_desc_links( $plugin_meta, $plugin_file ) {
    if ( plugin_basename( __FILE__ ) === $plugin_file ) {
        $new_link = '<a href="https://brightbridgedev.com/custom-plugins/'.BBWDDYNOMNUITM_P_SLUG.'" target="_blank">Support</a>';
        $plugin_meta[] = $new_link;
    }
    return $plugin_meta;
}
add_filter( 'plugin_row_meta', 'BBWDDYNOMENUITM_p_desc_links', 10, 2 );