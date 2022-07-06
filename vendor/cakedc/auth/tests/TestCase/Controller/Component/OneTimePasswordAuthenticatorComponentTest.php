<?php
declare(strict_types=1);

/**
 * Copyright 2010 - 2019, Cake Development Corporation (https://www.cakedc.com)
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright Copyright 2010 - 2019, Cake Development Corporation (https://www.cakedc.com)
 * @license MIT License (http://www.opensource.org/licenses/mit-license.php)
 */

namespace CakeDC\Users\Test\TestCase\Controller\Component;

use Cake\Controller\Controller;
use Cake\Core\Configure;
use Cake\Routing\Router;
use Cake\TestSuite\TestCase;
use Cake\Utility\Security;
use CakeDC\Auth\Controller\Component\OneTimePasswordAuthenticatorComponent;

class OneTimePasswordAuthenticatorComponentTest extends TestCase
{
    /**
     * @var mixed
     */
    public $backupUsersConfig;

    /**
     * @var \Cake\Http\ServerRequest&\PHPUnit\Framework\MockObject\MockObject|mixed
     */
    public $request;

    /**
     * @var \Cake\Http\Response&\PHPUnit\Framework\MockObject\MockObject|mixed
     */
    public $response;

    /**
     * @var \Cake\Controller\Controller|mixed
     */
    public $Controller;

    /**
     * @var \Cake\Controller\ComponentRegistry|mixed
     */
    public $Registry;

    public $OneTimePasswordAuthenticator;

    public $fixtures = [
        'plugin.CakeDC/Auth.Users',
    ];

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->backupUsersConfig = Configure::read('Users');

        Router::reload();
        $builder = Router::createRouteBuilder('/');
        $builder->connect('/route/*', [
            'plugin' => 'CakeDC/Users',
            'controller' => 'Users',
            'action' => 'requestResetPassword',
        ]);
        $builder->connect('/notAllowed/*', [
            'plugin' => 'CakeDC/Users',
            'controller' => 'Users',
            'action' => 'edit',
        ]);

        Security::setSalt('YJfIxfs2guVoUubWDYhG93b0qyJfIxfs2guwvniR2G0FgaC9mi');
        Configure::write('App.namespace', 'Users');
        Configure::write('OneTimePasswordAuthenticator.login', true);

        $this->request = $this->getMockBuilder(\Cake\Http\ServerRequest::class)
                ->setMethods(['is', 'method'])
                ->getMock();
        $this->request->expects($this->any())->method('is')->will($this->returnValue(true));
        $this->response = $this->getMockBuilder(\Cake\Http\Response::class)
                ->setMethods(['stop'])
                ->getMock();
        $this->Controller = new Controller($this->request, $this->response);
        $this->Registry = $this->Controller->components();
        $this->Controller->OneTimePasswordAuthenticator = new OneTimePasswordAuthenticatorComponent($this->Registry);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown(): void
    {
        parent::tearDown();

        $_SESSION = [];
        unset($this->Controller, $this->OneTimePasswordAuthenticator);
        Configure::write('Users', $this->backupUsersConfig);
        Configure::write('OneTimePasswordAuthenticator.login', false);
    }

    /**
     * Test initialize
     */
    public function testInitialize()
    {
        $this->Controller->OneTimePasswordAuthenticator = new OneTimePasswordAuthenticatorComponent($this->Registry);
        $this->assertInstanceOf(\CakeDC\Auth\Controller\Component\OneTimePasswordAuthenticatorComponent::class, $this->Controller->OneTimePasswordAuthenticator);
    }

    /**
     * test base64 qr-code returned from component
     *
     * @return void
     */
    public function testGetQRCodeImageAsDataUri()
    {
        $this->Controller->OneTimePasswordAuthenticator->initialize([]);
        $result = $this->Controller->OneTimePasswordAuthenticator->getQRCodeImageAsDataUri('test@localhost.com', '123123');

        $this->assertStringContainsString('data:image/png;base64', $result);
    }

    /**
     * Making sure we return secret
     *
     * @return void
     */
    public function testCreateSecret()
    {
        $this->Controller->OneTimePasswordAuthenticator->initialize([]);
        $result = $this->Controller->OneTimePasswordAuthenticator->createSecret();
        $this->assertNotEmpty($result);
    }

    /**
     * Testing code verification in the component
     *
     * @return void
     */
    public function testVerifyCode()
    {
        $this->Controller->OneTimePasswordAuthenticator->initialize([]);
        $secret = $this->Controller->OneTimePasswordAuthenticator->createSecret();
        $verificationCode = $this->Controller->OneTimePasswordAuthenticator->tfa->getCode($secret);

        $verified = $this->Controller->OneTimePasswordAuthenticator->verifyCode($secret, $verificationCode);
        $this->assertTrue($verified);
    }
}
