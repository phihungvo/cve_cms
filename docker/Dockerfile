FROM php:8.3

RUN apt-get update && apt-get upgrade -y && \
    apt-get install -y --no-install-recommends \
        cron \
        default-mysql-client \
        git \
        libbz2-dev \
        libicu-dev \
        libgd-dev \
        libonig-dev \
        libxslt-dev \
        libzip-dev \
        procps \
        psmisc \
        unzip \
        vim \
        php-pear \
        php-dev && \
    apt-get clean && \
    apt-get autoclean && \
    apt-get autoremove -y && \
    rm -rf /var/lib/apt/lists/*

RUN pecl channel-update pecl.php.net && \
    (pecl install redis-6.1.0 || pecl install redis-6.0.2) && \
    docker-php-ext-enable redis

RUN docker-php-ext-install \
    bcmath \
    bz2 \
    intl \
    mbstring \
    opcache \
    pdo_mysql \
    pcntl \
    sockets \
    xsl \
    zip \
    gd

COPY ./docker/php.ini /usr/local/etc/php/
COPY ./docker/crontab /etc/cron.d/crontab
RUN chmod 600 /etc/cron.d/crontab

WORKDIR /app

COPY composer.json ./

COPY --from=composer:2 /usr/bin/composer /usr/local/bin/composer
RUN COMPOSER_ALLOW_SUPERUSER=1 composer config --global process-timeout 600 && \
    COMPOSER_ALLOW_SUPERUSER=1 composer update --no-dev --optimize-autoloader --classmap-authoritative --no-scripts

COPY . ./

RUN echo ":set noshowmode" > /root/.vimrc && \
    git config --global safe.directory /app

CMD ["./docker/init.sh"]