Policy
######

Policy son clases que resuelven los permisos para un objeto determinado. Puede
crear una policy para cualquier clase de su aplicación a la que desee aplicar
comprobaciones de permisos.

Creando una Policy
==================

Puede crear una policy en su directorio ``src/Policy``. Las clases policy
no tienen una clase o interfaz base común que se espera implementar.
Las clases de la aplicación se 'resuelven' en una clase policy coincidente.
Consulte la sección :doc:`policy-resolvers` para saber cómo se pueden resolver las policy.

Por lo general, querrá poner sus policy en **src/Policy** y usar el sufijo de clase
``Policy``. Por ahora crearemos una clase policy para la entidad `Article` en nuestra 
aplicación. En **src/Policy/ArticlePolicy.php** ponga el siguiente contenido::

    <?php
    namespace App\Policy;

    use App\Model\Entity\Article;
    use Authorization\IdentityInterface;

    class ArticlePolicy
    {
    }

Además de las entidades, los objetos table y query pueden tener una policy.
Los objetos query tendrán su método llamado ``repository()``, y se generará una clase
policy basada en el nombre de la 'table'. Una clase 'table' de ``App\Model\Table\ArticlesTable``
se mapeará a ``App\Policy\ArticlesTablePolicy``.

Puede generar clases policy vacías para objetos ORM usando ``bake``:

.. code-block:: bash

    # Create an entity policy
    bin/cake bake policy --type entity Article

    # Create a table policy
    bin/cake bake policy --type table Articles

Escribiendo Métodos Policy
==========================

La clase policy que acabamos de crear no hace mucho en este momento. Definamos un método que
nos permita comprobar si un usuario puede actualizar un artículo::

    public function canUpdate(IdentityInterface $user, Article $article)
    {
        return $user->id == $article->user_id;
    }

Los métodos policy deben devolver objetos ``true`` o un objeto ``Result`` para indicar el éxito ('pass').
Todos los demás valores se interpretarán como un fallo ('fail').

Los métodos policy recibirán un ``null`` en el parámetro ``$user`` al manejar usuarios
no autenticados. Si desea que los métodos policy automáticamente den 'fail' para usuarios
anónimos, puede usar la sugerencia de tipo ``IdentityInterface``.

.. _policy-result-objects:

Objetos Policy Result
=====================

Además de los valores booleanos, los métodos policy pueden devolver un objeto ``Result``.
Los objetos ``Result`` permiten que se proporcione más contexto sobre por qué el método
policy dió 'pass'/'fail'::

   use Authorization\Policy\Result;

   public function canUpdate(IdentityInterface $user, Article $article)
   {
       if ($user->id == $article->user_id) {
           return new Result(true);
       }
       // Results let you define a 'reason' for the failure.
       return new Result(false, 'not-owner');
   }

Cualquier valor devuelto que no sea ``true`` o un objeto ``ResultInterface``
se considerará un 'fail'.

Alcances Policy
---------------

Además de que las policy pueden definir verificaciones de autorización 'pass'/'fail',
también pueden definir 'alcances'. Los métodos de alcance le permiten modificar otro
objeto aplicando condiciones de autorización. Un caso de uso perfecto para esto es
restringir una list view al usuario actual::

    namespace App\Policy;

    class ArticlesTablePolicy
    {
        public function scopeIndex($user, $query)
        {
            return $query->where(['Articles.user_id' => $user->getIdentifier()]);
        }
    }

Condiciones Previas de la Policy
--------------------------------

En algunas policy, es posible que desee aplicar comprobaciones comunes en todas las
operaciones de una policy. Esto es útil cuando necesita denegar todas las acciones al
recurso proporcionado. Para utilizar las condiciones previas, debe implementar ``BeforePolicyInterface``
en su policy::

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

De los hooks 'before' se espera que devuelvan uno de tres valores:

- ``true`` El usuario puede proceder con la acción.
- ``false`` El usuario no puede proceder con la acción.
- ``null`` El hook 'before' no tomó una decisión y se invocará
  el método de autorización.
