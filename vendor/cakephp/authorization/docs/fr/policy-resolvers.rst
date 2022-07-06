Résolveurs de Policy
####################

Le résolveur de policy se charge de faire correspondre des classes de policy à
chaque objet ressource. Nous fournissons quelques résolveurs pour vous permettre
de commencer, mais vous pouvez créer votre propre résolveur en implémentant
``Authorization\Policy\ResolverInterface``. Les résolveurs intégrés sont:

* ``MapResolver`` vous permet de faire correspondre des noms de ressources aux
  noms de leurs classes de policy, ou à des objets ou des callables.
* ``OrmResolver`` résout une policy en appliquant des conventions pour les
  objets ORM habituels.
* ``ResolverCollection`` vous permet d'agréger plusieurs résolveurs, et de les
  exécuter les uns à la suite des autres.

Utiliser MapResolver
====================

``MapResolver`` fait correspondre des noms de classes de ressources à des noms
de classes de policy, des objets policy ou des callables::

    use Authorization\Policy\MapResolver;

    $mapResolver = new MapResolver();

    // Mappe une classe de ressource à un nom de classe de policy
    $mapResolver->map(Article::class, ArticlePolicy::class);

    // Mappe une classe de ressource à une instance de policy.
    $mapResolver->map(Article::class, new ArticlePolicy());

    // Mappe une classe de ressource à une fonction callable
    $mapResolver->map(Article::class, function ($resource, $mapResolver) {
        // Renvoie un objet policy.
    });

Utiliser OrmResolver
====================

Le ``OrmResolver`` est un résolveur de policy basé sur des conventions pour
l'ORM de CakePHP. L'OrmResolver applique les conventions suivantes:

#. Les policies se trouvent dans ``App\Policy``
#. Les classes de policy se terminent avec le suffixe de classe ``Policy``.

Le OrmResolver peut résoudre des policies pour les types d'objets suivants:

* Entities - En utilisant le nom de classe de l'entity.
* Tables - En utilisant le nom de classe de la table.
* Queries - En utilisant le résultat de la méthode ``repository()`` de la query
  pour obtenir un nom de classe.

Les règles suivantes s'appliquent dans tous les cas:

#. On utilise le nom de classe de la ressource pour générer le nom de classe
   d'une policy. Par exemple ``App\Model\Entity\Bookmark`` correspondra à
   ``App\Policy\BookmarkPolicy``.
#. Les ressources de plugins rechercheront d'abord une policy d'application, par
   exemple ``App\Policy\Bookmarks\BookmarkPolicy`` pour
   ``Bookmarks\Model\Entity\Bookmark``.
#. Si on ne trouve aucune policy d'application en priorité, on recherche une
   policy du plugin. Par exemple ``Bookmarks\Policy\BookmarkPolicy``.

Pour les objets tables, la transformation du nom de table ferait correspondre
``App\Model\Table\ArticlesTable`` à ``App\Policy\ArticlesTablePolicy``.
Pour les objets query, la méthode ``repository()`` sera appelée, et une policy
sera générée à partir de la classe de table obtenue.

Le OrmResolver peut être personnalisé par son constructeur::

    use Authorization\Policy\OrmResolver;

    // Changer pour utiliser un namespace d'application personnalisé.
    $appNamespace = 'App';

    // Fait correspondre des policies d'un namespace à un autre.
    // Ici nous avons mappé des policies pour les classes du namespace ``Blog``
    // pour qu'elles soient recherchées dans le namespace ``Cms``.
    $overrides = [
        'Blog' => 'Cms',
    ];
    $resolver = new OrmResolver($appNamespace, $overrides)

Utiliser ResolverCollection
===========================

``ResolverCollection`` vous permet d'agréger plusieurs résolveurs::

    use Authorization\Policy\ResolverCollection;
    use Authorization\Policy\MapResolver;
    use Authorization\Policy\OrmResolver;

    $ormResolver = new OrmResolver();
    $mapResolver = new MapResolver();

    // Vérifie le MapResolver, et se rabat sur l'OrmResolver si une ressource
    // n'est pas mappée explicitement.
    $resolver = new ResolverCollection([$mapResolver, $ormResolver]);

Créer un Resolver
=================

Vous pouvez écrire votre propre résolveur en implémentant
``Authorization\Policy\ResolverInterface``, qui nécessite de définir la méthode
``getPolicy($resource)``.
