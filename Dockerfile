FROM php:7.4-cli

RUN apt-get update && apt-get install -y nano wget unzip zip

VOLUME [ "/app" ]

COPY ./config/git-auth-config.json /root/.composer/auth.json

COPY ./bin/joist /usr/local/bin/joist
RUN chmod +x /usr/local/bin/joist

COPY ./scripts /usr/local/bin
RUN chmod +x /usr/local/bin/*.sh

RUN /usr/local/bin/get-composer.sh

WORKDIR /app

EXPOSE 80

ENTRYPOINT /bin/bash
