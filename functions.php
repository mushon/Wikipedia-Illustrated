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
function get_latest_cat_img($cat, $size){
  $args = array(
    'numberposts' => -1,
    'category_name' => $cat
    );
  $myposts = get_posts($args);
  foreach($myposts as $post) {
    if(has_post_thumbnail($post->ID)){
      $imgid = get_post_thumbnail_id($post->ID);
      if (!$size) $size = 'medium';
      return wp_get_attachment_link($imgid, $size);
      break;
    }
  }
}

// Original PHP code by Chirp Internet: www.chirp.com.au
// Please acknowledge use of this code by including this header.

function myTruncate($string, $limit, $break=".", $pad="&hellip;") {
// return with no change if string is shorter than $limit
  if(strlen($string) <= $limit) return $string;
  
  // is $break present between $limit and the end of the string?
  if(false !== ($breakpoint = strpos($string, $break, $limit))) {
    if($breakpoint < strlen($string) - 1) {
      $string = substr($string, 0, $breakpoint) . $pad;
    }
  }
  
  return $string;  
}

// Set the linked image size to large:
/*
function my_image_tag_class($class){
  $size='large';
  return $class;
}
add_filter('get_image_tag_class','my_image_tag_class');
*/

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
  global $post;
  
  $content = '';
  if(is_home()){
    ?>
    <div id="intro">
      <div class="container">
        <h1><?php bloginfo('description'); ?> </h1>
      </div>
    </div>
    <?php
	} elseif(is_single()){
    $cat = get_the_category();
    $article_title = get_post_meta($post->ID, 'article_title', true);
    if (!$article_title){
      $article_title = get_latest_in_cat($cat[0]->cat_name, 'article_title');
    }
    $article_quote = get_post_meta($post->ID, 'article_quote', true);
    if (!$article_quote){
      $article_quote = get_latest_in_cat($cat[0]->cat_name, 'article_quote');
    }
    
    $content .= '<div class="category-header">';
    $content .= '<a href="' . get_category_link( $cat[0]->cat_ID ) . '" class="container">';
		$content .= '<span class="page-title">';
		$content .= $cat[0]->cat_name;
		$content .= ': <span>';
		$content .= $article_title;
		$content .= '</span>' . "\n";
		$content .= '</span> - ' . "\n";
		$content .= '<span class="article-quote">';
		//Truncate the quote:
		$content .= myTruncate($article_quote, 100, ' ', '&hellip;');
		$content .= '</span>';
		$content .= '</a>';
		$content .= '</div>';
	}

  $content .= "\n";
  echo $content;
	}
}	
add_action('thematic_header','childtheme_header_extension',10);

// ---------------------------------------------------------------- Home Page:

// Make the default index loop show excerpts
function childtheme_content() {
  if(is_home()){
    return 'excerpt';
  }
}
//add_filter('thematic_content','childtheme_content');

// ---------------------------------------------------------------- Post Structure:

// Removing Thematic's Postmeta
function childtheme_postheader_postmeta(){
  //nothing
}
add_filter('thematic_postheader_postmeta', 'childtheme_postheader_postmeta');

// Functions to change order of Post Meta

function childtheme_postheader($old){
  $new  = $old;
  $new .= '<div class="left-col">';
  
  return $new;
}

add_filter('thematic_postheader','childtheme_postheader');

// Set the post footer:
function childtheme_postfooter() {
  if(!is_page()){?>
    </div>
  	<div class="entry-utility meta">
      <?php if(is_home()){ ?>
        <div class="cat-links"><?php $cat = get_the_category(); echo '
        <a href="' . get_category_link($cat[0]->cat_ID) . '"><span>' . $cat[0]->cat_name . ':</span> ' . get_latest_in_cat($cat[0]->cat_name, article_title) ?></a></div>
        <div class="article-quote">
          <?php echo get_latest_in_cat($cat[0]->cat_name, article_quote) ?>
        </div>
      <?php } ?>
  		<?php the_tags( __( '<span class="tag-links">', 'sandbox' ), " ", "</span>" ) ?>
  		<div class="date"><?php the_time('M jS, Y'); ?></div>
  		<div class="comments-link"><?php comments_popup_link( __( 'No Comments', 'sandbox' ), __( '1 Comment', 'sandbox' ), __( '% Comments', 'sandbox' ) ) ?></div>
  		<?php edit_post_link( __( 'Edit', 'sandbox' ), "<span class='edit-link'>", "</span>" ) ?>
  	</div>
  	<?php
	}
}
add_filter ('thematic_postfooter', 'childtheme_postfooter');

