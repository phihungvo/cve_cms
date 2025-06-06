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
RUN pecl install redis && \
    docker-php-ext-enable redis

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

# Copy configuration files
COPY ./docker/php.ini /usr/local/etc/php/
COPY ./docker/crontab /etc/cron.d/crontab

RUN chmod 0600 /etc/cron.d/crontab

# Set working directory
WORKDIR /app

# Install Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Copy application code
COPY ./ /app

# Configure vim and git
RUN echo ":set noshowmode" > /root/.vimrc && \
    git config --global safe.directory /app

# Install dependencies
RUN COMPOSER_ALLOW_SUPERUSER=1 composer install --no-dev --optimize-autoloader --classmap-authoritative

# Start application
CMD ["./docker/init.sh"]
