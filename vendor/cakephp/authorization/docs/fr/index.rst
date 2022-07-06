Prise en main rapide
####################

Installation
============

Installez le plugin avec `composer <https://getcomposer.org/>`__ depuis le
répertoire racine de votre project CakePHP (là où se trouve le fichier
**composer.json**).

.. code-block:: shell

    php composer.phar require "cakephp/authorization:^2.0"

Chargez le plugin en ajoutant la ligne suivante dans le fichier
``src/Application.php`` de votre projet::

    $this->addPlugin('Authorization');

Pour commencer
==============

Le plugin Authorization s'intègre dans votre application en tant que middleware,
et sur option comme composant (*component*) pour faciliter la vérification des
autorisations. Commençons par mettre en place le middleware. Dans
**src/Application.php**, ajoutez les imports de classes suivants::

    use Authorization\AuthorizationService;
    use Authorization\AuthorizationServiceInterface;
    use Authorization\AuthorizationServiceProviderInterface;
    use Authorization\Middleware\AuthorizationMiddleware;
    use Authorization\Policy\OrmResolver;
    use Psr\Http\Message\ResponseInterface;
    use Psr\Http\Message\ServerRequestInterface;

Ajoutez ``AuthorizationProviderInterface`` aux interfaces implémentées par votre
classe Application::

    class Application extends BaseApplication implements AuthorizationServiceProviderInterface

Puis ajoutez ceci à votre méthode ``middleware()``::

    // Ajoute authorization (après authentication, si vous utilisez aussi ce plugin).
    $middleware->add(new AuthorizationMiddleware($this));

Le ``AuthorizationMiddleware`` appellera une méthode crochet (*hook*) de votre
application au démarrage du traitement de la requête. Cette méthode permet à
votre application de définir le ``AuthorizationService`` qu'elle veut utiliser.
Ajoutez la méthode suivante à votre **src/Application.php**::

    public function getAuthorizationService(ServerRequestInterface $request): AuthorizationServiceInterface
    {
        $resolver = new OrmResolver();

        return new AuthorizationService($resolver);
    }

Cela configure les :doc:`/policy-resolvers` basiques qui confronteront les
entities de l'ORM avec leurs classes de policies (politiques d'autorisations).

Ensuite, ajoutons le ``AuthorizationComponent`` à ``AppController``. Dans
**src/Controller/AppController.php**, ajoutez ceci à la méthode
``initialize()``::

    $this->loadComponent('Authorization.Authorization');

En chargeant le :doc:`/component` nous pourrons plus facilement vérifier les
autorisations action par action. Par exemple, nous pouvons faire ceci::

    public function edit($id = null)
    {
        $article = $this->Article->get($id);
        $this->Authorization->authorize($article, 'update');

        // Le reste de l'action
    }

En appelant ``authorize``, nous pouvons utiliser nos :doc:`/policies` pour
renforcer les règles de contrôle d'accès à notre application. Vous pouvez
vérifier les permissions depuis n'importe quel endroit en utilisant
:doc:`l'identity stockée dans la requête <checking-authorization>`.


Pour Aller Plus Loin
====================

* :doc:`/policies`
* :doc:`/policy-resolvers`
* :doc:`/middleware`
* :doc:`/component`
* :doc:`/checking-authorization`
* :doc:`/request-authorization-middleware`
