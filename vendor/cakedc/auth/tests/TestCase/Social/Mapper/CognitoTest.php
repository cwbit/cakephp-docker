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

namespace CakeDC\Auth\Test\TestCase\Social\Mapper;

use Cake\TestSuite\TestCase;
use CakeDC\Auth\Social\Mapper\Cognito;

/**
 * Class CognitoTest
 *
 * @package CakeDC\Auth\Test\TestCase\Social\Mapper
 */
class CognitoTest extends TestCase
{
    public function testMap()
    {
        $token = new \League\OAuth2\Client\Token\AccessToken([
            'access_token' => 'test-token',
            'expires' => 1490988496,
        ]);
        $rawData = [
            'token' => $token,
            'sub' => '10',
            'zoneinfo' => 'Abcd Pong',
            'email' => 'test@gmail.com',
            'website' => 'https://user.Cognito.com/+TestUser',
            'name' => 'John Doe',
            'family_name' => 'Kent',
            'given_name' => 'Clark',
            'profile' => '<span>I am the best test user in the world.</span>',
            'picture' => 'https://lh3.Cognitousercontent.com/photo.jpg',
        ];
        $expected = [
            'id' => '10',
            'username' => null,
            'full_name' => 'John Doe',
            'first_name' => 'Clark',
            'last_name' => 'Kent',
            'email' => 'test@gmail.com',
            'avatar' => 'https://lh3.Cognitousercontent.com/photo.jpg',
            'gender' => null,
            'link' => 'https://user.Cognito.com/+TestUser',
            'bio' => '<span>I am the best test user in the world.</span>',
            'locale' => null,
            'zoneinfo' => 'Abcd Pong',
            'validated' => true,
            'credentials' => [
                'token' => 'test-token',
                'secret' => null,
                'expires' => 1490988496,
            ],
            'raw' => $rawData,
        ];
        $providerMapper = new Cognito();
        $user = $providerMapper($rawData);
        $this->assertEquals($expected, $user);

        //Check _firstName and _lastName methods uses the 'name' field  when needed
        unset($rawData['family_name'], $rawData['given_name']);
        $expected['last_name'] = 'Doe';
        $expected['first_name'] = 'John';
        $expected['raw'] = $rawData;
        $providerMapper = new Cognito();
        $user = $providerMapper($rawData);
        $this->assertEquals($expected, $user);
    }
}
