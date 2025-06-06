FROM php:8.3

# Update and install required packages
RUN apt-get update && apt-get upgrade -y && \
    apt-get install -y --no-install-recommends \
        cron \
        default-mysql-client \
        git \
        libbz2-dev \
        libicu-dev \
        libgd-dev \
        libjpeg62-turbo-dev \
        libonig-dev \
        libxslt-dev \
        libzip-dev \
        procps \
        psmisc \
        unzip \
        vim \
        ffmpeg \
        build-essential \
        autoconf && \
    apt-get clean && \
    apt-get autoclean && \
    apt-get autoremove -y && \
    rm -rf /var/lib/apt/lists/*

# Install Redis via PECL
RUN pecl install redis

# Install Redis via PECL with fallback
# RUN pecl channel-update pecl.php.net || true && \
#     (pecl install redis-6.1.0 || \
#      pecl install redis-6.0.2 || \
#      (wget https://github.com/phpredis/phpredis/archive/refs/tags/6.0.2.tar.gz -O /tmp/redis.tar.gz && \
#       tar -xzf /tmp/redis.tar.gz -C /tmp && \
#       mv /tmp/phpredis-6.0.2 /tmp/phpredis && \
#       cd /tmp/phpredis && \
#       phpize && \
#       ./configure && \
#       make && \
#       make install && \
#       rm -rf /tmp/phpredis /tmp/redis.tar.gz)) && \
#     docker-php-ext-enable redis

# Install PHP extensions
RUN docker-php-ext-configure gd --with-jpeg && \
    docker-php-ext-install \
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

RUN docker-php-ext-enable redis

# Copy configuration files
COPY ./docker/php.ini /usr/local/etc/php/
COPY ./docker/crontab /etc/cron.d/crontab

RUN chmod 0600 /etc/cron.d/crontab

# Set working directory
WORKDIR /app

# # Install Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer


# Copy application code
COPY  ./ /app

# Configure vim and git
RUN echo ":set noshowmode" > /root/.vimrc && \
    git config --global safe.directory /app

# RUN COMPOSER_ALLOW_SUPERUSER=1 ./composer update --no-dev --optimize-autoloader --classmap-authoritative --no-scripts
RUN COMPOSER_ALLOW_SUPERUSER=1 ./composer update


# Start application
CMD ["./docker/init.sh"]
