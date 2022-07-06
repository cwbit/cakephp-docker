Policies
########

Policies are classes that resolve permissions for a given object. You can create
policies for any class in your application that you wish to apply permissions
checks to.

Creating Policies
=================

You can create policies in your ``src/Policy`` directory. Policy classes don't
have a common base class or interface they are expected to implement.
Application classes are then 'resolved' to a matching policy class. See the
:doc:`policy-resolvers` section for how policies can be resolved.

Generally you'll want to put your policies in **src/Policy** and use the
``Policy`` class suffix. For now we'll create a policy class for the `Article`
entity in our application.  In **src/Policy/ArticlePolicy.php** put the
following content::

    <?php
    namespace App\Policy;

    use App\Model\Entity\Article;
    use Authorization\IdentityInterface;

    class ArticlePolicy
    {
    }

In addition to entities, table objects and queries can have policies resolved.
Query objects will have their ``repository()`` method called, and a policy class
will be generated based on the table name. A table class of
``App\Model\Table\ArticlesTable`` will map to ``App\Policy\ArticlesTablePolicy``.

You can generate empty policy classes for ORM objects using ``bake``:

.. code-block:: bash

    # Create an entity policy
    bin/cake bake policy --type entity Article

    # Create a table policy
    bin/cake bake policy --type table Articles

Writing Policy Methods
======================

The policy class we just created doesn't do much right now. Lets define a method
that allows us to check if a user can update an article::

    public function canUpdate(IdentityInterface $user, Article $article)
    {
        return $user->id == $article->user_id;
    }

Policy methods must return ``true`` or a ``Result`` objects to indicate success.
All other values will be interpreted as failure.

Policy methods will receive ``null`` for the ``$user`` parameter when handling
unauthencticated users. If you want to automatically fail policy methods for
anonymous users you can use the ``IdentityInterface`` typehint.

.. _policy-result-objects:

Policy Result Objects
=====================

In addition to booleans, policy methods can return a ``Result`` object.
``Result`` objects allow more context to be provided on why the policy
passed/failed::

   use Authorization\Policy\Result;

   public function canUpdate(IdentityInterface $user, Article $article)
   {
       if ($user->id == $article->user_id) {
           return new Result(true);
       }
       // Results let you define a 'reason' for the failure.
       return new Result(false, 'not-owner');
   }

Any return value that is not ``true`` or a ``ResultInterface`` object will be
considered a failure.

Policy Scopes
-------------

In addition to policies being able to define pass/fail authorization checks,
they can also define 'scopes'. Scope methods allow you to modify another object
applying authorization conditions. A perfect use case for this is restricting
a list view to the current user::

    namespace App\Policy;

    class ArticlesTablePolicy
    {
        public function scopeIndex($user, $query)
        {
            return $query->where(['Articles.user_id' => $user->getIdentifier()]);
        }
    }

Policy Pre-conditions
---------------------

In some policies you may wish to apply common checks across all operations in
a policy. This is useful when you need to deny all actions to the provided
resource. To use pre-conditions you need to implement the ``BeforePolicyInterface``
in your policy::

    namespace App\Policy;

    use Authorization\Policy\BeforePolicyInterface;

    class ArticlesPolicy implements BeforePolicyInterface
    {
        public function before($user, $resource, $action)
        {
            if ($user->getOriginalData()->is_admin) {
                return true;
            }
            // fall through
        }
    }

Before hooks are expected to return one of three values:

- ``true`` The user is allowed to proceed with the action.
- ``false`` The user is not allowed to proceed with the action.
- ``null`` The before hook did not make a decision, and the authorization method
  will be invoked.
