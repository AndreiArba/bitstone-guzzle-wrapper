<?php

namespace Tests\Unit;

use Bitstone\GuzzleWrapper\Dispatcher;

class RequestTest extends \PHPUnit_Framework_TestCase
{
    protected $testUrl;
    protected $http;

    public function setUp()
    {
        $this->testUrl = 'http://kiss.bitstoneint.com/api/v1/login';
        $this->http = new Dispatcher();
        parent::setUp();
    }
    /**
     * Testing that the results exists.
     *
     * @return void
     */
    public function testResultExists()
    {
        $response = $this->http->post($this->testUrl, ['email' => 'admin@email.com', 'password' => 'asdasd']);

        $this->assertTrue(count($response) > 0);
    }

    /**
     * Testing the status field of the response.
     *
     * @return void
     */
    public function testStatusSuccess()
    {
        $response = $this->http->post($this->testUrl, ['email' => 'admin@email.com', 'password' => 'asdasd']);

        $this->assertTrue(isset($response['status']) && $response['status'] == 'success');
    }

    /**
     * Full test with authentication in header using the magic method call
     */
    public function testHeaderAuthenticationExample()
    {
        $authenticationResponse = $this->http->post($this->testUrl, ['email' => 'admin@email.com', 'password' => 'asdasd']);

        $this->assertTrue(isset($authenticationResponse['data']) && isset($authenticationResponse['data']['authToken']));

        $token = $authenticationResponse['data']['authToken'];

        $this->assertTrue(isset($authenticationResponse['data']) && isset($authenticationResponse['data']['user'])
            && isset($authenticationResponse['data']['user']['id']));

        $userId = $authenticationResponse['data']['user']['id'];

        $response = $this->http->get('http://kiss.bitstoneint.com/api/v1/profile', ['id' => $userId], ['authToken' => $token]);

        $this->assertTrue($response['status'] == 'success');
    }

    /**
     * Full test with authentication in header using the request method call
     */
    public function testAuthenticationWithoutMagicMethod()
    {
        $authenticationResponse = $this->http->request('POST', $this->testUrl, ['email' => 'admin@email.com', 'password' => 'asdasd']);

        $this->assertTrue(isset($authenticationResponse['data']) && isset($authenticationResponse['data']['authToken']));

        $token = $authenticationResponse['data']['authToken'];

        $this->assertTrue(isset($authenticationResponse['data']) && isset($authenticationResponse['data']['user'])
            && isset($authenticationResponse['data']['user']['id']));

        $userId = $authenticationResponse['data']['user']['id'];

        $response = $this->http->request('GET', 'http://kiss.bitstoneint.com/api/v1/profile', ['id' => $userId], ['authToken' => $token]);

        $this->assertTrue($response['status'] == 'success');
    }

}