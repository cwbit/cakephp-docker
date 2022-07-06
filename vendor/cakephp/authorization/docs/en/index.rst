Quick Start
###########

Installation
============

Install the plugin with `composer <https://getcomposer.org/>`__ from your CakePHP
Project's ROOT directory (where the **composer.json** file is located)

.. code-block:: shell

    php composer.phar require "cakephp/authorization:^2.0"
    
Version 2 of the Authorization Plugin is compatible with CakePHP 4.

Load the plugin by adding the following statement in your project's
``src/Application.php``::

    $this->addPlugin('Authorization');

Getting Started
===============

The Authorization plugin integrates into your application as a middleware layer
and optionally a component to make checking authorization easier. First, lets
apply the middleware. In **src/Application.php** add the following to the class
imports::

    use Authorization\AuthorizationService;
    use Authorization\AuthorizationServiceInterface;
    use Authorization\AuthorizationServiceProviderInterface;
    use Authorization\Middleware\AuthorizationMiddleware;
    use Authorization\Policy\OrmResolver;
    use Psr\Http\Message\ResponseInterface;
    use Psr\Http\Message\ServerRequestInterface;

Add the ``AuthorizationProviderInterface`` to the implemented interfaces on your application::

    class Application extends BaseApplication implements AuthorizationServiceProviderInterface

Then make your application's ``middleware()`` method look like::

    public function middleware(MiddlewareQueue $middlewareQueue): MiddlewareQueue
    {
        // Middleware provided by CakePHP
        $middlewareQueue->add(new ErrorHandlerMiddleware(Configure::read('Error')))
            ->add(new AssetMiddleware())
            ->add(new RoutingMiddleware($this))
            ->add(new BodyParserMiddleware())

            // If you are using Authentication it should be *before* Authorization.
            ->add(new AuthenticationMiddleware($this));

            // Add the AuthorizationMiddleware *after* routing, body parser
            // and authentication middleware.
            ->add(new AuthorizationMiddleware($this));

        return $middlewareQueue();
    }

The placement of the ``AuthorizationMiddleware`` is important and must be added
*after* your authentication middleware. This ensures that the request has an
``identity`` which can be used for authorization checks.

The ``AuthorizationMiddleware`` will call a hook method on your application when
it starts handling the request. This hook method allows your application to
define the ``AuthorizationService`` it wants to use. Add the following method your
**src/Application.php**::

    public function getAuthorizationService(ServerRequestInterface $request): AuthorizationServiceInterface
    {
        $resolver = new OrmResolver();

        return new AuthorizationService($resolver);
    }

This configures basic :doc:`/policy-resolvers` that will match
ORM entities with their policy classes.

Next, lets add the ``AuthorizationComponent`` to ``AppController``. In
**src/Controller/AppController.php** add the following to the ``initialize()``
method::

    $this->loadComponent('Authorization.Authorization');

By loading the :doc:`/component` we'll be able to check
authorization on a per-action basis more easily. For example, we can do::

    public function edit($id = null)
    {
        $article = $this->Article->get($id);
        $this->Authorization->authorize($article, 'update');

        // Rest of action
    }

By calling ``authorize`` we can use our :doc:`/policies` to enforce our
application's access control rules. You can check permissions anywhere by using
the :doc:`identity stored in the request <checking-authorization>`.


Further Reading
===============

* :doc:`/policies`
* :doc:`/policy-resolvers`
* :doc:`/middleware`
* :doc:`/component`
* :doc:`/checking-authorization`
* :doc:`/request-authorization-middleware`
