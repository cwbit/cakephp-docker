Authorization
=============
The new authorization plugin [cakephp/authorization](https://github.com/cakephp/authorization/)
offers an improved way to handle authorization, we have created some additional polices to use
with [Request Authorization Middleware](https://github.com/cakephp/authorization/blob/master/src/Middleware/RequestAuthorizationMiddleware.php):

 - CollectionPolicy, check permission with a list of policies, stop at first success
 - RbacPolicy, check permissions using the `CakeDC\Auth\Rbac\Rbac`
 - SuperuserPolicy, check if user is 'superuser' and allow all;

RbacPolicy
----------
The resource must be and request object, normally an instance of `Cake\Http\ServerRequest`. This
policy uses the `CakeDC\Auth\Rbac\Rbac`, it will create a new instance for rbac if the request
'rbac' attribute is `null`.

You can set a custom config for Rbac with array data:

```
new RbacPolicy([
    'adapter' => [
        'autoload_config' => 'permissions',
        'role_field' => 'role',
        'default_role' => 'user',
        'permissions' => [],
        'log' => false
    ]
]);
```

You could also set an instance of Rbac directly:
```
new RbacPolicy([
    'adapter' => new Rbac()
]);
```

SuperuserPolicy
---------------
This policy allow all for a superuser. Configurations:
 - superuser_field, superuser field in the users entity, default, is_superuser

If the current user 'superuser_field' is true, he'll get full permissions in your app.

Using the policies
------------------

At your Application::middleware add the authorization and request authorization middlewares:
 
```
    $middlewareQueue->add(new AuthorizationMiddleware($this, Configure::read('Auth.AuthorizationMiddleware')));
    $middlewareQueue->add(new RequestAuthorizationMiddleware());
```

As usual create or update the Application::getAuthorizationService method:
   
````
    public function getAuthorizationService(ServerRequestInterface $request, ResponseInterface $response)
    {
        $map = new MapResolver();
            $map->map(
                ServerRequest::class,
                new CollectionPolicy([
                    SuperuserPolicy::class,//First check super user policy
                    RbacPolicy::class, Only check with rbac if user is not super user
                ])
            );
    
            $orm = new OrmResolver();
    
            $resolver = new ResolverCollection([
                $map,
                $orm
            ]);
    
            return new AuthorizationService($resolver);
    }     
```
