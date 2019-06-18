<?php

/**
 * Gamer Prices API class
 *
 *
 * @package GamerPrices
 * @since 1.0
 */
class GP_Api {
	
	/* Constants */
	const API_HOST = 'https://api.gamerprices.com';
	const API_TIMEOUT = 15;
	const API_VERSION = 'v1';
	const CACHE_GROUP = 'gamerprices';
	
	/* */
	private $license_key;
	private $host;
	private $locale;
	
	/**
	 *
	 * @param string $license_key
	 *        	Licence Key to use the GamerPrices API (@see https://api.gamerprices.com)
	 * @param string $host
	 *        	Optional
	 * @param string $locale
	 *        	Optional. Default fr_fr
	 * @since 1.0
	 */
	public function __construct($license_key, $host = self::API_HOST, $locale = 'fr_fr') {
		$this->license_key = $license_key;
		$this->host = $host;
		$this->locale = $locale;
	}

	/**
	 * Check Api Key
	 * 
	 * @param string $key
	 * @return array with user roles
	 */
	public function check_api_key() {
		return $this->request ( 'checkapikey', 'POST', array (
				'apiKey' => $this->license_key 
		) );
	}
	
	/**
	 * Search game 
	 *
	 * @param string $search
	 *        	Search query
	 * @param string $platform
	 *        	Optional. Default 'pc'
	 * @param boolean $only_master
	 *        	Optional. Default false.
	 *        	if true, no edition, no dlc
	 * @return object the search result
	 * @since 1.0
	 */
	public function search($search, $platform = 'pc', $only_master = FALSE) {
		return $this->request ( 'game/'. $this->locale . '/search', 'GET', array (
				'search'       => $search,
				'platform'     => $platform,
				'onlyMaster'   => $only_master
		) );
	}
	
	/**
	 * Search game
	 *
	 * @param string $id
	 *        	Game Id
	 * @param array $platforms
	 *        	Optional. Default 'pc'
	 * @param string $support
	 *        	Optional. Default NULL.
	 *        	support: key, box. null value -> both
	 * @param number $cache
	 *        	Optional. Default 30 minutes
	 * @return object the compare result
	 * @since 1.0
	 */
	public function compare($id, $platforms = [ 'pc' ] , $edition_id = NULL, $support = NULL, $cache = 30) {
		return $this->request ( 'game/'. $this->locale . '/compare', 'GET', array (
				'gameId'    => $id,
				'editionId' => $edition_id,
				'platform'  => $platforms,
				'support'   => $support
		), $cache );
	}
	
	/**
	 * Get the tops games by platforms
	 *
	 * @param array $platforms
	 *        	Optional. Default (pc, ps4, xboxone, wiiu).
	 *        	Platforms availables : pc,ps4,xboxone,wiiu,ps3,xbox360,nintendods,psvita,wii,psp,linux,mac
	 * @param number $size
	 *        	Optional. Default 5 by platform
	 * @param number $cache
	 *        	Optional. Default 30 minutes
	 * @return object the top games by platform
	 * @since 1.0
	 */
	public function tops($platforms = ['pc', 'ps4', 'xboxone', 'wiiu'], $size = 5, $cache = 30) {
		return $this->request ( $this->locale . '/top', 'GET', array (
				'platform' => $platforms,
				'size'     => $size 
		), $cache );
	}
	
	/**
	 * Request the GamerPrices APIs
	 *
	 * @param string $endpoint
	 *        	API endpoint
	 * @param boolean $localizable
	 * 			Optional. Default 'TRUE' 
	 * @param string $method
	 *        	Optional. Default 'GET' method Type
	 * @param array $data
	 *        	Optional. Default null.
	 * @param int $cache
	 *        	Optional. Cache the request (only for GET methods & non debug mode) during X minutes
	 * @return object Response of the request
	 * @since 1.0
	 */
	private function request($endpoint, $method = 'GET', $data = null, $cache = 0) {
		$uri = implode ( '/', array (
				$this->base_uri ( ),
				$endpoint 
		) );
		
		$http_args = array (
				'method' => $method,
				'body' => $data,
				'headers' => array (
						'Accept' => 'application/json',
						'X-GP-APIKEY' => $this->license_key 
				),
				'httpversion' => '1.1',
				'timeout' => self::API_TIMEOUT 
		);
		
		// Add data
		if (! empty ( $data )) {
			if ($method === 'GET') {
				$uri .= '?' . $this->http_build_query( $data );
			} else {
				$http_args ['body'] = $data;
			}
		}
		
		// Request & attempt to put response in cache
		$is_cacheable = ($method === 'GET' && $cache > 0);
		$cache_key = md5 ( $uri );
		$response = false;
		
		if ($is_cacheable) {
			$response = wp_cache_get ( $cache_key, self::CACHE_GROUP );
			if (false === $response) {
				$response = get_transient ( $cache_key );
			}
		}
		
		if (false === $response or WP_DEBUG) {
			$response = wp_remote_request ( $uri, $http_args );
			if (is_wp_error ( $response ) or empty ( $response ['response'] ) or $response ['response'] ['code'] !== 200) {
				return null;
			}
		}
		
		if ($is_cacheable) {
			set_transient ( $cache_key, $response, 60 * $cache );
			wp_cache_set ( $cache_key, $response, self::CACHE_GROUP, 60 * $cache );
		}
		
		var_dump($response ['body']);
		
		return json_decode ( $response ['body'] );
	}
	
	/**
	 * Get the base URI of the API
	 *
	 * @return string Base URI of the API
	 * @since 1.0
	 */
	private function base_uri( $localizable = TRUE ) {
		return implode ( '/', array (
				$this->host,
				self::API_VERSION 
		) );
	}
	
	/**
	 * Build the query params
	 *
	 * @param array $values        	
	 * @param string $name        	
	 * @param string $nested        	
	 * @return string http query params
	 * @since 1.0
	 */
	private function http_build_query($values, $name = '', $nested = false) {
		if (! is_array ( $values ))
			return false;
		$result = array ();
		
		foreach ( ( array ) $values as $key => $value ) {
			if ($nested) {
				if (is_numeric ( $key ))
					$key = $name;
				else
					$key = $name . "[$key]";
			} else {
				if (is_int ( $key ))
					$key = $name . $key;
			}
			
			if (is_array ( $value ) || is_object ( $value )) {
				$result [] = $this->http_build_query ( $value, $key, TRUE );
				continue;
			}
			
			$result [] = urlencode ( $key ) . "=" . (is_bool ( $value ) ? var_export ( $value, TRUE ) : urlencode ( $value ));
		}
		
		return implode ( "&", $result );
	}
}

?>