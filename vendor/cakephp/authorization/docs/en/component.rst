AuthorizationComponent
######################

The ``AuthorizationComponent`` exposes a few conventions based helper methods for
checking permissions from your controllers. It abstracts getting the user and
calling the ``can`` or ``applyScope`` methods. Using the AuthorizationComponent
requires use of the Middleware, so make sure it is applied as well. To use the
component, first load it::

    // In your AppController
    public function initialize()
    {
        parent::initialize();
        $this->loadComponent('Authorization.Authorization');
    }

Automatic authorization checks
==============================

``AuthorizationComponent`` can be configured to automatically apply
authorization based on the controller's default model class and current action
name. In the following example ``index`` and ``add`` actions will be authorized::

    $this->Authorization->authorizeModel('index', 'add');

You can also configure actions to skip authorization. This will make actions **public**,
accessible to all users. By default all actions require authorization and
``AuthorizationRequiredException`` will be thrown if authorization checking is enabled.

Authorization can be skipped for individual actions::

    $this->loadComponent('Authorization.Authorization', [
        'skipAuthorization' => [
            'login',
        ]
    ]);

Checking Authorization
======================

In your controller actions or callback methods you can check authorization using
the component::

    // In the Articles Controller.
    public function edit($id)
    {
        $article = $this->Articles->get($id);
        $this->Authorization->authorize($article);
        // Rest of the edit method.
    }

Above we see an article being authorized for the current user. Since we haven't 
specified the action to check the request's ``action`` is used. You can specify
a policy action with the second parameter::

    // Use a policy method that doesn't match the current controller action.
    $this->Authorization->authorize($article, 'update');

The ``authorize()`` method will raise an ``Authorization\Exception\ForbiddenException``
when permission is denied. If you want to check authorization and get a boolean
result you can use the ``can()`` method::

    if ($this->Authorization->can($article, 'update')) {
        // Do something to the article.
    }

Anonymous Users
===============

Some resources in your application may be accessible to users who are not logged
in. Whether or not a resource can be accessed by an un-authenticated
user is in the domain of policies. Through the component you can check
authorization for anonymous users. Both the ``can()`` and ``authorize()`` support
anonymous users. Your policies can expect to get ``null`` for the 'user' parameter
when the user is not logged in.

Applying Policy Scopes
======================

You can also apply policy scopes using the component::

$query = $this->Authorization->applyScope($this->Articles->find());

If the current action has no logged in user a ``MissingIdentityException`` will
be raised.

If you want to map actions to different authorization methods use the 
``actionMap`` option::

   // In your controller initialize() method:
   $this->Authorization->mapActions([
       'index' => 'list',
       'delete' => 'remove',
       'add' => 'insert',
   ]);

   // or map actions individually.
   $this->Authorization
       ->mapAction('index','list')
       ->mapAction('delete', 'remove')
       ->mapAction('add', 'insert');

Example::

    //ArticlesController.php

    public function index()
    {
        $query = $this->Articles->find();

        //this will apply `list` scope while being called in `index` controller action.
        $this->Authorization->applyScope($query); 
        ...
    }

    public function delete($id)
    {
        $article = $this->Articles->get($id);

        //this will authorize against `remove` entity action while being called in `delete` controller action.
        $this->Authorization->authorize($article); 
        ...
    }

    public function add()
    {
        //this will authorize against `insert` model action while being called in `add` controller action.
        $this->Authorization->authorizeModel(); 
        ...
    }

Skipping Authorization
======================

Authorization can also be skipped inside an action::

    //ArticlesController.php

    public function view($id)
    {
        $this->Authorization->skipAuthorization();
        ...
    }
