<?php
/**
* print html meta
*/
function wp_idea_stream_posted_on() {
	printf( __( '<span class="%1$s">Posted on</span> %2$s <span class="meta-sep">by</span> %3$s', 'wp-idea-stream' ),
		'meta-prep meta-prep-author',
		sprintf( '<a href="%1$s" title="%2$s" rel="bookmark"><span class="entry-date">%3$s</span></a>',
			get_permalink(),
			esc_attr( get_the_time() ),
			get_the_date()
		),
		sprintf( '<span class="author vcard"><a class="url fn n" href="%1$s" title="%2$s">%3$s</a></span>',
			get_author_idea_url( get_the_idea_author_meta( 'ID' ) ),
			sprintf( esc_attr__( 'View all ideas by %s', 'wp-idea-stream' ), get_idea_author() ),
			get_idea_author()
		)
	);
}

function wp_idea_stream_desc_edit(){
	?>
	<form action="" method="post">
	<textarea id="ideastream_desc_content" name="_ideastream_desc_content" style="width:98%"><?php if ( get_displayed_idea_author_meta( 'description' ) ) : displayed_idea_author_meta( 'description' ); endif; ?></textarea>
	<?php wp_nonce_field('wp-ideastream-desc-check-referrer','wp-ideastream-desc-check');?>
	<input type="submit" name="_ideastream_desc_save" value="<?php _e('Save this description','wp-idea-stream');?>"/>
	</form>
	<?php
}

function wp_idea_stream_new_form(){
	return home_url( '/is/new-idea/' );
}

function wp_idea_stream_posted_in_cat(){
	echo get_the_term_list( get_the_ID(), 'category-ideas', __('Categories: ','wp-idea-stream'), ', ',' ');
}

function wp_idea_stream_posted_in_tag(){
	return get_the_term_list( get_the_ID(), 'tag-ideas', __('with ', 'wp-idea-stream'), ', ',' ');
}

/* idea author tags */

function get_author_idea_url($id){
	$user_info = get_userdata($id);
	$link = home_url( '/is/idea-author/'.$user_info->user_login.'/' );
	return $link;
}

function get_the_idea_author_meta( $field ){
	global $authordata;
	return $authordata->$field;
}

function get_idea_author(){
	global $authordata;
	return $authordata->display_name;
}

function the_idea_author_meta( $field ){
	echo get_the_idea_author_meta( $field );
}

function wp_idea_stream_get_the_author($user_slug){
	$user = get_userdatabylogin($user_slug);
	return $user->ID;
}

function wp_idea_stream_loggedin_user_displayed($noidea = false){
	global $current_user, $user_slug;
	if(!$noidea){
		if($current_user->user_login==get_the_idea_author_meta('user_login')) return true;
		else return false;
	}
	else{
		if($current_user->user_login == $user_slug) return true;
		else return false;
	}
}

function get_displayed_idea_author_meta($field){
	global $user_slug;
	$user = get_userdatabylogin($user_slug);
	return $user->$field;
}
function displayed_idea_author_meta($field){
	echo get_displayed_idea_author_meta($field);
}
function get_displayed_idea_author(){
	global $user_slug;
	$user = get_userdatabylogin($user_slug);
	return $user->display_name;
}

function wp_idea_stream_paginate_link($paged = false){
	global $idea_meta;
	$pagid = 1;
	if(isset($_GET['pagid']) && !$paged){
		$pagid = intval($_GET['pagid']);
	}
	elseif(get_query_var('paged')){
		$pagid = get_query_var('paged');
	}
	$offset = $idea_meta['per_page'] * ($pagid - 1);
	
	$max_pages = ceil($idea_meta['all_count'] / $idea_meta['per_page']);
	
	if($max_pages > 1){
		if(!$paged){
			$page_links = paginate_links( array(
		        'base' => add_query_arg( 'pagid', '%#%' ),
		        'format' => '',
		        'prev_text' => __('&laquo;'),
		        'next_text' => __('&raquo;'),
		        'total' => $max_pages,
		        'current' => $pagid
		     ));
		}
		else{
			$page_links = paginate_links( array(
		        'base' => add_query_arg( 'paged', '%#%' ),
		        'format' => '',
		        'prev_text' => __('&laquo;'),
		        'next_text' => __('&raquo;'),
		        'total' => $max_pages,
		        'current' => $pagid
		     ));
		}
			
		
 		if ( $page_links ) { ?>
			<div class="nav-previous"><?php $page_links_text = sprintf( __( 'Page: ' ) . '%s',
			$page_links
		); echo $page_links_text."</div>";

		}
	}
}
function wp_idea_stream_number_ideas(){
	global $idea_meta;
	return $idea_meta['all_count'];
}

