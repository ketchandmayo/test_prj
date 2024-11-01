# Используем базовый образ PHP с Apache
FROM php:8.1-apache

# Устанавливаем необходимые пакеты для Composer
RUN apt-get update && apt-get install -y \
    unzip \
    curl

# Устанавливаем Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Копируем исходный код в директорию контейнера
COPY . /var/www/html

# Устанавливаем права на директорию
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html

# Включаем модуль rewrite
RUN a2enmod rewrite

RUN sed -i 's/AllowOverride None/AllowOverride All/g' /etc/apache2/apache2.conf

# Открываем порт 80
EXPOSE 80