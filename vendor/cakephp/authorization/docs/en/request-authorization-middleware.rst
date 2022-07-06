Request Authorization Middleware
################################

This middleware is useful when you want to authorize your requests, for example
each controller and action, against a role based access system or any other kind
of authorization process that controls access to certain actions.

This **must** be added after the Authorization, Authentication and
RoutingMiddleware in the Middleware Queue!

The logic of handling the request authorization will be implemented in the
request policy. You can add all your logic there or just pass the information
from the request into an ACL or RBAC implementation.

Using it
========

Create a policy for handling the request object. The plugin ships with an
interface we can implement. Start by creating **src/Policy/RequestPolicy.php**
and add::

    namespace App\Policy;

    use Authorization\Policy\RequestPolicyInterface;
    use Cake\Http\ServerRequest;

    class RequestPolicy implements RequestPolicyInterface
    {
        /**
         * Method to check if the request can be accessed
         *
         * @param \Authorization\IdentityInterface|null $identity Identity
         * @param \Cake\Http\ServerRequest $request Server Request
         * @return bool
         */
        public function canAccess($identity, ServerRequest $request)
        {
            if ($request->getParam('controller') === 'Articles'
                && $request->getParam('action') === 'index'
            ) {
                return true;
            }

            return false;
        }
    }

Next, map the request class to the policy inside
``Application::getAuthorizationService()``, in **src/Application.php** ::

    use App\Policy\RequestPolicy;
    use Authorization\AuthorizationService;
    use Authorization\AuthorizationServiceInterface;
    use Authorization\AuthorizationServiceProviderInterface;
    use Authorization\Middleware\AuthorizationMiddleware;
    use Authorization\Middleware\RequestAuthorizationMiddleware;
    use Authorization\Policy\MapResolver;
    use Authorization\Policy\OrmResolver;
    use Psr\Http\Message\ResponseInterface;
    use Cake\Http\ServerRequest;


    public function getAuthorizationService(ServerRequestInterface $request): AuthorizationServiceInterface {
        $mapResolver = new MapResolver();
        $mapResolver->map(ServerRequest::class, RequestPolicy::class);
        return new AuthorizationService($mapResolver);
    }

Ensure you're loading the RequestAuthorizationMiddleware **after** the
AuthorizationMiddleware::

    public function middleware(MiddlewareQueue $middlewareQueue): MiddlewareQueue {
        // other middleware...
        // $middlewareQueue->add(new AuthenticationMiddleware($this));

        // Add authorization (after authentication if you are using that plugin too).
        $middlewareQueue->add(new AuthorizationMiddleware($this));
        $middlewareQueue->add(new RequestAuthorizationMiddleware());
    }