function wp_idea_stream_allowed_html_tags(){
	$allowedideatags = array(
		'a' => array(
			'class' => array (),
			'href' => array (),
			'id' => array (),
			'title' => array (),
			'rel' => array (),
			'rev' => array (),
			'name' => array (),
			'target' => array()),
		'b' => array(),
		'big' => array(),
		'blockquote' => array(
			'id' => array (),
			'cite' => array (),
			'class' => array(),
			'lang' => array(),
			'xml:lang' => array()),
		'br' => array (
			'class' => array ()),
		'del' => array(
			'datetime' => array ()),
		'em' => array(),
		'font' => array(
			'color' => array (),
			'face' => array (),
			'size' => array ()),
		'h1' => array(
			'align' => array (),
			'class' => array (),
			'id'    => array (),
			'style' => array ()),
		'h2' => array (
			'align' => array (),
			'class' => array (),
			'id'    => array (),
			'style' => array ()),
		'h3' => array (
			'align' => array (),
			'class' => array (),
			'id'    => array (),
			'style' => array ()),
		'h4' => array (
			'align' => array (),
			'class' => array (),
			'id'    => array (),
			'style' => array ()),
		'h5' => array (
			'align' => array (),
			'class' => array (),
			'id'    => array (),
			'style' => array ()),
		'h6' => array (
			'align' => array (),
			'class' => array (),
			'id'    => array (),
			'style' => array ()),
		'hr' => array (
			'align' => array (),
			'class' => array (),
			'noshade' => array (),
			'size' => array (),
			'width' => array ()),
		'i' => array(),
		'img' => array(
			'alt' => array (),
			'align' => array (),
			'border' => array (),
			'class' => array (),
			'height' => array (),
			'hspace' => array (),
			'longdesc' => array (),
			'vspace' => array (),
			'src' => array (),
			'style' => array (),
			'width' => array ()),
		'li' => array (
			'align' => array (),
			'class' => array ()),
		'p' => array(
			'class' => array (),
			'align' => array (),
			'dir' => array(),
			'lang' => array(),
			'style' => array (),
			'xml:lang' => array()),
		'span' => array (
			'class' => array (),
			'dir' => array (),
			'align' => array (),
			'lang' => array (),
			'style' => array (),
			'title' => array (),
			'xml:lang' => array()),
		'strike' => array(),
		'strong' => array(),
		'table' => array(
			'align' => array (),
			'bgcolor' => array (),
			'border' => array (),
			'cellpadding' => array (),
			'cellspacing' => array (),
			'class' => array (),
			'dir' => array(),
			'id' => array(),
			'rules' => array (),
			'style' => array (),
			'summary' => array (),
			'width' => array ()),
		'tbody' => array(
			'align' => array (),
			'char' => array (),
			'charoff' => array (),
			'valign' => array ()),
		'td' => array(
			'abbr' => array (),
			'align' => array (),
			'axis' => array (),
			'bgcolor' => array (),
			'char' => array (),
			'charoff' => array (),
			'class' => array (),
			'colspan' => array (),
			'dir' => array(),
			'headers' => array (),
			'height' => array (),
			'nowrap' => array (),
			'rowspan' => array (),
			'scope' => array (),
			'style' => array (),
			'valign' => array (),
			'width' => array ()),
		'tfoot' => array(
			'align' => array (),
			'char' => array (),
			'class' => array (),
			'charoff' => array (),
			'valign' => array ()),
		'th' => array(
			'abbr' => array (),
			'align' => array (),
			'axis' => array (),
			'bgcolor' => array (),
			'char' => array (),
			'charoff' => array (),
			'class' => array (),
			'colspan' => array (),
			'headers' => array (),
			'height' => array (),
			'nowrap' => array (),
			'rowspan' => array (),
			'scope' => array (),
			'valign' => array (),
			'width' => array ()),
		'thead' => array(
			'align' => array (),
			'char' => array (),
			'charoff' => array (),
			'class' => array (),
			'valign' => array ()),
		'tr' => array(
			'align' => array (),
			'bgcolor' => array (),
			'char' => array (),
			'charoff' => array (),
			'class' => array (),
			'style' => array (),
			'valign' => array ()),
		'u' => array(),
		'ul' => array (
			'class' => array (),
			'style' => array (),
			'type' => array ()),
		'ol' => array (
			'class' => array (),
			'start' => array (),
			'style' => array (),
			'type' => array ())
			);
	return $allowedideatags;
}

