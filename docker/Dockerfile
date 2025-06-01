FROM php:8.3

RUN apt-get update && apt-get upgrade -y

# Cài đặt các gói cần thiết
RUN apt install -yqq --no-install-recommends \
    cron default-mysql-client git \
    libbz2-dev libicu-dev libldb-dev libonig-dev \
    libxslt-dev libzip-dev procps psmisc unzip vim \
    autoconf build-essential

RUN rm -rf /var/lib/apt/lists/*
RUN apt clean && apt autoclean && apt autoremove --assume-yes

# Cài đặt Redis từ mã nguồn
RUN git clone --branch 6.0.2 https://github.com/phpredis/phpredis.git /tmp/phpredis && \
    cd /tmp/phpredis && \
    phpize && \
    ./configure && \
    make && make install && \
    docker-php-ext-enable redis && \
    rm -rf /tmp/phpredis

RUN docker-php-ext-install bcmath bz2 intl mbstring opcache pdo_mysql pcntl sockets xsl zip

COPY ./docker/php.ini /usr/local/etc/php/
COPY ./docker/crontab /etc/cron.d/crontab
RUN chmod 0600 /etc/cron.d/crontab

WORKDIR /app
COPY composer.json /app/

# Dùng image composer để cài phụ thuộc
COPY --from=composer:2 /usr/bin/composer /usr/local/bin/composer
RUN COMPOSER_ALLOW_SUPERUSER=1 composer update --no-dev --optimize-autoloader --classmap-authoritative --no-scripts

COPY ./ /app

RUN echo ":set noshowmode" >> $HOME/.vimrc
RUN git config --global --add safe.directory /app

CMD ["./docker/init.sh"]