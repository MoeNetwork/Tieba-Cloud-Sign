FROM alpine:3.12.7
ENV LANG=zh_CN.UTF-8 \
    TZ=Asia/Shanghai \
    WORKDIR=/var/www \https://github.com/MoeNetwork/Tieba-Cloud-Sign/blob/master/docker/Dockerfile
    PS1="\u@\h:\w \$ "
RUN sed -i 's/dl-cdn.alpinelinux.org/mirrors.aliyun.com/' /etc/apk/repositories \
    && apk add --no-cache --update \
       s6-overlay \
       bash \
       git \
       tzdata \
       shadow \
       caddy \
       php7 \
       php7-fpm \
       php7-curl \
       php7-json \
       php7-mbstring \
       php7-mysqli \
       php7-zip \
       php7-gd \
    && ln -sf /usr/share/zoneinfo/${TZ} /etc/localtime \
    && echo -e "${TZ}" > /etc/timezone \
    && echo -e "max_execution_time = 3600\nupload_max_filesize=128M\npost_max_size=128M\nmemory_limit=1024M\ndate.timezone=${TZ}" > /etc/php7/conf.d/99-overrides.ini \
    && echo -e "[global]\nerror_log = /dev/stdout\ndaemonize = no\ninclude=/etc/php7/php-fpm.d/*.conf" > /etc/php7/php-fpm.conf \
    && echo -e "[www]\nuser = caddy\ngroup = caddy\nlisten = 127.0.0.1:9000\nlisten.owner = caddy\nlisten.group = caddy\npm = ondemand\npm.max_children = 75\npm.max_requests = 500\npm.process_idle_timeout = 10s\nchdir = $WORKDIR" > /etc/php7/php-fpm.d/www.conf \
    && echo -e ":8080\nroot * $WORKDIR\nlog {\n    level warn\n}\nphp_fastcgi 127.0.0.1:9000\nfile_server" > /etc/caddy/Caddyfile \
    && rm -rf \
       $WORKDIR/* \
       /var/cache/apk/* \
       /tmp/* \
    && git clone -b master https://github.com/MoeNetwork/Tieba-Cloud-Sign $WORKDIR \
    && cp -r $WORKDIR/docker/s6-overlay/etc/cont-init.d/* /etc/cont-init.d \
    && cp -r $WORKDIR/docker/s6-overlay/etc/services.d/* /etc/services.d
WORKDIR $WORKDIR
ENTRYPOINT ["/init"]
