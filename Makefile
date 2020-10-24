dir=${CURDIR}
export COMPOSE_PROJECT_NAME=component-cms-webservice

ifndef APP_ENV
	# Determine if .env file exist
	ifneq ("$(wildcard .env)","")
		include .env
	endif
endif

project=-p ${COMPOSE_PROJECT_NAME}
service=${COMPOSE_PROJECT_NAME}:latest
interactive:=$(shell [ -t 0 ] && echo 1)
ifneq ($(interactive),1)
	optionT=-T
endif
ifeq ($(GITLAB_CI),1)
	phpunitOptions=--coverage-text --colors=never
endif

build:
	@docker-compose -f docker-compose.yml build

start:
	@docker-compose -f docker-compose.yml $(project) up -d

stop:
	@docker-compose -f docker-compose.yml $(project) down

restart: stop start

env-dev:
	@make exec cmd="cp ./.env.dev ./.env"

ssh:
	@docker-compose $(project) exec $(optionT) app bash

ssh-mysql:
	@docker-compose $(project) exec mysql bash

ssh-redis:
	@docker-compose $(project) exec redis sh

exec:
	@docker-compose $(project) exec $(optionT) app $$cmd

exec-bash:
	@docker-compose $(project) exec $(optionT) app bash -c "$(cmd)"

report-prepare:
	mkdir -p $(dir)/reports/coverage

report-clean:
	rm -rf $(dir)/reports/*

wait-for-db:
	@make exec cmd="php artisan db:wait"

composer-install-no-dev:
	@make exec-bash cmd="COMPOSER_MEMORY_LIMIT=-1 composer install --optimize-autoloader --no-dev"

composer-install:
	@make exec-bash cmd="COMPOSER_MEMORY_LIMIT=-1 composer install --optimize-autoloader"

composer-update:
	@make exec-bash cmd="COMPOSER_MEMORY_LIMIT=-1 composer update"

key-generate:
	@make exec cmd="php artisan key:generate"

info:
	@make exec cmd="php artisan --version"
	@make exec cmd="php artisan env"
	@make exec cmd="php --version"

logs:
	@docker logs -f ${COMPOSE_PROJECT_NAME}-app

logs-mysql:
	@docker logs -f ${COMPOSE_PROJECT_NAME}-mysql

logs-redis:
	@docker logs -f ${COMPOSE_PROJECT_NAME}-redis

drop-migrate:
	@make exec cmd="php artisan migrate:fresh"

migrate-no-test:
	@make exec cmd="php artisan migrate --force"

migrate:
	@make exec cmd="php artisan migrate --force"

seed:
	@make exec cmd="php artisan db:seed --force"

test:
	@make exec cmd="php artisan test"

phpunit:
	@make exec cmd="./vendor/bin/phpunit -c phpunit.xml --coverage-html reports/coverage $(phpunitOptions) --coverage-clover reports/clover.xml --log-junit reports/junit.xml"

# Update code coverage on coveralls.io.
# Note: COVERALLS_REPO_TOKEN should be set on CI side.
report-code-coverage:
	@make exec-bash cmd="export COVERALLS_REPO_TOKEN=${COVERALLS_REPO_TOKEN} && php ./vendor/bin/php-coveralls -v --coverage_clover reports/clover.xml --json_path reports/coverals.json"

phpcs:
	@make exec-bash cmd="./vendor/bin/phpcs --version && ./vendor/bin/phpcs --standard=PSR2 --colors -p app"

ecs:
	@make exec-bash cmd="error_reporting=0 ./vendor/bin/ecs --clear-cache check app"

ecs-fix:
	@make exec-bash cmd="error_reporting=0 ./vendor/bin/ecs --clear-cache --fix check app"

phpmetrics:
	@make exec cmd="make phpmetrics-process"

# Generates PhpMetrics static analysis, should be run inside symfony container
phpmetrics-process:
	@mkdir -p reports/phpmetrics
	@if [ ! -f reports/junit.xml ] ; then \
		printf "\033[32;49mjunit.xml not found, running tests...\033[39m\n" ; \
		./vendor/bin/phpunit -c phpunit.xml --coverage-html reports/coverage --coverage-clover reports/clover.xml --log-junit reports/junit.xml ; \
	fi;
	@echo "\033[32mRunning PhpMetrics\033[39m"
	@php ./vendor/bin/phpmetrics --version
	@./vendor/bin/phpmetrics --junit=reports/junit.xml --report-html=reports/phpmetrics .
