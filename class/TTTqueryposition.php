<?php 

class TTTqueryposition extends WP_Query {

    public $_cq_extras = false;
    public $_cq_slug = false;
    
    function __construct($params = false) {

        if (!is_array($params) && !empty($params)) {
            $this->_cq_slug = $params;
            $params = get_option('tttqueryposition_'.$params);
        }

        if (isset($_REQUEST['preview'])) {
            $common = new TTTqueryposition_Common();
            $params = $common->parse_form( $_REQUEST['slug'] );
        }

        if (isset($params['__extra_query'])) {
            foreach($params['__extra_query'] as $cq_value) {
                $this->cq_get_query($cq_value);
            }
        }

        if (is_array($this->_cq_exclude)) {
            if (isset($params['post__not_in'])) {
                $params['post__not_in'] = array_merge(
                    (array) $params['post__not_in'], $this->_cq_exclude
                );
            }
            else {
                $params['post__not_in'] = $this->_cq_exclude;
            }
        }

        if (has_filter('ttt_queryposition_wp_query_args_'.$this->_cq_slug)) {
            $this->query(apply_filters('ttt_queryposition_wp_query_args_'.$this->_cq_slug, $params));
        }
        else {
            $this->query(apply_filters('ttt_queryposition_wp_query_args',$params));
        }


        $this->reindex();

    }

    function reindex() {
        unset($new);

        if (!is_array($this->_cq_extras)) return;

        ksort($this->_cq_extras, SORT_NUMERIC);

        $limit = count($this->_cq_extras) + count( $this->posts );


        for( $count=1; $count<=$limit; $count++ ) {
            if (isset($this->_cq_extras[$count])) {
                $new[] = $this->_cq_extras[$count];
                unset($this->_cq_extras[$count]);
            }
            else {
                $new[] = array_shift($this->posts);
            }
        }

        if (count($this->_cq_extras) > 0) {
            foreach($this->_cq_extras as $_post) {
                $new[] = $_post;
            }
        }

        unset($this->_cq_extras);

        $this->posts = $new;
        $this->post_count = count( $this->posts );
    }

    function get_post_extra_template() {
        if (!is_object($this->post)) return false;
        if (!isset($this->post->_extra_template)) return false;

        return $this->post->_extra_template;
    }

    function cq_get_query($value) {

        if (
            !isset($value['positions']) ||
            !is_array($value['positions']) ||
            count($value['positions']) <= 0
        ) {
            throw new Exception("You must to define positions for extra query");
            return false;
        }

        if (isset($value['params']['callback_filter'])) {
            $_query = apply_filters($value['params']['callback_filter'],$value['params']);
            if (!$_query || !isset($_query->posts)) {
                return false;
            }
        }
        elseif (isset($value['callback_class'])) {
            /**
             * BETA
             **/
            if (!is_callable($value['callback_class'])) {
                throw new Exception("The class are not accesible (".$value['_extra_template'].')');
                return false;
            }

            $_class = $value['callback_class'][0];
            $_params = $value['callback_class'][1];

            $_query = new $_class($_params, $value);
            if (!$_query || !isset($_query->posts)) {
                throw new Exception("Must return a WP_Query object or similar strucutre for the loop (".$value['_extra_template'].')');
                return false;
            }
        }
        else {

            if (has_filter('ttt_queryposition_wp_query_args_'.$this->_cq_slug)) {
                $_query = new WP_Query(apply_filters('ttt_queryposition_wp_query_args_'.$this->_cq_slug,$value['params']));
            }
            else{
                $_query = new WP_Query(apply_filters('ttt_queryposition_wp_query_args',$value['params']));
            }

            if (!$_query->have_posts()) return;
        }

        foreach( $_query->posts as $_post ) {
            if (count($value['positions']) <= 0) break;

            $position = (int) array_shift($value['positions']);

            if (isset($this->_cq_extras[$position])) {
                throw new Exception("The position ".$position." is all ready in use by other extra query");
            }

            if (isset($value['_extra_template'])) {
                $_post->_extra_template = $value['_extra_template'];
            }

            $this->_cq_extras[$position] = $_post;
            $this->_cq_exclude[] = $_post->ID;
        }

    }

}

?>
