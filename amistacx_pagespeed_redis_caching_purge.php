<?php
/**
 * Plugin Name: aMiSTACX PageSpeed Redis Caching Purge
 * Description: A simple solution to purge the entire cache of PageSpeed and Redis at any time.
 * Version: 1.0.0
 * Author: aMiSTACX
 * Author URI: https://amistacx.com/wordpress/
 * License: GPLv3
 * Text Domain: amistacx
 *
 * @package aMiSTACX
 */

class PageSpeedRedisCachingPurge {
	
	public function __construct() {
		if(get_option( 'page_speed_redis_caching_purge_module_cant_enabled' ) == '1') {
			add_action( 'admin_init', array( $this, 'prefix_disable_plugin' ) );
			add_action( 'admin_notices', array( $this, 'cant_active_module_notice' ) );
			delete_option( 'page_speed_redis_caching_purge_module_cant_enabled' );
			
			return;
		}

		add_action('admin_enqueue_scripts', array($this,'enqueue'));
		add_action('wp_enqueue_scripts', array($this,'enqueue'));
		add_action('admin_bar_menu', array($this,'admin_bar_menu'),100);
		add_action('wp_ajax_amistacx_redis_purge_click', array($this,'amistacx_redis_purge_click'));
		add_action('wp_ajax_nopriv_amistacx_redis_purge_click', array($this,'amistacx_redis_purge_click'));
		add_action('wp_ajax_amistacx_pagespeed_purge_click', array($this,'amistacx_pagespeed_purge_click'));
		add_action('wp_ajax_nopriv_amistacx_pagespeed_purge_click', array($this,'amistacx_pagespeed_purge_click'));
		add_action('wp_ajax_amistacx_pagespeed_purge_url_page', array($this,'amistacx_pagespeed_purge_url_page'));
		add_action('wp_ajax_nopriv_amistacx_pagespeed_purge_url_page', array($this,'amistacx_pagespeed_purge_url_page'));
		add_filter( 'plugin_action_links_' . plugin_basename(__FILE__), array($this,'plugin_settings_links') );
		add_action('admin_menu', array($this, 'amistacx_pagespeed_purge_menu'));
		add_action( 'admin_init', array($this, 'init') );
	}

	public function init(){
		register_setting( 'amistacx_pagespeed_purge_settings', 'pagespeed_purge' );
		register_setting( 'amistacx_pagespeed_purge_settings', 'redis_purge' );
    }
    public function admin_bar_menu($wp_admin_bar){
	    $pageSpeedCleanButton = array(
		    'title' => 'Purge PS Cache',
		    'href' => '#',
		    'id' => 'amistacx-page-speed-purge-button',
		    'parent' => false
	    );

	    $redisCleanButton = array(
		    'title' => 'Purge Redis Cache',
		    'href' => '#',
		    'id' => 'amistacx-redis-purge-button',
		    'parent' => false
	    );
	    $pageSpeedCleanButtonIsEnable = get_option('pagespeed_purge');
	    $redisCleanButtonIsEnable = get_option('redis_purge');
	    if($pageSpeedCleanButtonIsEnable) {
		    $wp_admin_bar->add_menu( $pageSpeedCleanButton );
	    }
	    if($redisCleanButtonIsEnable) {
		    $wp_admin_bar->add_menu( $redisCleanButton );
	    }
    }
	public function enqueue()
	{
		wp_enqueue_style( 'sweetalert2-style', plugins_url( '/css/sweetalert2.min.css', __FILE__ ) );
		wp_enqueue_script( 'sweetalert2-scrypt', plugins_url( '/js/sweetalert2.min.js', __FILE__ ), array( 'jquery' ) );
		wp_enqueue_script( 'general-script', plugins_url( '/js/general-script.js', __FILE__ ), array( 'jquery' ) );
		wp_localize_script('general-script', 'custom_ajax',
			array(
				'url' => admin_url('admin-ajax.php')
			)
		);
	}

	public function pagspeed_module_is_installed(){
		$response = wp_remote_get(home_url('/'));
		$apache_pagespeed = wp_remote_retrieve_header( $response, 'X-Mod-Pagespeed' );
		$nginx_pagespeed = wp_remote_retrieve_header( $response, 'X-Page-Speed' );
		return ($apache_pagespeed || $nginx_pagespeed)?true:false;
	}

    public function redis_module_is_installed(){
		$res = shell_exec('redis-cli ping');
		return (trim($res) == 'PONG')?true:false;
	}

	public function amistacx_redis_purge_click() {
		$message = 'No output. Please check the Redis.';
		$res = shell_exec('redis-cli flushall');
		if($res){
			$message = $res;
		}
		$return = array(
			'message'  => $message,
		);

		wp_send_json($return);
	}

	public function amistacx_pagespeed_purge_click() {
		$site_url = get_site_url();
		$url = $site_url."/pagespeed_admin/cache?purge=*";
		$error = false;

		$response = $this->send_get_request($url, $error);
		$return = array(
			'message'  => $response,
			'error' => $error
		);
		wp_send_json($return);
	}

