<?php
/**
 * Interface functions to receive data from Elasticsearch API.
 *
 * @link       https://github.com/rotaract/rotaract-club-finder
 * @since      1.0.0
 *
 * @package    Rotaract_Appointments
 * @subpackage Rotaract_Appointments/includes
 */

/**
 * Interface functions to receive data from Elasticsearch API.
 *
 * @link       https://github.com/rotaract/rotaract-club-finder
 * @since      1.0.0
 *
 * @package    Rotaract_Appointments
 * @subpackage Rotaract_Appointments/includes
 */
class Rotaract_Club_Finder_Elastic_Caller {

	/**
	 * The host URL auf the Elasticsearch instance containing Rotaract events.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $elastic_host    The host URL auf the Elasticsearch instance containing Rotaract events.
	 */
	private string $elastic_host;

	/**
	 * Set the Elasticsearch host URL if defined.
	 *
	 * @since    1.0.0
	 */
	public function __construct() {
		if ( defined( 'ROTARACT_ELASTIC_HOST' ) ) {
			$this->elastic_host = trailingslashit( ROTARACT_ELASTIC_HOST );
		}
	}

	/**
	 * Receive clubs from elastic that match the search_param.
	 *
	 * @param String $api_path absolute API path.
	 * @param String $search_param API attributes in JSON format.
	 *
	 * @return array of clubs
	 */
	private function elastic_request( string $api_path, string $search_param ): array {
		if ( ! $this->isset_elastic_host() ) {
			return array();
		}
		$url    = $this->elastic_host . $api_path;
		$header = array(
			'Content-Type' => 'application/json',
		);

		$res      = wp_remote_post(
			$url,
			array(
				'headers' => $header,
				'body'    => $search_param,
			)
		);
		$res_body = wp_remote_retrieve_body( $res );

		$result = json_decode( $res_body )->hits->hits;
		return $result ?: array();
	}


	/**
	 * Check if Elasticsearch host URL is set.
	 *
	 * @return boolean
	 */
	public function isset_elastic_host(): bool {
		return isset( $this->elastic_host );
	}

	/**
	 * Receive clubs within a particular range of a given location.
	 *
	 * @param String $range Radius in which may be found next to the searched location.
	 * @param String $lat Location latitude.
	 * @param String $lng Location longitude.
	 *
	 * @return array of clubs
	 */
	public function get_clubs( string $range, string $lat, string $lng ): array {
		$path         = 'clubs/_search';
		$search_param = array(
			'_source' => array(
				'location',
				'name',
				'district_name',
				'homepage_url',
			),
			'query'   => array(
				'filter' => array(
					'geo_distance' => array(
						'distance' => $range . 'km',
						'location' => array(
							'lat' => $lat,
							'lng' => $lng,
						),
					),
					'terms'        => array(
						'status' => array(
							'active',
							'founding',
							'preparing',
						),
					),
				),
			),
		);

		return $this->elastic_request( $path, wp_json_encode( $search_param ) );
	}

}
