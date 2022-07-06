Vérifier une Autorisation
#########################

Une fois que vous avez appliqué le :doc:`/middleware` à votre application et
ajouté une ``identity`` à votre requête, vous pouvez commencer à vérifier les
autorisations. Le middleware enveloppe l'\ ``identity`` dans chaque requête par
un ``IdentityDecorator`` qui ajoute les méthodes liées à l'autorisation.

Vous pouvez passer l'\ ``identity`` à vos modèles, services ou templates, ce qui
vous permet de vérifier facilement l'autorisation depuis n'importe quel endroit
de votre application. Pour savoir comment personnaliser ou remplacer le
décorateur par défaut, consultez la section :ref:`identity-decorator`.

Vérifier l'Autorisation pour Une Seule Ressource
================================================

La méthode ``can`` vous permet de vérifier l'autorisation sur une seule
ressource. Typiquement, ce sera une entity de l'ORM, ou un objet du domaine de
l'application. Vos :doc:`/policies` fournissent la logique pour prendre la
décision d'autorisation::

    // Obtenir l'identity à partir de la requête
    $user = $this->request->getAttribute('identity');

    // Vérifier l'autorisation sur $article
    if ($user->can('delete', $article)) {
        // Faire l'opération delete
    }

Si vos policies renvoient des :ref:`policy-result-objects`, pensez à vérifier
leur statut avec ``canResult()`` qui renvoie l'instance Result::

   // Assuming our policy returns a result.
   $result = $user->canResult('delete', $article);
   if ($result->getStatus()) {
       // Procéder à l'effacement
   }

Appliquer des Conditions de Portée (Scope)
==========================================

Quand vous avez besoin de vérifier l'autorisation sur une collection d'objets,
telle qu'une requête (*query*) paginée, vous serez souvent amené à ne vouloir
récupérer que les enregistrements auxquel l'utilisateur courant a accès. Le
plugin implémente ce concept sous le nom de 'scopes'. Les policies de scope vous
permettent de limiter (*scope*) une query ou un result set et de renvoyer la
liste modifiée ou l'objet query::

    // Obtenir l'identity à partir de la requête HTML
    $user = $this->request->getAttribute('identity');

    // Appliquer les conditions de permissions à la query de façon à ne
    // retourner que les enregistrements auxquels l'utilisateur actuel a accès.
    $query = $user->applyScope('index', $query);

Dans les actions du controller, vous pouvez utiliser le :doc:`/component` pour
traiter sur-le-champ les vérifications d'autorisation susceptibles de lever une
exception en cas d'échec.
