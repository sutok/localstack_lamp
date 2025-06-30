FROM amazonlinux:2

RUN yum update -y && \
    amazon-linux-extras enable php8.2 && \
    yum install -y php php-fpm php-cli php-mbstring php-xml php-pdo php-devel php-pear make gcc unzip git && \
    yum install -y unzip curl && \
    arch=$(uname -m) && \
    if [ "$arch" = "x86_64" ]; then \
        url="https://awscli.amazonaws.com/awscli-exe-linux-x86_64.zip"; \
    elif [ "$arch" = "aarch64" ]; then \
        url="https://awscli.amazonaws.com/awscli-exe-linux-aarch64.zip"; \
    else \
        echo "Unsupported architecture: $arch"; exit 1; \
    fi && \
    curl -o "awscliv2.zip" "$url" && \
    unzip awscliv2.zip && \
    ./aws/install && \
    rm -rf awscliv2.zip aws && \
    yum clean all \
    && sed -i 's|^listen = .*|listen = 9000|' /etc/php-fpm.d/www.conf \
    && sed -i 's|^\(listen.allowed_clients\)|;\1|' /etc/php-fpm.d/www.conf


    
# Xdebug 3 インストール
RUN pecl install xdebug-3.2.2 && \
    echo "zend_extension=$(find /usr/lib64/php/modules/ -name xdebug.so)" > /etc/php.d/15-xdebug.ini && \
    echo "xdebug.mode=debug" >> /etc/php.d/15-xdebug.ini && \
    echo "xdebug.start_with_request=yes" >> /etc/php.d/15-xdebug.ini && \
    echo "xdebug.client_host=host.docker.internal" >> /etc/php.d/15-xdebug.ini && \
    echo "xdebug.client_port=9003" >> /etc/php.d/15-xdebug.ini && \
    echo "xdebug.idekey=VSCODE" >> /etc/php.d/15-xdebug.ini && \
    echo "xdebug.remote_handler = \"dbgp\"" >> /etc/php.d/15-xdebug.ini 

# Composer インストール
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

WORKDIR /var/www/html

# COPY ../src .

# RUN composer install

# PHP-FPM 設定 (UNIX ソケット用)
# RUN sed -i '/^listen = .*/{s/^/;/;a\
# listen = 9000\
# }' /etc/php-fpm.d/www.conf && \
#     sed -i 's|^;listen.owner = nobody|listen.owner = nginx|' /etc/php-fpm.d/www.conf && \
#     sed -i 's|^;listen.group = nobody|listen.group = nginx|' /etc/php-fpm.d/www.conf

EXPOSE 80

CMD ["php-fpm", "-F"]
