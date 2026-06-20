FROM php:8.1-apache

# Dependências de sistema: unzip (Composer), libzip-dev (extensão zip) e
# PHPIZE_DEPS (ferramentas de build necessárias para compilar extensões PECL como o pcov)
RUN apt-get update \
    && apt-get install -y --no-install-recommends unzip libzip-dev $PHPIZE_DEPS \
    && rm -rf /var/lib/apt/lists/*

# Extensões necessárias:
# - mysqli: usada em includes/conexao.php
# - zip: usada pelo Composer para baixar pacotes
# - pcov: driver de cobertura de código para o PHPUnit (sem ele, o PHPUnit falha
#   com "No code coverage driver available" ao tentar gerar relatório de cobertura)
RUN docker-php-ext-install mysqli zip \
    && docker-php-ext-enable mysqli zip \
    && pecl install pcov \
    && docker-php-ext-enable pcov

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
