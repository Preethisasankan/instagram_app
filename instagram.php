<?php
/**
 * Create an PHP app to get the instagram data from specfic hashtag "nature" and save it in csv file
 */
class instagram {
	/**
	 * The oAuth Token URL
	 */
	const TOKEN_URL = 'https://api.instagram.com/oauth/access_token';
	/**
	 * The Hashtag base url
	 */
	const H_BASE_URL = 'graph.facebook.com/';
	/**
	 * The Instagram Client ID
	 *
	 *  @var string
	 */
	private $_clientId;
	/**
	 * The Instagram Client secret.
	 *
	 *  @var string
	 */
	private $_clientSecret;
	/**
	 * The Instagram redirect URL
	 *
	 *  @var URL
	 */
	private $_redirectUrl;
	/**
	 * The short life code.
	 *
	 * https://developers.facebook.com/docs/instagram-basic-display-api/overview#instagram-user-access-tokens
	 * https://api.instagram.com/oauth/authorize?client_id=608297303151714&redirect_uri=http://localhost/wordpress/&scope=user_profile,user_media&response_type=code
	 *
	 * @var string
	 */
	private $_code;
	 /**
	  * The short life Access Token.
	  *
	  * @var string
	  */
	private $_accessToken;
	/**
	 * The User ID of the Instagram
	 * @var string
	 */
	protected $_user_id;
	/**
	 * Constructor function
	 *
	 * @param $credentials array credentials
	 */
	public function __construct( $credentials ) {
		$this->_clientId     = $credentials['client_id'];
		$this->_clientSecret = $credentials['client_secret'];
		$this->_redirectUrl  = $credentials['redirect_url'];
		$this->_code         = $credentials['code'];
		$this->set_user_id_and_access_token();
	}
	/**
	 * This function is to set the Access token & User id
	 */
	public function set_user_id_and_access_token() {
		$tokenUrl   = self::TOKEN_URL;
		$dataString = array(
			'grant_type'    => 'authorization_code',
			'client_id'     => $this->_clientId,
			'client_secret' => $this->_clientSecret,
			'redirect_uri'  => $this->_redirectUrl,
			'code'          => $this->_code,
		);
		// print_r($dataString);
		$ch = curl_init();
		curl_setopt( $ch, CURLOPT_URL, $tokenUrl );
		curl_setopt( $ch, CURLOPT_POST, count( $dataString ) );
		curl_setopt( $ch, CURLOPT_POSTFIELDS, http_build_query( $dataString ) );
		curl_setopt( $ch, CURLOPT_HTTPHEADER, array( 'Accept: application/json' ) );
		curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
		curl_setopt( $ch, CURLOPT_SSL_VERIFYPEER, false );
		curl_setopt( $ch, CURLOPT_TIMEOUT, 90 );

		$result = curl_exec( $ch );

		$result = json_decode( $result, true );
		// print_r( $result );
		if ( $result['code'] == 200 ) {
			$this->_accessToken = $result['access_token'];
			$this->_user_id     = $result['user_id'];
		} else {
			throw new Exception( $result['error_message'] );
		}
		return $result;
	}
	/**
	 * this function is to get the top rated most of specific hashtag
	 */
	public function get_data_from_specific_hashtag( $keyword, $limit ) {
		$base_url = self::H_BASE_URL;
		// fields to retrive
		$fields                   = 'id,media_type,comments_count,like_count';
		$get_hash_searchurl       = $base_url . 'ig_hashtag_search?user_id=' . $this->_user_id . '&q=' . $keyword;
		$make_call_response       = file_get_contents( $get_hash_searchurl );
		$make_call_response_array = json_decode( $make_call_response, true );
		if ( ! array_key_exists( 'id', $make_call_response ) ) {
			throw new Exception( 'Error in receiving hashtag ID' );
		}
		$hashtag_id                   = $make_call_response_array['id'];
		$get_hash_top_media_url       = $base_url . $hashtag_id . '/top_media?user_id=' . $this->_user_id . '&fields=' . $fields;
		$make_call_response_top       = file_get_contents( $get_hash_top_media_url );
		$make_call_response_top_array = json_decode( $make_call_response_top, true );
		if ( ! array_key_exists( 'data', $make_call_response_top_array ) ) {
			throw new Exception( 'Error in receiving hashtag Data' );
		}
		$data_of_specfic = array_slice( $make_call_response_top_array['data'], 0, $limit );
		return $data_of_specfic;

	}
	public function get_export_hash_data_csv( $keyword, $limit, $filename ) {
		$top_rate_data = $this->get_data_from_specific_hashtag( $keyword, $limit );
		$fh            = fopen( $filename, 'w' );
		// write out the headers
		fputcsv( $fh, array_keys( current( $top_rate_data ) ) );

		// write out the data
		foreach ( $top_rate_data as $row ) {
			fputcsv( $fh, $row );
		}
		rewind( $fh );
		$csv = stream_get_contents( $fh );
		fclose( $fh );

	}
}

$instagram = new instagram(
	array(
		'client_id'     => 'Client_ID',
		'client_secret' => 'Client_secret',
		'redirect_url'  => 'url',
		'code'          => 'code',
	)
);
$instagram->get_export_hash_data_csv( 'nature', 100, 'test.csv' );
