<?php
/**
 * Interface functions to receive data from OpenCage API.
 *
 * @link       https://github.com/rotaract/rotaract-club-finder
 * @since      2.0.0
 *
 * @package    Rotaract_Club_Finder
 * @subpackage Rotaract_Club_Finder/includes
 */

/**
 * Interface functions to receive data from OpenCage API.
 *
 * @link       https://github.com/rotaract/rotaract-club-finder
 * @since      2.0.0
 *
 * @package    Rotaract_Club_Finder
 * @subpackage Rotaract_Club_Finder/includes
 */
class Rotaract_OpenCage_Caller {

	/**
	 * The OpenCage URL to make API calls to.
	 *
	 * @since    2.0.0
	 * @access   private
	 * @var      string    $opencage_api_key    The OpenCage URL to make API calls to.
	 */
	private string $opencage_api_url = 'https://api.opencagedata.com/geocode/v1/json';

	/**
	 * The secret key for OpenCage API calls.
	 *
	 * @since    2.0.0
	 * @access   private
	 * @var      string    $opencage_api_key    The secret key for OpenCage API calls.
	 */
	private string $opencage_api_key;

	/**
	 * Set the OpenCage API key if defined.
	 *
	 * @since    2.0.0
	 */
	public function __construct() {
		if ( defined( 'OPENCAGE_API_KEY' ) ) {
			$this->opencage_api_key = OPENCAGE_API_KEY;
		}
	}

	/**
	 * Check if OpenCage API key is set.
	 *
	 * @since  2.0.0
	 * @return boolean
	 */
	public function isset_opencage_api_key(): bool {
		return isset( $this->opencage_api_key );
	}

	/**
	 * Receive geodata from OpenCage that match the search_param.
	 *
	 * @param String $search_phrase Search phrase by user.
	 *
	 * @since  2.0.0
	 * @return array of latitude and longitude
	 */
	public function opencage_request( string $search_phrase ): array {
		if ( ! $this->isset_opencage_api_key() ) {
			return array();
		}
		$url    = $this->opencage_api_url . '?q=' . $search_phrase . '+germany&key=' . $this->opencage_api_key . '&pretty=1';
		$header = array(
			'Content-Type' => 'application/json',
		);

		$res      = wp_remote_post(
			$url,
			array(
				'headers' => $header,
				'body'    => $search_phrase,
			)
		);
		$res_body = wp_remote_retrieve_body( $res );

		$result = json_decode( $res_body )->results[0]->geometry;
		return array(
			'lat' => $result->lat,
			'lng' => $result->lng,
		);
	}

}
