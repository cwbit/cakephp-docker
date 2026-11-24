FROM phpdockerio/php:8.5-fpm
WORKDIR "/var/www/myapp"

# Install selected extensions and other stuff
RUN apt-get update \
    && apt-get -y --no-install-recommends upgrade \
    && apt-get -y --no-install-recommends install \
        php8.5-intl \
        php8.5-mysql \
        php8.5-sqlite3 \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/* /usr/share/doc/*

# Fail the build if PHP is older than 8.5.13 (November 2026 security release)
RUN php -r 'exit(version_compare(PHP_VERSION, "8.5.13", "<") ? 1 : 0);'
