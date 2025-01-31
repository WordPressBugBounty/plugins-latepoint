<?php
/*
 * Copyright (c) 2024 LatePoint LLC. All rights reserved.
 */

class OsTransactionIntentHelper {


	public static function generate_continue_intent_url( $transaction_intent_key ) {
		return OsRouterHelper::build_admin_post_link( [
			'orders',
			'continue_transaction_intent'
		], [ 'transaction_intent_key' => $transaction_intent_key ] );
	}


	public static function get_order_id_from_intent_key( $intent_key ) {
		if ( empty( $intent_key ) ) {
			return false;
		}
		$transaction_intent = new OsTransactionIntentModel();
		$transaction_intent = $transaction_intent->where( [ 'intent_key' => $intent_key ] )->set_limit( 1 )->get_results_as_models();

		if ( $transaction_intent && $transaction_intent->order_id ) {
			return $transaction_intent->order_id;
		} else {
			return null;
		}
	}

	public static function get_transaction_intent_by_intent_key( string $intent_key ) : OsTransactionIntentModel {
		$transaction_intent = new OsTransactionIntentModel();
		if(empty($intent_key)) return $transaction_intent;
		$transaction_intent = $transaction_intent->where( [ 'intent_key' => $intent_key ] )->set_limit( 1 )->get_results_as_models();
		if(!empty($transaction_intent)){
			return $transaction_intent;
		}else{
			return new OsTransactionIntentModel();
		}
	}

	public static function is_converted( $transaction_intent_id ) {
		$transaction_intent = new OsTransactionIntentModel( $transaction_intent_id );
		if ( ! empty( $transaction_intent->transaction_id ) ) {
			return $transaction_intent->transaction_id;
		} else {
			return false;
		}
	}

}