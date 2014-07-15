<?php
/*
Plugin Name: WP Idea Stream
Plugin URI: http://imathi.eu/tag/ideastream/
Description: Adds an Idea Management System to your WordPress!
Version: 1.1.1
Requires at least: 3.9
Tested up to: 4.0
License: GNU/GPL 2
Author: imath
Author URI: http://imathi.eu/
Text Domain: wp-idea-stream
Domain Path: /languages/
*/

define ( 'WP_IDEA_STREAM_PLUGIN_NAME', 'wp-idea-stream' );
define ( 'WP_IDEA_STREAM_PLUGIN_URL', WP_PLUGIN_URL . '/' . WP_IDEA_STREAM_PLUGIN_NAME );
define ( 'WP_IDEA_STREAM_PLUGIN_DIR', WP_PLUGIN_DIR . '/' . WP_IDEA_STREAM_PLUGIN_NAME );
define ( 'WP_IDEA_STREAM_PLUGIN_URL_JS', plugins_url('js' , __FILE__) );
define ( 'WP_IDEA_STREAM_PLUGIN_URL_IMG',  plugins_url('images' , __FILE__) );
define ( 'WP_IDEA_STREAM_VERSION', '1.1.1' );

/*custom post type*/
add_action('init','wp_idea_stream_register_post_type');

function wp_idea_stream_register_post_type(){
	$ideas_args = array(
		'public'=>true,
		'query_var'=>'ideas',
		'rewrite'=>array(
			'slug'=>'is/idea',
			'with_front'=>false),
		'supports'=>array(
			'title',
			'editor',
			'author',
			'excerpt',
			'comments',
			'custom-fields'),
		'labels'=>array(
			'name'=> __('Ideas', 'wp-idea-stream'),
			'menu_name' => __('IdeaStream','wp-idea-stream'),
			'all_items'=> __('All Ideas','wp-idea-stream'),
			'singular_name'=> __('Idea','wp-idea-stream'),
			'add_new'=> __('Add New Idea','wp-idea-stream'),
			'add_new_item'=> __('Add New Idea','wp-idea-stream'),
			'edit_item'=> __('Edit Idea','wp-idea-stream'),
			'new_item'=> __('New Idea','wp-idea-stream'),
			'view_item'=> __('View Idea','wp-idea-stream'),
			'search_items'=> __('Search Ideas','wp-idea-stream'),
			'not_found'=>__('No Ideas Found','wp-idea-stream'),
			'not_found_in_trash'=>__('No Ideas Found in Trash','wp-idea-stream')),
		'menu_icon' => WP_IDEA_STREAM_PLUGIN_URL.'/images/is-logomenu.png',
		'taxonomies'=>array(
			'tag-ideas', 'category-ideas'),
			);
		/*register the ideas post-type*/
		register_post_type('ideas', $ideas_args);
}
/*custom taxo*/
add_action('init','wp_idea_stream_register_taxo');

function wp_idea_stream_register_taxo(){
	$idea_cats_args = array(
		'hierarchical'=>true,
		'query_var'=>'category-ideas',
		'rewrite'=>array(
			'slug'=>'is/category',
			'with_front'=>false),
		'labels'=>array(
			'name'=> __('Categories','wp-idea-stream'),
			'singular_name'=> __('Category','wp-idea-stream'),
			'edit_item'=> __('Edit Category','wp-idea-stream'),
			'update_item'=> __('Update Category','wp-idea-stream'),
			'add_new_item'=> __('Add New Category','wp-idea-stream'),
			'new_item_name'=> __('New Category Name','wp-idea-stream'),
			'all_items'=> __('All Categories','wp-idea-stream'),
			'search_items'=> __('Search Categories','wp-idea-stream'),
			'parent_item'=> __('Parent Category','wp-idea-stream'),
			'parent_item_colon'=> __('Parent Category:','wp-idea-stream')),
		);
	$idea_tags_args = array(
		'hierarchical'=>false,
		'query_var'=>'tag-ideas',
		'show_tagcloud'=>true,
		'rewrite'=>array(
			'slug'=>'is/tag',
			'with_front'=>false),
		'labels'=>array(
			'name'=> __('Tags','wp-idea-stream'),
			'singular_name'=> __('Tag','wp-idea-stream'),
			'edit_item'=> __('Edit Tag','wp-idea-stream'),
			'update_item'=> __('Update Tag','wp-idea-stream'),
			'add_new_item'=> __('Add New Tag','wp-idea-stream'),
			'new_item_name'=> __('New Tag Name','wp-idea-stream'),
			'all_items'=> __('All Tags','wp-idea-stream'),
			'search_items'=> __('Search Tags','wp-idea-stream'),
			'popular_items'=> __('Popular Tags','wp-idea-stream'),
			'separate_items_with_commas'=> __('Separate tags with commas','wp-idea-stream'),
			'add_or_remove_items'=> __('Add or remove tags','wp-idea-stream'),
			'choose_from_most_used'=> __('Choose from the most popular tags','wp-idea-stream')),
		);
	
	/*register the Category taxo*/
	register_taxonomy('category-ideas', array('ideas'), $idea_cats_args);
	
	/*register the Tag taxo*/
	register_taxonomy('tag-ideas', array('ideas'), $idea_tags_args);
}

