# Sử dụng Ubuntu 24.04 làm base image
FROM ubuntu:24.04

ENV DEBIAN_FRONTEND=noninteractive
ARG PHP_VERSION=${PHP_VERSION}

# Cập nhật hệ thống và cài đặt các gói cần thiết
RUN apt-get update && apt-get install -y \
    software-properties-common \
    curl \
    wget \
    unzip \
    libcurl4-openssl-dev \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    libonig-dev \
    libzip-dev \
    libxml2-dev \
    zip \
    git \
    build-essential

# Thêm PPA để cài đặt PHP (nếu cần)
RUN add-apt-repository ppa:ondrej/php && apt-get update

# Chỉnh sửa OpenSSL để cho phép khóa DH nhỏ
RUN echo "[system_default_sect]" >> /etc/ssl/openssl.cnf && \
    echo "CipherString = DEFAULT:@SECLEVEL=1" >> /etc/ssl/openssl.cnf

RUN apt-get update && apt upgrade -y openssl
# Cài đặt PHP-FPM và các extension cần thiết
RUN apt-get install -y php-json 
RUN apt-get install -y php${PHP_VERSION}-fpm
RUN apt-get install -y php${PHP_VERSION}-mysql 
RUN apt-get install -y php${PHP_VERSION}-gd 
RUN apt-get install -y php${PHP_VERSION}-zip 
RUN apt-get install -y php${PHP_VERSION}-curl 
RUN apt-get install -y php${PHP_VERSION}-mbstring 
RUN apt-get install -y php${PHP_VERSION}-xml 
RUN apt-get install -y php${PHP_VERSION}-cli 
RUN apt-get install -y php${PHP_VERSION}-redis 
RUN apt-get install -y php${PHP_VERSION}-memcached 
RUN apt-get install -y php${PHP_VERSION}-inotify  
RUN update-alternatives --set php /usr/bin/php${PHP_VERSION}

WORKDIR /var/www
EXPOSE 9000
# Command để khởi động PHP-FPM
# CMD ["php-fpm${PHP_VERSION}", "-F"]
CMD php-fpm${PHP_VERSION} -F