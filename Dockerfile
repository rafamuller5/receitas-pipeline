FROM php:8.1-apache

# Extensão necessária — includes/conexao.php usa mysqli
RUN docker-php-ext-install mysqli \
    && docker-php-ext-enable mysqli

# Habilita mod_rewrite (comum em apps PHP com URLs amigáveis)
RUN a2enmod rewrite

# Copia o código da aplicação para o diretório padrão do Apache
COPY . /var/www/html/

# Permissões básicas
RUN chown -R www-data:www-data /var/www/html

EXPOSE 80
