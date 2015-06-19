# WordPress Plugin - TTT QueryPosition #

The objective of this plugin is create a "WP_Query" with more than one "WP_Query" at the same time and define in witch position you want the result of each "subquery".

# How to use #


```
#!php

<?php
$QueryPos = new TTTqueryposition(array(
    'posts_per_page' => 3,
    'ignore_sticky_posts' => true,
    '__extra_query' => array(
        array(
            'positions' => array(1, 4),
            '_extra_template' => 'query1',
            'params' => array(
                'post_type' => array('advertorial','quotes'),
                'ignore_sticky_posts' => true,
                'posts_per_page' => 2
            )
        ),
        array(
            'positions' => array(6, 8),
            '_extra_template' => 'query2',
            'params' => array(
                'post_type' => array('author'),
                'ignore_sticky_posts' => true,
                'posts_per_page' => 2
            )
        ),
    )
));

if ( $QueryPos->have_posts() ) {
    while ( $QueryPos->have_posts() ) {
        $QueryPos->the_post();
        if ($QueryPos->get_post_extra_template() == 'query1') {
            echo "Advertorial/Quotes";
            the_title();
        }
        else {
            echo "Author";
            the_title();
        }
    }
}

?>



```
