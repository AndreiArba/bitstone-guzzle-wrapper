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
     * @var array
     */
    protected $data = [];

    /**
     * @param $type
     * @param $url
     * @param array $options
     * @param array $headers
     * @return mixed
     * @throws ClientException
     * @throws HttpException
     * @throws InformationalException
     * @throws RedirectException
     * @throws ServerException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function request($type, $url, $options = [], $headers = [])
    {
        try {
            $this->data = [];

            $client = new GuzzleClient();

            $this->prepareHeaders($headers);

            $this->setDataByHeaderType($type, $options);

            $response = $client->request($type, $url, $this->data);

            $this->checkStatusCode($response->getStatusCode());

            return json_decode($response->getBody(), true);
        } catch (RequestException $e) {
            $this->checkStatusCode($e->getCode(), $e->getMessage(), $e->getTraceAsString(), $e->getResponse());
        } catch (Exception $e) {
            throw new HttpException(500, $e->getMessage(), $e->getTraceAsString(), $e->getResponse());
        }
    }

    /**
     * Setting the data parameter according to request header
     * For application/x-www-form-urlencoded or any other non-json header data needs to stay in form_params
     * @param $type
     * @param $options
     */
    public function setDataByHeaderType($type, $options)
    {
        if (isset($this->data['headers'])) {
            $headers = $this->data['headers'];
        } else {
            $headers = [];
        }

        $method = strtoupper($type);

        if ($method == 'GET') {
            $this->data['query'] = $options;
        } else {
            //TODO - handle multipart as well
            if ($headers['Content-Type'] === 'application/json') {
                $this->data['json'] = $options;

            } else {
                $this->data['form_params'] = $options;
            }
        }
    }

    /**
     * @param $data
     */
    public function setData($data)
    {
        $this->data = $data;
    }

    /**
     * @return mixed
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * @param $headers
     */
    public function prepareHeaders($headers)
    {
        if (!isset($headers['Content-Type'])) {
            $headers['Content-Type'] = 'application/json';
        }

        if (!isset($headers['Accept'])) {
            $headers['Accept'] = 'application/json';
        }

        $this->data['headers'] = $headers;
    }

    /**
     * This method will help rethrowing the request exceptions
     * @param $code
     * @param $message
     * @param $stackTrace
     * @throws ClientException
     * @throws InformationalException
     * @throws RedirectException
     * @throws ServerException
     */

    protected function checkStatusCode($code, $message = '', $stackTrace = '', $clientResponse = null)
    {
        //Status code 5**
        if ($this->isServerError($code)){
            throw new ServerException($code, $message, $stackTrace, $clientResponse);
        }

        //Status code 4**
        if ($this->isClientError($code)){
            throw new ClientException($code, $message, $stackTrace, $clientResponse);
        }

        //Status code 3**
        if ($this->isRedirect($code)){
            throw new RedirectException($code, $message, $stackTrace, $clientResponse);
        }

        //Status code 1**
        if ($this->isInformational($code)){
            throw new InformationalException($code, $message, $stackTrace, $clientResponse);
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
     * @throws ClientException
     * @throws HttpException
     * @throws InformationalException
     * @throws RedirectException
     * @throws ServerException
     * @throws \GuzzleHttp\Exception\GuzzleException
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

        throw new HttpException(400);
    }
}
