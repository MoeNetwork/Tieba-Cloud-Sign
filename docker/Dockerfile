FROM alpine:3.15

ENV LANG=zh_CN.UTF-8 \
    TZ=Asia/Shanghai \
    WORKDIR=/var/www \
    PS1="\u@\h:\w \$ "

RUN apk add --no-cache curl tar xz \
    && curl -L -o /tmp/s6-overlay-symlinks-noarch.tar.xz "https://github.com/just-containers/s6-overlay/releases/download/v3.1.0.1/s6-overlay-symlinks-noarch.tar.xz" \
    && tar -Jxpf /tmp/s6-overlay-symlinks-noarch.tar.xz -C / \
    && curl -L -o /tmp/s6-overlay-noarch.tar.xz "https://github.com/just-containers/s6-overlay/releases/download/v3.1.0.1/s6-overlay-noarch.tar.xz" \
    && tar -Jxpf /tmp/s6-overlay-noarch.tar.xz -C / \
    && curl -L -o /tmp/s6-overlay.tar.xz "https://github.com/just-containers/s6-overlay/releases/download/v3.1.0.1/s6-overlay-$(uname -m | sed s/amd64/x86_64/ | sed s/armv7l/armhf/ ).tar.xz" \
    && tar -Jxpf /tmp/s6-overlay.tar.xz -C / \
    && apk add --no-cache --update \
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
       php7-session \
    && ln -sf /usr/share/zoneinfo/${TZ} /etc/localtime \
    && echo -e "${TZ}" > /etc/timezone \
    && echo -e "max_execution_time = 3600\nupload_max_filesize=128M\npost_max_size=128M\nmemory_limit=1024M\ndate.timezone=${TZ}" > /etc/php7/conf.d/99-overrides.ini \
    && echo -e "[global]\nerror_log = /dev/stdout\ndaemonize = no\ninclude=/etc/php7/php-fpm.d/*.conf" > /etc/php7/php-fpm.conf \
    && echo -e "[www]\nuser = caddy\ngroup = caddy\nlisten = 127.0.0.1:9000\nlisten.owner = caddy\nlisten.group = caddy\npm = ondemand\npm.max_children = 75\npm.max_requests = 500\npm.process_idle_timeout = 10s\nchdir = /var/www" > /etc/php7/php-fpm.d/www.conf \
    && echo -e ":8080\nroot * /var/www\nlog {\n    level warn\n}\nphp_fastcgi 127.0.0.1:9000\nfile_server" > /etc/caddy/Caddyfile \
    && rm -rf \
       /var/www/* \
       /var/cache/apk/* \
       /tmp/* \
    && git clone --depth=1 -b master https://github.com/MoeNetwork/Tieba-Cloud-Sign /var/www \
    && mkdir /etc/cont-init.d \
    && mkdir /etc/services.d \
    && cp -r /var/www/docker/s6-overlay/etc/* /etc/

ENTRYPOINT ["/init"]