function is_user_allowed_to_feature_ideas(){
	global $current_user;
	$ideastream_allowed_featuring_members = get_option('_ideastream_allowed_featuring_members');
	if(current_user_can('manage_options')) return true;
	elseif($ideastream_allowed_featuring_members!="" && count($ideastream_allowed_featuring_members)>0 && in_array($current_user->ID, $ideastream_allowed_featuring_members)) return true;
	else return false;
}

function is_new_idea(){
	if( strpos( $_SERVER['REQUEST_URI'], 'is/new-idea') !== false ) {
		return true;
	}
	else return false;
}
function is_all_ideas(){
	if( strpos( $_SERVER['REQUEST_URI'], 'is/all-ideas' ) !== false ) {
		return true;
	}
	else return false;
}
function is_featured_ideas(){
	if( strpos( $_SERVER['REQUEST_URI'], 'is/featured-ideas' ) !== false ) {
		return true;
	}
	else return false;
}
function is_idea_author(){
	if( strpos( $_SERVER['REQUEST_URI'], 'is/idea-author' ) !== false ) {
		return true;
	}
	else return false;
}

function wpis_editor_the_title() {
	$title = ! empty( $_POST["_wp_is_title"] ) ? $_POST["_wp_is_title"] : '';
	echo apply_filters( 'wpis_editor_the_title', $title ); 
}

function wpis_editor_the_content() {
	$content = ! empty( $_POST["content"] ) ? $_POST["content"] : '';
	return apply_filters( 'wpis_editor_the_content', $content );
}

function wp_idea_stream_load_editor_new(){
	global $wp_idea_stream_submit_errors;
	if ( is_user_logged_in() ) {
		if( isset( $_GET['moderation'] ) ){
			$ideastream_moderation_message = get_option('_ideastream_moderation_message');
			if( !$ideastream_moderation_message || $ideastream_moderation_message == "" ){
				$ideastream_moderation_message = __( 'Your idea is awaiting moderation. Thanks for submitting it.', 'wp-idea-stream' );
			}
			?>
				<div id="ideastream_moderation"><p><?php echo $ideastream_moderation_message;?></p></div>
			<?php
		}
		else{
			
			if( count( $wp_idea_stream_submit_errors ) > 0 ){
				?>
				<div id="ideastream_errors">
					<p><?php _e('Please make sure to check the following error(s) :','wp-idea-stream');?></p>
					<ul>
						
					<?php foreach( $wp_idea_stream_submit_errors as $error ) :?>
						<li><?php echo $error ;?></li>
					<?php endforeach;?>
					
					</ul>
				</div>
				<?php
			}
			
			//editor args
			$args = array("textarea_name" => "content",
				'wpautop' => true,
				'media_buttons' => false,
				'editor_class' => 'ideastream_tinymce',
				'textarea_rows' => get_option('default_post_edit_rows', 10),
				'teeny' => false,
				'dfw' => false,
				'tinymce' => true,
				'quicktags' => false
			);
			
			?>
			
			<form action="" method="post" enctype="multipart/form-data">
				
				<div class="new-idea-form">
					<label for="_wp_is_title"> <?php _e('Title of your Idea','wp-idea-stream');?></label>
					<input type="text" id="wp_is_title" name="_wp_is_title" value="<?php wpis_editor_the_title();?>"/>
				</div>
				
				<div class="new-idea-form">
					<label for="content"> <?php _e('Content of your Idea','wp-idea-stream');?></label>
					
					<?php wp_editor( wpis_editor_the_content(), "ideastream_content", $args );?>
					
				</div>
			
				<div class="new-idea-form">
					<label for="_wp_is_category"> <?php _e('Choose at least one category','wp-idea-stream');?></label>
						<p>
							<?php
							// getting taxo cat
							$table_taxo = get_terms('category-ideas', 'orderby=count&hide_empty=0');
							?>
							<?php if( count($table_taxo) >= 0) :?>
								<?php foreach($table_taxo as $taxo):?>
									<input type="checkbox" name="_wp_is_category[]" id="wp_is_category-<?php echo $taxo->term_id;?>" value="<?php echo $taxo->term_id;?>"><?php echo $taxo->name ?>&nbsp;
								<?php endforeach;?>
							<?php endif;?>
						</p>
				</div>

				<div class="new-idea-form">
					<label for="_wp_is_tags"><?php _e('Add your tag(s)','wp-idea-stream');?></label>
					<small><?php _e('Type your tag, then hit the return or space key to add it','wp-idea-stream');?></small><br/>
					<input type="text" id="wp_is_tags" name="_wp_is_tags"/>
				</div>

				<?php do_action('wp_idea_stream_add_extra_fields');

				echo wp_nonce_field('wp-ideastream-check-referrer','wp-ideastream-check', true, false);
			
				?>

				<div class="is-action-btn">
				<input type="submit" name="_wp_is_submit_idea" id="wp_is_submit_idea" value="<?php _e('Submit your idea','wp-idea-stream');?> &rarr;" class="ideastream_btn"/>
				</div>
				
			</form>

			<?php
		}
	}
	else{
		$ideastream_login_message = get_option('_ideastream_login_message');
		if(!$ideastream_login_message || $ideastream_login_message==""){
			$ideastream_login_message = __('You need to be member of this site and logged in to submit an idea','wp-idea-stream');
		}
		?>
		<div id="ideastream_notlogged">
			<p><?php echo $ideastream_login_message;?></p>
			<?php do_action('wp_idea_stream_submit_idea_not_logged');?>
		</div>
		<?php
	}
}

