<?php
/**
 * Interface functions to receive data from Elasticsearch API.
 *
 * @link       https://github.com/rotaract/rotaract-club-finder
 * @since      2.0.0
 *
 * @package    Rotaract_Club_Finder
 * @subpackage Rotaract_Club_Finder/includes
 */

use Elasticsearch\Client;
use Elasticsearch\ClientBuilder;

require plugin_dir_path( dirname( __DIR__ ) ) . 'vendor/autoload.php';

/**
 * Interface functions to receive data from Elasticsearch API.
 *
 * @link       https://github.com/rotaract/rotaract-club-finder
 * @since      2.0.0
 *
 * @package    Rotaract_Club_Finder
 * @subpackage Rotaract_Club_Finder/includes
 */
class Rotaract_Club_Finder_Elastic_Caller {

	/**
	 * The elasticsearch API client instance.
	 *
	 * @since    2.0.0
	 * @access   private
	 * @var      Client $client    The elasticsearch API client instance.
	 */
	private Client $client;

	/**
	 * Set the Elasticsearch host URL if defined.
	 *
	 * @since    2.0.0
	 */
	public function __construct() {
		if ( defined( 'ROTARACT_ELASTIC_CLOUD_ID' ) &&
			defined( 'ROTARACT_ELASTIC_API_ID' ) &&
			defined( 'ROTARACT_ELASTIC_API_KEY' ) ) {
			try {
				$this->client = ClientBuilder::create()
					->setElasticCloudId( ROTARACT_ELASTIC_CLOUD_ID )
					->setApiKey( ROTARACT_ELASTIC_API_ID, ROTARACT_ELASTIC_API_KEY )
					->build();
			} catch ( Exception $exception) {
				error_log($exception);
			}
		}
	}

	/**
	 * Check if Elasticsearch host URL is set.
	 *
	 * @since  2.0.0
	 * @return boolean
	 */
	public function isset_client(): bool {
		return isset( $this->client );
	}

	/**
	 * Receive clubs from elastic that match the search_param.
	 *
	 * @param array $params API attributes in JSON format.
	 *
	 * @return array of clubs
	 */
	private function elastic_request( array $params ): array {
		if ( ! $this->isset_client() ) {
			return array();
		}
		return $this->client->search( $params )['hits']['hits'];
	}

	/**
	 * Receive clubs within a particular range of a given location.
	 *
	 * @param String $range Radius in which may be found next to the searched location.
	 * @param String $lat Location latitude.
	 * @param String $lng Location longitude.
	 *
	 * @since  2.0.0
	 * @return array of clubs
	 */
	public function get_clubs( string $range, string $lat, string $lng ): array {
		$params = array(
			'index' => 'clubs',
			'body'  => array(
				'_source' => array(
					'location',
					'name',
					'district_name',
					'homepage_url',
				),
				'query'   => array(
					'bool' => array(
						'filter' => array(
							array(
								'geo_distance' => array(
									'distance'      => $range . 'km',
									'distance_type' => 'plane',
									'location'      => array(
										'lat' => $lat,
										'lon' => $lng,
									),
								),
							),
							array(
								'terms' => array(
									'status' => array(
										'active',
										'founding',
										'preparing',
									),
								),
							),
						),
						'should' => array(
							'distance_feature' => array(
								'field'  => 'location',
								'pivot'  => ( $range / 2 ) . 'km',
								'origin' => array(
									'lat' => $lat,
									'lon' => $lng,
								),
							),
						),
					),
				),
			),
		);

		return $this->elastic_request( $params );
	}

}
