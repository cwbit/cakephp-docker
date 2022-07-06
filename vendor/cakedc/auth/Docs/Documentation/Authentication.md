Authentication
==============
The new authentication plugin [cakephp/authentication](https://github.com/cakephp/authentication/)
offers an improved way to handle authentication, we have created some additional features to enable:
 - social auth;
 - reCaptcha in form;
 - cookie login with session;
 - two-factor  (one-time password) authentication

Custom Authenticators
---------------------
- **CookieAuthenticator**, preserve cookie auth token at session, needed for login that perform redirect
like in social authentication.

- **FormAuthenticator**, allows to use reCaptcha verification in your login form. Configurations:
    
    - keyCheckEnabledRecaptcha, config key used to check if reCaptcha is enabled. Default, 'Users.reCaptcha.login'
    - baseClassName, optional fullname class for base form authenticator

- **SocialAuthenticaor**, allows to authenticate a user with social provider (facebook, google, twitter, etc).
You need to use the middleware `CakeDC\Auth\Middleware\SocialAuthMiddleware` and identifier
`CakeDC\Auth\Identifier\SocialIdentifier` to make this authenticator work correctly.
Check how enabled social authentication at [users plugin.](https://github.com/CakeDC/Users)

- **TwoFactorAuthenticator**, used to complete the two-factor authentication. Configurations:

    - loginUrl: The login URL, string or array of URLs. Default is null and all pages will be checked.
    - urlChecker: The URL checker class or object. Default is DefaultUrlChecker.


Authentication Service
----------------------
The custom authentication service allows you to use two-factor authentication and get a list of 
failure processed (see `\CakeDC\Auth\Authentication\AuthenticationService::getFailures`).
When the authenticator get a valid result the service will proceed to two-factor verification,
to ignore for a specific authenticator use the config key 'skipTwoFactorVerify'.
Sample usage:

```php
<?php
namespace App;

use Authentication\AuthenticationServiceProviderInterface;
use Authentication\Middleware\AuthenticationMiddleware;
use CakeDC\Auth\Authentication\AuthenticationService;
use Cake\Http\BaseApplication;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class Application extends BaseApplication implements AuthenticationServiceProviderInterface
{

    /**
     * Returns a service provider instance.
     *
     * @param \Psr\Http\Message\ServerRequestInterface $request Request
     * @param \Psr\Http\Message\ResponseInterface $response Response
     * @return \Authentication\AuthenticationServiceInterface
     */
    public function getAuthenticationService(ServerRequestInterface $request, ResponseInterface $response)
    {
        $service = new AuthenticationService();

        $fields = [
            'username' => 'email',
            'password' => 'password'
        ];

        // Load identifiers
        $service->loadIdentifier('Authentication.Password', compact('fields'));

        // Load the authenticators, you want session first
        $service->loadAuthenticator('Authentication.Session', [
            'skipTwoFactorVerify' => true
        ]);
        $service->loadAuthenticator('Authentication.Form', [
            'fields' => $fields,
            'loginUrl' => '/users/login'
        ]);

        return $service;
    }

    /**
     * Define the HTTP middleware layers for an application.
     *
     * @param \Cake\Http\MiddlewareQueue $middlewareQueue The middleware queue to set in your App Class
     * @return \Cake\Http\MiddlewareQueue
     */
    public function middleware($middlewareQueue)
    {
        // Various other middlewares for error handling, routing etc. added here.

        // Add the authentication middleware
        $authentication = new AuthenticationMiddleware($this);

        // Add the middleware to the middleware queue
        $middlewareQueue->add($authentication);

        return $middlewareQueue;
    }
}
```

