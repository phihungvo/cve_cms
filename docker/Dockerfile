FROM php:8.3

RUN apt-get update && apt-get upgrade -y

# Install required packages
RUN apt install -yqq --no-install-recommends \
    cron default-mysql-client git \
    libbz2-dev libicu-dev libldb-dev libonig-dev \
    libxslt-dev libzip-dev procps psmisc unzip vim \
    autoconf build-essential

RUN rm -rf /var/lib/apt/lists/*
RUN apt clean && apt autoclean && apt autoremove --assume-yes

# Try installing Redis via PECL with specific version
RUN pecl channel-update pecl.php.net && \
    (pecl install redis-6.1.0 || pecl install redis-6.0.2) && \
    docker-php-ext-enable redis

# Install PHP extensions
RUN docker-php-ext-install bcmath bz2 intl mbstring opcache pdo_mysql pcntl sockets xsl zip

COPY ./docker/php.ini /usr/local/etc/php/
COPY ./docker/crontab /etc/cron.d/crontab
RUN chmod 0600 /etc/cron.d/crontab

WORKDIR /app
COPY composer.json /app/

# Use composer image for dependencies
COPY --from=composer:2 /usr/bin/composer /usr/local/bin/composer
RUN COMPOSER_ALLOW_SUPERUSER=1 composer update --no-dev --optimize-autoloader --classmap-authoritative --no-scripts

COPY ./ /app

RUN echo ":set noshowmode" >> $HOME/.vimrc
RUN git config --global --add safe.directory /app

CMD ["./docker/init.sh"]