	public function amistacx_pagespeed_purge_url_page() {
		$site_url = get_site_url();
		$url = '*';
		$error = false;
		$response = '';
		
		if ( !empty($_POST['pagespeed_url']) ) {
			$user_url = trim($_POST['pagespeed_url']);
			$url = trim(parse_url($user_url, PHP_URL_PATH), '/');
			
			if (true !== ($is_valid_url = $this->is_valid_url($user_url))) {
				$response = $is_valid_url;
				$error = true;
			}
		}

		if(!$error) {
			$query = $site_url."/pagespeed_admin/cache?purge=".$url;
			$response = $this->send_get_request($query, $error);
		}
		
		$return = array(
			'message'  => $response,
			'error' => $error
		);

		wp_send_json($return);
	}
	
	private function is_valid_url($url) {
		$error = false;
		$parsed_url = parse_url($url);

		if(empty(trim($parsed_url['path'], '/'))) {
			$error = "URL's path is empty";
		}
		
		if(!empty($parsed_url['host']) && ($parsed_url['host'] != parse_url(get_site_url(), PHP_URL_HOST))) {
			$error = "Host name is invalid";
		}
		
		return $error ?: true;
	}
	
	private function send_get_request($url, &$error){
		$response = wp_remote_get($url);
		$code = wp_remote_retrieve_response_code($response);
		$message = wp_remote_retrieve_response_message($response);
		
		if (is_wp_error($response)) {
			$error = true;
			return $response->get_error_message();
		} elseif (200 != $code) {
			$error = true;
			return $message ? : 'Unknown error occurred';
		} else {
			return wp_remote_retrieve_body( $response );
		}
	}

	public function plugin_settings_links( $links ) {
		$links[] = '<a href="'. menu_page_url( 'amistacx_pagespeed_purge', false ) .'">Settings</a>';
		return $links;
	}
	public function amistacx_pagespeed_purge_menu() {
		add_options_page('aMiSTACX PageSpeed Redis Caching Purge Admin Settings', 'aMiSTACX PageSpeed Redis Caching Purge', 'manage_options', 'amistacx_pagespeed_purge', array($this,'amistacx_pagespeed_purge_page'));
	}
	public function cant_active_module_notice() {
		echo '<div class="error"><p><strong>aMiSTACX PageSpeed Redis Caching Purge</strong> cannot be activated. Redis and Pagespeed is not configured or not installed.</p></div>';
	}
	public function prefix_disable_plugin() {
		if (is_plugin_active( plugin_basename( __FILE__ ) ) ) {
			deactivate_plugins( plugin_basename( __FILE__ ) );
			if ( isset( $_GET['activate'] ) ) {
				unset( $_GET['activate'] );
			}
		}
	}
	public function amistacx_pagespeed_purge_page(){
	?>
    <div class="wrap">
        <h2>aMiSTACX PageSpeed Redis Caching Purge Admin Settings</h2>
        <p><a href="https://amistacx.com/wordpress" target="_blank">Developer's homepage</a></p>
        <form method="post" action="options.php">
			<?php settings_fields( 'amistacx_pagespeed_purge_settings' ); ?>
            <table class="form-table">
                <tr valign="top">
                    <th scope="row">Enable Dashboard Button</th>
                    <td>
                        <label>
                            <?php
                            $disabled_string = '';
                            $checked_string = checked( 1, get_option('pagespeed_purge'), false );
                            if(!$this->pagspeed_module_is_installed()){
	                            $disabled_string = 'disabled';
	                            $checked_string = '';
                            }
                            ?>
                            <input <?= $disabled_string ?> type="checkbox" name="pagespeed_purge" <?= $checked_string ?> value="1"/>
                            PageSpeed Purge
                        </label>
                    </td>
                </tr>
                <tr valign="top">
                    <th scope="row">Enable Dashboard Button</th>
                    <td>
                        <label>
	                        <?php
	                        $redis_is_installed = $this->redis_module_is_installed();
	                        $disabled_string = '';
	                        $checked_string = checked( 1, get_option('redis_purge'), false );
	                        if(!$redis_is_installed ){
		                        $disabled_string = 'disabled';
		                        $checked_string = '';
                            }
	                        ?>
                            <input <?= $disabled_string ?> type="checkbox" name="redis_purge" <?= $checked_string ?> value="1"/>
                            Redis Cache Purge
                        </label>
                    </td>
                </tr>
            </table>
            <p class="submit">
                <input type="hidden" name="action" value="update" />
                <input type="submit" class="button-primary" value="<?php _e('Save Changes') ?>" />
            </p>
        </form>
        <hr>
        <h2>PageSpeed URL Purge:</h2>
        <table class="form-table">
            <tr valign="top">
                <th scope="row">URL</th>
                <td><input id="pagespeed_url" type="text" name="pagespeed_url" value="*" size="50" /></td>
            </tr>
        </table>
        <p class="submit">
            <input id="pagespeed_send" type="button" class="button-primary" value="<?php _e('Purge') ?>" />
        </p>
    </div>
<?php }
    public static function install(){
	    if(!self::pagspeed_module_is_installed() && !self::redis_module_is_installed()){
			add_option( 'page_speed_redis_caching_purge_module_cant_enabled', '1' );
        } 
    }
}

global $skeleton;
$skeleton = new PageSpeedRedisCachingPurge();
register_activation_hook( __FILE__, array( 'PageSpeedRedisCachingPurge', 'install' ) );

