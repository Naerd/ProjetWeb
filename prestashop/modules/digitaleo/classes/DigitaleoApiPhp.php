<?php
/**
 * NOTICE OF LICENSE.
 *
 * This source file is subject to a commercial license from DIGITALEO SAS
 * Use, copy, modification or distribution of this source file without written
 * license agreement from the DIGITALEO SAS is strictly forbidden.
 *
 *  @author	    Digitaleo
 *  @copyright 2016 Digitaleo
 *  @license   All Rights Reserved
 */

class DigitaleoApiPhp
{
    /**
     * Available Output format.
     *
     * @var array
     */
    protected static $outputFormatAllowed = array(
        'application/json',
        'application/xml',
        'text/csv',
        'application/js',
        'application/bin',
    );

    const INPUT_JSON = 'application/json';
    const INPUT_FORM_DATA = 'multipart/form-data';
    const INPUT_URLENCODED = 'application/x-www-form-urlencoded';

    const GRANT_REFRESH = 'refresh_token';
    const GRANT_PASSWORD = 'password';
    const GRANT_CLIENT = 'client_credentials';
    const GRANT_FINAL_USER_DIGITALEO = 'digitaleo_finaluser';
    const GRANT_FINAL_USER_FACEBOOK = 'facebook_finaluser';

    const VERB_GET = 'GET';
    const VERB_POST = 'POST';
    const VERB_PUT = 'PUT';
    const VERB_DELETE = 'DELETE';

    /**
     * Informations de connexion de cURL.
     *
     * @var array
     */
    public $curlInfos;

    /**
     * Format of the response.
     *
     * @var string
     */
    protected $format;

    /**
     * Sortie directe dans l'outputStream de php.
     *
     * @var bool
     */
    protected $immediateOutput;

    /**
     * Base URL to access the API.
     *
     * @var string
     */
    private $baseUrl;

    /**
     * Current Credential.
     *
     * @var Credential
     */
    private $credential = null;

    /**
     * Timeout culr option.
     *
     * @var string
     */
    private $timeout = null;

    /**
     * Content Type.
     *
     * @var string
     */
    private $contentType;

    /**
     * Code HTTP.
     *
     * @var int
     */
    private $responseCode;

    /**
     * Response API.
     *
     * @var string
     */
    private $response = '';

    /**
     * REQUEST TYPE (GET, POST, PUT, DELETE).
     *
     * @var string
     */
    private $verb;

    /**
     * URI called.
     *
     * @var string
     */
    private $callUri;

    /**
     * Version wrapper.
     *
     * @var string
     */
    private $version = '2.1';

    /**
     * Headers HTTP utilisé pour la requete.
     *
     * @var array
     */
    private $additionnalHeaders;

    /**
     * Constructor.
     *
     * @param string       $baseUrl           Base URL to access the API
     * @param string       $outputFormat      [Optional] Format of the response
     * @param bool|string  $immediateOutput   [Optional] Output should be direct or not
     * @param array|string $additionalHeaders [Optional] Headers to add by default to requests
     *                                        key / value array
     *                                        ex : ['Accept'=> 'application/json']
     *
     * @throws Exception
     */
    public function __construct(
        $baseUrl = null,
        $outputFormat = 'application/json',
        $immediateOutput = false,
        $additionalHeaders = array()
    ) {
        // Check extension cURL
        if (!extension_loaded('curl')) {
            throw new \Exception('Extension "curl" is not loaded.');
        }

        include_once dirname(__FILE__).'/DigitaleoApiCredential.php';

        if (!empty($baseUrl)) {
            $this->setBaseUrl($baseUrl);
        }

        $this->setFormat($outputFormat);
        $this->setImmediateOutput($immediateOutput);
        $this->setAdditionnalHeaders($additionalHeaders);
        $this->contentType = self::INPUT_URLENCODED;
    }

