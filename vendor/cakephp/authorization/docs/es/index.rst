Inicio Rápido
#############

Instalación
===========

Instale el plugin con `composer <https://getcomposer.org/>`__ desde el directorio
ROOT de su proyecto CakePHP (donde se encuentra el archivo **composer.json**)

.. code-block:: shell

    php composer.phar require "cakephp/authorization:^2.0"

Cargue el complemento agregando la siguiente declaración en el archivo 
``src/Application.php`` de su proyecto::

    $this->addPlugin('Authorization');

Empezando
=========

El plugin Authorization se integra en su aplicación como una capa middleware y, opcionalmente,
un componente para facilitar la verificación de la autorización. Primero, apliquemos el middleware.
En **src/Application.php** agregue lo siguiente a las importaciones de la clase::

    use Authorization\AuthorizationService;
    use Authorization\AuthorizationServiceInterface;
    use Authorization\AuthorizationServiceProviderInterface;
    use Authorization\Middleware\AuthorizationMiddleware;
    use Authorization\Policy\OrmResolver;
    use Psr\Http\Message\ResponseInterface;
    use Psr\Http\Message\ServerRequestInterface;

Agregue la ``AuthorizationProviderInterface`` a las interfaces implementadas en su aplicación::

    class Application extends BaseApplication implements AuthorizationServiceProviderInterface

Luego agregue lo siguiente a su método ``middleware()``::

    // Add authorization (after authentication if you are using that plugin too).
    $middleware->add(new AuthorizationMiddleware($this));

``AuthorizationMiddleware`` llamará a un método hook en su aplicación cuando comience
a manejar la request. Este método hook permite a su aplicación definir el ``AuthorizationService``
que quiere usar. Agregue el siguiente método a su **src/Application.php**::

    public function getAuthorizationService(ServerRequestInterface $request): AuthorizationServiceInterface
    {
        $resolver = new OrmResolver();

        return new AuthorizationService($resolver);
    }

Esto configura :doc:`/policy-resolvers` que hará coincidir las entidades ORM 
con sus clases policy.

A continuación, agregue el ``AuthorizationComponent`` a ``AppController``. En 
**src/Controller/AppController.php** agregue lo siguiente al método ``initialize()``::

    $this->loadComponent('Authorization.Authorization');

Al cargar :doc:`/component`, podremos verificar la autorización
por acción más fácilmente. Por ejemplo, podemos hacer::

    public function edit($id = null)
    {
        $article = $this->Article->get($id);
        $this->Authorization->authorize($article, 'update');

        // Rest of action
    }

Al llamar ``authorize`` podemos usar nuestro :doc:`/policies` para hacer cumplir
las reglas de control de acceso de nuestra aplicación. Puede verificar los permisos
en cualquier lugar utilizando :doc:`identity stored in the request <checking-authorization>`.


Otras lecturas
==============

* :doc:`/policies`

.. * :doc:`/policy-resolvers`
.. * :doc:`/middleware`
.. * :doc:`/component`
.. * :doc:`/checking-authorization`
.. * :doc:`/request-authorization-middleware`
