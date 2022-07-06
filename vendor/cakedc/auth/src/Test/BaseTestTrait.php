<?php
declare(strict_types=1);

namespace CakeDC\Auth\Test;

use Cake\Console\ConsoleIo;
use Cake\Core\Configure;
use Cake\ORM\TableRegistry;

trait BaseTestTrait
{
    /**
     * Sets up the session as a logged in user for an user with id $id
     *
     * @param string $id User id to login.
     * @return void
     */
    public function loginAsUserId($id)
    {
        $data = TableRegistry::getTableLocator()
            ->get(Configure::read('Users.table', 'Users'))->get($id)->toArray();
        $this->session(['Auth' => ['User' => $data]]);
    }

    /**
     * Login as a username
     *
     * @param string $username Username to login.
     * @return void
     */
    public function loginAsUserName($username)
    {
        $data = TableRegistry::getTableLocator()
            ->get(Configure::read('Users.table', 'Users'))->findByUsername($username)->first()->toArray();
        $this->session(['Auth' => ['User' => $data]]);
    }

    /**
     * @return bool
     */
    protected function _isVerboseOrDebug()
    {
        return !empty(array_intersect(['--debug', '--verbose', '-v'], $_SERVER['argv']));
    }

    /**
     * Run permissions tests
     *
     * @param string $url Test url
     * @param string $username Username.
     * @param string $method Request method.
     * @param string $ajax Ajax value
     * @param string $responseCode Reponse code.
     * @param string $responseContains Text the response should contains.
     * @throws \PHPUnit\Exception
     */
    protected function _testPermissions($url, $username, $method, $ajax, $responseCode, $responseContains)
    {
        if ($this->_isVerboseOrDebug()) {
            (new ConsoleIo())->info(__(
                "\nUrl: {0} Username: {1} Method: {2} Ajax?: {3} Response Code: {4} Response Contains: {5} ",
                $url,
                $username,
                $method,
                $ajax,
                $responseCode,
                $responseContains
            ), 0);
        }
        $this->loginAsUserName($username);
        if ($ajax === 'ajax') {
            $_SERVER['HTTP_X_REQUESTED_WITH'] = 'XMLHttpRequest';
        }
        if ($method === 'post') {
            $this->enableCsrfToken();
            $this->enableSecurityToken();
            $this->post($url);
        } else {
            $this->get($url);
        }
        if ($responseCode === '200') {
            $this->assertResponseOk();
        } else {
            $this->assertResponseCode((int)$responseCode);
        }

        if ($responseContains) {
            $this->assertResponseContains($responseContains);
        } else {
            $this->assertEmpty((string)$this->_response->getBody());
        }
    }

    /**
     * Test permissions
     *
     * @param string $csv CSV name
     * @return array
     * @dataProvider provider
     */
    public function testPermissions($csv)
    {
        $this->assertTrue(file_exists(TESTS . 'Provider' . DS . $csv));
        $rows = array_map('str_getcsv', file(TESTS . 'Provider' . DS . $csv));
        foreach ($rows as $row) {
            if ($row[0][0] === '#') {
                continue;
            }
            [$url, $username, $method, $ajax, $responseCode, $responseContains] = array_pad($row, 6, null);
            $this->setUp();
            $this->_testPermissions($url, $username, $method, $ajax, $responseCode, $responseContains);
            $this->tearDown();
        }
    }
}