// ---------------------------------------------------------------- Category titles:
  
// Retrieve previous naming options:

function childtheme_override_page_title(){

  global $post;
  
  $content = '';
  if (is_category()) {
    $cat = get_the_category();
    $wiki_name = get_latest_in_cat($cat[0]->cat_name, 'wiki_name');
    if($wiki_name){
  		$wiki_url = 'Wikipedia.org/wiki/' . $wiki_name;
    } else {
      $wiki_url = 'Wikipedia.org';
    }
    $article_name = get_latest_in_cat($cat[0]->cat_name, 'article_title');
    if(!$article_name){
      $article_name = "Haven't chosen one yet";
    }
		
    $content .= '<div class="category-header">';
    $content .= '<div class="category-text">';
		$content .= '<h1 class="page-title">';
		$content .= single_cat_title('', FALSE);
		$content .= ': <span>';
		$content .= $article_name;
		$content .= '</span>' . "\n";
		$content .= '<a href="http://en.' .$wiki_url . '" target="blank" class="wiki_link">' . $wiki_url . '</a>';
		$content .= '</h1>' . "\n";
		$content .= '<blockquote class="article-quote">';
		$content .= get_latest_in_cat($cat[0]->cat_name, 'article_quote');
		$content .= '</blockquote>';
		$content .= get_prev_ops($cat[0]->cat_name);
		$content .= '</div>';
    $content .= '<div class="category-image">';
		$content .= get_latest_cat_img($cat[0]->cat_name, 'medium');
		$content .= '</div>';
		$content .= '<br class="clear"/>';
		$content .= '</div>';
	} 

  $content .= "\n";
  echo apply_filters('childtheme_override_page_title', $content);
  
}

function get_prev_ops($cat){
  $args = array(
    'category_name' => $cat,
    'meta_key' => 'wiki_name',
    'offset' => 1
    );
  $myposts = get_posts($args);
  if ($myposts){
    $prev  = "<h3>Other Options for this letter:</h3>";
    $prev .= "<ul id='previous_options'>";
      foreach($myposts as $post) {
        $prev .= "<li><strong>" . $cat . ":</strong> <a href='" . get_permalink($post->ID) . "'>";
        $prev .= get_post_meta($post->ID, article_title, true);
        $prev .= "</a></li>";
      }
    $prev .= "</ul>";
    return $prev;
  } else {
    return "";
  }
}


function child_post_thumb() {
    return array(200,200);
}
add_filter('thematic_post_thumb_size','child_post_thumb');

// ---------------------------------------------------------------- Comments:

function childtheme_commenthead(){
  ?>
  <div>
    <h2 class="callForAction">Now Discuss:</h2>
  </div>
  <?php
}

add_action ('thematic_abovecommentslist', 'childtheme_commenthead');

function my_comments($comment, $args, $depth) {
    $GLOBALS['comment'] = $comment;
    $GLOBALS['comment_depth'] = $depth;
    ?>
    	<li id="comment-<?php comment_ID() ?>" class="<?php thematic_comment_class() ?>">
        <div class="comment-meta">
          <div class="comment-author vcard">
          	<?php thematic_commenter_link() ?>
          	<span class="date"><?php comment_date('M jS, y') ?></span>
          	<?php edit_comment_link('Edit', '<span class="edit-comment meta">', '</span>'); ?> 
          </div>
        </div>
        
        <?php if ($comment->comment_approved == '0') _e("\t\t\t\t\t<span class='unapproved'>Your comment is awaiting moderation.</span>\n", 'sandbox') ?>
        <div class="comment-body"><?php comment_text() ?></div>

						
			<?php // echo the comment reply link with help from Justin Tadlock http://justintadlock.com/ and Will Norris http://willnorris.com/
				if($args['type'] == 'all' || get_comment_type() == 'comment') :
					comment_reply_link(array_merge($args, array(
						'reply_text' => __('Reply','thematic'),
						'login_text' => __('Log in to reply.','thematic'),
						'depth' => $depth,
						'before' => '<div class="comment-reply-link">',
						'after' => '</div>'
					)));
				endif;
			?>
<?php }

function my_callback() {
	$content = 'type=comment&callback=my_comments';
	return $content;
}
add_filter('list_comments_arg', 'my_callback');

// ---------------------------------------------------------------- Widgets:

function child_remove_widget_area() {
  unregister_sidebar('primary-aside');
}
add_action( 'admin_init', 'child_remove_widget_area');
?>