<?php

class OsTranslationHelper {

	protected static array $cache = [];

	/**
	 * Registry of default booking-form strings, keyed by their raw English source text.
	 *
	 * These strings are auto-seeded into the database in English (see
	 * OsStepsHelper::get_default_value_for_step_settings() and OsEventsHooksHelper::add_step_settings())
	 * so translate_default() can compare a stored value against them at render time: if it still
	 * matches a known default, the translated version is returned; if an admin has customized it,
	 * the stored value is returned unchanged, since there is nothing to translate.
	 *
	 * Use the `latepoint_translatable_default_strings` filter to register more strings (e.g. from
	 * the pro plugin, with its own text domain) or to unset an entry to opt a string out of
	 * automatic translation.
	 *
	 * @return array<string, string> Map of English source string => translated string.
	 */
	public static function get_translatable_default_strings(): array {
		$locale = determine_locale();

		if ( isset( self::$cache[ $locale ]['exact'] ) ) {
			return self::$cache[ $locale ]['exact'];
		}

		$strings = [
			// Booking / services step.
			'Service Selection'                            => __( 'Service Selection', 'latepoint' ),
			'Please select a service for which you want to schedule an appointment' => __( 'Please select a service for which you want to schedule an appointment', 'latepoint' ),
			'Available Services'                           => __( 'Available Services', 'latepoint' ),

			// Locations step.
			'Location Selection'                           => __( 'Location Selection', 'latepoint' ),
			'Please select a location where you want to schedule an appointment' => __( 'Please select a location where you want to schedule an appointment', 'latepoint' ),
			'Available Locations'                          => __( 'Available Locations', 'latepoint' ),

			// Agents step.
			'Agent Selection'                              => __( 'Agent Selection', 'latepoint' ),
			'Please select an agent that will be providing you a service' => __( 'Please select an agent that will be providing you a service', 'latepoint' ),
			'Available Agents'                             => __( 'Available Agents', 'latepoint' ),

			// Date & time step.
			'Select Date & Time'                           => __( 'Select Date & Time', 'latepoint' ),
			'Please select date and time for your appointment' => __( 'Please select date and time for your appointment', 'latepoint' ),
			'Date & Time Selection'                        => __( 'Date & Time Selection', 'latepoint' ),

			// Customer info step.
			'Enter Your Information'                       => __( 'Enter Your Information', 'latepoint' ),
			'Please enter your contact information'        => __( 'Please enter your contact information', 'latepoint' ),
			'Customer Information'                         => __( 'Customer Information', 'latepoint' ),

			// Verify step.
			'Verify Order Details'                         => __( 'Verify Order Details', 'latepoint' ),
			'Double check your reservation details and click submit button if everything is correct' => __( 'Double check your reservation details and click submit button if everything is correct', 'latepoint' ),

			// Payment - times step.
			'Payment Time Selection'                       => __( 'Payment Time Selection', 'latepoint' ),
			'Please choose when you would like to pay for your appointment' => __( 'Please choose when you would like to pay for your appointment', 'latepoint' ),
			'When would you like to pay?'                  => __( 'When would you like to pay?', 'latepoint' ),

			// Payment - portions step.
			'Payment Portion Selection'                    => __( 'Payment Portion Selection', 'latepoint' ),
			'Please select how much you would like to pay now' => __( 'Please select how much you would like to pay now', 'latepoint' ),
			'How much would you like to pay now?'          => __( 'How much would you like to pay now?', 'latepoint' ),

			// Payment - methods step.
			'Payment Method Selection'                     => __( 'Payment Method Selection', 'latepoint' ),
			'Please select a payment method you would like to make a payment with' => __( 'Please select a payment method you would like to make a payment with', 'latepoint' ),
			'Select payment method'                        => __( 'Select payment method', 'latepoint' ),

			// Payment - processors step.
			'Payment Processor Selection'                  => __( 'Payment Processor Selection', 'latepoint' ),
			'Please select a payment processor you want to process the payment with' => __( 'Please select a payment processor you want to process the payment with', 'latepoint' ),
			'Select payment processor'                     => __( 'Select payment processor', 'latepoint' ),

			// Payment - pay step.
			'Make a Payment'                               => __( 'Make a Payment', 'latepoint' ),
			'Please enter your payment information so we can process the payment' => __( 'Please enter your payment information so we can process the payment', 'latepoint' ),
			'Enter your payment information'               => __( 'Enter your payment information', 'latepoint' ),

			// Confirmation step.
			'Confirmation'                                 => __( 'Confirmation', 'latepoint' ),
			'Your order has been placed. Please retain this confirmation for your record.' => __( 'Your order has been placed. Please retain this confirmation for your record.', 'latepoint' ),
			'Order Confirmation'                           => __( 'Order Confirmation', 'latepoint' ),
			'Appointment Confirmed'                        => __( 'Appointment Confirmed', 'latepoint' ),
			'We look forward to seeing you.'               => __( 'We look forward to seeing you.', 'latepoint' ),

			// Events (event registration steps).
			'Event Information'                            => __( 'Event Information', 'latepoint' ),
			'Review the event details before you register' => __( 'Review the event details before you register', 'latepoint' ),
			'Ticket Selection'                             => __( 'Ticket Selection', 'latepoint' ),
			'Please select the number of tickets you\'d like to reserve' => __( 'Please select the number of tickets you\'d like to reserve', 'latepoint' ),
			'Select Your Tickets'                          => __( 'Select Your Tickets', 'latepoint' ),

			// Shared.
			'<h5>Questions?</h5><p>Call (858) 939-3746 for help</p>' => __( '<h5>Questions?</h5><p>Call (858) 939-3746 for help</p>', 'latepoint' ),
		];

		/**
		 * Filters the registry of booking-form default strings eligible for automatic translation.
		 *
		 * Add entries here (e.g. from an add-on, with its own text domain) to make more defaults
		 * translatable, or unset an entry to opt a string out and always render it verbatim.
		 *
		 * @param array<string, string> $strings Map of English source string => translated string.
		 */
		$strings = apply_filters( 'latepoint_translatable_default_strings', $strings );

		self::$cache[ $locale ]['exact'] = $strings;

		return $strings;
	}

