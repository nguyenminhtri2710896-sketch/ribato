# FROM nginx:alpine

# LABEL maintainer="Mahmoud Zalt <mahmoud@zalt.me>"

# COPY nginx.conf /etc/nginx/

# # If you're in China, or you need to change sources, will be set CHANGE_SOURCE to true in .env.

# ARG CHANGE_SOURCE=false
# RUN if [ ${CHANGE_SOURCE} = true ]; then \
#     # Change application source from dl-cdn.alpinelinux.org to aliyun source
#     sed -i 's/dl-cdn.alpinelinux.org/mirrors.aliyun.com/' /etc/apk/repositories \
# ;fi

# RUN apk update \
#     && apk upgrade \
#     && apk --update add logrotate \
#     && apk add --no-cache openssl \
#     && apk add --no-cache bash

# RUN apk add --no-cache curl

# RUN set -x ; \
#     addgroup -g 82 -S www-data ; \
#     adduser -u 82 -D -S -G www-data www-data && exit 0 ; exit 1

# ARG PHP_UPSTREAM_CONTAINER=php-fpm
# ARG PHP_UPSTREAM_PORT=9000

# # Create 'messages' file used from 'logrotate'
# RUN touch /var/log/messages

# # Copy 'logrotate' config file
# COPY logrotate/nginx /etc/logrotate.d/

# # Set upstream conf and remove the default conf
# RUN echo "upstream php-upstream { server ${PHP_UPSTREAM_CONTAINER}:${PHP_UPSTREAM_PORT}; }" > /etc/nginx/conf.d/upstream.conf \
#     && rm /etc/nginx/conf.d/default.conf

# ADD ./startup.sh /opt/startup.sh
# RUN sed -i 's/\r//g' /opt/startup.sh
# CMD ["/bin/bash", "/opt/startup.sh"]

# EXPOSE 80 81 443
FROM ubuntu:24.04
ARG DEBIAN_FRONTEND=noninteractive

# Cài đặt các gói cần thiết
RUN apt-get update && apt-get install -y \
    openssl \
    libssl-dev \
    build-essential \
    libpcre3 \
    libpcre3-dev \
    zlib1g \
    zlib1g-dev \
    libjpeg-dev \
    libpng-dev \
    libgd-dev \
    wget
ARG PHP_UPSTREAM_CONTAINER=php-fpm
ARG PHP_UPSTREAM_PORT=9000
# Tải xuống mã nguồn Nginx
RUN wget http://nginx.org/download/nginx-1.24.0.tar.gz && \
    tar -zxvf nginx-1.24.0.tar.gz && \
    cd nginx-1.24.0 && \
    ./configure --with-http_image_filter_module --with-http_gzip_static_module --with-http_ssl_module --prefix=/etc/nginx && \
    make && \
    make install

# RUN apt-get install nginx-module-image-filter
# Before CMD, add:
RUN ln -sf /dev/stdout /etc/nginx/logs/access.log && \
    ln -sf /dev/stderr /etc/nginx/logs/error.log

# Copy file cấu hình
COPY nginx.conf /etc/nginx/conf/nginx.conf

COPY logrotate/nginx /etc/logrotate.d/
RUN echo "upstream php-upstream { server ${PHP_UPSTREAM_CONTAINER}:${PHP_UPSTREAM_PORT}; }" > /etc/nginx/conf/upstream.conf
EXPOSE 80
CMD ["/etc/nginx/sbin/nginx", "-g", "daemon off;"]