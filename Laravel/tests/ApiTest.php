<?php

use App\User;

class ApiTest extends TestCase
{

    /**
     * @dataProvider userProvider
     */
    public function testRegistration($name, $email, $password)
    {
        if ($user = User::byEmail($email)) {
            $user->timerows()->delete();
            $user->delete();
        }

        // API Registration
        $response = $this->call('POST', '/api/user', ['email' => $email, 'name' => $name, 'password' => $password]);
        $this->assertEquals(201, $response->getStatusCode());
        $user = json_decode($response->getContent());
        $this->assertEquals($user->name, $name);
        $this->assertEquals($user->email, $email);
    }

    /**
     * @dataProvider userProvider
     */
    public function testLogin($name, $email, $password)
    {
        // API Login
        $response = $this->call('POST', '/api/user/login', ['email' => $email, 'password' => $password]);
        $this->assertEquals(200, $response->getStatusCode());

        $responseData = json_decode($response->getContent());
        $user = $responseData->user;
        $this->assertEquals($name, $user->name);
    }

    /**
     * @dataProvider userProvider
     */
    public function testSettings($name, $email, $password)
    {
        list($token, $userId) = $this->getTokenId($email, $password);
        // API Settings
        $user = $this->request('PUT', '/api/user/' . $userId, ['hours' => 5], $token);
        $this->assertEquals(5, $user->hours);
    }

    /**
     * @dataProvider userProvider
     */
    public function testTimeRows($name, $email, $password)
    {
        list($token, $userId) = $this->getTokenId($email, $password);
        User::find($userId)->timerows()->delete();

        $data = ['note' => 'Work 1', 'date' => date("Y-m-d"), 'duration' => 3];
        $uri = '/api/user/' . $userId . '/timerow';

        // API Save time row
        $first = $this->request('POST', $uri, $data, $token);

        // API Get time row
        $response = $this->request('GET', $uri, [], $token);
        $this->assertEquals('Work 1', $response[0]->note);
        $this->assertEquals(date("Y-m-d"), $response[0]->date);
        $this->assertEquals(3, $response[0]->duration);
        // Row is red
        $this->assertEquals(1, $response[0]->isRed);

        $data = ['note' => 'Work 2', 'date' => date("Y-m-d"), 'duration' => 4];
        $uri = '/api/user/' . $userId . '/timerow';
        $second = $this->request('POST', $uri, $data, $token);

        $response = $this->request('GET', $uri, [], $token);
        $this->assertEquals(2, count($response));
        // Row is green now
        $this->assertEquals(1, $response[0]->isGreen);

        // API Delete time row
        $this->request('DELETE', $uri . '/' . $first->id, [], $token);
        $response = $this->request('GET', $uri, [], $token);
        $this->assertEquals(1, count($response));

        $this->request('DELETE', $uri . '/' . $second->id, [], $token);
        $response = $this->request('GET', $uri, [], $token);
        $this->assertEquals(0, count($response));
    }

    public function userProvider()
    {
        return [
            ['Name', 'test@email', 'pass']
        ];
    }

    /**
     * Login and get token
     * @param $email
     * @param $pass
     * @return mixed
     */
    protected function getTokenId($email, $pass)
    {
        // API Login
        $response = $this->call('POST', '/api/user/login', ['email' => $email, 'password' => $pass]);
        $this->assertEquals(200, $response->getStatusCode());
        $responseData = json_decode($response->getContent());
        return [$responseData->token, $responseData->user->id];
    }

    /**
     * Make API request
     * @param $method string POST, PUT, GET, DELETE
     * @param $uri string URI
     * @param $data array
     * @param $token string AUTH token
     * @param int $assertCode
     * @return mixed
     */
    protected function request($method, $uri, $data, $token, $assertCode = 200)
    {
        $response = $this->call($method, $uri, $data, [], [], ['HTTP_X-Auth-Token' => $token]);
        $this->assertEquals($assertCode, $response->getStatusCode());
        return json_decode($response->getContent());
    }
}
