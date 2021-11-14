# cakephp-docker
A Docker Compose setup for containerized CakePHP Applications

This setup spools up the following containers

* **mysql** (8.0)
* **nginx**
* **php-fpm** (php 7.4)
* **mailhog** (smtp server for testing)

The guide will walk you thru the following things

* [Quick Start](#quick-start)
* [Installation](#installation)
* [How to use `bin/cake`, `mysql` and other commandline utils now that you're using containers](#now-how-to-run-bincake-and-mysql)
* [OK, so what did the defaults set up?](#ok-so-what-did-the-defaults-set-up)
* [Installing Docker on my Host](#installing-docker-on-my-host)
* [Troubleshooting](#troubleshooting)
  * [nginx open logs/access.log failed no such file or directory](#nginx-open-logsaccesslog-failed-no-such-file-or-directory)
  * [creating a CakePHP app](#creating-a-CakePHP-app)

## Quick Start

For those looking to get started in `60 sec` using just the defaults (which are fine for dev) do the following:

1. Download the ZIP file for this repo
1. Create the following folder structure
 * Put your CakePHP app inside the `cakephp` folder (assumes you already have one; if not, look here)
 * and the files from this repo into the `docker` folder

	```
	    somefolder
	        docker
	            .. put the zip files in here ..
	        cakephp
	            .. put your cake app in here ..
	```

	If you want to do that all from commandline...

	```bash
    cd ~/path/to/your/local/dev/folder
    mkdir myapp
    mkdir myapp/cakephp
	```

	And then to simultaneously download the latest master file, unpack it, stuff it into a folder named docker, and clone the `.env` file ... run this...

	```bash
    cd myapp
    curl -Lo cakephp-docker.zip https://github.com/cwbit/cakephp-docker/archive/master.zip && \
    unzip cakephp-docker.zip && \
    mv cakephp-docker-master docker && \
    cp docker/.env.sample docker/.env
    rm cakephp-docker.zip
	```
3. Move your cakephp app into the `cakephp` folder or if you're creating one from scratch skip to the next step
4. From commandline, `cd` into the `docker` directory and run `docker-compose up`

	```bash
	$ cd /path/to/somefolder/docker
	$ docker-compose up

	Starting myapp-mysql
	Starting myapp-mailhog
	Starting myapp-php-fpm
	Starting myapp-nginx
	Attaching to myapp-mailhog, myapp-mysql, myapp-php-fpm, myapp-nginx
	myapp-mailhog    | 2017/06/15 16:34:26 Using in-memory storage
	...
	myapp-mysql      | 2017-06-15T16:34:27.401334Z 0 [Note] mysqld (mysqld 5.7.17) starting as process 1 ...
	...
	myapp-mysql      | 2017-06-15T16:34:27.408857Z 0 [Warning] Setting lower_case_table_names=2 because file system for /var/lib/mysql/ is case insensitive
	...
	myapp-mysql      | 2017-06-15T16:34:28.332626Z 0 [Note] mysqld: ready for connections.
	myapp-mysql      | Version: '5.7.17'  socket: '/var/run/mysqld/mysqld.sock'  port: 3306  MySQL Community Server (GPL)
	myapp-mailhog    | [APIv1] KEEPALIVE /api/v1/events
	... you'll probably see more crap spit out here ...
	```
5. If you're creating a new cakephp app, follow the steps in [creating a CakePHP app](#creating-a-CakePHP-app)
6. That's it! Go to `localhost:8180` and your app will be live.

All these defaults can be completely overridden. Start with the [Installation](#installation) section to get a feel for what's going on, and then tweak the defaults to suit your individual project needs.



## Installation

Clone this repo (or just download the zip) and put it in a `docker` folder inside your root app folder

Here is an example of what my typical setup looks like

```
	myapp-folder
		cakephp
			src
			config
			..
		docker
			.env
			.env.sample
			docker-compose.yml
			mysql
				my.cnf
			nginx
				nginx.conf
			php-fpm
				Dockerfile
				php-ini-overrides.ini
```

Next, **Update the Environment File**

Copy or Rename `docker/.env.sample` to `docker/.env`.
This is an environment file that your Docker Compose setup will look for automatically which gives us a great, simple way to store things like your mysql database credentials outside of the repo.

By default the file will contain the following

```
MYSQL_ROOT_PASSWORD=root
MYSQL_DATABASE=myapp
MYSQL_USER=myapp
MYSQL_PASSWORD=myapp
```

Docker Compose will automatically replace things like `${MYSQL_USER}` in the `docker-compose.yml` file with whatever corresponding variables it finds defined in `.env`

Lastly, **Find/Replace** `myapp` with the name of your app.

> **WHY?** by default the files are set to name the containers based on your app prefix. By default this is `myapp`.
> A find/replace on `myapp` is safe and will allow you to customize the names of the containers
>
> e.g. myapp-mysql, myapp-php-fpm, myapp-nginx, myapp-mailhog

**Build and Run your Containers**

```bash
cd /path/to/your/app/docker
docker-compose up
```

That's it. You can now access your CakePHP app at

`localhost:8180`

> **tip**: start docker-compose with `-d` to run (or re-run changed containers) in the background.
>
> `docker-compose up -d`

**Connecting to your database**

Also by default the first time you run the app it will create a `MySQL` database with the credentials you specified in your `.env` file (see above)

``` yaml
host : myapp-mysql
username : myapp
password : myapp
database : myapp
```

You can access your MySQL database (with your favorite GUI app) on

`localhost:8106`

Your `cakephp/config/app_local.php` file should be set to the following (it connects through the docker link)

```php
  'Datasources' => [
    'default' => [
      'host' => 'myapp-mysql',
      'port' => '3306',
      'username' => 'myapp',
      'password' => 'myapp',
      'database' => 'myapp',
    ],
```

To change these defaults edit the variables in the `docker/.env` file or tweak the `docker-compose.yml` file under `myapp-mysql`'s `environment` section.

## Now, how to run `bin/cake` and `mysql`

Now that you're running stuff in containers you need to access the code a little differently

You can run things like `composer` on your host, but if you want to run `bin/cake` or use MySQL from commandline you just need to connect into the appropriate container first

**access your php server**

```bash
docker exec -it myapp-php-fpm /bin/bash
```
> remember to replace `myapp` with whatever you really named the container

**access mysql cli**

```bash
docker exec -it myapp-mysql /usr/bin/mysql -u root -p myapp
```
> remember to replace `myapp` with whatever you really named the container and with your actual database name and user login


## OK, so what did the defaults set up?

There are 4 containers that I use all the time that will be spooled up automatically

### `myapp-nginx` - the web server

First we're creating an nginx server. The configuration is set based on the CakePHP suggestions for nginx and `myapp-nginx` will handle all the incoming requests from the client and forward them to the `myapp-php-fpm` server which is what actually runs your PHP code.

You can configure the **nginx server** by editing the `/nginx/nginx.conf` file

### `myapp-php-fpm` - the PHP processor

This container runs `php` (and it's extensions) needed for your CakePHP app

It automatically includes the following extensions

* `php7.4-intl` (required for CakePHP 4.0+)
* `php7.4-mbstring`
* `php7.4-sqlite3` (required for DebugKit)
* `php7.4-mysql`

It also includes some php ini overrides (see `php-fpm\php-ini-overrides.ini`)

This container will (by default) look for your web app code in `../cakephp/` (relative to the `docker-compose` file).

You can configure what **PHP extensions** are loaded by editing `/php-fpm/Dockerfile`

You can configure **PHP overrides** by editing `/php-fpm/php-ini-overrides.ini`

### `myapp-mysql` - the database server

The first time you run the docker containers it will create a folder in your root structure called `mysql` (at the same level as your `docker` folder) and this is where it will store all your database data.

Since the data is stored on your host device you can bring the mysql container up and down or completely destroy and rebuild it without ever actually touching your data - it is "safely" stored on the host.

You can configure **MySQL overrides** by editing `/mysql/my.cnf`

### `myapp-mailhog` - the smtp server

This is just a built-in mail server you can use to 'send' and intercept mail coming from your application.

Set up your `cakephp/config/app_local.php` with the following

```php
    'EmailTransport' => [
        ...
	    'mailhog' => [
	        # These are default settings for the MailHog container - make sure it's running first
	        'className' => 'Smtp',
	        'host' => 'myapp-mailhog',
	        'port' => 1025,
	        'timeout' => 30,
	      ],
	      ...
```          

You can access the **Web GUI** (using the defaults) for mailhog at

`localhost:8125`

To send mail over the transport layer just set your `Email::transport('mailhog')`


## Installing Docker on my Host

If you've never worked with Docker before they have some super easy ways to install the needed runtimes on almost any host

* Mac, Windows, Ubuntu, Centos, Debian, Fedora, Azure, AWS

You can download the (free) community edition here [https://www.docker.com/community-edition#/download]()

**Cloud Hosting Docker Applications**

[DigitalOcean](https://m.do.co/c/640e75c994b4) has been super reliable for us as a host and has a one-click deploy of a  docker host.

Just click `CREATE DROPLET` and then under `Choose an Image` pick the `One-click Apps` (tab) and choose `Docker X.Y.Z on X.Y` and you're good to go; DO will spool up a droplet with `docker` and `docker-compose` already installed and ready to run.

## Troubleshooting

### nginx open logs/access.log failed no such file or directory

submitted by @jeroenvdv

`myapp-nginx | nginx: [emerg] open() "/var/www/myapp/logs/access.log" failed (2: No such file or directory)`

This is caused by not installing CakePHP completely and can be fixed by creating the logs folder in your `myapp/cakephp` folder.

If you are starting fresh and need to install cake using the container you just created then follow the next step, [Creating a CakePHP app](#creating-a-CakePHP-app).

### creating a CakePHP app

Most of the time I set this up without an existing CakePHP app. This will cause the initial `up` command to fail because folders are missing. To solve this run the install command (from the official CakePHP docs) but set the app name to `.` instead of `myapp`.

```bash
docker exec -it myapp-php-fpm /bin/bash
```
> remember to replace `myapp` with whatever you really named the container

and then, inside the container
```bash
composer create-project --prefer-dist cakephp/app:~4.0 .
```
Next, fix the database connection strings by following the steps in [Connecting to your database](#Connecting-to-your-database) (above).

That's it. You should have lots of happy green checkmarks at `localhost:8180` or whatever you set nginx to respond to.
