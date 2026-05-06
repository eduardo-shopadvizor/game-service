FROM eu.gcr.io/saz-shared-services/custom-saz/php-xdebug-84:1.1.0

ARG NEXUS_TOKEN
ENV COMPOSER_REGISTRY_TOKEN="$NEXUS_TOKEN"

USER root:root

COPY --chown=${APP_USER}:${APP_GROUP} etc/infrastructure/supervisor/supervisord.conf "${APP_HOME}/etc/infrastructure/supervisor/supervisord.conf"

RUN cat "${APP_HOME}/etc/infrastructure/supervisor/supervisord.conf" >> "/etc/supervisord.conf" \
    && rm "${APP_BASE_HOME}/etc/infrastructure/scripts/initialize.sh"

USER "${APP_USER}":"${APP_GROUP}"

RUN "${APP_BASE_HOME}/etc/infrastructure/scripts/config_registry.sh"