	/**
	 * Same registry as get_translatable_default_strings(), keyed by a normalized version of the
	 * English source string, so translate_default() can still match a default value that was
	 * round-tripped through the booking-form-settings contenteditable preview without being
	 * changed (which can introduce HTML-entity/whitespace differences).
	 *
	 * @return array<string, string> Map of normalized English source string => translated string.
	 */
	protected static function get_normalized_translatable_default_strings(): array {
		$locale = determine_locale();

		if ( isset( self::$cache[ $locale ]['normalized'] ) ) {
			return self::$cache[ $locale ]['normalized'];
		}

		$normalized = [];
		foreach ( self::get_translatable_default_strings() as $default => $translated ) {
			$normalized[ self::normalize( $default ) ] = $translated;
		}

		self::$cache[ $locale ]['normalized'] = $normalized;

		return $normalized;
	}

	/**
	 * Returns the translated version of $value if it still matches one of the plugin's known
	 * default strings for booking-form settings, otherwise returns $value unchanged (an admin has
	 * customized it, so there is nothing to translate).
	 */
	public static function translate_default( string $value ): string {
		if ( $value === '' ) {
			return $value;
		}

		$strings = self::get_translatable_default_strings();

		if ( isset( $strings[ $value ] ) ) {
			return $strings[ $value ];
		}

		$normalized_strings = self::get_normalized_translatable_default_strings();
		$normalized_value   = self::normalize( $value );

		return $normalized_strings[ $normalized_value ] ?? $value;
	}

	/**
	 * Normalizes a string for comparison against the default-strings registry, undoing the
	 * non-breaking-space and HTML-entity differences that a default value can pick up when it is
	 * round-tripped through the booking-form-settings contenteditable preview without being changed.
	 */
	protected static function normalize( string $value ): string {
		$value = str_replace( "\xc2\xa0", ' ', $value );
		$value = wp_specialchars_decode( $value, ENT_QUOTES );

		return trim( $value );
	}
}
