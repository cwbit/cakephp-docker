Authorization Middleware
########################

Authorization is applied to your application as a middleware. The
``AuthorizationMiddleware`` handles the following responsibilities:

* Decorating the request 'identity' with a decorator that adds the ``can``,
  ``canResult``, and ``applyScope`` methods if necessary.
* Ensuring that authorization has been checked/bypassed in the request.

To use the middleware implement ``AuthorizationServiceProviderInterface`` in your
application class. Then pass your app instance into the middleware and add the
middleware to the queue.

A basic example would be::

    namespace App;

    use Authorization\AuthorizationService;
    use Authorization\AuthorizationServiceProviderInterface;
    use Authorization\Middleware\AuthorizationMiddleware;
    use Authorization\Policy\OrmResolver;
    use Cake\Http\BaseApplication;

    class Application extends BaseApplication implements AuthorizationServiceProviderInterface
    {
        public function getAuthorizationService(ServerRequestInterface $request, ResponseInterface $response)
        {
            $resolver = new OrmResolver();

            return new AuthorizationService($resolver);
        }

        public function middleware($middlewareQueue)
        {
            // other middleware
            $middlewareQueue->add(new AuthorizationMiddleware($this));

            return $middlewareQueue;
        }
    }

The authorization service requires a policy resolver. See the
:doc:`/policies` documentation on what resolvers are available and how
to use them.

.. _identity-decorator:

Identity Decorator
==================

By default the ``identity`` in the request will be decorated (wrapped) with
``Authorization\IdentityDecorator``. The decorator class proxies method calls,
array access and property access to the decorated identity object. To access the
underlying identity directly use ``getOriginalData()``::

    $originalUser = $user->getOriginalData();

If your application uses the `cakephp/authentication
<https://github.com/cakephp/authentication>`_ plugin then the
``Authorization\Identity`` class will be used. This class implements the
``Authentication\IdentityInterface`` in addition to the
``Authorization\IdentityInterface``. This allows you to use the
``Authentication`` lib's component and helper to get the decorated identity.

Using your User class as the Identity
-------------------------------------

If you have an existing ``User`` or identity class you can skip the decorator by
implementing the ``Authorization\IdentityInterface`` and using the
``identityDecorator`` middleware option. First lets update our ``User`` class::

    namespace App\Model\Entity;

    use Authorization\AuthorizationServiceInterface;
    use Authorization\IdentityInterface;
    use Authorization\Policy\ResultInterface;
    use Cake\ORM\Entity;

    class User extends Entity implements IdentityInterface
    {
        /**
         * Authorization\IdentityInterface method
         */
        public function can($action, $resource): bool
        {
            return $this->authorization->can($this, $action, $resource);
        }

        /**
         * Authorization\IdentityInterface method
         */
        public function canResult($action, $resource): ResultInterface
        {
            return $this->authorization->canResult($this, $action, $resource);
        }

        /**
         * Authorization\IdentityInterface method
         */
        public function applyScope($action, $resource)
        {
            return $this->authorization->applyScope($this, $action, $resource);
        }

        /**
         * Authorization\IdentityInterface method
         */
        public function getOriginalData()
        {
            return $this;
        }

        /**
         * Setter to be used by the middleware.
         */
        public function setAuthorization(AuthorizationServiceInterface $service)
        {
            $this->authorization = $service;

            return $this;
        }

        // Other methods
    }

Now that our user implements the necessary interface, lets update our middleware
setup::

    // In your Application::middleware() method;

    // Authorization
    $middlewareQueue->add(new AuthorizationMiddleware($this, [
        'identityDecorator' => function ($auth, $user) {
            return $user->setAuthorization($auth);
        }
    ]));

You no longer have to change any existing typehints, and can start using
authorization policies anywhere you have access to your user.

If you also use the Authentication plugin make sure to implement both interfaces.::

    use Authorization\IdentityInterface as AuthorizationIdentity;
    use Authentication\IdentityInterface as AuthenticationIdentity;

    class User extends Entity implements AuthorizationIdentity, AuthenticationIdentity
    {
        ...
        
        /**
         * Authentication\IdentityInterface method
         *
         * @return string
         */
        public function getIdentifier()
        {
            return $this->id;
        }
        
        ...
    }

Ensuring Authorization is Applied
---------------------------------

By default the ``AuthorizationMiddleware`` will ensure that each request
containing an ``identity`` also has authorization checked/bypassed. If
authorization is not checked an ``AuthorizationRequiredException`` will be raised.
This exception is raised **after** your other middleware/controller actions are
complete, so you cannot rely on it to prevent unauthorized access, however it is
a helpful aid during development/testing. You can disable this behavior via an
option::

    $middlewareQueue->add(new AuthorizationMiddleware($this, [
        'requireAuthorizationCheck' => false
    ]));