//load template tags
require_once(dirname(__FILE__).'/includes/wp-idea-stream-templatetags.php');
require_once(dirname(__FILE__).'/includes/wp-idea-stream-filters.php');

add_action('template_redirect','wp_idea_stream_catch_uri', 99);

function wp_idea_stream_catch_uri(){
	global $idea_meta, $wp_query, $wp_idea_stream_submit_errors, $post;
	if(isset($_POST['_wp_is_submit_idea'])){
		require_once(dirname(__FILE__).'/includes/wp-idea-stream-add-new.php');
	}
	if(isset($_POST['_ideastream_desc_save'])){
		global $current_user;
		if(!wp_verify_nonce($_POST['wp-ideastream-desc-check'],'wp-ideastream-desc-check-referrer')){
			wp_die(__('To update your Biographical Info, please use this blog form','wp-idea-stream'));
		}
		if(isset($_POST['_ideastream_desc_content'])){
			update_user_meta($current_user->ID, 'description', wp_kses($_POST['_ideastream_desc_content'], array()));
		}
	}
	if(isset($_GET['cat-is'])){
		$cat_ideas = get_term($_GET['cat-is'], 'category-ideas' );
		$link = get_term_link($cat_ideas, 'category-ideas');
		wp_redirect($link);
		die();
	}
	
	do_action( 'wp_idea_stream_manage_twentyup');
	
	if( get_query_var( 'ideas' ) && ! get_query_var('feed') ){
		wp_enqueue_script('jquery');
		wp_enqueue_script('jraty', WP_IDEA_STREAM_PLUGIN_URL .'/js/jquery.raty.min.js', 'jquery');
		wp_enqueue_style('style-idea', WP_IDEA_STREAM_PLUGIN_URL .'/css/single.css');
		$template = locate_template( 'single-idea.php', true );
		if ( empty( $template ) ) {
			load_template( dirname( __FILE__ ) . '/templates/single-idea.php', true );
		} 
		die();
	}
	elseif(get_query_var('category-ideas') && !get_query_var('feed')){
		$idea_meta = array("all_count" => $wp_query->found_posts, "per_page" => $wp_query->query_vars['posts_per_page']);
		wp_enqueue_script('jquery');
		wp_enqueue_script('jraty', WP_IDEA_STREAM_PLUGIN_URL .'/js/jquery.raty.min.js', 'jquery');
		$template = locate_template('category-ideas.php', true);
		if ( empty( $template ) ) {
			load_template(dirname( __FILE__ ) . '/templates/category-ideas.php', true);
		} 
		die();
	}
	elseif( get_query_var('tag-ideas') && ! get_query_var('feed') ) {
		$idea_meta = array("all_count" => $wp_query->found_posts, "per_page" => $wp_query->query_vars['posts_per_page']);
		wp_enqueue_script('jquery');
		wp_enqueue_script('jraty', WP_IDEA_STREAM_PLUGIN_URL .'/js/jquery.raty.min.js', 'jquery');
		$template = locate_template('tag-ideas.php', true);
		if ( empty( $template ) ){
			load_template(dirname( __FILE__ ) . '/templates/tag-ideas.php', true);
		} 
		die();
	}
	elseif( get_query_var('pagename') == 'new-idea' ) {
		status_header( 200 );
		wp_idea_stream_dummy_post();
		wp_enqueue_script('jquery');
		wp_enqueue_script('si-tag-editor', WP_IDEA_STREAM_PLUGIN_URL .'/js/jquery.tag.editor-min.js', 'jquery');
		wp_enqueue_style('style-subidea', WP_IDEA_STREAM_PLUGIN_URL .'/css/style.css');
		$template = locate_template( 'new-idea.php', true );
		if ( empty( $template ) ) {
			load_template( dirname( __FILE__ ) . '/templates/new-idea.php', true );
		} 
		die();
	}
	elseif( get_query_var('pagename') == 'idea-author' ){
		status_header( 200 );
		$wp_query->is_404 = false;
		wp_enqueue_script('jquery');
		wp_enqueue_script('jraty', WP_IDEA_STREAM_PLUGIN_URL .'/js/jquery.raty.min.js', 'jquery');
		global $user_slug;
		$user_slug = get_query_var( 'user_slug' );
		$pagid = 1;
		if(isset($_GET['pagid'])) $pagid = $_GET['pagid'];
		$args = array(
			'post_type'=> 'ideas',
			'author' => wp_idea_stream_get_the_author($user_slug),
			'paged' => $pagid
		);
		query_posts( $args );
		$q = new WP_Query ($args);
		$idea_meta = array("all_count" => $q->found_posts, "per_page" => $q->query_vars["posts_per_page"]);
		$template = locate_template('idea-author.php', true);
		if ( empty( $template ) ){
			load_template(dirname( __FILE__ ) . '/templates/idea-author.php', true);
		} 
		die();
	}
	elseif( get_query_var('pagename') == 'featured-ideas' ){
		status_header( 200 );
		$wp_query->is_404 = false;
		$wp_query->is_page = true;
		wp_enqueue_script('jquery');
		wp_enqueue_script('jraty', WP_IDEA_STREAM_PLUGIN_URL .'/js/jquery.raty.min.js', 'jquery');
		$pagid = 1;
		if(isset($_GET['pagid'])) $pagid = $_GET['pagid'];
		$args = array(
			'post_type'=> 'ideas',
			'meta_key' => 'idea_stream_featured',
			'meta_value' => '1',
			'paged' => $pagid
		);
		query_posts( $args );
		$q = new WP_Query ($args);
		$idea_meta = array("all_count" => $q->found_posts, "per_page" => $q->query_vars["posts_per_page"]);
		$template = locate_template('featured-ideas.php', true);
		if( empty( $template ) ){
			load_template(dirname( __FILE__ ) . '/templates/featured-ideas.php', true);
		} 
		die();
	}
	elseif( get_query_var('pagename') == 'all-ideas' || wp_idea_stream_is_all_ideas_onfront() ){
		status_header( 200 );
		$wp_query->is_404 = false;
		wp_enqueue_script('jquery');
		wp_enqueue_script('jraty', WP_IDEA_STREAM_PLUGIN_URL .'/js/jquery.raty.min.js', 'jquery');
		$pagid = 1;
		if(isset($_GET['pagid'])) $pagid = $_GET['pagid'];
		$args = array(
			'post_type'=> 'ideas',
			'paged' => $pagid
		);
		query_posts( $args );
		$q = new WP_Query ($args);
		$idea_meta = array("all_count" => $q->found_posts, "per_page" => $q->query_vars["posts_per_page"]);
		$template = locate_template('all-ideas.php', true);
		if( empty( $template ) ){
			load_template(dirname( __FILE__ ) . '/templates/all-ideas.php', true);
		} 
		die();
	}
}

