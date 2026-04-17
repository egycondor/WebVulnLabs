FROM php:8.2-fpm-bookworm

# OWASP CRS 4.x release tag (https://github.com/coreruleset/coreruleset/releases)
ARG CRS_VERSION=4.4.0

RUN apt-get update \
    && apt-get install -y --no-install-recommends \
        nginx \
        libnginx-mod-http-modsecurity \
        libmodsecurity3 \
        libldap2-dev \
        ca-certificates \
        curl \
    && docker-php-ext-configure ldap \
    && docker-php-ext-install ldap \
    && rm -rf /var/lib/apt/lists/*

RUN mkdir -p /etc/nginx/modsecurity /etc/modsecurity /var/www/errors /var/log/nginx \
    && curl -fsSL "https://github.com/coreruleset/coreruleset/archive/refs/tags/v${CRS_VERSION}.tar.gz" \
        | tar xz -C /tmp \
    && mv "/tmp/coreruleset-${CRS_VERSION}" /etc/modsecurity/crs \
    && cp /etc/modsecurity/crs/crs-setup.conf.example /etc/modsecurity/crs/crs-setup.conf \
    && curl -fsSL "https://raw.githubusercontent.com/owasp-modsecurity/ModSecurity/v3/master/modsecurity.conf-recommended" \
        -o /etc/nginx/modsecurity/modsecurity.conf-recommended \
    && cp /etc/modsecurity/crs/unicode.mapping /etc/nginx/modsecurity/unicode.mapping

COPY docker/modsecurity/main.conf /etc/nginx/modsecurity/main.conf
COPY docker/nginx.conf /etc/nginx/sites-available/default
COPY docker/entrypoint.sh /entrypoint.sh
COPY docker/errors/blocked.html /var/www/errors/blocked.html
COPY public /var/www/html

RUN chown -R www-data:www-data /var/www/html \
    && chmod +x /entrypoint.sh

EXPOSE 80

ENTRYPOINT ["/entrypoint.sh"]
