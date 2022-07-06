2.0 Migration Guide
###################

Authorization 2.0 contains new features and a few breaking changes.

Breaking Changes
================

The ``IdentityInterface`` has had typehinting added. If you have implemented the
``IdentityInterface`` you will need to update your application's implementation
to reflect the new typehints.

In addition to typehints ``IdentityInterface`` has a ``canResult()`` method
added. This method always returns a ``ResultInterface`` object while ``can()``
always returns a boolean. In 1.x the ``can()`` method would return a boolean or
``ResultInterface`` depending on what the policy returned. This made knowing the
return value of ``can()`` very hard. The new methods and additional typings
make ``IdentityInterface`` simpler and more reliable to use.