add_action('wp_head', "wp_idea_stream_add_catandtag_feed");

function wp_idea_stream_add_catandtag_feed(){
	if( get_query_var( 'category-ideas' ) ){
		$title = get_bloginfo( 'name' ) . " &raquo; ". get_query_var( 'category-ideas' );
		$feed = home_url( $_SERVER['REQUEST_URI'] .'feed/' );
	}
	if( get_query_var( 'tag-ideas' ) ){
		$title = get_bloginfo( 'name' ) . " &raquo; " . get_query_var( 'tag-ideas' );
		$feed = home_url( $_SERVER['REQUEST_URI'] .'feed/' );
	} 
	if( ! empty( $feed ) ){
		?>
		<link rel="alternate" type="application/rss+xml" title="<?php echo $title;?>" href="<?php echo $feed;?>">
		<?php
	}
}

function wp_idea_stream_is_all_ideas_onfront(){
	global $wp_query;
	if( get_option( 'page_on_front' ) == 'all-ideas' && empty( $wp_query->post->ID )) return true;
	else return false;
}

function wp_idea_stream_page_title( $title ){
	global $user_slug;
	if( is_new_idea() ){
		return __( 'New Idea', 'wp-idea-stream' ) . " | ";
	}
	if( is_all_ideas() || ( is_home() && get_option( 'page_on_front' ) == 'all-ideas' && !is_featured_ideas() ) ){
		return __( 'All Ideas','wp-idea-stream' ) . " | ";
	}
	if( is_featured_ideas() ){
		return __( 'Featured Ideas', 'wp-idea-stream' ) . " | ";
	}
	if( is_idea_author() ){
		return __( 'Author Ideas', 'wp-idea-stream' ) . " | " . $user_slug . " | ";
	}
	else return $title;
}

