# cakephp-docker
A Docker Compose setup for containerized CakePHP Applications

This setup spools up the following containers

* **mysql** (5.7)
* **nginx**
* **php-fpm** (php 7.1)
* **mailhog** (smtp server for testing)

The guide will walk you thru the following things

* [Installation](#installation)
* [Now, how to use `bin/cake` or `mysql`](#now-how-to-run-bincake-and-mysql)
* [OK, so what did the defaults set up?](#ok-so-what-did-the-defaults-set-up)
* [Installing Docker on my Host](#installing-docker-on-my-host)

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
			docker-compose.yml
			nginx
				nginx.conf
			php-fpm
				Dockerfile
				php-ini-overrides.ini
```

Then, **Find/Replace** `myapp` with the name of your app.

> **WHY?** by default the files are set to name the containers based on your app prefix. By default this is `myapp`.
> A find/replace on `myapp` is safe and will allow you to customize the names of the containers
> 
> e.g. myapp-mysql, myapp-php-fpm, myapp-nginx, myapp-mailhog

**Build and Run your Containers**

```bash
cd /path/to/your/app/docker
docker-compose -up
```

That's it. You can now access your CakePHP app at 

`localhost:8180`

**Connecting to your database**

Also by default the first time you run the app it will create a `MySQL` database with the following credentials

``` yaml
host : myapp-mysql
username : myapp
password : myapp
database : myapp
```
You can access your MySQL database (with your favorite GUI app) on 

`localhost:8106`

Your `app/config.php` file should be set to the following (it connects through the docker link)

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

To change these defaults edit the `docker-compose.yml` file under `myapp-mysql`'s `environment` section.

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


### `myapp-php-fpm` - the PHP processor

This container runs `php` (and it's extensions) needed for your CakePHP app

It automatically includes the following extensions

* `php7.1-intl` (required for CakePHP 3.x +)
* `php7.1-mbstring`
* `php7.1-sqlite3` (required for DebugKit)
* `php7.1-mysql`

It also includes some php ini overrides (see `php-fpm\php-ini-overrides.ini`)

This container will (by default) look for your web app code in `../cakephp/` (relative to the `docker-compose` file).

### `myapp-mysql` - the database server

The first time you run the docker containers it will create a folder in your root structure called `mysql` and this is where it will store all your database data.

Since the data is stored on your host device you can bring the mysql container up and down or completely destroy and rebuild it without ever actually touching your data - it is "safely" stored on the host

### `myapp-mailhog` - the smtp server

This is just a built-in mail server you can use to 'send' and intercept mail coming from your application.

Set up your `app/config.php` with the following

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
