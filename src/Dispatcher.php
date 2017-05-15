<?php

namespace Bitstone\GuzzleWrapper;

use Exception;
use GuzzleHttp\Client as GuzzleClient;
use GuzzleHttp\Exception\RequestException;
use Bitstone\GuzzleWrapper\Exceptions\HttpException;
use Bitstone\GuzzleWrapper\Exceptions\ClientException;
use Bitstone\GuzzleWrapper\Exceptions\ServerException;
use Bitstone\GuzzleWrapper\Exceptions\RedirectException;
use Bitstone\GuzzleWrapper\Exceptions\InformationalException;

class Dispatcher
{
    /**
     * @param $type
     * @param $url
     * @param array $options
     * @param array $headers
     * @return mixed
     * @throws ClientException
     * @throws InformationalException
     * @throws RedirectException
     * @throws ServerException
     */
    public function request($type, $url, $options = [], $headers = [])
    {
        $data = [];

        try {
            $client = new GuzzleClient();

            $data['headers'] = $this->prepareHeaders($headers);

            (strtoupper($type) == 'GET') ? $data['query'] = $options : $data['json'] = $options;

            $response = $client->request($type, $url, $data);

            $this->checkStatusCode($response->getStatusCode());

            return json_decode($response->getBody(), true);
        } catch (RequestException $e) {
            $this->checkStatusCode($e->getCode());
        } catch (Exception $e) {
            throw new HttpException(500);
        }
    }

    /**
     * @param $headers
     * @return mixed
     */

    protected function prepareHeaders($headers)
    {
        if (!isset($headers['Content-Type'])) {
            $headers['Content-Type'] = 'application/json';
        }

        if (!isset($headers['Accept'])) {
            $headers['Accept'] = 'application/json';
        }

        return $headers;
    }

    /**
     * This method will help rethrowing the request exceptions
     * @param $response
     * @throws ClientException
     * @throws InformationalException
     * @throws RedirectException
     * @throws ServerException
     */

    protected function checkStatusCode($code)
    {
        //Status code 5**
        if ($this->isServerError($code)){
            throw new ServerException($code);
        }

        //Status code 4**
        if ($this->isClientError($code)){
            throw new ClientException($code);
        }

        //Status code 3**
        if ($this->isRedirect($code)){
            throw new RedirectException($code);
        }

        //Status code 1**
        if ($this->isInformational($code)){
            throw new InformationalException($code);
        }
    }

    /**
     * Checks if HTTP Status code is Server Error (5xx)
     * @param integer $status
     * @return bool
     */
    public function isServerError($status)
    {
        return $status >= 500 && $status < 600;
    }

    /**
     * Checks if HTTP Status code is a Client Error (4xx)
     * @param integer $status
     * @return bool
     */
    public function isClientError($status)
    {
        return $status >= 400 && $status < 500;
    }

    /**
     * Checks if HTTP Status code is a Redirect (3xx)
     * @param integer $status
     * @return bool
     */
    public function isRedirect($status)
    {
        return $status >= 300 && $status < 400;
    }

    /**
     * Checks if HTTP Status code is Information (1xx)
     * @param integer $status
     * @return bool
     */
    public function isInformational($status)
    {
        return $status < 200;
    }

    /**
     * This method is used for the magic calls
     * @param $function
     * @param $args
     * @return mixed
     * @throws HttpException
     */
    public function __call($function, $args)
    {
        if (!isset($args[1]) || !is_array($args[1])) {
            $args[1] = [];
        }

        if (!isset($args[2]) || !is_array($args[2])) {
            $args[2] = [];
        }

        if (in_array(strtoupper($function), ['GET', 'HEAD', 'POST', 'PUT', 'DELETE', 'OPTIONS'])) {
            return $this->request($function, $args[0], $args[1], $args[2]);
        }

        throw new HttpException(500);
    }
}
