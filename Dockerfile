FROM php:8.1-apache

# Dependências de sistema: unzip (necessário para o Composer baixar pacotes)
# e libzip-dev (necessário para compilar a extensão zip do PHP)
RUN apt-get update \
    && apt-get install -y --no-install-recommends unzip libzip-dev \
    && rm -rf /var/lib/apt/lists/*

# Extensões necessárias — includes/conexao.php usa mysqli; zip é usado pelo Composer
RUN docker-php-ext-install mysqli zip \
    && docker-php-ext-enable mysqli zip

# Habilita mod_rewrite (comum em apps PHP com URLs amigáveis)
RUN a2enmod rewrite

# Composer, copiado direto da imagem oficial — assim a VM não precisa ter
# PHP/Composer instalados; tudo roda dentro deste container.
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Copia o código da aplicação para o diretório padrão do Apache
COPY . /var/www/html/

# Permissões básicas
RUN chown -R www-data:www-data /var/www/html

EXPOSE 80