    /**
     * Define base URL to access the API.
     *
     * @param string $baseUrl Base URL
     */
    public function setBaseUrl($baseUrl)
    {
        if (Tools::substr($baseUrl, -1) !== '/') {
            $baseUrl .= '/';
        }
        $this->baseUrl = $baseUrl;
    }

    /**
     * Define the curl timeout.
     *
     * @param string $timeout Curl Timeout
     *
     * @return Eo_Rest_WrapperOauth
     */
    public function setTimeout($timeout)
    {
        $this->timeout = $timeout;

        return $this;
    }

    /**
     * Define format of the response.
     *
     * @param string $format Format response
     *
     * @throws \Exception
     */
    public function setFormat($format)
    {
        $format = Tools::strtolower($format);
        if (!in_array($format, self::$outputFormatAllowed)) {
            $formats = implode(', ', self::$outputFormatAllowed);
            throw new \Exception('Only '.$formats.' are supported.');
        }
        $this->format = $format;
    }

    /**
     * @param string $contentType contenttype's value
     *
     * @throws Exception
     */
    public function setContentType($contentType)
    {
        if (!in_array($contentType, array(self::INPUT_URLENCODED, self::INPUT_JSON, self::INPUT_FORM_DATA))) {
            throw new \Exception('content type not supported');
        }
        $this->contentType = $contentType;
    }

    /**
     * Define immediate ouput status.
     *
     * @param bool $active Active direct ouput or not
     */
    public function setImmediateOutput($active)
    {
        $this->immediateOutput = $active;
    }

    /**
     * Set Additionnal Headers.
     *
     * @param array $additionnalHeaders Headers to add by default to requests
     *                                  key / value array
     *                                  ex : ['Accept'=> 'application/json']
     */
    public function setAdditionnalHeaders($additionnalHeaders)
    {
        $this->additionnalHeaders = $additionnalHeaders;
    }

    // <editor-fold desc="credentials" defaultstate="collapsed">

    /**
     * Définition de Credential pour le grant type "client_credentials".
     *
     * @param string $url          URL du serveur d'autorisation
     * @param string $clientId     Client ID
     * @param string $clientSecret Client Secret
     * @param string $token        oauth token (Optional)
     *
     * @return Credential credential
     */
    public function setOauthClientCredentials($url, $clientId, $clientSecret, $token = null)
    {
        $credential = new Credential();
        $credential->grantType = self::GRANT_CLIENT;
        $credential->clientId = $clientId;
        $credential->clientSecret = $clientSecret;
        $credential->url = $url;
        $credential->token = $token;
        $this->setCredential($credential);

        return $credential;
    }

    /**
     * Définition de Credential pour le grant type "password".
     *
     * @param string $url          URL du serveur d'autorisation
     * @param string $clientId     Client ID
     * @param string $clientSecret Client Secret
     * @param string $username     user name
     * @param string $password     user password
     * @param string $token        oauth token (Optional)
     *
     * @return Credential credential
     */
    public function setOauthPasswordCredentials($url, $clientId, $clientSecret, $username, $password, $token = null)
    {
        $credential = new Credential();
        $credential->grantType = self::GRANT_PASSWORD;
        $credential->clientId = $clientId;
        $credential->clientSecret = $clientSecret;
        $credential->username = $username;
        $credential->password = $password;
        $credential->url = $url;
        $credential->token = $token;
        $this->setCredential($credential);

        return $credential;
    }

    /**
     * Définition de Credential pour le grant type "digitaleo finaluser".
     *
     * @param string $url          URL du serveur d'autorisation
     * @param string $clientId     Client ID
     * @param string $clientSecret Client Secret
     * @param string $username     user name
     * @param string $password     user password
     * @param string $token        oauth token (Optional)
     *
     * @return Credential
     *
     * @throws Exception
     */
    public function setOauthFinalUserDigitaleoCredential(
        $url,
        $clientId,
        $clientSecret,
        $username,
        $password,
        $token = null
    ) {
        $credential = new Credential();
        $credential->grantType = self::GRANT_FINAL_USER_DIGITALEO;
        $credential->clientId = $clientId;
        $credential->clientSecret = $clientSecret;
        $credential->username = $username;
        $credential->password = $password;
        $credential->url = $url;
        $credential->token = $token;
        $this->setCredential($credential);

        return $credential;
    }

