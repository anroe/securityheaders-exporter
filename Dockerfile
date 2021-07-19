FROM php:7.4.2-cli

RUN apt-get update && apt-get install -y openssl libssl-dev wget git procps curl libcurl4-gnutls-dev

RUN cd /tmp && git clone https://github.com/swoole/swoole-src.git && \
    cd swoole-src && \
    git checkout v4.6.7 && \
    phpize  && \
    ./configure --enable-openssl --enable-swoole-curl --enable-http2 --enable-mysqlnd && \
    make && make install

RUN touch /usr/local/etc/php/conf.d/swoole.ini && \
    echo 'extension=swoole.so' > /usr/local/etc/php/conf.d/swoole.ini

RUN wget -O /usr/local/bin/dumb-init https://github.com/Yelp/dumb-init/releases/download/v1.2.2/dumb-init_1.2.2_amd64
RUN chmod +x /usr/local/bin/dumb-init

RUN apt-get remove --purge -y git wget && apt-get autoremove -y && rm -rf /var/lib/apt/lists/* && rm -rf /tmp/*

COPY exporter.php /opt

ENTRYPOINT ["/usr/local/bin/dumb-init", "--", "php"]