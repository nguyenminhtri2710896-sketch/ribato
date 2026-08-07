# Download base image ubuntu 22.04
FROM ubuntu:24.04


# LABEL about the custom image
LABEL maintainer="noname@zk.com"
LABEL version="0.1"
LABEL description="workspace for web."

# Disable Prompt During Packages Installation
ARG DEBIAN_FRONTEND=noninteractive

# Cập nhật hệ thống và cài đặt các công cụ cần thiết
RUN apt-get update && apt-get install -y \
    curl \
    wget \
    vim \
    bash \
    sudo \
    && rm -rf /var/lib/apt/lists/*


ARG TZ=UTC
ENV TZ ${TZ}

RUN ln -snf /usr/share/zoneinfo/$TZ /etc/localtime && echo $TZ > /etc/timezone
# Cập nhật và cài đặt OpenSSL
RUN apt update && apt install -y openssl ca-certificates


###########################################################################
# Crontab
###########################################################################
USER root
COPY ./crontab /etc/cron.d
RUN chmod -R 644 /etc/cron.d

# Update Ubuntu Software repository
RUN apt update
RUN apt upgrade -y

RUN echo "[system_default_sect]" >> /etc/ssl/openssl.cnf && \
    echo "CipherString = DEFAULT:@SECLEVEL=1" >> /etc/ssl/openssl.cnf

# INSTALL PHP
ARG PHP_VERSION=${PHP_VERSION}
RUN apt-get install software-properties-common -y
RUN add-apt-repository ppa:ondrej/php
RUN apt update

# RUN apt install -y php{PHP_VERSION}-intl
RUN apt install -y php${PHP_VERSION}-mysql
RUN apt install -y php${PHP_VERSION}-gd
RUN apt install -y php${PHP_VERSION}-zip
RUN apt install -y php${PHP_VERSION}-curl
RUN apt install -y php${PHP_VERSION}-mbstring
RUN apt install -y php${PHP_VERSION}-xml
RUN apt install -y php${PHP_VERSION}-cli
RUN apt install -y php-json
RUN apt install -y php${PHP_VERSION}-redis
RUN apt install -y php${PHP_VERSION}-memcached
RUN apt install -y php${PHP_VERSION}-inotify

RUN apt install -y composer
RUN apt install -y zip
RUN apt install -y mysql-client
RUN update-alternatives --set php /usr/bin/php${PHP_VERSION}


RUN apt update && apt upgrade -y openssl

RUN apt install -y supervisor
RUN mkdir -p /var/log/supervisor /etc/supervisor/conf.d

COPY supervisor/supervisord.conf /etc/supervisor/supervisord.conf
WORKDIR /var/www/
CMD ["sh", "-c", "exec /usr/bin/supervisord -n -c /etc/supervisor/supervisord.conf"]