Guide de Migration vers 2.0
###########################

Authorization 2.0 contient de nouvelles fonctionnalités et quelques changements
entraînant une rupture de compatibilité.

Ruptures de Compatibilité
=========================

Le typehinting a été ajouté dans ``IdentityInterface``. Si vous avez implémenté
``IdentityInterface``, il faudra que vous mettiez à jour l'implémentation de
votre application pour qu'elle reflète les nouveaux typehints.

En plus des typehints, une nouvelle méthode ``canResult()`` a été ajoutée à
``IdentityInterface``. Cette méthode renvoie toujours un objet
``ResultInterface`` tandis que ``can()`` renvoie toujours un booléen. Dans 1.x,
la méthode ``can()`` renvoyait un booléen ou un ``ResultInterface`` selon ce que
renvoyait la policy. Cela rendait très difficile de savoir ce que renvoyait
``can()``. Les nouvelles méthodes et le typehint supplémentaire rendent
``IdentityInterface`` plus simple et plus pratique à utiliser.