add_filter('wp_title','wp_idea_stream_page_title');

function wp_idea_stream_bp_page_title($title){
	global $user_slug;
	if( is_new_idea() ){
		return get_bloginfo( 'name' ) . " | " . __( 'New Idea', 'wp-idea-stream' );
	}
	if( is_all_ideas() || ( is_home() && get_option( 'page_on_front' ) == 'all-ideas' && ! is_featured_ideas() ) ){
		return get_bloginfo( 'name' ) . " | " . __( 'All Ideas','wp-idea-stream');
	}
	if(is_featured_ideas()){
		return get_bloginfo( 'name' ) . " | " . __( 'Featured Ideas','wp-idea-stream' );
	}
	if(is_idea_author()){
		return get_bloginfo( 'name' ) . " | " . __( 'Author Ideas','wp-idea-stream' ) . " | " . $user_slug;
	}
	else return $title;
}

if( function_exists( 'bp_init' ) ){
	add_filter('bp_page_title', 'wp_idea_stream_bp_page_title');
}


function wp_idea_stream_add_footer_js(){
	global $post;
	$builtin_rating_option = get_option('_ideastream_builtin_rating');
	if(get_query_var('pagename') == 'new-idea'){
		?>
		<script type="text/javascript">
		jQuery(document).ready(function() {
            jQuery("#wp_is_tags").tagEditor(
            {
				separator: ' ',
				completeOnSeparator: true,
                completeOnBlur: true,
				confirmRemoval: false
            });
            jQuery("#resetTagsButton").click(function() {
                jQuery("#wp_is_tags").tagEditorResetTags();
            });
        });
		</script>
		<?php
	}
	if(get_post_type( $post->ID )=="ideas"){
		?>
		<script type="text/javascript">
		<?php if(is_single() && $builtin_rating_option != "no"):?>
			function postVote(score, id){
				if(!window.ajaxurl){
					ajaxurl ="<?php echo admin_url('admin-ajax.php');?>";
				}
				jQuery(".rating-info").html("<?php _e('Saving your rating, please wait','wp-idea-stream');?>");
				var data = {
					action: 'wp_idea_stream_vote',
					vote: score,
					idea:id
				};
				jQuery.post(ajaxurl, data, function(response) {
					if(response){
						jQuery(".rating-info").html("<?php _e('Thanks to your rate the average rating of this idea is now : ','wp-idea-stream');?>"+response);
						jQuery.fn.raty.start(response, "#rates-"+id);
					}
					else jQuery(".rating-info").html("Vote Ko");
				});
			}
			<?php if(!is_user_logged_in()):?>
				jQuery(document).ready(function(){
					jQuery(".rating-ideas").click(function(){
						alert("<?php _e('You must be logged in to rate this idea!','wp-idea-stream');?>");
					});
				});
			<?php endif;?>
		<?php elseif ( $builtin_rating_option != "no" ): ?>
			jQuery(document).ready(function(){
				jQuery(".rating-ideas").each(function(){
					var score = jQuery(this).attr("rel");
					jQuery(this).raty(
				    {
				        half:  true,
						readOnly:true,
						start:score,
						noRatedMsg:'<?php _e("not rated yet","wp-idea-stream");?>',
					<?php if( get_option( '_ideastream_hint_list' ) != "" && count( get_option( '_ideastream_hint_list' ) ) > 0 ){
						$hintlist = "['". implode("','", array_map( 'esc_js', (array) get_option( '_ideastream_hint_list' ) ) )."']";
						echo 'hintList:'.$hintlist.',';
						echo 'number:'.count( get_option( '_ideastream_hint_list' ) ).',';
					}
					?>
						path: '<?php echo WP_IDEA_STREAM_PLUGIN_URL;?>/images'
				    });
				});

				jQuery( ".rating-ideas").on( 'click', function() {
					location.href = jQuery( this ).data( 'link' );
				} );
			});
		<?php endif;?>
		</script>
		<?php
	}
	if(is_user_logged_in() && is_idea_author()){
		?>
		<script type="text/javascript">
		jQuery("#ideastream-edit-btn").click(function() {
            jQuery("#ideastream-desc-edit").show();
			jQuery("#ideastream-desc-action").hide();
        });
		</script>
		<?php
	}
}

