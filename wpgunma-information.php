<?php
/**
 * Plugin Name:     WordPress Meetup Gunma Information
 * Plugin URI:      https://github.com/ko31/wpgunma-information
 * Description:     This plugin will notify you about WordPress Meetup Gunma events on the dashboard.
 * Author:          ko31
 * Author URI:      https://go-sign.info
 * Text Domain:     wpgunma-information
 * Domain Path:     /languages
 * Version:         0.1.0
 *
 * @package         Wpgunma_Information
 */

if ( ! defined( 'WPINC' ) ) {
	die;
}

function wpgunma_add_dashboard_widgets() {

	wp_add_dashboard_widget(
		'wpgunma_information_dashboard_widget',
		__( 'WordPress Meetup Gunma Information', 'wpgunma-information' ),
		'wpgunma_information_dashboard_widget_function'
	);
}

add_action( 'wp_dashboard_setup', 'wpgunma_add_dashboard_widgets' );

function wpgunma_information_dashboard_widget_function() {
	$events = wpgunma_information_new_events();
	?>
	<div id="community-events" class="community-events" aria-hidden="false">
		<div class="activity-block">
			<p>
                <span id="wpgunma-community-events-location-message" aria-hidden="false">
	<?php
	if ( $events ):
		_e( 'We look forward to having you join us!', 'wpgunma-information' );
	else:
		_e( 'Information could not be found.', 'wpgunma-information' );
	endif;
	?>
                </span>
			</p>
		</div>
		<?php
		if ( $events ):
			?>
			<ul class="wpgunma-community-events-results activity-block last" aria-hidden="false">
				<?php
				foreach ( $events as $event ) :
					$started_at = new DateTime( $event->started_at );
					$event_date = $started_at->format( get_option( 'date_format' ) );
					$event_time = $started_at->format( get_option( 'time_format' ) );
					?>
					<li class="event event-meetup wp-clearfix">
						<div class="event-info">
							<div class="dashicons event-icon" aria-hidden="true"></div>
							<div class="event-info-inner">
								<a class="event-title"
								   href="<?php echo esc_attr( $event->event_url ); ?>"
								   target="_blank"><?php echo esc_html( $event->title ); ?></a>
								<span class="event-city"><?php echo esc_html( $event->place ); ?></span>
							</div>
						</div>
						<div class="event-date-time">
							<span class="event-date"><?php echo esc_html( $event_date ); ?></span>
							<span class="event-time"><?php echo esc_html( $event_time ); ?></span>
						</div>
					</li>
				<?php
				endforeach;
				?>
			</ul>
		<?php
		endif;
		?>
	</div>
	<?php
}

function wpgunma_information_new_events() {
	$url      = sprintf( 'https://connpass.com/api/v1/event/?keyword=%s&count=3', urlencode( '群馬 WordPress Meetup' ) );
	$response = wp_remote_get( $url );
	if ( is_wp_error( $response ) ) {
		return false;
	}

	$body = json_decode( $response['body'] );
	if ( empty( $body->events ) ) {
		return false;
	}

	return $body->events;
}
