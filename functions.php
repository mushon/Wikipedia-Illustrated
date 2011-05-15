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

//manu key

function menu_key(){
  $letters = array("A", "B", "C", "D", "E", "F", "G", "H", "I", "J", "K", "L", "M", "N", "O", "P", "Q", "R", "S", "T", "V", "U", "W", "X", "Y", "Z");
  return($letters);
}

// Returns the latest value entered to a post in this category:
function get_latest_in_cat($cat, $key){
  $args = array(
    'numberposts' => 1,
    'category_name' => $cat,
    'meta_key' => $key
    );
  $myposts = get_posts($args);
  foreach($myposts as $post) {
    return get_post_meta($post->ID, $key, true);
  }
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

// ---------------------------------------------------------------- Subscribe Bar:

// This will create your widget area
function my_widgets_init() {
    register_sidebar(array(
       	'name' => 'Header Aside',
       	'id' => 'header-aside',
       	'before_widget' => '<li id="%1$s" class="widgetcontainer %2$s">',
       	'after_widget' => "",
		'before_title' => "<h3 class=\"widgettitle\">",
		'after_title' => "</h3>\n",
    ));

}
add_action( 'init', 'my_widgets_init' );

// adding the widget area to your child theme
function my_header_widgets() { ?>
  
  <div id="topbar">
    <div class="container"><?php
    
      //aside for all the pages
      if ( function_exists('dynamic_sidebar') && is_sidebar_active('header-aside') ) {
          echo '<div id="header-aside" class="aside">'. "\n" . '<ul class="xoxo">' . "\n";
          dynamic_sidebar('header-aside');
          echo '' . "\n" . '</div><!-- #header-aside .aside -->'. "\n";
      }
      
  			$user = wp_get_current_user();
  			//user is logged in, $user->ID will be their ID, etc..			
  			if ( is_user_logged_in()) {
  				$username = "<a href='" . wp_login_url() . "'>" . $user->display_name . "</a>" ;
  			} else {
  				$username =  wp_loginout('',0);
  			}
      
      $contribute  = "<div id='contribute'>";
      $contribute .= "  <a href='" . get_site_url() . "/contribute' class='button'>Contribute Your Work</a>";
      $contribute .= " &bull; " . $username;
      $contribute .=  "</div>";
      echo $contribute;
        ?>
    </div>
  </div><?php

  
}
add_action('thematic_belowheader', 'my_header_widgets',1);

// --------------------------------------------------------------- Login page:

// custom login for theme
function childtheme_custom_login() {
	echo '<link rel="stylesheet" type="text/css" href="' . get_bloginfo('stylesheet_directory') . '/customlogin.css" />';
}
 
add_action('login_head', 'childtheme_custom_login');

function childtheme_login_headerurl(){
  return "http://wikipediaillustrated.org";  
}

add_filter('login_headerurl', 'childtheme_login_headerurl');

function childtheme_login_headertitle(){
  return "Back to Wikipedia Illustrated";  
}

add_filter('login_headertitle', 'childtheme_login_headertitle');

function childtheme_message(){
  
  $message  = "<div id='login-message'>";
  $message .= " <h3>Want to contribute visually?</h3>";
  $message .= " <ol><li>Read the <a href='" . get_site_url() . "/contribute'>Contribution Guidelines</a></li>";
  $message .= "   <li>Go to the " . wp_register( '', '', 0) . "</li> area";
  $message .= "   <li>Or if you're coming back sign in here:";
  $message .= " </ol>";
  $message .= "</div>";
  return $message;  
}

add_filter('login_message', 'childtheme_message');

// ----------------------------------------------------------------  HEADER:

// Remove description from doctitle:

function homeDocTitle($content){
  if ( is_home() || is_front_page() ){
    $content = get_bloginfo('name') . " | Drafting a new path towards visual free culture";
  } elseif ( is_category(menu_key()) ) {
    $content = get_bloginfo('name') . " | ";
    $content .= __('Drafts for the Letter "', 'thematic');
    $content .= single_cat_title("", false) . '"';
  }
  return $content;
}
add_filter('thematic_doctitle', 'homeDocTitle');

//Remove Blog Description
 
function remove_thematic_headerstuff() {
  remove_action('thematic_header','thematic_blogdescription',5);
	remove_action('thematic_header','thematic_access',9);
}
add_action('init','remove_thematic_headerstuff');

function childtheme_header_container(){
  ?><div class="container"><?php
}
add_action('thematic_header','childtheme_header_container',0);

// Build a custom menu for articles/letters:

function childtheme_header_extension() {

  if ( function_exists( 'add_theme_support' ) ) {
  
  	// This theme uses wp_nav_menu()
  	add_theme_support( 'nav-menus' );
  }
  	?>
  	
    <div id="access">
      <div class="menu">
        <div class="list-label">Articles:</div>
        <ul id="letters" class="sf-menu sf-js-enabled">
          <?php
          $letters = menu_key();
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
        <!-- <div class="list-label slash">/</div> -->
		</div>
        
  		  <?php wp_nav_menu( 'sort_column=menu_order&container_class=menu&menu_class=sf-menu' ); ?>
  		</div><!-- #access -->
		</div> <!-- .container -->
  
	<?php
  global $post;
  
  $content = '';
  if(is_category(menu_key())) {
    
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
		
    $cat_head = '<div class="category-header">';
    $cat_head.= '<div class="container">';
    $cat_head.= '<div class="category-text">';
		$cat_head.= '<h1><span>';
		$cat_head.= single_cat_title('', FALSE);
		$cat_head.= ': </span>';
		$cat_head.= $article_name;
		$cat_head.= '' . "\n";
		$cat_head.= '<a href="http://en.' .$wiki_url . '" target="blank" class="wiki_link">' . $wiki_url . '</a>';
		$cat_head.= '</h1>' . "\n";
		$cat_head.= '<blockquote class="article-quote">';
		$cat_head.= get_latest_in_cat($cat[0]->cat_name, 'article_quote');
		$cat_head.= '</blockquote>';
		$cat_head.= '<a href="http://wikipedia.org/wiki/' . $wiki_name .'" title="read the full article on Wikipedia.org" target="blank" class="wikilink">wikipedia.org/wiki/' . $wiki_name . ' </a>';
		$cat_head.= get_prev_ops($cat[0]->cat_name);
		$cat_head.= '</div>';
    $cat_head.= '<div class="category-image wp-caption alignnone">';
		$cat_head.= get_latest_cat_img($cat[0]->cat_name, 'medium');
		$cat_head.= '<p class="wp-caption-text">';
		
		// determines whether the illustration was already edited into Wikipedia, or whther it's a draft:
		$edit = get_latest_in_cat($cat[0]->cat_name, 'edit');
		if($edit){
		  $cat_head.= 'Edited into Wikipedia [ ';
  		$cat_head.= '<a href="http://wikipedia.org/wiki/' . $wiki_name .'" title="read the full article on Wikipedia.org" target="blank">view article</a> | ';
  		$cat_head.= '<a href="http://wikipedia.org/wiki/?' . $wiki_name .'&oldid=' . $edit . '" title="read the full article on Wikipedia.org" target="blank">view edit</a> ]';
		} else {
		  $cat_head.= 'Latest draft for "' . $article_name . '"';
		}
		$cat_head.= '</p>';
		$cat_head.= '</div>';
		$cat_head.= '</div>';
		$cat_head.= '</div>';
    
    echo $cat_head;
    
  	add_filter('thematic_page_title','childtheme_page_title');

	}
	
}	
add_action('thematic_header','childtheme_header_extension',10);

// ---------------------------------------------------------------- Category specific:

function childtheme_page_title($content){
  
  if (is_category(menu_key())) {
    /*note in this case you must get rid of the .= here and replace it with = or else you will get double results. the .= just means that you are adding something on the end of the existing variable and the variable was already defined in the parent function*/
    $content = '<h1 class="page-title">';
    $content .= __('Drafts for the letter', 'thematic');
    $content .= ' <span>';
    $content .= single_cat_title('', FALSE);
    $content .= '</span>:</h1>' . "\n";
    $content .= '<div class="archive-meta">';
    if ( !(''== category_description()) ) : $content .= apply_filters('archive_meta', category_description()); endif;
    $content .= '</div>';
  }
  
  $content .= "\n";
  
  /*when filter you must always end your function w RETURN $variable or else nothing gets sent back to the parent function */
  
  return $content;
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

// ---------------------------------------------------------------- Home Page:

// Make the default index loop show excerpts
function childtheme_content() {
  if(is_home()){
    return 'excerpt';
  }
}
//add_filter('thematic_content','childtheme_content');

function childtheme_belowheader(){
  if(is_home() || !is_paged() ){
    ?>
    <div id="intro">
      <div class="container">
        <h1><?php bloginfo('description'); ?> </h1>
      </div>
    </div>
    <?php
	}
}
	
add_action('thematic_belowheader','childtheme_belowheader');

//Exclude the Guest category:

function exclude_category($query) {
  $ex_cat = '-' . get_category_by_slug( 'guest' )->term_id;
	if ( $query->is_feed || $query->is_home ) {
		$query->set('cat', $ex_cat);
	}
return $query;
}
add_filter('pre_get_posts', 'exclude_category');

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
	global $post;
  if (in_category(menu_key()) || in_category(array('guest'))) {
    $wiki_name = get_post_meta($post->ID, 'wiki_name', true);
  	// determines whether the illustration was already edited into Wikipedia, or whther it's a draft:
  	$edit = get_post_meta($post->ID, 'edit', true);
    $new .= '<p class="status">';
  	if($edit){
      $new .= '<a class="done" href="http://wikipedia.org/wiki/' . $wiki_name .'" title="read the full article on Wikipedia.org" target="blank">DONE</a> ';
  	  $new .= 'Edited into Wikipedia [ ';
  		$new .= '<a href="http://wikipedia.org/wiki/' . $wiki_name .'" title="read the full article on Wikipedia.org" target="blank">view article</a> | ';
  		$new .= '<a href="http://wikipedia.org/wiki/?' . $wiki_name .'&oldid=' . $edit . '" title="read the full article on Wikipedia.org" target="blank">view edit</a> ]';
  	} else {
      $new .= '<span class="draft">DRAFT</span> ';
  	  $new .= 'a later version will be contributed to Wikipedia';
  	}
    $new .= '</p>';
  }
  
  return $new;
}

add_filter('thematic_postheader','childtheme_postheader');

// Set the post footer:
function childtheme_postfooter() {
  if(!is_page()){?>
    </div>
  	<div class="entry-utility meta">
      <?php if(!is_category()){ 
        $post_id = get_the_id();
        $cat = get_the_category();
        if (in_category('guest')) {
          $article_title = get_post_meta($post_id, 'article_title', true);
          $article_quote = get_post_meta($post_id, 'article_quote', true);
          $wiki_name = get_post_meta($post_id, 'wiki_name', true);
        } elseif(in_category(menu_key())) {
          $article_title = get_latest_in_cat($cat[0]->cat_name, article_title);
          $article_quote = get_latest_in_cat($cat[0]->cat_name, article_quote);
          $wiki_name = get_latest_in_cat($cat[0]->cat_name, wiki_name);
        }
        ?>
        <div class="cat-links"><?php echo '
        <a href="' . get_category_link($cat[0]->cat_ID) . '"><span>' . $cat[0]->cat_name . ':</span> ' . $article_title ?></a></div>
          <?php 
          if ($article_quote){
            echo '<div class="article-quote">' . strip_tags($article_quote, '<p><strong><em>') . '</div><a title="read the full article on Wikipedia.org" href="http://wikipedia.org/wiki/' . $wiki_name .'" target="blank" class="wikilink">wikipedia.org/wiki/' . $wiki_name . ' </a>';
          }
        } 
        if (in_category(array('guest'))){
          ?> <p class="post-credit">By <?php the_author_posts_link(); ?> (<?php the_author_posts();?>) </p> <?php;
        }
        
        ?>
  		<?php the_tags( __( '<span class="tag-links">', 'sandbox' ), " ", "</span>" ) ?>
  		<div class="date"><?php the_time('M jS, Y'); ?></div>
  		<div class="comments-link"><?php comments_popup_link( __( 'No Comments', 'sandbox' ), __( '1 Comment', 'sandbox' ), __( '% Comments', 'sandbox' ) ) ?></div>
  		<?php edit_post_link( __( 'Edit', 'sandbox' ), "<span class='edit-link'>", "</span>" ) ?>
  	</div>
  	<?php
	}
}
add_filter ('thematic_postfooter', 'childtheme_postfooter');

// ---------------------------------------------------------------- Post navigation:

// We will use this one later...

function new_prev_nav_args() {
  if(is_single()){
    $args = array ('format'              => '%link',
									 'link'                => '<span class="meta-nav">&laquo; Earlier draft for this letter</span> %title',
									 'in_same_cat'         => TRUE,
									 'excluded_categories' => '');
		//reset for the meta category		
    if (in_category('meta')) { $args['link'] = '<span class="meta-nav meta-cat">&laquo;</span> %title';}
		return $args;
  }
}

//add_filter('thematic_previous_post_link_args', 'new_prev_nav_args');

function new_next_nav_args() {
  if(is_single()){
    $args = array ('format'              => '%link',
									 'link'                => '<span class="meta-nav">Newer draft for this letter &raquo;</span> %title',
									 'in_same_cat'         => TRUE,
									 'excluded_categories' => '');
		//reset for the meta category
    if (in_category('meta')) { $args['link'] = '%title <span class="meta-nav meta-cat">&raquo;</span>';}
		return $args;
  }
}

//add_filter('thematic_next_post_link_args', 'new_next_nav_args');

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


function my_comment_form_args($args){
  $extranotes .= '<div id="form-extra-notes" class="form-section"><p><strong>To embed an image or a video</strong> in your comment paste a link to it from Flickr, YouTube (or <abbr title="YouTube, Vimeo, DailyMotion, blip.tv, Viddler, Flickr, Scribd, Photobucket, SmugMug...">other services</abbr>) in a separate line.</p></div>';
  $args['comment_notes_after'] = $extranotes . $args['comment_notes_after'];
  return $args;
}

add_filter('thematic_comment_form_args', 'my_comment_form_args');

// ---------------------------------------------------------------- Widgets:

function child_remove_widget_area() {
  unregister_sidebar('primary-aside');
}
add_action( 'admin_init', 'child_remove_widget_area');

// ---------------------------------------------------------------- JS:


function custom_js(){
  //Add the shorten features (truncating text the JS way)

  ?>
  <script type="text/javascript" src="<?php bloginfo('stylesheet_directory')?>/js/jquery.jtruncate.pack.js"></script>
  <script type="text/javascript">
    jQuery(document).ready(function(){
      jQuery(".post .article-quote").jTruncate({  
        length: 400,  
        minTrail: 90,  
        moreText: "[ + ]",  
        lessText: "[ - ]",  
        /* ellipsisText: " (truncated)",   */
        /* moreAni: "fast",   */
        lessAni: "fast"
      }); 
    });
  
  </script>
  <?php

  //Remove alt tags for the menu using javascript
  ?>
  <script type="text/javascript">
  
    jQuery(document).ready(function(){
  
      jQuery("a.fancybox img").each(function(){
        jQuery(this).after(' <p class="enlarge">click to enlarge</p>');
      }),
      
      jQuery("a.fancybox img").each(function(){
        jQuery(this).parent().width(jQuery(this).outerWidth());
      }),
      
      jQuery("img.alignright").each(function(){
        jQuery(this).parent().addClass('a-right');
      }),
      
      jQuery("img.alignleft").each(function(){
        jQuery(this).parent().addClass('a-left');
      })
    });
  
  </script>
  <?php
}
add_filter('wp_head','custom_js');


?>