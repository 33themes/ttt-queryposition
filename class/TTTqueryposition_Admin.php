<?php 

class TTTqueryposition_Admin extends TTTqueryposition_Common {

    private $role = 'edit_pages';

    function __construct() {
        parent::__construct();

    }

    function init() {
        if( current_user_can('edit_posts') ) {
            add_action('add_meta_boxes', array( &$this, 'add_meta_boxes' ) );
            add_action('admin_menu', array( &$this, 'menu' ) );
            $this->ajax();
        }
    }

    public function menu() {
        $shows = apply_filters('ttt_querypositions_shows');
        if (!is_array($shows) || $shows <= 0) return false;

        add_menu_page( 'TTTqueryposition', 'TTT Positions main', $this->role, 'ttt-queryposition', array( &$this, 'menu_page' ) );

        foreach($shows as $slug) {
            add_submenu_page('ttt-queryposition', $slug, $slug, $this->role, 'ttt-queryposition-'.$slug, array( &$this, 'positions_page') );
        }

    }

    public function enqueue_common() {
        
        add_action( 'print_media_templates', array( $this, 'print_media_templates' ) );

        wp_enqueue_media();
        wp_enqueue_style(  'ttt-queryposition-css', plugins_url('template/admin/css/common.css' , dirname(__FILE__) ) );
        wp_enqueue_script( 'ttt-queryposition-js', plugins_url('template/admin/js/common.js' , dirname(__FILE__) ), array( 'jquery', 'jquery-masonry', 'underscore' ) );
    }

    public function menu_page() {
        echo "ok";
    }

    public function positions_page() {
        $ttt_querypositions_slug  = str_replace('ttt-queryposition-','',$_GET['page']);

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $this->save_positions($ttt_querypositions_slug);
        }

        $this->enqueue_common();



        wp_localize_script('ttt-queryposition-js', 'tttquerypositionConf',array(
            'ajax' => admin_url('admin-ajax.php'),
            'preview_url' => get_site_url(),
            'slug' => $ttt_querypositions_slug,
            'Nonce' => wp_create_nonce( 'ttt-queryposition-preview-nonce' ),
        ));

        echo '<div id="TTTqueryposition">';
        require_once( TTTINC_QUERYPOSITION .'/template/admin/main.php' );
        echo '</div>';
    }

    public function ajax() {
        add_action('wp_ajax_ttt-queryposition_list', array( &$this, 'list_callback' ) );
    }

    public function _header_callback() {
        header("Content-Type: application/json", true);
        header("Expires: Tue, 03 Jul 2001 06:00:00 GMT");
        header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
        header("Cache-Control: no-store, no-cache, must-revalidate");
        header("Cache-Control: post-check=0, pre-check=0", false);
        header("Pragma: no-cache");
    }

    public function list_callback() {
        $this->_header_callback();

        echo json_encode(array('ok'), JSON_HEX_AMP);

        die();
    }

    public function save_positions($slug) {

        $params = $this->parse_form($slug);

        
        $this->set($slug, $params);
        $this->set($slug.'_gui', $_POST);

    }

}