add_action('wp_footer', 'wp_idea_stream_add_footer_js');

/*voting functions*/
add_action('wp_ajax_wp_idea_stream_vote', 'wp_idea_stream_vote_callback');
add_action('wp_ajax_nopriv_wp_idea_stream_vote', 'wp_idea_stream_vote_callback');

function wp_idea_stream_count_ratings($post_meta, $user_id=false){
	$total_votes = 0;
	$star_votes = 0;
	$readonly = 0;
	foreach($post_meta as $vote => $array_ids){
		if($user_id && in_array($user_id, $array_ids["user_ids"])) $readonly =1;
		$star_votes += $vote * count($array_ids);
		$total_votes += count($array_ids);
	}
	if($user_id) return number_format($star_votes/$total_votes, 1)."|".$readonly;
	else return number_format($star_votes/$total_votes, 1);
}

function wp_idea_stream_ratings_single(){
	$builtin_rating_option = get_option('_ideastream_builtin_rating');
	if($builtin_rating_option == "no") return false;
	$start = 0;
	$readonly = 1;
	if(is_user_logged_in()){
		global $current_user;
		$user_id = $current_user->ID;
		$readonly = 0;
	}
	$the_votes = get_post_meta(get_the_ID(),"_ideastream_rates", true);
	if($the_votes!="" && count($the_votes)>0){
		if(is_user_logged_in()){
			$data_votes = explode('|',wp_idea_stream_count_ratings($the_votes, $user_id));
			$readonly = $data_votes[1];
			$start = $data_votes[0];
		}
		else{
			$start = wp_idea_stream_count_ratings($the_votes);
		}
	}
	?>
	<div id="rates-<?php the_ID(); ?>" class="rating-ideas"></div><div class="rating-info"></div>
	<script type="text/javascript">
	jQuery("#rates-<?php the_ID(); ?>").raty(
    {
        half:  true,
		readOnly:<?php if($readonly!=0) echo "true"; else echo "false";?>,
		start:<?php echo $start;?>,
		targetKeep:true,
		target:     '.rating-info',
		noRatedMsg:'<?php _e("not rated yet","wp-idea-stream");?>',
	<?php if(get_option('_ideastream_hint_list')!="" && count(get_option('_ideastream_hint_list')) >0){
		$hintlist = "['". implode( "','", array_map( 'esc_js', (array) get_option('_ideastream_hint_list') ) )."']";
		echo 'hintList:'.$hintlist.',';
		echo 'number:'.count(get_option('_ideastream_hint_list')).',';
	}
	?>
		path: '<?php echo WP_IDEA_STREAM_PLUGIN_URL;?>/images',
		click: function(score, evt) {
			<?php if(is_user_logged_in()):?>
		    	postVote(score, <?php the_ID(); ?>);
				jQuery.fn.raty.readOnly(true, "#rates-<?php the_ID(); ?>");
			<?php endif;?>
		}
    });
	</script>
	<?php
}

function wp_idea_stream_ratings(){
	$builtin_rating_option = get_option('_ideastream_builtin_rating');
	if($builtin_rating_option == "no") return false;
	$start = 0;
	$average_vote = get_post_meta(get_the_ID(),"_ideastream_average_rate", true);
	if($average_vote!=""){
		$start = $average_vote;
	}
	?>
	<div id="rates-<?php the_ID(); ?>" class="rating-ideas" rel="<?php echo $start;?>" data-link="<?php the_permalink(); ?>"></div>
	<?php
}

