#!/bin/sh

APP_HOME=${APP_HOME:-.}
APP_ENV=${APP_ENV:-dev}

case "$APP_ENV" in
    dev)
        "$APP_HOME"/bin/console doctrine:database:drop --if-exists --force
        "$APP_HOME"/bin/console doctrine:database:create
        "$APP_HOME"/bin/console doctrine:migrations:migrate --no-interaction
        "$APP_HOME"/bin/console doctrine:fixtures:load --no-interaction
    ;;
    prod)
        "$APP_HOME"/bin/console doctrine:database:create --if-not-exists
        "$APP_HOME"/bin/console doctrine:migrations:migrate --no-interaction
    ;;
esac
