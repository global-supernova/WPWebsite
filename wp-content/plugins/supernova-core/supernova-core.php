<?php
/**
 * Plugin Name:       Supernova Core
 * Plugin URI:        https://github.com/global-supernova/wpwebsite
 * Description:        Site-specific functionality for the Global Supernova website: registers a "Project" custom post type and shortcodes. Keeping this in a plugin means it survives theme changes.
 * Version:           1.0.0
 * Requires at least: 6.0
 * Requires PHP:      7.4
 * Author:            Global Supernova
 * License:           GPL-2.0-or-later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       supernova-core
 *
 * @package SupernovaCore
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'SUPERNOVA_CORE_VERSION', '1.0.0' );

/**
 * Register the "Project" custom post type.
 */
function supernova_core_register_project_cpt() {
	$labels = array(
		'name'          => __( 'Projects', 'supernova-core' ),
		'singular_name' => __( 'Project', 'supernova-core' ),
		'add_new_item'  => __( 'Add New Project', 'supernova-core' ),
		'edit_item'     => __( 'Edit Project', 'supernova-core' ),
		'all_items'     => __( 'All Projects', 'supernova-core' ),
		'menu_name'     => __( 'Projects', 'supernova-core' ),
	);

	$args = array(
		'labels'       => $labels,
		'public'       => true,
		'has_archive'  => true,
		'menu_icon'    => 'dashicons-star-filled',
		'rewrite'      => array( 'slug' => 'projects' ),
		'show_in_rest' => true,
		'supports'     => array( 'title', 'editor', 'excerpt', 'thumbnail', 'custom-fields' ),
	);

	register_post_type( 'project', $args );
}
add_action( 'init', 'supernova_core_register_project_cpt' );

/**
 * A simple call-to-action shortcode: [supernova_cta text="..." url="..." label="..."]
 *
 * @param array $atts Shortcode attributes.
 * @return string Rendered HTML.
 */
function supernova_core_cta_shortcode( $atts ) {
	$atts = shortcode_atts(
		array(
			'text'  => __( 'Ready to get started?', 'supernova-core' ),
			'url'   => home_url( '/' ),
			'label' => __( 'Get in touch', 'supernova-core' ),
		),
		$atts,
		'supernova_cta'
	);

	return sprintf(
		'<div class="supernova-cta"><p>%1$s</p><a class="button button--primary" href="%2$s">%3$s</a></div>',
		esc_html( $atts['text'] ),
		esc_url( $atts['url'] ),
		esc_html( $atts['label'] )
	);
}
add_shortcode( 'supernova_cta', 'supernova_core_cta_shortcode' );

/**
 * Contact form shortcode: [supernova_contact_form]
 *
 * Renders an accessible contact form. Submissions are posted to
 * admin-post.php and handled by supernova_core_handle_contact(), which
 * validates, emails the site admin, and redirects back here (POST-redirect-GET)
 * with a status flag so refreshing the page cannot re-submit.
 *
 * Optional attribute:
 *   to="you@example.com"  Override the recipient (defaults to the admin email).
 *
 * @param array $atts Shortcode attributes.
 * @return string Rendered HTML.
 */