function wp_idea_stream_vote_callback(){
	global $current_user;
	$user_id = $current_user->ID; 
	$idea_id = $_POST['idea'];
	if(strlen($_POST['vote']) >1 ){
		$vote_user = substr($_POST['vote'], 0, 1);
		$vote_user +=1;
	}
	else $vote_user = $_POST['vote'];
	$the_votes = get_post_meta($idea_id,"_ideastream_rates", true);
	if($the_votes!="" && count($the_votes)>0){
		foreach($the_votes as $vote => $array_ids){
			if(!in_array($user_id, $array_ids["user_ids"])){
				if($vote == $vote_user){
					$the_votes[$vote]["user_ids"] = array_merge($array_ids["user_ids"], array($user_id));
				}
				else{ 
					$the_votes[$vote_user]["user_ids"] = array($user_id);
				}
			}

		}
	}
	else $the_votes[$vote_user]["user_ids"] = array($user_id);
	
	update_post_meta($idea_id, "_ideastream_rates", $the_votes);
	$average_rate = wp_idea_stream_count_ratings($the_votes);
	update_post_meta($idea_id, "_ideastream_average_rate", $average_rate);
	echo $average_rate;
	die();
}

/**
* beginning of sharing functions
*/

function wp_idea_stream_sharing_services(){
	$ideastream_sharing_options = get_option('_ideastream_sharing_options');
	if($ideastream_sharing_options=="no") return false;
	$ideastream_twitter_account = get_option('_ideastream_twitter_account');
	?>
	<ul class="ideastream_share">
		<li class="ideastream_share_text"><?php _e('Share this idea!', 'wp-idea-stream');?></li>
		<li class="ideastream_share_email">
			<a href="mailto:?&subject=<?php echo rawurlencode(get_the_title());?>&body=<?php echo get_permalink();?>" title="<?php _e('Share by email','wp-idea-stream');?>"><img src="<?php echo WP_IDEA_STREAM_PLUGIN_URL;?>/images/share_mail.png" border="0" alt="<?php _e('Share by email','wp-idea-stream');?>"></a>
		</li>
		<?php if($ideastream_twitter_account && $ideastream_twitter_account!=""):?>
		<li class="ideastream_share_twitter">
			<a href="http://twitter.com/share" class="twitter-share-button" data-count="horizontal" data-via="<?php echo $ideastream_twitter_account;?>" data-url="<?php the_permalink();?>" data-text="<?php the_title();?>" data-lang="en">Tweet</a>
			<script type="text/javascript" src="http://platform.twitter.com/widgets.js"></script>
		</li>
		<?php endif;?>
		<?php do_action('wp_idea_stream_add_sharing_services');?>
	</ul>
	<?php
}
/* end of sharing functions */

/**
* beginning of "official feature" of an idea
*/

add_action('comment_form_logged_in_after','wp_idea_stream_feature_idea_form');
if(function_exists('bp_init')){
	add_action('bp_blog_comment_form','wp_idea_stream_feature_idea_form');
}

function wp_idea_stream_feature_idea_form(){
	$ideastream_feature_from_comments = get_option('_ideastream_feature_from_comments');
	if($ideastream_feature_from_comments=="no" || $ideastream_feature_from_comments=="") return false;
	$featured = get_post_meta(get_the_ID(), 'idea_stream_featured', true);
	if(get_post_type( get_the_ID() )=="ideas" && is_user_allowed_to_feature_ideas()){
		?>
		<div id="ideastream_featured">
			<label for="_idea_stream_featured_idea"><?php _e('Feature this idea ?','wp-idea-stream');?></label>
			<input type="radio" value="1" name="_idea_stream_featured_idea" id="idea_stream_featured_idea_yes" <?php if($featured==1) echo "checked";?>><?php _e('Yes');?>&nbsp;<input type="radio" value="0" name="_idea_stream_featured_idea" id="idea_stream_featured_idea_no" <?php if($featured!=1) echo "checked";?>><?php _e('No');?>&nbsp;
		</div>
		<?php
	}
	
}

add_action('comment_post','wp_idea_stream_feature_idea_manage');

function wp_idea_stream_feature_idea_manage(){
	global $post;
	$featured = get_post_meta($post->ID, 'idea_stream_featured', true);
	if($_REQUEST['_idea_stream_featured_idea'] =="1" && $featured==""){
		add_post_meta($post->ID, 'idea_stream_featured', 1);
	}
	elseif($_REQUEST['_idea_stream_featured_idea'] =="0" && $featured=="1"){
		delete_post_meta($post->ID, 'idea_stream_featured');
	}
}
/* end of featuring ideas */

/**
* Widgets !
*/
require_once( dirname(__FILE__).'/includes/wp-idea-stream-widgets.php' );


