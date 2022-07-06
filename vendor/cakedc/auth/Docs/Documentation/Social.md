Social Layer
============
The social layer provide a easier way to handle social provider authentication
with provides using OAuth1 or OAuth2. The idea is to provide a base 
interface for both OAuth and OAuth2.

***Make sure to load the bootstap.php file of this plugin!***

We have mappers to allow you a quick start with these providers:

- Amazon
- Facebook
- Google
- Instagram
- LinkedIn
- Pinterest
- Tumblr 
- Twitter

You must define 'options.redirectUri', 'options.clientId' and
'options.clientSecret' for any provider you want to enable. eg,
for facebook you could add these at your bootstrap.php:

```php
\Cake\Configure\Configure::write('OAuth.providers.facebook.options.redirectUri', $redirectUrl);
\Cake\Configure\Configure::write('OAuth.providers.facebook.options.clientId', 'myFacebookAppClientId');
\Cake\Configure\Configure::write('OAuth.providers.facebook.options.clientSecret', 'myFacebookAppClientSecret');
```

***Make sure to load the bootstap.php file of this plugin, cause we need
the base 'OAuth' config array!***

Basic usage without middleware
------------------------------

In any controller add an action to authenticate
```
...

use CakeDC\Auth\Social\MapUser;
use CakeDC\Auth\Social\Service\ServiceFactory;
...

    /**
     *  Init link and auth process against provider
     *
     * @param string $alias of the provider.
     *
     * @throws \Cake\Http\Exception\NotFoundException Quando o provider informado não existe
     * @return  \Cake\Http\Response Redirects on successful
     */
    public function social($alias = null)
    {
        return $this->redirect(
            (new ServiceFactory())
                ->createFromProvider($alias)
                ->getAuthorizationUrl($this->request)
        );
    }
    
    /**
     * Callback to get user information from provider
     *
     * @param string $alias of the provider.
     *
     * @throws \Cake\Http\Exception\NotFoundException Quando o provider informado não existe
     * @return  \Cake\Http\Response Redirects to profile if okay or error
     */
    public function callbackSocial($alias = null)
    {
        try {
            $server = (new ServiceFactory())
                ->setRedirectUriField('callbackLinkSocialUri')
                ->createFromProvider($alias);

            if (!$server->isGetUserStep($this->request)) {
                $this->Flash->error($message);

                return $this->redirect(['action' => 'profile']);
            }
            $data = $server->getUser($this->request);
            $data = (new MapUser())($server, $data);
           
            //your code
        } catch (\Exception $e) {
            $this->log($log);
        }
    }
```
Working with cakephp/authentication
-----------------------------------
If you're using the new cakephp/authentication we recommend you to use
the SocialAuthenticator and SocialMiddleware provided in this plugin. For more
details of how to handle social authentication with cakephp/authentication, please check
how we implemented at CakeDC/Users plugins.