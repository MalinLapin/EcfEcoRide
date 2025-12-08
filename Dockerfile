#Image officiel de base
FROM php:8.2-apache

# on ajoute l'extension pdo pour mysql
RUN docker-php-ext-install pdo_mysql
# on ajoute l'extension pour mongodb (qui ne provient pas de php) d'où PECL pour PHP Extension Community Library
RUN pecl install  mongodb && docker-php-ext-enable mongodb

# on copie ce qui ce trouve dans /usr/bin/composer, de la dernière version de l'image de composer, au niveaux de /usr/bin/composer dans mon container
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# on ajoute le module de réécriture d'apache pour l'utilisation de Fastroute
RUN a2enmod rewrite

# on copy l'ensemble de mon code (sauf ce qui est dans le .dockerignore) a l'emplacement /var/www/html/ de mon container.
COPY . /var/www/html/

# on donne l'autorisation à Apache qui s'exécute comme étant www-data à écrire des fichiers.
RUN chown -R www-data:www-data /var/www/html