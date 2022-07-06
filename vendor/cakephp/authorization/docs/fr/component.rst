AuthorizationComponent
######################

Le ``AuthorizationComponent`` propose quelques méthodes de convenance, basées
sur des conventions, pour vérifier les permissions depuis vos controllers. Il
rend transparents l'obtention de l'utilisateur et l'appel aux méthodes ``can``
ou ``applyScope``. Vous devez utiliser le Middleware pour pouvoir utiliser
l'AuthorizationComponent, donc vérifiez qu'il est effectivement en place. Pour
utiliser le composant, commençons par le charger::

    // Dans votre AppController
    public function initialize()
    {
        parent::initialize();
        $this->loadComponent('Authorization.Authorization');
    }

Vérifications Automatiques d'Autorisation
=========================================

``AuthorizationComponent`` peut être configuré pour appliquer automatiquement
des règles d'autorisation selon la classe du modèle par défaut associé au
controller et le nom de l'action en cours. Dans l'exemple suivant, les actions
``index`` et ``add`` seront autorisées::

    $this->Authorization->authorizeModel('index', 'add');

Vous pouvez aussi le configurer de sorte que certaines actions sautent (*skip*)
l'autorisation. Cela rendra ces actions **publiques**, accessibles à tous les
utilisateurs. Par défaut, toutes les actions nécessitent une autorisation et une
``AuthorizationRequiredException`` sera levée si la vérification d'autorisation
est activée.

L'autorisation peut être sautée pour des actions individuelles::

    $this->loadComponent('Authorization.Authorization', [
        'skipAuthorization' => [
            'login',
        ]
    ]);

Vérifier l'Autorisation
=======================

Dans vos actions de controller ou vos méthodes callback, vous pouvez vérifier
l'autorisation en utilisant le composant::

    // Dans le controller Articles.
    public function edit($id)
    {
        $article = $this->Articles->get($id);
        $this->Authorization->authorize($article);
        // Le reste de la méthode edit.
    }

Ci-dessus, nous voyons un article autorisé pour l'utilisateur courant. Puisque
nous n'avons pas spécifié l'action à vérifier, c'est le paramètre ``action`` de
la requête qui sera utilisé. Vous pouvez spécifier une action de policy en
second paramètre::

    // Utilisation d'une méthode de policy autre que l'action en cours dans le controller.
    $this->Authorization->authorize($article, 'update');

La méthode ``authorize()`` soulèvera une
``Authorization\Exception\ForbiddenException`` si la permission est refusée.
Si vous voulez vérifier l'autorisation et obtenir un booléen comme résultat,
utilisez la méthode ``can()``::

    if ($this->Authorization->can($article, 'update')) {
        // Faire quelque chose sur l'article.
    }

Utilisateurs Anonymes
=====================

Certaines ressources de votre application peuvent être accessibles aux
utilisateurs non connectés. Savoir si un utilisateur, connecté ou pas, peut ou
non accéder à une ressource relève du domaine des policies. Avec le composant,
vous pouvez vérifier l'autorisation pour les utilisateurs anonymes. Les deux
méthodes ``can()`` et ``authorize()`` supportent les utilisateurs anonymes. Vos
policies peuvent s'attendre à recevoir ``null`` pour le paramètre 'user' si
l'utilisateur n'est pas connecté.

Appliquer les Périmètres (Scopes) des Policies
==============================================

Vous pouvez aussi appliquer des scopes de policy avec le composant::

$query = $this->Authorization->applyScope($this->Articles->find());

Si l'action courante n'a pas d'utilisateur connecté, cela lèvera une
``MissingIdentityException``.

Si vous voulez mapper des actions vers différentes méthodes d'autorisation,
utilisez l'option ``actionMap``::

   // Dans la méthode initialize() de votre controller:
   $this->Authorization->mapActions([
       'index' => 'list',
       'delete' => 'remove',
       'add' => 'insert',
   ]);

   // ou mapper des actions individuellement.
   $this->Authorization
       ->mapAction('index','list')
       ->mapAction('delete', 'remove')
       ->mapAction('add', 'insert');

Exemple::

    //ArticlesController.php

    public function index()
    {
        $query = $this->Articles->find();

        // cela appliquera le scope `list` puisque l'appel
        // est fait depuis l'action `index` du controller.
        $this->Authorization->applyScope($query); 
        ...
    }

    public function delete($id)
    {
        $article = $this->Articles->get($id);

        // l'autorisation sera accordée selon l'action `remove` de l'entity
        // puisque l'appel est fait depuis l'action `delete` du controller.
        $this->Authorization->authorize($article); 
        ...
    }

    public function add()
    {
        // l'autorisation sera accordée selon l'action `insert` du model
        // puisque l'appel est fait depuis l'action `add` du controller.
        $this->Authorization->authorizeModel(); 
        ...
    }

Sauter l'Autorisation
=====================

Vous pouvez sauter l'autorisation depuis l'intérieur d'une action::

    //ArticlesController.php

    public function view($id)
    {
        $this->Authorization->skipAuthorization();
        ...
    }
