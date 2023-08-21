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

use Meilisearch\Client;

require plugin_dir_path(dirname(__DIR__)) . 'vendor/autoload.php';

/**
 * Interface functions to receive data from Meilisearch API.
 *
 * @link       https://github.com/rotaract/rotaract-club-finder
 * @since      2.0.0
 *
 * @package    Rotaract_Club_Finder
 * @subpackage Rotaract_Club_Finder/includes
 */
class Rotaract_Club_Finder_Meilisearch_Caller
{
  /**
   * The Meilisearch API client instance.
   *
   * @since    2.0.0
   * @access   private
   * @var      Client $client    The Meilisearch API client instance.
   */
  public Client $client;

  /**
   * Set the Meilisearch host URL if defined.
   *
   * @since    2.0.0
   */
  public function __construct()
  {
    if (
      defined('ROTARACT_MEILISEARCH_API_KEY')
    ) {
      $this->client =  new Client('https://search.rotaract.de', ROTARACT_MEILISEARCH_API_KEY);
    }
  }


  /**
   * Check if Meili Client is set.
   *
   * @since  2.0.0
   * @return boolean
   */
  public function isset_client(): bool
  {
    return isset($this->client);
  }


  /**
   * Receive clubs from meilisearch that match the filter.
   *
   * @param array $filter Filters for the search query.
   *
   * @return array of clubs
   */
  private function meili_request(array $filter)
  {
    if (!$this->isset_client()) {
      return array();
    }

    return $this->client->index('Club')->search('', $filter)->getHits();
  }

  /**
   * Setting filter for search query and receive clubs within a particular range of a given location.
   *
   * @param String $range Radius (in meters) in which may be found next to the searched location.
   * @param String $lat Location latitude.
   * @param String $lng Location longitude.
   *
   * @since  2.0.0
   * @return array of clubs
   */
  public function get_clubs($latitude, $longitude, $range)
  {
    $filter = [
      'filter' => '_geoRadius(' . $latitude . ',' . $longitude . ', ' . $range . ')'
    ];
    return $this->meili_request($filter);
  }
}
