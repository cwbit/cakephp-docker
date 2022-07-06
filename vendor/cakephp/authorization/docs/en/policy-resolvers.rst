Policy Resolvers
################

Mapping resource objects to their respective policy classes is a behavior
handled by a policy resolver. We provide a few resolvers to get you started, but
you can create your own resolver by implementing the
``Authorization\Policy\ResolverInterface``. The built-in resolvers are:

* ``MapResolver`` allows you to map resource names to their policy class names, or
  to objects and callables.
* ``OrmResolver`` applies conventions based policy resolution for common ORM
  objects.
* ``ResolverCollection`` allows you to aggregate multiple resolvers together,
  searching them sequentially.

Using MapResolver
=================

``MapResolver`` lets you map resource class names to policy classnames, policy
objects, or factory callables::

    use Authorization\Policy\MapResolver;

    $mapResolver = new MapResolver();

    // Map a resource class to a policy classname
    $mapResolver->map(Article::class, ArticlePolicy::class);

    // Map a resource class to a policy instance.
    $mapResolver->map(Article::class, new ArticlePolicy());

    // Map a resource class to a factory function
    $mapResolver->map(Article::class, function ($resource, $mapResolver) {
        // Return a policy object.
    });

Using OrmResolver
=================

The ``OrmResolver`` is a conventions based policy resolver for CakePHP's ORM. The
OrmResolver applies the following conventions:

#. Policies live in ``App\Policy``
#. Policy classes end with the ``Policy`` class suffix.

The OrmResolver can resolve policies for the following object types:

* Entities - Using the entity classname.
* Tables - Using the table classname.
* Queries - Using the return of the query's ``repository()`` to get a classname.

In all cases the following rules are applied:

#. The resource classname is used to generate a policy class name. e.g
   ``App\Model\Entity\Bookmark`` will map to ``App\Policy\BookmarkPolicy``
#. Plugin resources will first check for an application policy e.g
   ``App\Policy\Bookmarks\BookmarkPolicy`` for ``Bookmarks\Model\Entity\Bookmark``.
#. If no application override policy can be found, a plugin policy will be
   checked. e.g. ``Bookmarks\Policy\BookmarkPolicy``

For table objects the class name tranformation would result in
``App\Model\Table\ArticlesTable`` mapping to ``App\Policy\ArticlesTablePolicy``.
Query objects will have their ``repository()`` method called, and a policy will be
generated based on the resulting table class.

The OrmResolver supports customization through its constructor::

    use Authorization\Policy\OrmResolver;

    // Change when using a custom application namespace.
    $appNamespace = 'App';

    // Map policies in one namespace to another.
    // Here we have mapped policies for classes in the ``Blog`` namespace to be 
    // found in the ``Cms`` namespace.
    $overrides = [
        'Blog' => 'Cms',
    ];
    $resolver = new OrmResolver($appNamespace, $overrides)

Using ResolverCollection
========================

``ResolverCollection`` allows you to aggregate multiple resolvers together::

    use Authorization\Policy\ResolverCollection;
    use Authorization\Policy\MapResolver;
    use Authorization\Policy\OrmResolver;

    $ormResolver = new OrmResolver();
    $mapResolver = new MapResolver();

    // Check the map resolver, and fallback to the orm resolver if
    // a resource is not explicitly mapped.
    $resolver = new ResolverCollection([$mapResolver, $ormResolver]);

Creating a Resolver
===================

You can implement your own resolver by implementing the
``Authorization\Policy\ResolverInterface`` which requires defining the
``getPolicy($resource)`` method.
