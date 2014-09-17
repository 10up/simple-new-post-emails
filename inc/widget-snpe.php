<?php
/**
 * Widget class.
 */
class SNPE_Widget extends WP_Widget {
	public function __construct() {
		$widget_ops = array( 'classname' => 'snpe-widget', 'description' => 'Allow a user to sign up for simple new post emails.' );
		parent::__construct( 'snpe-widget', 'Simple New Post Emails', $widget_ops );
	}

	public function widget( $args, $instance ) {
		// Currently only on for logged in users
		if ( ! is_user_logged_in() ) {
			return;
		}

		wp_enqueue_script( 'snpe-widget', plugins_url( 'js/snpe-widget.js', dirname( __FILE__ ) ), array( 'jquery' ), '0.1', true );

		wp_localize_script( 'snpe-widget', 'snpe_vars', array( 'ajaxurl' => admin_url( 'admin-ajax.php', 'relative' ) ) );

		$user = wp_get_current_user();

		$title = empty( $instance['title'] ) ? 'New Post Emails' : $instance['title'];

		echo $args['before_widget'];
		echo $args['before_title'] . $title . $args['after_title'];
?>
<form name="snpe_options" class="snpe-options">
	<p>
		<label for="snpe_send">
			<input name="snpe_send" type="checkbox" id="snpe_send" value="Y"<?php checked( $user->snpe_send, 'Y' ); ?> />
			<span class="spinner" style="display:none;width:20px;height:20px;background:url(<?php echo includes_url( '/images/spinner-2x.gif' ); ?>) no-repeat center;background-size:20px 20px">&nbsp;</span>
			Email me when a new post is published
		</label>
	</p>

	<input type="hidden" name="action" value="snpe-options-save" />
	<?php wp_nonce_field( 'snpe-options-save' ); ?>
</form>
<?php
		echo $args['after_widget'];
	}

	public function update( $new_instance, $old_instance ) {
		$instance = $old_instance;
		$instance['title'] = wp_strip_all_tags( $new_instance['title'] );
		return $instance;
	}

	public function form( $instance ) {
		$instance = wp_parse_args( (array) $instance, array( 'title' => '' ) );
		$title = strip_tags( $instance['title'] );
?>
<p>
	<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e('Title:'); ?></label>
	<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" />
</p>
<?php
	}
}
