FROM php:7.4-apache

ARG BUILD_ARGUMENT_DEBUG_ENABLED=false
ENV DEBUG_ENABLED=${BUILD_ARGUMENT_DEBUG_ENABLED}
ARG BUILD_ARGUMENT_ENV=dev
ENV ENV=${BUILD_ARGUMENT_ENV}
ENV APP_HOME /var/www/html

RUN case ${ENV} in \
      dev) \
        echo 'Building development environment.' \
        ;; \
      beta) \
        echo 'Building beta environment.' \
        ;; \
      prod) \
        echo 'Building production environment.' \
        ;; \
      *) \
        echo 'Set correct `BUILD_ARGUMENT_ENV` in docker build-args like BUILD_ARGUMENT_ENV=dev. Available option are dev,beta,prod.' \
        && exit 2 \
        ;; \
    esac

# install all the dependencies and enable PHP modules
RUN apt-get update && apt-get upgrade -y && apt-get install -y \
      cron \
      git \
      libicu-dev \
      libreadline-dev \
      libxml2 \
      libxml2-dev \
      libzip-dev \
      net-tools \
      procps \
      sudo \
      vim \
      unzip \
      zlib1g-dev \
    && pecl install redis \
    && docker-php-ext-configure pdo_mysql --with-pdo-mysql=mysqlnd \
    && docker-php-ext-configure intl \
    && docker-php-ext-install \
      pdo_mysql \
      sockets \
      intl \
      opcache \
      zip \
    && docker-php-ext-enable redis \
    && docker-php-source delete \
    && rm -rf /tmp/* \
    && rm -rf /var/list/apt/* \
    && rm -rf /var/lib/apt/lists/* \
    && apt-get clean \
    && a2dissite 000-default.conf \
    && usermod -u 1000 www-data && groupmod -g 1000 www-data \
    && chown -R www-data:www-data ${APP_HOME}

COPY ./docker/general/laravel.conf /etc/apache2/sites-available/laravel.conf
COPY ./docker/${BUILD_ARGUMENT_ENV}/php.ini /usr/local/etc/php/php.ini
RUN a2ensite laravel.conf && a2enmod rewrite

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer
RUN chmod +x /usr/bin/composer

WORKDIR ${APP_HOME}

RUN mkdir -p /var/www/.composer && chown -R www-data:www-data /var/www/.composer

USER www-data

COPY --chown=www-data:www-data . ${APP_HOME}/
COPY --chown=www-data:www-data .env.$ENV ${APP_HOME}/.env

USER root
