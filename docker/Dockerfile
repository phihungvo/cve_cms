FROM php:8.3

RUN apt-get update && apt-get upgrade -y

# Cài đặt các gói cần thiết, bao gồm autoconf và build-essential
RUN apt install -yqq --no-install-recommends \
    cron default-mysql-client git \
    libbz2-dev libicu-dev libldb-dev libonig-dev \
    libxslt-dev libzip-dev procps psmisc unzip vim \
    autoconf build-essential

RUN rm -rf /var/lib/apt/lists/*

RUN apt clean && apt autoclean && apt autoremove --assume-yes

# Cập nhật kênh PECL và cài đặt redis
RUN pecl channel-update pecl.php.net && \
    pecl install redis-6.0.2 && \
    docker-php-ext-enable redis

RUN docker-php-ext-install bcmath bz2 intl mbstring opcache pdo_mysql pcntl sockets xsl zip

COPY ./docker/php.ini /usr/local/etc/php/
COPY ./docker/crontab /etc/cron.d/crontab

RUN chmod 0600 /etc/cron.d/crontab

WORKDIR /app

COPY composer.json /app/
COPY ./ /app

RUN echo ":set noshowmode" >> $HOME/.vimrc

RUN git config --global --add safe.directory /app

# Sử dụng composer update để sinh composer.lock
RUN COMPOSER_ALLOW_SUPERUSER=1 composer update --no-dev --optimize-autoloader --classmap-authoritative --no-scripts

CMD ["./docker/init.sh"]