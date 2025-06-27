FROM amazonlinux:2

RUN yum update -y \
    && amazon-linux-extras enable php8.2 \
    && yum clean metadata \
    && yum install -y php php-cli php-fpm php-mbstring php-xml php-mysqlnd php-pdo php-gd php-json php-curl php-zip php-opcache php-devel php-pecl-apcu php-pecl-redis php-intl php-bcmath php-dom php-fileinfo php-tokenizer php-ctype php-session php-simplexml php-xmlwriter php-xmlreader php-phar php-posix php-sockets php-exif php-iconv php-gmp php-pcntl php-soap php-pear php-process php-ldap php-pecl-xdebug \
    && yum clean all

CMD ["php-fpm", "-F"] 
