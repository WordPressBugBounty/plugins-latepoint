<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

class OsAnalyticsHelper {

	/**
	 * Initialize BSF Analytics.
	 *
	 * @return void
	 */
	public static function init() {

		add_action( 'latepoint_settings_updated', array( self::class, 'update_contribute_option' ) );

		if ( ! class_exists( 'BSF_Analytics_Loader' ) ) {
			require_once LATEPOINT_ABSPATH . 'lib/kit/bsf-analytics/class-bsf-analytics-loader.php';
		}

		if ( ! class_exists( 'Astra_Notices' ) ) {
			require_once LATEPOINT_ABSPATH . 'lib/kit/astra-notices/class-astra-notices.php';
		}

		$bsf_analytics = \BSF_Analytics_Loader::get_instance();

		$bsf_analytics->set_entity(
			[
				'latepoint' => [
					'product_name'        => 'LatePoint',
					'path'                => LATEPOINT_ABSPATH . 'lib/kit/bsf-analytics',
					'author'              => 'LatePoint',
					'time_to_display'     => '+24 hours',
					'deactivation_survey' => apply_filters(
						'latepoint_deactivation_survey_data',
						[
							[
								'id'                => 'deactivation-survey-latepoint',
								'popup_logo'        => LATEPOINT_IMAGES_URL . 'logo.svg',
								'plugin_slug'       => 'latepoint',
								'popup_title'       => 'Quick Feedback',
								'support_url'       => 'https://latepoint.com/support/',
								'popup_description' => 'If you have a moment, please share why you are deactivating LatePoint:',
								'show_on_screens'   => [ 'plugins' ],
								'plugin_version'    => LATEPOINT_VERSION,
							],
						]
					),
				],
			]
		);

		add_filter( 'bsf_core_stats', [ __CLASS__, 'add_latepoint_analytics_data' ] );
	}

	/**
	 * Toggle contribute to latepoint from general settings.
	 *
	 * @param array<mixed> $settings settings array.
	 * @return bool
	 */
	public static function update_contribute_option( $settings ) {
		if ( isset( $settings['contribute_to_latepoint'] ) && 'on' === $settings['contribute_to_latepoint'] ) {
			$enable_tracking = 'yes';
		} else {
			$enable_tracking = '';
		}

		return update_option( 'latepoint_usage_optin', $enable_tracking );
	}

	/**
	 * Add LatePoint specific analytics data.
	 *
	 * @param array $stats_data Existing stats data.
	 * @return array
	 */
	public static function add_latepoint_analytics_data( $stats_data ) {
		$stats_data['plugin_data']['latepoint'] = [
			'free_version'  => LATEPOINT_VERSION,
			'db_version'    => LATEPOINT_DB_VERSION,
			'site_language' => get_locale(),
		];

		$stats_data['plugin_data']['latepoint']['numeric_values'] = [
			'total_bookings'  => self::get_table_count( LATEPOINT_TABLE_BOOKINGS ),
			'total_services'  => self::get_table_count( LATEPOINT_TABLE_SERVICES ),
			'total_agents'    => self::get_table_count( LATEPOINT_TABLE_AGENTS ),
			'total_customers' => self::get_table_count( LATEPOINT_TABLE_CUSTOMERS ),
			'total_locations' => self::get_table_count( LATEPOINT_TABLE_LOCATIONS ),
		];

		// Add KPI tracking data.
		$kpi_data = self::get_kpi_tracking_data();
		if ( ! empty( $kpi_data ) ) {
			$stats_data['plugin_data']['latepoint']['kpi_records'] = $kpi_data;
		}

		return $stats_data;
	}

	/**
	 * Get KPI tracking data for the last 2 days (excluding today).
	 *
	 * @return array KPI data organized by date.
	 */
	private static function get_kpi_tracking_data() {
		$kpi_data = [];
		$today    = current_time( 'Y-m-d' );

		for ( $i = 1; $i <= 2; $i++ ) {
			$date     = gmdate( 'Y-m-d', strtotime( $today . ' -' . $i . ' days' ) );
			$bookings = self::get_daily_count( LATEPOINT_TABLE_BOOKINGS, $date );
			$orders   = self::get_daily_count( LATEPOINT_TABLE_ORDERS, $date );

			$kpi_data[ $date ] = [
				'numeric_values' => [
					'bookings' => $bookings,
					'orders'   => $orders,
				],
			];
		}

		return $kpi_data;
	}

	/**
	 * Get count of rows created on a specific date.
	 *
	 * @param string $table_name Full table name.
	 * @param string $date Date in Y-m-d format.
	 * @return int
	 */
	private static function get_daily_count( $table_name, $date ) {
		global $wpdb;

		$start_date = $date . ' 00:00:00';
		$end_date   = $date . ' 23:59:59';

		$count = $wpdb->get_var(
			$wpdb->prepare(
				'SELECT COUNT(*) FROM %i WHERE created_at >= %s AND created_at <= %s',
				$table_name,
				$start_date,
				$end_date
			)
		);

		return $count ? (int) $count : 0;
	}

	/**
	 * Get total row count from a table.
	 *
	 * @param string $table_name Full table name.
	 * @return int
	 */
	private static function get_table_count( $table_name ) {
		global $wpdb;
		$count = $wpdb->get_var( $wpdb->prepare( 'SELECT COUNT(*) FROM %i', $table_name ) );
		return $count ? (int) $count : 0;
	}
}
