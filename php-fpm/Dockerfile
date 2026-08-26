FROM phpdockerio/php:8.5-fpm
WORKDIR "/var/www/myapp"

# Install selected extensions and other stuff
RUN apt-get update \
    && apt-get -y --no-install-recommends install \
        php8.5-intl \
        php8.5-mysql \
        php8.5-sqlite3 \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/* /usr/share/doc/*
