<?php
/*
Plugin Name: TTT Query Positions
Plugin URI: http://www.33themes.com
Description: Simple query system to cross extra queries and set a position
Version: 0.1
Author: 33 Themes GmbH
Author URI: http://www.33themes.com
*/


define('TTTINC_QUERYPOSITION', dirname(__FILE__) );
define('TTTVERSION_QUERYPOSITION', 0.1 );


function ttt_autoload_queryposition( $class ) {
	if ( 0 !== strpos( $class, 'TTTqueryposition' ) )
		return;
	
	$file = TTTINC_QUERYPOSITION . '/class/' . $class . '.php';
    if (is_file($file)) {
		require_once $file;
		return true;
    }
	
	throw new Exception("Unable to load $class at ".$file);
}

if ( function_exists( 'spl_autoload_register' ) ) {
	spl_autoload_register( 'ttt_autoload_queryposition' );
} else {
	require_once TTTINC_queryposition . '/class/TTTqueryposition.php';
}

function tttqueryposition_init () {
	$s = load_plugin_textdomain( 'tttqueryposition', false, dirname( plugin_basename( __FILE__ ) ) . '/lang/' );
    if ( !is_admin() ) {
    // 	global $TTTqueryposition_Front;
    // 	$TTTqueryposition_Front = new TTTqueryposition_Front();
    // 	$TTTqueryposition_Front->init();
    }
    else {
    	global $TTTqueryposition_Admin;
    	$TTTqueryposition_Admin = new TTTqueryposition_Admin();
    	$TTTqueryposition_Admin->init();
    }
}

add_action('init', 'tttqueryposition_init');


if (!function_exists('get_post_extra_template')) {
    function get_post_extra_template() {
        global $post;
        if (!is_object($post)) return false;
        if (!isset($post->_extra_template)) return false;

        return $post->_extra_template;
    }
}