function supernova_core_contact_form_shortcode( $atts ) {
	$atts = shortcode_atts(
		array(
			'to' => get_option( 'admin_email' ),
		),
		$atts,
		'supernova_contact_form'
	);

	// Recover status, errors, and previously entered values after a redirect.
	$errors = array();
	$old    = array(
		'name'    => '',
		'email'   => '',
		'subject' => '',
		'message' => '',
	);
	$notice = '';

	$token = isset( $_GET['sn_contact'] ) ? sanitize_key( wp_unslash( $_GET['sn_contact'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Recommended

	if ( 'sent' === $token ) {
		$notice = 'success';
	} elseif ( $token ) {
		$stored = get_transient( 'supernova_contact_' . $token );
		if ( is_array( $stored ) ) {
			$notice = 'error';
			$errors = isset( $stored['errors'] ) ? (array) $stored['errors'] : array();
			$old    = wp_parse_args( isset( $stored['old'] ) ? (array) $stored['old'] : array(), $old );
			delete_transient( 'supernova_contact_' . $token );
		}
	}

	ob_start();
	?>
	<div class="supernova-contact">
		<?php if ( 'success' === $notice ) : ?>
			<div class="supernova-contact__notice supernova-contact__notice--success" role="status">
				<?php esc_html_e( 'Thank you — your message has been sent. We will be in touch shortly.', 'supernova-core' ); ?>
			</div>
		<?php elseif ( 'error' === $notice ) : ?>
			<div class="supernova-contact__notice supernova-contact__notice--error" role="alert">
				<?php esc_html_e( 'Please correct the following and try again:', 'supernova-core' ); ?>
				<ul>
					<?php foreach ( $errors as $error ) : ?>
						<li><?php echo esc_html( $error ); ?></li>
					<?php endforeach; ?>
				</ul>
			</div>
		<?php endif; ?>

		<form class="supernova-contact__form" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" method="post" novalidate>
			<input type="hidden" name="action" value="supernova_contact">
			<input type="hidden" name="_wp_http_referer" value="<?php echo esc_attr( wp_unslash( isset( $_SERVER['REQUEST_URI'] ) ? $_SERVER['REQUEST_URI'] : '' ) ); ?>">
			<?php wp_nonce_field( 'supernova_contact', 'supernova_contact_nonce' ); ?>

			<?php // Honeypot: hidden from humans, tempting to bots. Must stay empty. ?>
			<div class="supernova-contact__hp" aria-hidden="true">
				<label for="supernova-website"><?php esc_html_e( 'Leave this field empty', 'supernova-core' ); ?></label>
				<input type="text" id="supernova-website" name="supernova_website" tabindex="-1" autocomplete="off">
			</div>

			<p class="supernova-contact__field">
				<label for="supernova-name"><?php esc_html_e( 'Name', 'supernova-core' ); ?> <span aria-hidden="true">*</span></label>
				<input type="text" id="supernova-name" name="supernova_name" required value="<?php echo esc_attr( $old['name'] ); ?>">
			</p>

			<p class="supernova-contact__field">
				<label for="supernova-email"><?php esc_html_e( 'Email', 'supernova-core' ); ?> <span aria-hidden="true">*</span></label>
				<input type="email" id="supernova-email" name="supernova_email" required value="<?php echo esc_attr( $old['email'] ); ?>">
			</p>

			<p class="supernova-contact__field">
				<label for="supernova-subject"><?php esc_html_e( 'Subject', 'supernova-core' ); ?></label>
				<input type="text" id="supernova-subject" name="supernova_subject" value="<?php echo esc_attr( $old['subject'] ); ?>">
			</p>

			<p class="supernova-contact__field">
				<label for="supernova-message"><?php esc_html_e( 'Message', 'supernova-core' ); ?> <span aria-hidden="true">*</span></label>
				<textarea id="supernova-message" name="supernova_message" rows="6" required><?php echo esc_textarea( $old['message'] ); ?></textarea>
			</p>

			<p class="supernova-contact__actions">
				<button type="submit" class="button button--primary"><?php esc_html_e( 'Send message', 'supernova-core' ); ?></button>
			</p>
		</form>
	</div>
	<?php
	return ob_get_clean();
}
add_shortcode( 'supernova_contact_form', 'supernova_core_contact_form_shortcode' );

/**
 * Handle a contact form submission (POST-redirect-GET).
 *
 * Hooked for both logged-in and anonymous visitors.
 */
function supernova_core_handle_contact() {
	$redirect = wp_get_referer();
	if ( ! $redirect ) {
		$redirect = home_url( '/' );
	}

	// Verify the nonce.
	if ( ! isset( $_POST['supernova_contact_nonce'] ) ||
		! wp_verify_nonce( sanitize_key( wp_unslash( $_POST['supernova_contact_nonce'] ) ), 'supernova_contact' ) ) {
		wp_safe_redirect( add_query_arg( 'sn_contact', supernova_core_stash_contact_error( array( __( 'Your session expired. Please try again.', 'supernova-core' ) ), array() ), $redirect ) );
		exit;
	}

	// Honeypot: a filled field means a bot. Pretend success and drop it.
	if ( ! empty( $_POST['supernova_website'] ) ) {
		wp_safe_redirect( add_query_arg( 'sn_contact', 'sent', $redirect ) );
		exit;
	}

	$name    = isset( $_POST['supernova_name'] ) ? sanitize_text_field( wp_unslash( $_POST['supernova_name'] ) ) : '';
	$email   = isset( $_POST['supernova_email'] ) ? sanitize_email( wp_unslash( $_POST['supernova_email'] ) ) : '';
	$subject = isset( $_POST['supernova_subject'] ) ? sanitize_text_field( wp_unslash( $_POST['supernova_subject'] ) ) : '';
	$message = isset( $_POST['supernova_message'] ) ? sanitize_textarea_field( wp_unslash( $_POST['supernova_message'] ) ) : '';

	$errors = array();
	if ( '' === $name ) {
		$errors[] = __( 'Please enter your name.', 'supernova-core' );
	}
	if ( '' === $email || ! is_email( $email ) ) {
		$errors[] = __( 'Please enter a valid email address.', 'supernova-core' );
	}
	if ( '' === $message ) {
		$errors[] = __( 'Please enter a message.', 'supernova-core' );
	}

	if ( ! empty( $errors ) ) {
		$old = array(
			'name'    => $name,
			'email'   => $email,
			'subject' => $subject,
			'message' => $message,
		);
		wp_safe_redirect( add_query_arg( 'sn_contact', supernova_core_stash_contact_error( $errors, $old ), $redirect ) );
		exit;
	}

	// Compose and send.
	$to = get_option( 'admin_email' );
	/* translators: %s: site name. */
	$mail_subject = sprintf( __( '[%s] New contact form submission', 'supernova-core' ), wp_specialchars_decode( get_bloginfo( 'name' ), ENT_QUOTES ) );
	if ( '' !== $subject ) {
		$mail_subject .= ': ' . $subject;
	}

	$body  = __( 'You have received a new message from your website contact form.', 'supernova-core' ) . "\n\n";
	$body .= __( 'Name:', 'supernova-core' ) . ' ' . $name . "\n";
	$body .= __( 'Email:', 'supernova-core' ) . ' ' . $email . "\n";
	if ( '' !== $subject ) {
		$body .= __( 'Subject:', 'supernova-core' ) . ' ' . $subject . "\n";
	}
	$body .= "\n" . $message . "\n";

	$headers = array(
		'Reply-To: ' . $name . ' <' . $email . '>',
	);

	$sent = wp_mail( $to, $mail_subject, $body, $headers );

	if ( ! $sent ) {
		wp_safe_redirect( add_query_arg( 'sn_contact', supernova_core_stash_contact_error( array( __( 'Sorry, the message could not be sent right now. Please try again later.', 'supernova-core' ) ), array( 'name' => $name, 'email' => $email, 'subject' => $subject, 'message' => $message ) ), $redirect ) );
		exit;
	}

	wp_safe_redirect( add_query_arg( 'sn_contact', 'sent', $redirect ) );
	exit;
}
add_action( 'admin_post_supernova_contact', 'supernova_core_handle_contact' );
add_action( 'admin_post_nopriv_supernova_contact', 'supernova_core_handle_contact' );

/**
 * Stash errors and previously entered values in a short-lived transient and
 * return a token to reference them after the redirect.
 *
 * @param array $errors Validation error messages.
 * @param array $old    Previously entered field values.
 * @return string Token (also used as the sn_contact query value).
 */
function supernova_core_stash_contact_error( $errors, $old ) {
	$token = wp_generate_password( 12, false );
	set_transient(
		'supernova_contact_' . $token,
		array(
			'errors' => $errors,
			'old'    => $old,
		),
		2 * MINUTE_IN_SECONDS
	);
	return $token;
}

/**
 * Flush rewrite rules on activation so custom post type URLs work immediately.
 */
function supernova_core_activate() {
	supernova_core_register_project_cpt();
	flush_rewrite_rules();
}
register_activation_hook( __FILE__, 'supernova_core_activate' );

/**
 * Clean up rewrite rules on deactivation.
 */
function supernova_core_deactivate() {
	flush_rewrite_rules();
}
register_deactivation_hook( __FILE__, 'supernova_core_deactivate' );
