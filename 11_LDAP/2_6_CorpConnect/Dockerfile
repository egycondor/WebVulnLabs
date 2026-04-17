FROM php:8.2-fpm-bookworm

RUN apt-get update \
    && apt-get install -y --no-install-recommends nginx libldap2-dev \
    && docker-php-ext-configure ldap \
    && docker-php-ext-install ldap \
    && rm -rf /var/lib/apt/lists/*

COPY docker/nginx.conf /etc/nginx/sites-available/default
COPY docker/entrypoint.sh /entrypoint.sh
COPY public /var/www/html

RUN chown -R www-data:www-data /var/www/html \
    && chmod +x /entrypoint.sh

EXPOSE 80

ENTRYPOINT ["/entrypoint.sh"]
