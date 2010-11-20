<?php

//  WIKIPEDIA ILLUSTRATED
//  Theme Functions
//

// I've included a "commented out" sample function below that'll add a home link to your menu
// More ideas can be found on "A Guide To Customizing The Thematic Theme Framework" 
// http://themeshaper.com/thematic-for-wordpress/guide-customizing-thematic-theme-framework/

// Adds a home link to your menu
// http://codex.wordpress.org/Template_Tags/wp_page_menu
//function childtheme_menu_args($args) {
//    $args = array(
//        'show_home' => 'Home',
//        'sort_column' => 'menu_order',
//        'menu_class' => 'menu',
//        'echo' => true
//    );
//	return $args;
//}
//add_filter('wp_page_menu_args','childtheme_menu_args');

// Unleash the power of Thematic's dynamic classes
// 
// define('THEMATIC_COMPATIBLE_BODY_CLASS', true);
// define('THEMATIC_COMPATIBLE_POST_CLASS', true);

// Unleash the power of Thematic's comment form
//
// define('THEMATIC_COMPATIBLE_COMMENT_FORM', true);

// Unleash the power of Thematic's feed link functions
//
// define('THEMATIC_COMPATIBLE_FEEDLINKS', true);

// ----------------------------------------------------------------  Helper Functions:

// Returns the latest value entered to a post in this category:
function get_latest_in_cat($cat, $key){
  $args = array(
    'numberposts' => 1,
    'category_name' => $cat,
    'meta_key' => $key
    );
  $myposts = get_posts($args);
  foreach($myposts as $post) { return get_post_meta($post->ID, $key, true); }
}

// Returns the latest value entered to a post in this category:
function get_latest_cat_img($cat){
  $args = array(
    'numberposts' => -1,
    'category_name' => $cat
    );
  $myposts = get_posts($args);
  foreach($myposts as $post) {
    if(has_post_thumbnail($post->ID)){
      return get_the_post_thumbnail( $post->ID, 'medium');
      break;
    }
  }
}


// ----------------------------------------------------------------  HEADER:

//Remove Blog Description
 
function remove_thematic_headerstuff() {
  remove_action('thematic_header','thematic_blogdescription',5);
	remove_action('thematic_header','thematic_access',9);
}
add_action('init','remove_thematic_headerstuff');

// Build a custom menu for articles/letters:
function childtheme_header_extension() { ?>
  
  <?php
  if ( function_exists( 'add_theme_support' ) ) {
  
  	// This theme uses wp_nav_menu()
  	add_theme_support( 'nav-menus' );
  	?>
  	
    <div id="access">
      <div class="menu">
        <div class="list-label">Articles:</div>
        <ul id="letters" class="sf-menu sf-js-enabled">
          <?php
          $letters = array("A", "B", "C", "D", "E", "F", "G", "H", "I", "J", "K", "L", "M", "N", "O", "P", "Q", "R", "S", "T", "V", "U", "W", "X", "Y", "Z");
          foreach ($letters as $letter) {
            //make the LI item anyway
            $item = "<li class='menu-item menu-item-type-post_type menu-item-" . $letter . "' id='menu-item-" . $letter . "'>";
            //find if there are any posts in this category yet:
            $catposts = get_posts('category_name='.$letter.'&numberposts=-1');
            $cat_count = sizeof($catposts);
            if($cat_count > 0){ 
            //only if there are any, show the link:
              $category_id = get_cat_ID( $letter );
              $category_link = get_category_link( $category_id );
              $item .= "<a href='" . $category_link . "'>" . $letter . "</a>";
            } else {
            //otherwise, show just the letter:
              $item .= "<span>" . $letter . "</span>";
            }
            $item .= "</li>";
            echo $item;
          }
          ?>
        </ul>
        <div class="list-label slash">/</div>
  		  <?php wp_nav_menu( 'sort_column=menu_order&container_class=menu&menu_class=sf-menu' ); ?>
  		</div>
		</div>
		<!-- #access -->
  
	<?php

	}
}	
add_action('thematic_header','childtheme_header_extension',10);


// ---------------------------------------------------------------- Post Structure:

// Removing Thematic's Postmeta
function childtheme_postheader_postmeta(){
  //nothing
}
add_filter('thematic_postheader_postmeta', 'childtheme_postheader_postmeta');

// Functions to change order of Post Meta

function childtheme_postheader($old){
  $new = '<div class="left-col">';
  $new .= $old;
  
  return $new;
}

add_filter('thematic_postheader','childtheme_postheader');

// Set the post footer:
function classlog_postfooter() {?>
  </div>
	<div class="entry-utility meta">
    <?php if(is_home()){ ?>
      <div class="cat-links"><?php $cat = get_the_category(); echo '
       <a href="' . get_category_link($cat[0]->cat_ID) . '"><span>' . $cat[0]->cat_name . ':</span> ' . get_latest_in_cat($cat[0]->cat_name, article_title) ?></a></div>
    <?php } ?>
		<div class="date"><?php the_time('M jS, Y'); ?></div>
		<?php the_tags( __( '<span class="tag-links">', 'sandbox' ), " ", "</span>" ) ?>
		<div class="comments-link"><?php comments_popup_link( __( 'No Comments', 'sandbox' ), __( '1 Comment', 'sandbox' ), __( '% Comments', 'sandbox' ) ) ?></div>
		<?php edit_post_link( __( 'Edit', 'sandbox' ), "<span class='edit-link'>", "</span>" ) ?>
	</div>
	<?php
}
add_filter ('thematic_postfooter', 'classlog_postfooter');

// ---------------------------------------------------------------- Category page:

function childtheme_override_page_title(){

  global $post;
  
  $content = '';
  if (is_category()) {
    $cat = get_the_category();
		$wiki_name = 'Wikipedia.org/wiki/' . get_latest_in_cat($cat[0]->cat_name, 'wiki_name');
		
    $content .= '<div class="category-header">';
    $content .= '<div class="category-text">';
		$content .= '<h1 class="page-title">';
		$content .= single_cat_title('', FALSE);
		$content .= ': <span>';
		$content .= get_latest_in_cat($cat[0]->cat_name, 'article_title');
		$content .= '</span>' . "\n";
		$content .= '<a href="http://en.' .$wiki_name . '" target="blank" class="wiki_link">' . $wiki_name . '</a>';
		$content .= '</h1>' . "\n";
		$content .= '<blockquote class="article-quote">';
		$content .= get_latest_in_cat($cat[0]->cat_name, 'article_quote');
		$content .= '</blockquote>';
		$content .= '</div>';
    $content .= '<div class="category-image">';
		$content .= get_latest_cat_img($cat[0]->cat_name);
		$content .= '</div>';
		$content .= '<br class="clear"/>';
		$content .= '</div>';
	}

  $content .= "\n";
  echo apply_filters('childtheme_override_page_title', $content);
  
}

function child_post_thumb() {
    return array(200,200);
}
add_filter('thematic_post_thumb_size','child_post_thumb');

?>