add_action('wp_idea_stream_insert_editor','wp_idea_stream_load_editor_new');

function wp_idea_stream_editor_settings( $type = false ) {
	if( empty($type) )
		return false;
		
	$editor_settings = get_option('_ideastream_editor_config');
	
	// by default we have image and links
	if( !is_array( $editor_settings ) )
		return 1;
	
	else {
		return $editor_settings[$type];
	}
}

function wp_idea_stream_load_twentyup() {
	
	if( get_query_var('ideas') || get_query_var('category-ideas') || get_query_var('tag-ideas') || get_query_var('pagename') == 'new-idea' || get_query_var('pagename') == 'idea-author' || get_query_var('pagename') == 'featured-ideas' || get_query_var('pagename') == 'all-ideas' || wp_idea_stream_is_all_ideas_onfront() ){
		
		$dep = is_new_idea() ? array('style-subidea') : false ;
		
		if( get_option("stylesheet") == "twentyeleven" )
			wp_enqueue_style('2011-fix', WP_IDEA_STREAM_PLUGIN_URL .'/css/2011.css', $dep );
		else if( get_option("stylesheet") == "twentytwelve" )
			wp_enqueue_style('2012-fix', WP_IDEA_STREAM_PLUGIN_URL .'/css/2012.css', $dep );
		
	}
}

add_action( 'wp_idea_stream_manage_twentyup', 'wp_idea_stream_load_twentyup' );


function wp_idea_stream_dummy_post() {
	global $wp_query, $post;

	$dummy = array(
		'ID'                    => 0,
		'post_status'           => 'publish',
		'post_author'           => 0,
		'post_parent'           => 0,
		'post_type'             => 'page',
		'post_date'             => 0,
		'post_date_gmt'         => 0,
		'post_modified'         => 0,
		'post_modified_gmt'     => 0,
		'post_content'          => '',
		'post_title'            => __( 'Submit your idea!', 'wp-idea-stream' ),
		'post_excerpt'          => '',
		'post_content_filtered' => '',
		'post_mime_type'        => '',
		'post_password'         => '',
		'post_name'             => '',
		'guid'                  => '',
		'menu_order'            => 0,
		'pinged'                => '',
		'to_ping'               => '',
		'ping_status'           => '',
		'comment_status'        => 'closed',
		'comment_count'         => 0,
		'filter'                => 'raw',

		'is_404'                => false,
		'is_page'               => true,
		'is_single'             => false,
		'is_archive'            => false,
		'is_tax'                => false,
	);

	// Set the $post global
	$post = new WP_Post( (object) $dummy );

	// Copy the new post global into the main $wp_query
	$wp_query->post       = $post;
	$wp_query->posts      = array( $post );

	// Prevent comments form from appearing
	$wp_query->post_count = 1;
	$wp_query->is_404     = $dummy['is_404'];
	$wp_query->is_page    = $dummy['is_page'];
	$wp_query->is_single  = $dummy['is_single'];
	$wp_query->is_archive = $dummy['is_archive'];
	$wp_query->is_tax     = $dummy['is_tax'];
}