Handling Unauthorized Requests
------------------------------

By default authorization exceptions thrown by the application are rethrown by the middleware.
You can configure handlers for unauthorized requests and perform custom action, e.g.
redirect the user to the login page.

The built-in handlers are:

* ``Exception`` - this handler will rethrow the exception, this is a default
  behavior of the middleware.
* ``Redirect`` - this handler will redirect the request to the provided URL.
* ``CakeRedirect`` - redirect handler with support for CakePHP Router.

Both redirect handlers share the same configuration options:

* ``url`` - URL to redirect to (``CakeRedirect`` supports CakePHP Router syntax).
* ``exceptions`` - a list of exception classes that should be redirected. By
  default only ``MissingIdentityException`` is redirected.
* ``queryParam`` - the accessed request URL will be attached to the redirect URL
  query parameter (``redirect`` by default).
* ``statusCode`` - HTTP status code of a redirect, ``302`` by default.

For example::

    use Authorization\Exception\MissingIdentityException;
    
    $middlewareQueue->add(new AuthorizationMiddleware($this, [
        'unauthorizedHandler' => [
            'className' => 'Authorization.Redirect',
            'url' => '/pages/unauthorized',
            'queryParam' => 'redirectUrl',
            'exceptions' => [
                MissingIdentityException::class,
                OtherException::class,
            ],
        ],
    ]));
    
All handlers get the thrown exception object given as a parameter.
This exception will always be an instance of ``Authorization\Exception\Exception``.
In this example the ``Authorization.Redirect`` handler just gives you the option to 
specify which exceptions you want to listen to.

So in this example where we use the ``Authorization.Redirect`` handler we can
add other ``Authorization\Exception\Exception`` based exceptions to the 
``execeptions`` array if we want to handle them gracefully::

    'exceptions' => [
        MissingIdentityException::class,
        ForbiddenException::class
    ],

See the `RedirectHandler source <https://github.com/cakephp/authorization/blob/2.next/src/Middleware/UnauthorizedHandler/RedirectHandler.php>`__

Configuration options are passed to the handler's ``handle()`` method as the
last parameter.

Add a flash message after being redirected by an unauthorized request
----------------------------------------------------------------------

Currently there is no straightforward way to add a flash message to the unauthorized redirect.

Therefore you need to create your own Handler which adds th flash message (or any 
other logic you want to happen on redirect)

How to create a custom UnauthorizedHandler
-------------------------------------------

#. Create this file ``src/Middleware/UnauthorizedHandler/CustomRedirectHandler.php``::

    <?php
    declare( strict_types = 1 );

    namespace App\Middleware\UnauthorizedHandler;

    use Authorization\Exception\Exception;
    use Authorization\Middleware\UnauthorizedHandler\RedirectHandler;
    use Psr\Http\Message\ResponseInterface;
    use Psr\Http\Message\ServerRequestInterface;

    class CustomRedirectHandler extends RedirectHandler {
        public function handle( Exception $exception, ServerRequestInterface $request, array $options = [] ): ResponseInterface {
            $response = parent::handle( $exception, $request, $options );
            $request->getFlash()->error( 'You are not authorized to access that location' );
            return $response;
        }
    }

#. Tell the AuthorizationMiddleware that it should use your new custom Handler::

    // in your src/Application.php
    
    use Authorization\Exception\MissingIdentityException;
    use Authorization\Exception\ForbiddenException;
    
    $middlewareQueue->add(new AuthorizationMiddleware($this, [
        'unauthorizedHandler' => [
            'className' => 'CustomRedirect', // <--- see here
            'url' => '/users/login',
            'queryParam' => 'redirectUrl',
            'exceptions' => [
                MissingIdentityException::class,
                ForbiddenException::class
            ],
            'custom_param' => true,
        ],
    ]));
    
As you can see you still have the same config parameters as if we are using ``Authorization.Redirect`` as a className.

This is, because we extend our handler based on the RedirectHandler present in the plugin. Therefore all that functionality is present + our own funtionality in the ``handle()`` function.
    
The ``custom_param`` appears in the ``$options`` array given to you in the ``handle()`` function inside your ``CustomRedirectHandler`` if you wish to add some more config parameters to your functionality.

You can look at `CakeRedirectHandler <https://github.com/cakephp/authorization/blob/2.next/src/Middleware/UnauthorizedHandler/CakeRedirectHandler.php>`__ or `RedirectHandler <https://github.com/cakephp/authorization/blob/2.next/src/Middleware/UnauthorizedHandler/RedirectHandler.php>`__ 
how such a Handler can/should look like.
