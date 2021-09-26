<?php
/**
 * Template Name: Front From Submission Template
 *
 */
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}
get_header();
?>

<div class="custom-form-wrapper">
    
    <div class="custom-form">

        
        <h1 class="form-title"><?php esc_html_e( 'Submit your Post' , 'front-form-expresstechsoftwares' ) ?></h1>
        <form name="" method="post" action="">
            
            <?php wp_nonce_field( 'expresstechsoftwares-custom-form' , 'nonce-check' ); ?>
            
            <div class="form-element">
            <label for="custom-title"><?php esc_html_e( 'Post Title' , 'front-form-expresstechsoftwares' );?></label>
            <input type="text" name="custom-title" id="custom-title">
            </div>
            
            <div class="form-element">
            <label for="custom-description"><?php esc_html_e( 'Post Description' , 'front-form-expresstechsoftwares' );?></label>
            <textarea rows="10" name="custom-description" id="custom-description"></textarea>
            </div>
            
            <div class="form-element">
            <label for=""><?php esc_html_e( 'Post Status' , 'front-form-expresstechsoftwares' );?></label>
            <select name="custom-status" id="custom-status">
                <option value="publish" selected="selected"><?php esc_html_e('Active')?></option>
                <option value="draft" ><?php esc_html_e('Draft')?></option>
            </select>
            </div>
            
            <div class="form-save">
                <input  type="submit" name="save-custom-form" id="save-custom-form" value="<?php esc_html_e( 'Save' , 'front-form-expresstechsoftwares' )?>" />
            </div>
            
        </form>
        
    </div>
    <div class="post-lising">
       <?php
       $paged = ( get_query_var( 'paged' ) ) ? get_query_var( 'paged' ) : 1;
       
       $args = array(
      'post_type' => array('post'),
      'post__not_in' => get_option( 'sticky_posts' ),
      'posts_per_page' => 5, 
      'paged' => $paged,
      'orderby' => 'date',
      'order' => 'desc',
      'post_status' => array( 'publish', 'draft' )
    );
       $customQuery = new WP_Query($args);
       if($customQuery->have_posts() ): 
           echo '<table>';
       echo '<theaad>';
       echo '<tr>';
            echo '<th>' . esc_html( 'Title', 'front-form-expresstechsoftwares' ) . '</th>';
            echo '<th>' . esc_html( 'Description', 'front-form-expresstechsoftwares' ) . '</th>';
            echo '<th>' . esc_html( 'Status', 'front-form-expresstechsoftwares' ) . '</th>';
            echo '<th>' . esc_html( 'Delete', 'front-form-expresstechsoftwares' ) . '</th>';
       echo '</tr>';
       echo '</theaad>';
           while($customQuery->have_posts()) :
                global $post;
                $customQuery->the_post();
                echo '<tr>';
                echo '<td>' . get_the_title() . '</td>';
                echo '<td>' . get_the_content() . ' </td>';
                echo '<td>' . get_post_status() . '</td>';
                echo '<td><button class="supp-post" data-id="' . get_the_ID() . '">del</button></td>';
                echo '<tr>';
           endwhile;
           echo '</table>';
        endif;
        wp_reset_query();
        if ( function_exists( "frontform_pagination" ) ) {
            frontform_pagination( $customQuery->max_num_pages ); 
            
        }
	
function frontform_pagination( $pages = '', $range = 4 ){
        global $paged;
        
        $showitems = ( $range * 2 ) + 1;
        if( empty( $paged ) ) $paged = 1;
        if( $pages == '' ){
            global $wp_query;
            $pages = $wp_query->max_num_pages;
            if( !$pages ){
                $pages = 1;
            }
        }
        if( 1 != $pages ){
            
            echo "<nav aria-label='Page navigation'>  <ul class='pagination'> <span>Page " . $paged . " of " . $pages . "</span>";
            if( $paged > 2 && $paged > $range+1 && $showitems < $pages ) 
                echo "<a href='".get_pagenum_link(1)."'>&laquo; First</a>";
            if( $paged > 1 && $showitems < $pages ) 
                echo "<a href='".get_pagenum_link($paged - 1)."'>&lsaquo; Previous</a>";
            for ( $i=1; $i <= $pages; $i++ ){
                
                if ( 1 != $pages &&( !($i >= $paged+$range+1 || $i <= $paged-$range-1) || $pages <= $showitems ) ){
                    
                    echo ( $paged == $i )? "<li class=\"page-item active\"><a class='page-link'>" . $i . "</a></li>":"<li class='page-item'> <a href='" . get_pagenum_link( $i ) . "' class=\"page-link\">".$i."</a></li>";
                }
            }
            if ( $paged < $pages && $showitems < $pages ) 
                echo " <li class='page-item'><a class='page-link' href=\"" . get_pagenum_link( $paged + 1 ) . "\"> > Next </a></li>";
            
            if ( $paged < $pages-1 &&  $paged+$range-1 < $pages && $showitems < $pages ) 
                echo " <li class='page-item'><a class='page-link' href='" . get_pagenum_link($pages) . "'> >> End </a></li>";
            
            echo "</ul></nav>";
        }
}
       
       ?>
    </div>
</div>

<?php

get_footer();
