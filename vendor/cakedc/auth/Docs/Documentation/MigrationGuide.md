Migration Guide
=============

1.x to 2.x
----------

* Change namespace of Rules to `CakeDC\Auth\Rbac\Rules`, for example `CakeDC\Auth\Rbac\Rules\Owner`

3.x to 4.x
----------

In this version we're using the [cakephp/authentication](https://github.com/cakephp/authentication/) and
[cakephp/authorization](https://github.com/cakephp/authorization/).

Please check [Authentication](Authentication.md) and [Authorization](Authorization.md) sections.

* Added [Authentication](Authentication.md) features with social, two-factor, form and cookie authenticators
* Added [Authorization](Authorization.md) features
* Added social identifier
* Added social middleware
* Added one-time password middleware
* Added rbac policy
* Added superuser policy
* Added [social namespace](Social.md) compatible with OAuth1 and OAuth2 with many providers
    - Amazon
    - Facebook
    - Google
    - Instagram
    - Linkedin
    - Pinterest
    - Tumblr
    - Twitter

* Removed ApiKeyAuthenticate in favor of cakephp/authentication [TokenAuthenticator](https://github.com/cakephp/authentication/blob/master/docs/Authenticators.md#token) 
* Removed RememberMeAuthenticate.php in favor of [CookieAuthenticator](../../src/Authenticator/CookieAuthenticator.php)
* Removed SimpleRbacAuthorize.php if favor of RbacPolicy with [Request Authorization Middleware](https://github.com/cakephp/authorization/blob/master/src/Middleware/RequestAuthorizationMiddleware.php)
* Removed SuperuserAuthorize.php in favor of [SuperuserPolicy](../../src/Policy/SuperuserPolicy.php)


4.x to 5.x
----------
* Required cakephp 4
* Renamed \CakeDC\Auth\Test\BaseTraitTest to \CakeDC\Auth\Test\BaseTestTrait