# Application Checklist Goodyear

## Développement

### Mise en place de l'environnement de développement

- Créez une base de données "checklist"

- Copiez la configuration défaut

```cmd
cp config/.env.example config/.env
```

(Adaptez le fichier `config/.env` à votre configuration locale)

- Installez les dépendances

```cmd
composer install
```

- Lancez le script de migrations de la base de données

```cmd
scripts/migrations.sh
```

### Compilation des assets

Les compilations des assets est géré par Laravel Mix ( installé par le plugin AssetsMix de Cakephp)

Pour installer les différentes dépendances

```cmd
yarn install
```

Ensuite pour compiler le JS et le CSS :

```cmd
yarn dev
```

Pour une compilation automatique à chaque modification.
:

```cmd
yarn dev watch
```

### Configuration Homestead

Dans votre `Homestead.yaml`, ajoutez les lignes suivantes.

```
    - map: checklist.test
      to: /home/vagrant/code/checklist
      type: "apache"
```

### Qualité

#### Tests automatisés

```cmd
composer run test
```

#### Code Sniffer

```cmd
composer run cs-check
```

### Code fixer

```cmd
composer cs-fix
```

### Création des comptes utilisateurs.

```cmd
bin/cake users add_user -e support@iteracode.fr -u support@iteracode.fr -p <Password> -r admin
```


### Génération du fichier I18N

```cmd
bin/cake i18n extract --paths src/Controller, config/variables.php, templates --overwrite --extract-core no --marker-error --merge yes
```



## Procédure de déploiement

Avant le premier déploiement (A lancer sur dokku)
```php
dokku apps:create gy-checklist
dokku mysql:create gy-checklist-mysql
dokku mysql:link gy-checklist-mysql gy-checklist

dokku domains:add gy-checklist gy-checklist.tests.iteracode.com

dokku mysql:backup-auth gy-checklist-mysql "KDBBQJGX7AMKEINUG64C" "d0jYfA4To6vl5dtp/9LJ9jat5nt+rmASQa/uRecmhT0" "" "" "https://ams3.digitaloceanspaces.com"
dokku mysql:backup gy-checklist-mysql db-backup-iteracode
dokku mysql:backup-schedule gy-checklist-mysql "0 3 \* \* \*" db-backup-iteracode
dokku config:set gy-checklist __ADMIN_MAIL__="support@iteracode.fr" MAIL_FROM=bonjour@iteracode.fr MAIL_HOST=smtp-relay.sendinblue.com MAIL_PORT=587 MAIL_USERNAME=bonjour@iteracode.fr MAIL_PASSWORD=sYdVh4GNLM927czT MAIL_TLS=true WEB_CONCURRENCY=8
dokku config:set gy-checklist DEBUG=false
```

Premier déploiement (A lancer en local)
```php
git remote add digital-dev dokku@138.197.191.87:gy-checklist
git push digital-dev dev:main
```

Après le premier déploiement (A lancer sur dokku)
```php
dokku config:set --no-restart gy-checklist DOKKU_LETSENCRYPT_EMAIL=support@iteracode.fr
dokku letsencrypt:enable gy-checklist
dokku letsencrypt:auto-renew

dokku enter gy-checklist web
bin/cake users add_user -e support@iteracode.fr -u support@iteracode.fr -p AdminIteracode -r administrator
bin/cake users activate_user support@iteracode.fr
bin/cake users reset_password support@iteracode.fr AdminIteracode
```

## Création des utilisateurs

```
NEI1211                              Christine LANDANSKI     christine_landanski@goodyear.com
AA10832                             Vincent MERLUZZI          vincent_merluzzi@goodyear.com
AC33448                             Anthony Blondel              anthony_blondel@goodyear.com

bin/cake users add_user -e christine_landanski@goodyear.com
bin/cake users add_user -e vincent_merluzzi@goodyear.com
bin/cake users add_user -e anthony_blondel@goodyear.com
```


### To do list :

- [x] Lien de création de catégorie dans la checklist
- [x] Lien de création de question dans la catégorie
- [x] Associer la table questions aux catégories
- [] Traductions
- [x] Display fields
