<?php

namespace SearchWP_CRO;

/**
 * Class Buoy
 */
class Buoy {

	private $query;
	private $engine;
	private $buoy_key;

	/**
	 * Constructor.
	 */
	public function __construct( $query, $engine, $buoy_key ) {
		$this->query    = $query;
		$this->engine   = $engine;
		$this->buoy_key = $buoy_key;

		add_filter( 'searchwp_query_main_join', array( $this, 'searchwp_query_main_join' ), 10, 2 );
		add_filter( 'searchwp_query_orderby', array( $this, 'searchwp_query_orderby' ) );
	}

	public function searchwp_query_main_join( $sql, $engine ) {
		global $wpdb;

		$sql = $sql . " LEFT JOIN {$wpdb->postmeta} AS {$this->buoy_key} ON {$wpdb->posts}.ID = {$this->buoy_key}.post_id AND {$this->buoy_key}.meta_key = '{$this->buoy_key}'";

		return $sql;
	}

	public function searchwp_query_orderby( $orderby ) {
		global $wpdb;

		$original_orderby = str_replace( 'ORDER BY', '', $orderby );
		$new_orderby      = "ORDER BY {$this->buoy_key}.meta_value+0 DESC, " . $original_orderby;

		// For MySQL compat we need to preface or ORDER BY
		// There is no good hook to do this, so we're taking advantage of how SearchWP builds this query.
		$new_orderby = ", {$this->buoy_key}.meta_value " . $new_orderby;

		return $new_orderby;
	}
}