/**
* Beginning of functions to add all-ideas on front page
* Inspired by BuddyPress theme activity on front feature
*/
function wp_idea_stream_wp_pages_filter( $page_html ) {
	
	if ( 'page_on_front' != substr( $page_html, 14, 13 ) )
		return $page_html;

	$selected = false;
	$page_html = str_replace( '</select>', '', $page_html );

	if ( 'page'== get_option( 'show_on_front' ) && get_option( 'page_on_front' ) == 'all-ideas' )
		$selected = ' selected="selected"';

	$page_html .= '<option class="level-0" value="all-ideas"' . $selected . '>' . __( 'IdeaStream', 'wp-idea-stream' ) . '</option></select>';
	return $page_html;
}
add_filter( 'wp_dropdown_pages', 'wp_idea_stream_wp_pages_filter' );

function wp_idea_stream_wp_page_on_front_update( $oldvalue, $newvalue ) {
	if ( ! is_admin() || ! is_super_admin() || empty( $_POST['page_on_front'] ) )
		return false;

	if ( 'all-ideas' == $_POST['page_on_front'] )
		return 'all-ideas';
	else
		return $oldvalue;
}
add_action( 'pre_update_option_page_on_front', 'wp_idea_stream_wp_page_on_front_update', 10, 2 );


function wp_idea_stream_fix_get_posts_on_activity_front() {
	global $wp_query;

	if ( !empty($wp_query->query_vars['page_id']) && 'all-ideas' == $wp_query->query_vars['page_id'] )
		$wp_query->query_vars['page_id'] = '"all-ideas"';
}
add_action( 'pre_get_posts', 'wp_idea_stream_fix_get_posts_on_activity_front' );

function wp_idea_stream_fix_the_posts_on_activity_front( $posts ) {
	global $wp_query;

	if ( empty( $posts ) && !empty( $wp_query->query_vars['page_id'] ) && '"all-ideas"' == $wp_query->query_vars['page_id'] )
		$posts = array( (object) array( 'ID' => 'all-ideas' ) );

	return $posts;
}
add_filter( 'the_posts', 'wp_idea_stream_fix_the_posts_on_activity_front' );

/* end of functions to add all-ideas on front page */


function wp_idea_stream_adminbar_menu($wp_admin_bar){
	if(is_user_logged_in()){
		global $current_user;
		$wp_admin_bar->add_menu( array( 'id' => 'wpideastream', 'title' => __('IdeaStream','wp-idea-stream'), 'href' => home_url( '/is/all-ideas/' ) ) );
		$wp_admin_bar->add_menu( array( 'id' => 'wpideastream-all', 'parent' => 'wpideastream', 'title' => __('All Ideas','wp-idea-stream'), 'href' => home_url( '/is/all-ideas/' ) ) );
		$wp_admin_bar->add_menu( array( 'id' => 'wpideastream-my', 'parent' => 'wpideastream', 'title' => __('My Ideas','wp-idea-stream'), 'href' => get_author_idea_url( $current_user->ID ) ) );
		$wp_admin_bar->add_menu( array( 'id' => 'wpideastream-featured', 'parent' => 'wpideastream', 'title' => __('Featured Ideas','wp-idea-stream'), 'href' => home_url( '/is/featured-ideas/' ) ) );
		$wp_admin_bar->add_menu( array( 'id' => 'wpideastream-new', 'parent' => 'wpideastream', 'title' => __('New Idea','wp-idea-stream'), 'href' => home_url( '/is/new-idea/' ) ) );
	}
}

add_action( 'admin_bar_menu', 'wp_idea_stream_adminbar_menu',999 );

add_action('admin_menu','wp_idea_stream_options_menu');

function wp_idea_stream_options_menu(){
	add_submenu_page( 'edit.php?post_type=ideas', __('IdeaStream Options','wp-idea-stream'), __('IdeaStream Options','wp-idea-stream'), 'edit_others_posts', 'ideastream-options', 'wp_idea_stream_options' );
	if(get_option('_ideastream_vestion')!=WP_IDEA_STREAM_VERSION) update_option('_ideastream_vestion', WP_IDEA_STREAM_VERSION);
}

function wp_idea_stream_options(){
	require_once(dirname(__FILE__).'/includes/wp-idea-stream-options.php');
}

add_action('admin_print_styles','wp_idea_stream_print_admin_css');

function wp_idea_stream_print_admin_css(){
	global $post;
	if( ( ! empty( $_GET['page'] ) && $_GET['page'] == "ideastream-options" ) || ( ! empty( $_GET['post_type'] ) && $_GET['post_type'] == "ideas" ) || ( ! empty( $post->ID ) && get_post_type( $post->ID ) == "ideas" ) ){
		wp_enqueue_style( 'ideastream-admin-css', WP_IDEA_STREAM_PLUGIN_URL.'/css/admin.css' );
	}
}