    /**
     * Définition de Credential pour le grant type "facebook user".
     *
     * @param string $url           URL du serveur d'autorisation
     * @param string $clientId      Client ID
     * @param string $clientSecret  Client Secret
     * @param string $facebookToken facebook token
     * @param string $token         oauth token (Optional)
     *
     * @return Credential
     *
     * @throws Exception
     */
    public function setOauthFinalUserFacebookCredential($url, $clientId, $clientSecret, $facebookToken, $token = null)
    {
        $credential = new Credential();
        $credential->grantType = self::GRANT_FINAL_USER_FACEBOOK;
        $credential->clientId = $clientId;
        $credential->clientSecret = $clientSecret;
        $credential->facebookToken = $facebookToken;
        $credential->url = $url;
        $credential->token = $token;
        $this->setCredential($credential);

        return $credential;
    }

    /**
     * Définition de Credential pour le grant type "refresh_token".
     *
     * @param string $url          URL du serveur d'autorisation
     * @param string $clientId     Client ID
     * @param string $clientSecret Client Secret
     * @param string $refreshToken refresh token
     * @param string $token        oauth token (Optional)
     *
     * @return Credential credential
     */
    public function setRefreshToken($url, $clientId, $clientSecret, $refreshToken, $token = null)
    {
        $credential = new Credential();
        $credential->grantType = self::GRANT_REFRESH;
        $credential->clientId = $clientId;
        $credential->clientSecret = $clientSecret;
        $credential->refreshToken = $refreshToken;
        $credential->url = $url;
        $credential->token = $token;
        $this->setCredential($credential);

        return $credential;
    }

    /**
     * Définition de Credential avec un token oauth.
     *
     * @param string $token Token (Optional)
     *
     * @return Credential
     */
    public function setOauthToken($token)
    {
        $credential = new Credential();
        $credential->token = $token;
        $this->setCredential($credential);

        return $credential;
    }

