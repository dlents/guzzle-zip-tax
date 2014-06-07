<?php
/**
 * Created by David Lents <david@lents.net>
 * Date: 2013-08-01
 * Time: 13:33
 * Created with: JetBrains PhpStorm
 */

class GuzzleZipTax {
    protected $api_key;
    protected $api_url;
    protected $api_version;
    protected $api_client;
    protected $api_response_flavor;
    protected $api_request_params;
    protected $api_response_codes;
    protected $api_response_result;

    /**
     * @param string $key
     * @param string $flavor
     */
    public function __construct($key = '', $flavor = 'JSON') {
        $this->api_key = $key;
        /**
         * Possible response codes and their meanings
         *  @note Unfortunately, this seems to be of no use because the API does not respond as documented
         *    + Invalid postal_code, city, state return code 100, with empty results rather than the expected error code
         * @link http://www.zip-tax.com/documentation
         */
        $this->api_response_codes = array(
            '100' => 'SUCCESS',
            '101' => 'INVALID_KEY',
            '102' => 'INVALID_STATE',
            '103' => 'INVALID_CITY',
            '104' => 'INVALID_POSTAL_CODE',
            '105' => 'INVALID_FORMAT'
        );
        $this->api_version        = 'v20';
        $this->api_url            = 'http://api.zip-tax.com';
        if (strtoupper($flavor) === 'XML') {
            $this->api_response_flavor = 'XML';
        }
        else {
            $this->api_response_flavor = 'JSON';
        }

        $this->api_request_params = array(
            'key'    => $this->api_key,
            'format' => $this->api_response_flavor
        );
        // Init Guzzle client
        $this->api_client = new Guzzle\Http\Client($this->api_url);
    }

    /**
     * @param string $zip
     * @param array $optional_params
     *
     * @return array|bool
     */
    public function fetch($zip, $optional_params = array()) {
        // the postalcode param is the only required param (other than the api key)
        if (empty($zip)) {
            trigger_error('A postal code parameter is required', E_USER_ERROR);
            return false;
        }

        $this->api_request_params['postalcode'] = $zip;

        // v20 has optional params: 'state', 'city'
        if (!empty($optional_params)) {
            $this->api_request_params = array_merge($this->api_request_params, $optional_params);
        }

        $this->api_response_result = $this->api_client->get(
            "/request/{$this->api_version}?",
            array(),
            array(
                 'query' => $this->api_request_params
            )
        )->send();

        return $this->parseResponse();
}

    /**
     * @return array
     */
    protected function parseResponse() {
        // The error checking may be for nought. Despite the docs, bad zip/city/state returns 100 (success) and empty result set
        switch ($this->api_response_flavor) {
            case 'XML':
                $ret = $this->api_response_result->xml();
                if (intval($ret->code) === 100) {
                    return $ret->response;
                }
                else {
                    $ret->error = $this->api_response_codes["{$ret->code}"];
                    $ret->request_uri = $this->api_response_result->getEffectiveUrl();
                    return $ret;
                }
                break;
            default:
                $ret = $this->api_response_result->json();
                if (intval($ret['rCode']) === 100) {
                    return $ret['results'];
                }
                else {
                    return array(
                        'error'       => $this->api_response_codes[$ret['rCode']],
                        'request_uri' => $this->api_response_result->getEffectiveUrl()
                    );
                }
                break;
        }
    }
}
