#Image officiel de base
FROM php:8.2-apache

# Installation des outils système + extensions PHP en une fois
RUN apt-get update && apt-get install -y \
    git \
    unzip \
    libzip-dev \
    libssl-dev \
    && docker-php-ext-install zip pdo pdo_mysql \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# on ajoute l'extension pour mongodb (qui ne provient pas de php) d'où PECL pour PHP Extension Community Library
RUN pecl install  mongodb && docker-php-ext-enable mongodb


# on copie ce qui ce trouve dans /usr/bin/composer, de la dernière version de l'image de composer, au niveaux de /usr/bin/composer dans mon container
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# on ajoute le module de réécriture d'apache pour l'utilisation de Fastroute
RUN a2enmod rewrite

# Modification du DocumentRoot pour pointer vers public/
RUN sed -i 's|DocumentRoot /var/www/html|DocumentRoot /var/www/html/public|' \
    /etc/apache2/sites-available/000-default.conf

# Modification du <Directory> pour permettre .htaccess
RUN sed -i 's|<Directory /var/www/>|<Directory /var/www/html/public/>|' \
    /etc/apache2/apache2.conf && \
    sed -i 's|AllowOverride None|AllowOverride All|' \
    /etc/apache2/apache2.conf

# on copy l'ensemble de mon code (sauf ce qui est dans le .dockerignore) a l'emplacement /var/www/html/ de mon container.
COPY . /var/www/html/

# on se place dans le dossier du projet
WORKDIR /var/www/html

# on installe les dépendances Composer 
RUN composer install

# on donne l'autorisation à Apache qui s'exécute comme étant www-data à écrire des fichiers.
RUN chown -R www-data:www-data /var/www/html
