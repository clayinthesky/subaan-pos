FROM php:8.2-apache
RUN apt-get update && apt-get install -y libicu-dev libpng-dev libjpeg62-turbo-dev libwebp-dev libsqlite3-dev libonig-dev unzip git && docker-php-ext-configure gd --with-jpeg --with-webp && docker-php-ext-install intl gd bcmath mbstring mysqli pdo_mysql pdo_sqlite sqlite3 && a2enmod rewrite && rm -rf /var/lib/apt/lists/*
COPY --from=composer:2 /usr/bin/composer /usr/local/bin/composer
WORKDIR /var/www/html
COPY . .
RUN composer install --no-dev --prefer-dist --no-interaction --optimize-autoloader && sed -i 's!/var/www/html!/var/www/html/public!g' /etc/apache2/sites-available/000-default.conf && chown -R www-data:www-data writable
EXPOSE 80
CMD ["apache2-foreground"]
