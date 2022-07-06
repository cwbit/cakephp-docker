SimpleRbacAuthorize
=============

Setup
---------------

SimpleRbacAuthorize is configured by default, but you can customize the way it works easily

Example, in your AppController, you can configure the way the SimpleRbac works by setting the options:

```php
$config['Auth']['authorize']['CakeDC/Users.SimpleRbac'] = [
        // autoload permissions.php
        'autoload_config' => 'permissions',
        // role field in the Users table
        'role_field' => 'role',
        // default role, used in new users registered and also as role matcher when no role is available
        'default_role' => 'user',
        /*
         * This is a quick roles-permissions implementation
         * Rules are evaluated top-down, first matching rule will apply
         * Each line define
         *      [
         *          'role' => 'admin',
         *          'plugin', (optional, default = null)
         *          'prefix', (optional, default = null)
         *          'extension', (optional, default = null)
         *          'controller',
         *          'action',
         *          'allowed' (optional, default = true)
         *      ]
         * You could use '*' to match anything
         * Suggestion: put your rules into a specific config file
         */
        'permissions' => [], // you could set an array of permissions or load them using a file 'autoload_config'
        // log will default to the 'debug' value, matched rbac rules will be logged in debug.log by default when debug enabled
        'log' => false
    ];
```

This is the default configuration, based on it the Authorize object will first check your ```config/permissions.php```
file and load the permissions using the configuration key ```Users.SimpleRbac.permissions```, there is an
example file you can copy into your ```config/permissions.php``` under the Plugin's config directory.

If you don't want to use a file for configuring the permissions, you just need to tweak the configuration and set
```'autoload_config' => false,``` then define all your rules in AppController (not a good practice as the rules
tend to grow over time).

The Users Plugin will use the field ```role``` field in the Users Table to match the role of the user and
check if there is a rule allowing him to access the url.

The ```default_role``` will be used to set the role of the registered users by default.

Check [Rbac](Rbac.md) for permissions and rules configuration and examples.
