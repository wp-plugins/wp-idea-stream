<?php
/**
 * WP Idea Stream Administration Thanks screens.
 *
 * About WP Idea Stream & credits screens
 *
 * @package WP Idea Stream
 * @subpackage admin/thanks
 *
 * @since 2.0.0
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * About screen
 *
 * @package WP Idea Stream
 * @subpackage admin/thanks
 *
 * @since 2.0.0
 *
 * @uses   wp_idea_stream_get_version() to get plugin's version
 * @uses   add_query_arg() to add query vars to an url
 * @uses   admin_url() to build a link inside the current blog's Administration
 * @uses   get_transient() to get the value of a transient
 * @uses   delete_transient() to delete a transient
 * @uses   wp_oembed_get() to get the vidéo démo of the plugin
 * @return string HTML output
 */
function wp_idea_stream_admin_about() {
	$display_version = wp_idea_stream_get_version();
	$settings_url = add_query_arg( 'page', 'ideastream', admin_url( 'options-general.php' ) );
	$has_upgraded = get_transient( '_ideastream_reactivated_upgrade' );
	$upgraded = __( 'activating', 'wp-idea-stream' );

	if ( ! empty( $has_upgraded ) ) {
		$upgraded = __( 'upgrading to', 'wp-idea-stream' );
		delete_transient( '_ideastream_reactivated_upgrade' );
	}
	?>
	<div class="wrap about-wrap">
		<h1><?php printf( esc_html_x( 'WP Idea Stream %s', 'about screen title', 'wp-idea-stream' ), $display_version ); ?></h1>
		<div class="about-text"><?php printf( esc_html__( 'Thank you for %1$s the latest version of WP Idea Stream! %2$s brings some cool improvements.', 'wp-idea-stream' ), $upgraded, $display_version ); ?></div>
		<div class="wp-idea-stream-badge"></div>

		<h2 class="nav-tab-wrapper">
			<a class="nav-tab nav-tab-active" href="<?php echo esc_url( admin_url( add_query_arg( array( 'page' => 'about-ideastream' ), 'index.php' ) ) ); ?>">
				<?php esc_html_e( 'About', 'wp-idea-stream' ); ?>
			</a>
			<a class="nav-tab" href="<?php echo esc_url( admin_url( add_query_arg( array( 'page' => 'credits-ideastream' ), 'index.php' ) ) ); ?>">
				<?php _e( 'Credits', 'wp-idea-stream' ); ?>
			</a>
		</h2>

		<div class="headline-feature">
			<h2 style="text-align:center"><?php echo esc_html_x( 'Share ideas, great ones will rise to the top!', 'IdeaStream Headline', 'wp-idea-stream' ); ?></h2>

			<div class="feature-section">
				<p>
					<?php esc_html_e( 'WP Idea Stream is a WordPress plugin to power idea management for your site. Your members will be able to easily create, share and rate ideas.', 'wp-idea-stream' ); ?>
				</p>

				<?php if ( ! empty( $has_upgraded ) ) : ?>
					<h4><?php esc_html_e( 'Important: these features are not supported anymore.', 'wp-idea-stream' );?></h4>
					<ul>
						<li><?php esc_html_e( 'Set the list of ideas as the front page of the blog.', 'wp-idea-stream' );?></li>
						<li><?php esc_html_e( 'Sharing options (twitter or email).', 'wp-idea-stream' );?></li>
						<li><?php esc_html_e( 'Disabling the built-in rating system.', 'wp-idea-stream' );?></li>
					</ul>
				<?php endif; ?>
			</div>
		</div>

		<div class="headline-feature">
			<h3><?php esc_html_e( 'Attach files to your ideas thanks to...', 'wp-idea-stream' ); ?></h3>

			<div class="featured-image">
				<img src="<?php echo esc_url( wp_idea_stream()->includes_url . 'admin/buddydrive-editor.gif' ); ?>" alt="<?php esc_attr_e( 'Attach files to your ideas', 'wp-idea-stream' ); ?>">
			</div>

			<div class="feature-section">
				<h3><?php esc_html_e( '... BuddyPress & BuddyDrive.', 'wp-idea-stream' ); ?></h3>
				<p><?php esc_html_e( 'You are using the BuddyPress (2.3+) and BuddyDrive (1.3+) plugins? Very good choice! Your members can now attach public files to their ideas.', 'wp-idea-stream' ); ?></p>
			</div>

			<div class="clear"></div>
		</div>

		<div class="changelog feature-list">
			<h2 class="about-headline-callout"><?php esc_html_e( 'Some Improvements..', 'wp-idea-stream' ); ?></h2>
			<div class="feature-section col one-col">
				<div class="col-1">
					<h4><?php esc_html_e( 'About wp-idea-stream-custom.php', 'wp-idea-stream' ); ?></h4>
					<p>
						<?php esc_html_e( 'On multisite configs, you can now include custom features specific to each blog where the plugin is activated by using a wp-idea-stream-custom file including the blog id in its name, eg:', 'wp-idea-stream' ); ?>
						&nbsp;<code>wp-idea-stream-custom-2.php</code>&nbsp;<?php esc_html_e( 'will only be loaded when the current blog has its ID == 2', 'wp-idea-stream' ); ?>&nbsp;
						<a href="https://github.com/imath/wp-idea-stream/wiki/wp-idea-stream-custom.php#on-multisite-configs-a-custom-file-for-each-blog"><?php esc_html_e( 'Learn more &rarr;', 'wp-idea-stream' ); ?></a>
					</p>
					<h4><?php esc_html_e( 'About sign-ups', 'wp-idea-stream' ); ?></h4>
					<p><?php printf( esc_html__( '%s now includes a sign-up page for regular configs & multisite ones when only user registration is enabled. If you are using BuddyPress or a multisite config for any other registration settings: their specific sign-ups page will be used.', 'wp-idea-stream' ), $display_version ); ?></p>
					<p><?php esc_html_e( 'About multisite configs having user registration enabled, the plugin will allow any child blog to have a specific registration management. New users will automatically be added as subscribers (or the chosen default role) to the child blog.', 'wp-idea-stream' ); ?></p>
					<h4><?php esc_html_e( 'WP Idea Stream template parts loading', 'wp-idea-stream' ); ?></h4>
					<p><?php esc_html_e( 'The way the plugin&#39;s specific template parts are loaded has been improved.', 'wp-idea-stream' ); ?></p>
					<h4><?php esc_html_e( 'About other small things...', 'wp-idea-stream' ); ?></h4>
					<ul>
						<li><?php esc_html_e( 'Default slugs are now translatable: make sure to use regular characters with no spaces and lower cases when translating them in your language pack.', 'wp-idea-stream' ); ?></li>
						<li><?php esc_html_e( 'The style of WP Idea Stream template parts has been optimized for the Twentyfifteen theme.', 'wp-idea-stream' ); ?></li>
					</ul>
				</div>
			</div>
		</div>

		<div class="changelog">
			<div class="return-to-dashboard">
				<a href="<?php echo esc_url( $settings_url );?>" title="<?php _e( 'Configure WP Idea Stream', 'wp-idea-stream' ); ?>"><?php _e( 'Go to the IdeaStream Settings page', 'wp-idea-stream' );?></a>
			</div>
		</div>

	</div>
	<?php
}

