<?php
/*
 Plugin Name: Simple New Post Emails
 Plugin URI: https://github.com/10up/simple-new-post-emails
 Description: Allow site members to check a box and get new posts via email. Includes a widget.
 Author: 10up
 Version: 0.1
 Author URI: http://10up.com
 */

/*
Copyright 2013 10up

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 2 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
*/

class Simple_New_Post_Emails {
	/**
	 * Set up hooks.
	 */
	public function __construct() {
		require_once( 'inc/widget-snpe.php' );

		add_action( 'widgets_init', array( $this, 'widgets_init' ) );
		add_action( 'personal_options', array( $this, 'personal_options' ) );
		add_action( 'edit_user_profile_update', array( $this, 'option_save' ) );
		add_action( 'personal_options_update', array( $this, 'option_save' ) );
		add_action( 'wp_ajax_snpe-options-save', array( $this, 'ajax_option_save' ) );
		add_action( 'publish_post', array( $this, 'publish_post' ), 10, 2 );
	}

	/**
	 * Register the widget.
	 * @return null
	 */
	public function widgets_init() {
		register_widget( 'SNPE_Widget' );
	}

	/**
	 * Add the setting to the user profile page.
	 * @param  WP_User $user User object.
	 * @return null
	 */
	public function personal_options( $user ) {
?>
<tr class="snpe-option">
	<th scope="row">New Post Emails</th>
	<td>
		<label for="snpe_send">
			<input name="snpe_send" type="checkbox" id="snpe_send" value="Y"<?php checked( $user->snpe_send, 'Y' ); ?> />
			Get an email when a new post is published
		</label>
	</td>
</tr>
<?php
	}

	/**
	 * Save our custom profile option.
	 * @param  int $user_id User ID
	 * @return bool         Whether or not the option was successfully saved.
	 */
	public function option_save( $user_id ) {
		if ( ! current_user_can( 'edit_user', $user_id ) ) {
			return false;
		}

		if ( isset( $_POST['snpe_send'] ) && 'Y' === $_POST['snpe_send'] ) {
			return update_usermeta( $user_id, 'snpe_send', 'Y' );
		} else {
			return delete_usermeta( $user_id, 'snpe_send' );
		}
	}

	/**
	 * Save our profile option on Ajax.
	 * @return null
	 */
	public function ajax_option_save() {
		check_admin_referer( 'snpe-options-save' );

		$user = wp_get_current_user();
		if ( ! $this->option_save( $user->ID ) ) {
			wp_send_json_error();
		}

		$data = isset( $_POST['snpe_send'] ) ? true : false;
		wp_send_json_success( $data );
	}

	/**
	 * Fire off emails if this is the first publish.
	 * @param  int     $post_id Post ID.
	 * @param  WP_Post $post    Post object.
	 * @return null
	 */
	public function publish_post( $post_id, $post ) {
		// We've already sent an email for this before, so bail.
		if ( 'Y' === get_post_meta( $post_id, 'snpe_sent', true ) ) {
			return;
		}

		$sent = false;

		$subject = $post->post_title;
		$message = $post->post_content;

		// Get the users who want emails
		$users = get_users( array( 'meta_key' => 'snpe_send', 'meta_value' => 'Y' ) );

		if ( empty( $users ) ) {
			return;
		}

		$sent = $this->send_mail( $users, $subject, $message );

		if ( $sent ) {
			update_post_meta( $post_id, 'snpe_sent', 'Y' );
		}
	}

	private function send_mail( $users, $subject, $message ) {
		// BCC all users specified
		$headers = array();

		foreach ( (array) $users as $user ) {
			$headers[] = 'Bcc: ' . $user->user_email;
		}

		// Plain text email, so strip those tags.
		$subject = wp_strip_all_tags( $subject );
		$message = wp_strip_all_tags( $message );

		return wp_mail( 'noreply@10up.com', $subject, $message, $headers );
	}
}

$simple_new_post_emails = new Simple_New_Post_Emails();
