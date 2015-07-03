<?php 

class TTTqueryposition_Common {

    const sname = 'tttqueryposition';

    function __construct() {

    }

    public function _s( $s = false ) {
        if ( $s === false) return self::name;
        return self::sname.'_'.$s;
    }
    
    public function del( $name ) {
        return delete_option( self::sname . '_' . $name );
    }
    
    public function get( $name ) {
        return get_option( self::sname . '_' . $name );
    }
    
    public function set( $name, $value ) {
        if (!get_option( self::sname . '_' . $name ))
            add_option( self::sname . '_' . $name, $value);
        
        update_option( self::sname . '_' . $name , $value);
    }

    public function get_sources($slug) {

        $sources = apply_filters('ttt_queryposition_show_'.$slug);
        if (!isset($sources['_main'])) {
            $sources = array_merge(array('main' => true), $sources);
        }

        return $sources;

    }

    public function parse_form($slug) {


        $posts_per_page = (int) $_POST['posts_per_page'];
        $main_posts_per_page = $posts_per_page;

        $sources = $this->get_sources($slug);
        $_count = 0;
        foreach ($sources as $key => $value) {
            if ($key == '_main') {
                $params = $value;
            }
            else {
                $params['__extra_query'][ $_count ] = array(
                    '_extra_template' => $key,
                    'params' => $value
                );

            }
            $_count++;

        }

        $_count = 0;
        for( $i=1; $i<=$posts_per_page; $i++) {
            $num = $_POST['position'.$i];
            if ($num <= 0) continue;
            $params['__extra_query'][$num]['positions'][] = $i;
        }

        foreach ($params['__extra_query'] as $key => $value) {
            $total = count($value['positions']);
            if (!is_array($value['positions']) || $total <= 0) {
                unset($params['__extra_query'][$key]);
                continue;
            }
            $params['__extra_query'][$key]['params']['posts_per_page'] = $total;

            $main_posts_per_page -= $total;

        }

        $params['posts_per_page'] = $main_posts_per_page;

        return $params;

    }

    // public function load_styles( $template = 'default' ) {
    //  if ( !isset($this->template_styles[ $template ]) ) {
    //      $_s = array(
    //          get_stylesheet_directory().'/ttt-queryposition/'.$template.'/styles.php',
    //          get_template_directory().'/ttt-queryposition/'.$template.'/styles.php',
    //          TTTINC_GALLERY . '/template/front/'.$template.'/styles.php'
    //      );
    //      foreach( $_s as $_template ) {
    //          if (!is_file($_template) || !is_readable($_template)) continue;
    //          
    //          require_once $_template;
    //          break;
    //      }
    //  }
    // 
    // }
    // 
    // public function template( $ttt_queryposition, $extras ) {
    // 
    // 
    //  $this->load_styles( $ttt_queryposition->template );
    // 
    //  ob_start();
    //  $_s = array(
    //      get_stylesheet_directory().'/ttt-queryposition/'.$ttt_queryposition->template.'/template.php',
    //      get_template_directory().'/ttt-queryposition/'.$ttt_queryposition->template.'/template.php',
    //      TTTINC_GALLERY . '/template/front/'.$ttt_queryposition->template.'/template.php'
    //  );
    // 
    //  if (is_array($extras))
    //     extract($extras);
    // 
    //  foreach( $_s as $_template ) {
    //      if (!is_file($_template) || !is_readable($_template)) continue;
    //      
    //      require $_template;
    //      break;
    //  }
    // 
    //  return ob_get_clean();
    // }



}
