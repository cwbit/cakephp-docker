Policies
########

Les stratégies (*policies*) sont des classes qui résolvent les permissions pour
un objet donné. Vous pouvez créer des policies pour n'importe quelle classe de
votre application à laquelle vous souhaitez appliquer des vérifications de
permissions.

Créer des Policies
==================

Vous pouvez créer des policies dans votre répertoire ``src/Policy``. Les classes
de policy n'ont pas de classe de base ni d'interface commune qu'elles devraient
implémenter. Les classes de l'application sont 'résolues' avec une classe de
policy qui leur correspond. Pour savoir comment les policies peuvent être
résolues, consultez la section :doc:`policy-resolvers`.

La plupart du temps, vous placerez vos policies dans **src/Policy** et
utiliserez le suffixe de classe ``Policy``. Pour l'instant nous allons créer une
classe de policy pour l'entité `Article` de notre application. Collez le
contenu suivant dans **src/Policy/ArticlePolicy.php**::

    <?php
    namespace App\Policy;

    use App\Model\Entity\Article;
    use Authorization\IdentityInterface;

    class ArticlePolicy
    {
    }

En plus des entities, les objets de table et les queries peuvent avoir des
policies. Sur les objets query, la méthode ``repository()`` sera appelée, et une
classe de policy sera générée à partir du nom de la table. Une classe de table
telle que ``App\Model\Table\ArticlesTable`` correspondra à
``App\Policy\ArticlesTablePolicy``.

Vous pouvez générer des classes de policy vides pour vos objets de l'ORM en
utilisant ``bake``:

.. code-block:: bash

    # Crée une policy d'entity
    bin/cake bake policy --type entity Article

    # Crée une policy de table
    bin/cake bake policy --type table Articles

Écrire des Méthodes de Policy
=============================

La classe de policy que nous venons de créer ne fait pas grand chose pour le
moment. Définissons une méthode qui nous permettra de vérifier si un utilisateur
a le droit de mettre à jour un article::

    public function canUpdate(IdentityInterface $user, Article $article)
    {
        return $user->id == $article->user_id;
    }

Les méthodes de policy doivent renvoyer ``true`` ou un objet ``Result`` pour
indiquer qu'elles ont réussi. Toutes les autres valeurs s'interprètent comme des
échecs.

Les méthodes de policy recevront ``null`` pour le paramètre ``$user`` lorsqu'il
s'agit d'utilisateurs non authentifiés. Si vous voulez que les méthodes de
policy échouent automatiquement pour les utilisateurs anonymes, vous pouvez
utiliser les typehints de ``IdentityInterface``.

.. _policy-result-objects:

Objets Result d'une Policy
==========================

À part les booléens, les méthodes de policy peuvent renvoyer un objet
``Result``. Les objets ``Result`` permettent de donner plus de contexte sur les
raisons pour lesquelles la policy est passée ou a échoué::

   use Authorization\Policy\Result;

   public function canUpdate(IdentityInterface $user, Article $article)
   {
       if ($user->id == $article->user_id) {
           return new Result(true);
       }
       // Les Results vous autorisent à définir la 'raison' de l'échec.
       return new Result(false, 'non-propriétaire');
   }

Toute valeur renvoyée qui n'est ni ``true`` ni un objet ``ResultInterface`` sera
considérée comme un échec.

Portées de Policy (Scope)
-------------------------

En plus de définir les validations ou échecs d'autorisation, les policies
peuvent définir des 'scopes'. Avec les méthodes *scope*, vous pouvez modifier un
autre objet sous certaines conditions d'accès. La vue d'une liste restreinte à
l'utilisateur courant en est un parfait exemple::

    namespace App\Policy;

    class ArticlesTablePolicy
    {
        public function scopeIndex($user, $query)
        {
            return $query->where(['Articles.user_id' => $user->getIdentifier()]);
        }
    }

Pré-conditions de Policy
------------------------

Dans certaines policies, vous voudrez peut-être appliquer des conditions à
toutes les opérations de la policy. Cela peut être utile pour interdire toutes
les actions sur la ressource demandée. Pour utiliser les pré-conditions, votre
policy doit implémenter ``BeforePolicyInterface``::

    namespace App\Policy;

    use Authorization\Policy\BeforePolicyInterface;

    class ArticlesPolicy implements BeforePolicyInterface
    {
        public function before($user, $resource, $action)
        {
            if ($user->getOriginalData()->is_admin) {
                return true;
            }
            // continuer
        }
    }

Les méthodes *before* sont censées renvoyer une de ces trois valeurs:

- ``true`` L'utilisateur est autorisé à effectuer l'action.
- ``false`` L'utilisateur n'est pas autorisé à effectuer l'action.
- ``null`` La méthode *before* n'a pas pris de décision, et la méthode
  d'autorisation doit être appelée.
