Checking Authorization
######################

Once you have applied the :doc:`/middleware` to your application and added an
``identity`` to the request, you can start checking authorization. The
middleware will wrap the ``identity`` in each request with an
``IdentityDecorator`` that adds authorization related methods.

You can pass the ``identity`` into your models, services or templates allowing
you to check authorization anywhere in your application easily. See the
:ref:`identity-decorator` section for how to customize or replace the default
decorator.

Checking Authorization for a Single Resource
============================================

The ``can`` method enables you to check authorization on a single resource.
Typically this is an ORM entity, or application domain object. Your
:doc:`/policies` provide logic to make the authorization decision::

    // Get the identity from the request
    $user = $this->request->getAttribute('identity');

    // Check authorization on $article
    if ($user->can('delete', $article)) {
        // Do delete operation
    }

If your policies return :ref:`policy-result-objects`
be sure to check their status as ``canResult()`` returns the result instance::

   // Assuming our policy returns a result.
   $result = $user->canResult('delete', $article);
   if ($result->getStatus()) {
       // Do deletion
   }

Applying Scope Conditions
=========================

When you need to apply authorization checks to a collection of objects like
a paginated query you will often want to only fetch records that the current
user has access to. This plugin implements this concept as 'scopes'. Scope
policies allow you to 'scope' a query or result set and return the updated list
or query object::

    // Get the identity from the request
    $user = $this->request->getAttribute('identity');

    // Apply permission conditions to a query so only
    // the records the current user has access to are returned.
    $query = $user->applyScope('index', $query);

The :doc:`/component` can be used in controller actions
to streamline authorization checks that raise exceptions on failure.
