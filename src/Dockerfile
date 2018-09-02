FROM lapsatech/centos7-httpd-php
MAINTAINER "Vadim Isaev" <vadim.o.isaev@gmail.com>

COPY docroot/ /var/www/
COPY conf/ /etc/httpd/conf.d/
COPY pki/ /etc/pki/tls/

ENV EURASIA36_WS_URL https://payara.local:443/
ENV EURASIA36_WS_USER admin
ENV EURASIA36_WS_PASSWORD admin

WORKDIR /var/www/
