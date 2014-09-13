<?php
/*
 Plugin Name: Simple New Post Emails
 Plugin URI: https://github.com/10up/simple-new-post-emails
 Description: Allow site members to check a box and get new posts via email. Includes a widget.
 Author: 10up
 Version: 0.1
 Author URI: http://10up.com
 */

class Simple_New_Post_Emails {
	/**
	 * Set up hooks.
	 */
	public function __construct() {
		add_action( 'widgets_init', array( $this, 'widgets_init' ) );
		add_action( 'personal_options', array( $this, 'personal_options' ) );
		add_action( 'publish_post', array( $this, 'publish_post' ), 10, 2 );
	}

	/**
	 * Register the widget.
	 * @return null
	 */
	public function widgets_init() {
	}

	/**
	 * Add the setting to the user profile page.
	 * @param  WP_User $user User object.
	 * @return null
	 */
	public function personal_options( $user ) {
	}

	/**
	 * Fire off emails if this is the first publish.
	 * @param  int     $post_id Post ID.
	 * @param  WP_Post $post    Post object.
	 * @return null
	 */
	public function publish_post( $post_id, $post ) {
	}
}

$simple_new_post_emails = new Simple_New_Post_Emails();