    /**
     * Retrieves an authentication token.
     *
     * @param bool $force
     *
     * @return Credential|void
     *
     * @throws Exception
     */
    public function callGetToken($force = false)
    {
        $credential = $this->getCredential();

        if (!isset($credential)) {
            throw new \Exception('Credential is required');
        }

        if (!isset($credential->grantType)) {
            return $credential;
        }

        if (isset($credential->token) && !$force) {
            return $credential;
        }

        $postFields = array();
        $postFields['client_id'] = $credential->clientId;
        $postFields['client_secret'] = $credential->clientSecret;
        $postFields['grant_type'] = $credential->grantType;
        if ($credential->grantType == self::GRANT_REFRESH) {
            $postFields['refresh_token'] = $credential->refreshToken;
        } elseif ($credential->grantType == self::GRANT_PASSWORD) {
            $postFields['username'] = $credential->username;
            $postFields['password'] = $credential->password;
        } elseif ($credential->grantType == self::GRANT_FINAL_USER_DIGITALEO) {
            $postFields['username'] = $credential->username;
            $postFields['password'] = $credential->password;
        } elseif ($credential->grantType == self::GRANT_FINAL_USER_FACEBOOK) {
            $postFields['token'] = $credential->facebookToken;
        }

        // Création d'une nouvelle ressource cURL
        $chOauth = curl_init();
        // Configuration de l'URL et d'autres options
        curl_setopt($chOauth, CURLOPT_URL, $credential->url);
        curl_setopt($chOauth, CURLOPT_HEADER, 0);
        curl_setopt($chOauth, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($chOauth, CURLOPT_POSTFIELDS, $postFields);
        curl_setopt($chOauth, CURLOPT_SSL_VERIFYPEER, false);

        // Requête HTTP
        $oAuthResult = Tools::jsonDecode(curl_exec($chOauth));

        // Fermeture de la session cURL
        curl_close($chOauth);

        if (isset($oAuthResult)) {
            // Récupération de l'access_token
            if (isset($oAuthResult->access_token)) {
                $credential->token = $oAuthResult->access_token;
            }
            if (isset($oAuthResult->refresh_token)) {
                $credential->refreshToken = $oAuthResult->refresh_token;
            }
        }

        return $credential;
    }

    /**
     * @param string $resource            API resource to call
     * @param array  $params              parameters to add to request
     * @param array  $additionnalsHeaders headers to add to request
     *                                    key / value array
     *                                    ex : ['Accept'=> 'application/json']
     *
     * @return mixed formatted http response
     *
     * @throws Exception
     */
    public function callGet($resource, $params = array(), $additionnalsHeaders = array())
    {
        return $this->call($resource, self::VERB_GET, $params, null, $additionnalsHeaders);
    }

    /**
     * @param string $resource            API resource to call
     * @param array  $body                data to post
     * @param array  $params              parameters to add to request
     * @param array  $additionnalsHeaders headers to add to request
     *                                    key / value array
     *                                    ex : ['Accept'=> 'application/json']
     *
     * @return mixed formatted http response
     *
     * @throws Exception
     */
    public function callPost($resource, $body, $params = array(), $additionnalsHeaders = array())
    {
        return $this->call($resource, self::VERB_POST, $params, $body, $additionnalsHeaders);
    }

    /**
     * @param string $resource            API resource to call
     * @param array  $files               files to post
     * @param array  $body                data to post
     * @param array  $params              parameters to add to request
     * @param array  $additionnalsHeaders headers to add to request
     *                                    key / value array
     *                                    ex : ['Accept'=> 'application/json']
     *
     * @return mixed formatted http response
     *
     * @throws Exception
     */
    public function callPostFile($resource, $files, $body = array(), $params = array(), $additionnalsHeaders = array())
    {
        $body = $this->formatRequestForFiles($files, $body, $additionnalsHeaders);
        $result = $this->call($resource, self::VERB_POST, $params, $body, $additionnalsHeaders);

        return $result;
    }

    /**
     * @param string $resource            API resource to call
     * @param array  $body                data to put
     * @param array  $params              parameters to add to request
     * @param array  $additionnalsHeaders headers to add to request
     *                                    key / value array
     *                                    ex : ['Accept'=> 'application/json']
     *
     * @return mixed formatted http response
     *
     * @throws Exception
     */
    public function callPut($resource, $body, $params = array(), $additionnalsHeaders = array())
    {
        return $this->call($resource, self::VERB_PUT, $params, $body, $additionnalsHeaders);
    }

    /**
     * @param string $resource            API resource to call
     * @param array  $files               files to post
     * @param array  $body                data to post
     * @param array  $params              parameters to add to request
     * @param array  $additionnalsHeaders headers to add to request
     *                                    key / value array
     *                                    ex : ['Accept'=> 'application/json']
     *
     * @return mixed formatted http response
     *
     * @throws Exception
     */
    public function callPutFile($resource, $files, $body = array(), $params = array(), $additionnalsHeaders = array())
    {
        $body = $this->formatRequestForFiles($files, $body, $additionnalsHeaders);
        $result = $this->call($resource, self::VERB_PUT, $params, $body, $additionnalsHeaders);

        return $result;
    }

    /**
     * @param string $resource            API resource to call
     * @param array  $params              parameters to add to request
     * @param array  $additionnalsHeaders headers to add to request
     *                                    key / value array
     *                                    ex : ['Accept'=> 'application/json']
     *
     * @return mixed formatted http response
     *
     * @throws Exception
     */
    public function callDelete($resource, $params = array(), $additionnalsHeaders = array())
    {
        return $this->call($resource, self::VERB_DELETE, $params, null, $additionnalsHeaders);
    }

    /**
     * Retourne la dernière requête effectuée.
     *
     * @return string
     */
    public function getLastRequest()
    {
        return $this->callUri;
    }

    /**
     *  HTTP status code.
     *
     * @return int HTTP status code
     */
    public function getResponseCode()
    {
        return $this->responseCode;
    }

    /**
     * @return string called uri
     */
    public function getUri()
    {
        return $this->callUri;
    }

    /**
     * @return string HTTP verb used
     */
    public function getVerb()
    {
        return $this->verb;
    }

    /**
     * Method of debug.
     *
     * @return array
     */
    public function getDetails()
    {
        $datas = array();
        $datas['response_code'] = $this->responseCode;
        $datas['url'] = $this->callUri;
        $callUri = parse_url($this->callUri);
        $datas['scheme'] = $callUri['scheme'];
        $datas['host'] = $callUri['host'];
        $datas['verb'] = $this->verb;
        $args = explode('&', $callUri['query']);
        foreach ($args as $arg) {
            $arg = explode('=', $arg);
            $datas['params_query'][$arg[0]] = $arg[1];
        }

        return $datas;
    }

    /**
     * @param mixed $handle
     *
     * @return mixed response
     */
    protected function callExec($handle)
    {
        $buffer = curl_exec($handle);
        $this->curlInfos = curl_getinfo($handle);

        return $this->createResponse($buffer);
    }

    /**
     * Set Credential.
     *
     * @param Credential $credential
     */
    public function setCredential(Credential $credential)
    {
        $this->credential = $credential;
    }

    /**
     * Get Credential.
     *
     * @return Credential
     */
    public function getCredential()
    {
        return $this->credential;
    }
    // </editor-fold>

    /**
     * Call the API.
     *
     * @param string $resource            Resource REST
     * @param string $httpVerb            HTTP Verb
     * @param array  $params              Params
     * @param null   $body
     * @param array  $additionnalsHeaders
     * @param bool   $force               Force get token
     *
     * @return bool
     *
     * @throws Exception
     */
    protected function call(
        $resource,
        $httpVerb,
        $params = array(),
        $body = null,
        $additionnalsHeaders = array(),
        $force = false
    ) {
        // Check base URL to access the API is set
        if (empty($this->baseUrl)) {
            throw new \InvalidArgumentException('Please set the base url to access the API.');
        }

        $this->callGetToken($force);

        $handle = $this->initCurl();
        $this->setCurlOptions($handle, $httpVerb, $body, $additionnalsHeaders);

        $uri = $this->createUri($resource, $params);
        curl_setopt($handle, CURLOPT_URL, $uri);
        curl_setopt($handle, CURLOPT_SSL_VERIFYPEER, false);

        $response = $this->callExec($handle);

        // Close curl process
        curl_close($handle);

        if ($this->getResponseCode() == 401 && !$force) {
            $response = $this->call($resource, $httpVerb, $params, $body, $additionnalsHeaders, true);
        }

        return $response;
    }

    /**
     * @param string $buffer response
     *
     * @return string
     */
    private function createResponse($buffer)
    {
        // Response code
        $this->responseCode = $this->curlInfos['http_code'];

        // RESPONSE
        $this->response = $buffer;

        return $this->response;
    }

    /**
     * Init curl.
     *
     * @return resource
     */
    private function initCurl()
    {
        $handle = curl_init();

        $configCurl = array(
            CURLOPT_HEADER => false,
            CURLOPT_RETURNTRANSFER => !$this->immediateOutput,
            CURLOPT_CONNECTTIMEOUT => 10,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        );
        $componentsUrl = parse_url($this->baseUrl);
        // Check secure URL
        if ($componentsUrl['scheme'] == 'https'
            || (array_key_exists('port', $componentsUrl) && $componentsUrl['port'] != 80)) {
            $configCurl[CURLOPT_SSL_VERIFYPEER] = false;
            $configCurl[CURLOPT_SSL_VERIFYHOST] = 2;
            $configCurl[CURLOPT_SSLVERSION] = 1;
        }
        curl_setopt_array($handle, $configCurl);

        return $handle;
    }

    /**
     * Set Curl options.
     *
     * @param resource $handle
     * @param string   $httpVerb
     * @param array    $body
     * @param array    $additionnalsHeaders
     *
     * @throws Exception
     */
    private function setCurlOptions($handle, $httpVerb, $body, $additionnalsHeaders = array())
    {
        /**
         * HTTP Header management.
         */
        $headers = array('Authorization' => 'Bearer '.$this->getCredential()->token);
        if (isset($this->contentType)) {
            $headers['Content-Type'] = $this->contentType;
        }
        if (isset($this->format)) {
            $headers['Accept'] = $this->format;
        }

        $headers = array_merge($headers, (array) $this->additionnalHeaders, $additionnalsHeaders);
        $contentType = $headers['Content-Type'];

        $headers = array_map("returnKeyValues", array_keys($headers), array_values($headers));

        curl_setopt($handle, CURLOPT_HTTPHEADER, $headers);

        if ($httpVerb == self::VERB_POST) {
            curl_setopt($handle, CURLOPT_POST, true);
            curl_setopt($handle, CURLOPT_POSTFIELDS, $this->formatInputData($contentType, $body));
        } elseif ($httpVerb == self::VERB_GET) {
            curl_setopt($handle, CURLOPT_POST, false);
        } elseif ($httpVerb == self::VERB_PUT) {
            curl_setopt($handle, CURLOPT_POST, false);
            curl_setopt($handle, CURLOPT_CUSTOMREQUEST, $httpVerb);
            curl_setopt($handle, CURLOPT_POSTFIELDS, $this->formatInputData($contentType, $body));
        } elseif ($httpVerb == self::VERB_DELETE) {
            curl_setopt($handle, CURLOPT_POST, false);
            curl_setopt($handle, CURLOPT_CUSTOMREQUEST, $httpVerb);
        } else {
            throw new \Exception('invalid verb');
        }

        $this->verb = $httpVerb;

        /*
         * Http request timeout
         */
        if (!empty($this->timeout)) {
            curl_setopt($handle, CURLOPT_TIMEOUT, $this->timeout);
        }
    }

    /**
     * Build the URI.
     *
     * @param string $resource resource to append to URI
     * @param array  $params   paramters to append to URI
     *
     * @return string
     */
    private function createUri($resource, $params = array())
    {
        $uri = $this->baseUrl.$resource;
        if (!empty($params) && is_array($params)) {
            $uri .= '?'.http_build_query($params);
        }
        $this->callUri = $uri;

        return $uri;
    }

    /**
     * Formats input data.
     *
     * @param string $contentType content type for the request
     * @param array  $params      input data
     *
     * @return mixed formated input data according to content type
     */
    private function formatInputData($contentType, $params)
    {
        $inputData = $params;
        if ($contentType == self::INPUT_JSON) {
            if (!is_string($inputData)) {
                $inputData = Tools::jsonEncode($params);
            }
        }
        if ($contentType == self::INPUT_URLENCODED) {
            if (!is_string($inputData)) {
                $inputData = http_build_query($params);
            }
        }

        return $inputData;
    }

    /**
     * Formats request for sending files
     * <ul>
     *      <li>forces content-type to multipart/form-data</li>
     *      <li>adds @ in front of each file path (needed by cUrl)</li>
     * </ul>.
     *
     * @param array $files              list of file paths
     * @param array $body               list of request body parameters
     * @param array $additionnalHeaders list of headers for request
     *
     * @return array list of request body parameters completed by formated files paths
     */
    private function formatRequestForFiles($files, $body, &$additionnalHeaders)
    {
        $additionnalHeaders = array_merge($additionnalHeaders, array('Content-Type' => self::INPUT_FORM_DATA));
        foreach ($files as $key => $value) {
            $body[$key] = '@'.$value;
        }

        return $body;
    }
}

function returnKeyValues($key, $value)
{
    return "$key: $value";
}