add_action('init', 'wp_idea_stream_add_rewrite_rules');

function wp_idea_stream_add_rewrite_rules(){
	add_rewrite_rule('is/idea-author/?([^/]*)', 'index.php?pagename=idea-author&user_slug=$matches[1]', 'top');
	add_rewrite_rule('is/all-ideas', 'index.php?pagename=all-ideas', 'top');
	add_rewrite_rule('is/new-idea', 'index.php?pagename=new-idea', 'top');
	add_rewrite_rule('is/featured-ideas', 'index.php?pagename=featured-ideas', 'top');
}

add_filter('query_vars', 'wp_idea_stream_add_query_vars');

function wp_idea_stream_add_query_vars($vars){
	$vars[] = 'user_slug';
	return $vars;
}

register_activation_hook ( __FILE__ , 'wp_idea_stream_activate');

function wp_idea_stream_activate(){
	if( get_option('_ideastream_vestion') && get_option('_ideastream_vestion') != WP_IDEA_STREAM_VERSION){
		update_option('_ideastream_vestion', WP_IDEA_STREAM_VERSION);
	}		
	elseif( ! get_option('_ideastream_vestion' ) ){
		wp_idea_stream_add_rewrite_rules();
		wp_idea_stream_register_post_type();
		wp_idea_stream_register_taxo();
		flush_rewrite_rules();
		update_option('_ideastream_vestion', WP_IDEA_STREAM_VERSION);
	}
}

function wp_idea_stream_activation_notice() {
	global $wp_rewrite;

	if ( ! is_admin() )
		return false;

	if ( empty( $wp_rewrite->permalink_structure ) && ! empty( $_POST['permalink_structure'] ) ) { ?>
		<div id="message" class="updated fade">
			<p><?php printf( __( '<strong>Idea Stream is almost ready</strong>. You must <a href="%s">update your permalink structure</a> to something other than the default for it to work.', 'wp-idea-stream' ), admin_url( 'options-permalink.php' ) ) ?></p>
		</div><?php
	}
	elseif( empty( $wp_rewrite->extra_permastructs['ideas'] ) ) {?>
		<div id="message" class="updated fade">
			<p><?php printf( __( '<strong>Idea Stream is almost ready</strong>. You must <a href="%s">refresh your permalink structure</a> to something other than the default <strong>in order to activate the Ideas post type.</strong>', 'wp-idea-stream' ), admin_url( 'options-permalink.php' ) ) ?></p>
		</div><?php
	}
}
add_action( 'admin_notices', 'wp_idea_stream_activation_notice' );

add_filter('body_class', 'wp_idea_stream_fix_404_new_idea');

function wp_idea_stream_fix_404_new_idea( $bodyclass ){
	if( is_new_idea() ){
		for( $i=0; $i < count( $bodyclass) ; $i++){
			if( $bodyclass[$i] == "error404" ){
				$bodyclass[$i] = "is_new_idea";
			}
		}
		return $bodyclass;
	}
	else return $bodyclass;
}
function wp_idea_stream_load_language_file(){
	load_plugin_textdomain(WP_IDEA_STREAM_PLUGIN_NAME, WP_IDEA_STREAM_PLUGIN_DIR.'/languages/', WP_IDEA_STREAM_PLUGIN_NAME.'/languages/');
}

add_action('plugins_loaded', 'wp_idea_stream_load_language_file');

/* let's filter to avoid the spam and trash link in notification message */

function wp_idea_stream_filter_notification($message, $comment_id){
	$comment = get_comment($comment_id);
	$post = get_post($comment->comment_post_ID);
	$user = get_userdata( $post->post_author );
	
	if( !user_can($user->ID, 'edit_comment') ){
		$notify_message  = sprintf( __( 'New comment on your post "%s"' ), $post->post_title ) . "\r\n";
		$notify_message .= __('Comment: ') . "\r\n" . $comment->comment_content . "\r\n\r\n";
		$notify_message .= sprintf( __('Permalink: %s'), get_permalink( $comment->comment_post_ID ) . '#comment-' . $comment_id ) . "\r\n";
		return $notify_message;
	}
	else return $message;
}

add_filter('comment_notification_text', 'wp_idea_stream_filter_notification', 9, 2);
?>