/**
 * Credits screen
 *
 * @package WP Idea Stream
 * @subpackage admin/thanks
 *
 * @since 2.0.0
 *
 * @uses   wp_idea_stream_get_version() to get plugin's version
 * @uses   add_query_arg() to add query vars to an url
 * @uses   admin_url() to build a link inside the current blog's Administration
 * @return string HTML output
 */
function wp_idea_stream_admin_credits() {
	$display_version = wp_idea_stream_get_version();
	$settings_url = add_query_arg( 'page', 'ideastream', admin_url( 'options-general.php' ) );
	?>
	<div class="wrap about-wrap">
		<h1><?php printf( esc_html_x( 'WP Idea Stream %s', 'credit screen title', 'wp-idea-stream' ), $display_version ); ?></h1>
		<div class="about-text"><?php printf( esc_html__( '%s version of WP Idea Stream was also successfully released thanks to them!', 'wp-idea-stream' ), $display_version ); ?></div>
		<div class="wp-idea-stream-badge"></div>

		<h2 class="nav-tab-wrapper">
			<a class="nav-tab" href="<?php echo esc_url( admin_url( add_query_arg( array( 'page' => 'about-ideastream' ), 'index.php' ) ) ); ?>">
				<?php esc_html_e( 'About', 'wp-idea-stream' ); ?>
			</a>
			<a class="nav-tab nav-tab-active" href="<?php echo esc_url( admin_url( add_query_arg( array( 'page' => 'credits-ideastream' ), 'index.php' ) ) ); ?>">
				<?php esc_html_e( 'Credits', 'wp-idea-stream' ); ?>
			</a>
		</h2>

		<div class="changelog">
			<h4 class="wp-people-group"><?php _e( 'The team!', 'wp-idea-stream' ); ?></h4>
			<ul class="wp-people-group " id="wp-people-group-core-team">
				<li class="wp-person" id="wp-person-imath">
					<a href="http://profiles.wordpress.org/imath"><img src="http://0.gravatar.com/avatar/8b208ca408dad63888253ee1800d6a03?s=60" class="gravatar" alt="Mathieu Viet" /></a>
					<a class="web" href="http://profiles.wordpress.org/imath">imath</a>
					<span class="title"><?php _e( 'Creator', 'wp-idea-stream' ); ?></span>
				</li>
				<li class="wp-person" id="wp-person-aglekis">
					<a href="http://profiles.wordpress.org/aglekis"><img src="http://0.gravatar.com/avatar/9aed4c3373374032e4ecdde02894d5fb?s=60" class="gravatar" alt="Grégoire Noyelle" /></a>
					<a class="web" href="http://profiles.wordpress.org/aglekis">Grégoire Noyelle</a>
					<span class="title"><?php _e( 'Contributor', 'wp-idea-stream' ); ?></span>
				</li>
			</ul>
		</div>

		<div class="changelog">
			<h4 class="wp-people-group"><?php esc_html_e( 'Special thanks.', 'wp-idea-stream' ); ?></h4>
			<div class="ideastream-credits">
				<a href="http://2015.paris.wordcamp.org/"><img src="http://2015.paris.wordcamp.org/files/2014/11/250-250.jpg" class="gravatar" alt="WordCamp Paris 2015" /></a>
			</div>
			<p><?php printf( esc_html__( 'WP Idea Stream was the choice of the WordCamp Paris 2015 organization team to manage their &quot;Call for Speakers&quot;. Some requested features were very specific to their need and were all added as custom hooks in the %s file.', 'wp-idea-stream' ), '<a href="https://github.com/imath/wc-talk">wp-idea-stream-custom.php</a>' ); ?></p>
			<p><?php esc_html_e( 'The plugin was completely transformed to let the speakers submit their talks privately. The managing team was able to discuss together using private comments and evaluate each talk using the built-in rating system.', 'wp-idea-stream' ); ?></p>
			<p><?php esc_html_e( 'Many thanks to WordCamp Paris organizers and speakers for this great experience.', 'wp-idea-stream' ); ?></p>
		</div>

		<h4 class="wp-people-group"><?php esc_html_e( 'WP Idea Stream&#39;s external libraries and useful code', 'wp-idea-stream' ); ?></h4>
		<ul class="wp-people-group " id="wp-people-group-project-leaders">
			<li class="wp-person" id="wp-person-sniperwolf">
				<a href="https://github.com/sniperwolf"><img src="https://avatars1.githubusercontent.com/u/741938?v=2&s=60" class="gravatar" alt="Fabrizio Fallico" /></a>
				<a class="web" href="https://github.com/sniperwolf">Fabrizio Fallico</a>
				<span class="title"><a href="https://github.com/sniperwolf/taggingJS">taggingJS</a></span>
			</li>
			<li class="wp-person" id="wp-person-wbotelhos">
				<a href="https://github.com/wbotelhos"><img src="https://avatars2.githubusercontent.com/u/116234?v=2&s=60" class="gravatar" alt="Washington Botelho" /></a>
				<a class="web" href="https://github.com/wbotelhos">Washington Botelho</a>
				<span class="title"><a href="https://github.com/wbotelhos/raty">Raty</a></span>
			</li>
			<li class="wp-person" id="wp-person-garyjones">
				<a href="https://github.com/GaryJones"><img src="https://avatars3.githubusercontent.com/u/88371?v=2&s=60" class="gravatar" alt="Gary Jones" /></a>
				<a class="web" href="https://github.com/GaryJones">Gary Jones</a>
				<span class="title"><a href="https://github.com/GaryJones/Gamajo-Template-Loader">Template Loader class</a></span>
			</li>
		</ul>

		<div class="changelog">
			<div class="return-to-dashboard">
				<a href="<?php echo esc_url( $settings_url );?>" title="<?php esc_html_e( 'Configure WP Idea Stream', 'wp-idea-stream' ); ?>"><?php esc_html_e( 'Go to the IdeaStream Settings page', 'wp-idea-stream' );?></a>
			</div>
		</div>

	</div>
	<?php
}
