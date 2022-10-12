# Executables (local)
DOCKER_COMP = docker-compose

# Docker containers
PHP_CONT = $(DOCKER_COMP) exec php
PHP_RUN = $(DOCKER_COMP) run php

# Executables
PHP      = $(PHP_CONT) php
COMPOSER = $(PHP_CONT) composer
SYMFONY  = $(PHP_CONT) bin/console
TESTS	 = $(PHP_CONT) bin/phpunit
BEHAT	 = $(PHP_CONT) vendor/bin/behat

# Misc
.DEFAULT_GOAL = help
.PHONY        = help build up start down logs sh composer vendor sf cc

## —— 🎵 🐳 The Symfony-docker Makefile 🐳 🎵 ——————————————————————————————————
help: ## Outputs this help screen
	@grep -E '(^[a-zA-Z0-9_-]+:.*?##.*$$)|(^##)' $(MAKEFILE_LIST) | awk 'BEGIN {FS = ":.*?## "}{printf "\033[32m%-30s\033[0m %s\n", $$1, $$2}' | sed -e 's/\[32m##/[33m/'

## —— Docker 🐳 ————————————————————————————————————————————————————————————————
build: ## Builds the Docker images
	@$(DOCKER_COMP) build --pull --no-cache

up: ## Start the docker hub in detached mode (no logs)
	@$(DOCKER_COMP) up --detach

start: build database-up up ## Build and start the containers

down: ## Stop the docker hub
	@$(DOCKER_COMP) down --remove-orphans

logs: ## Show live logs
	@$(DOCKER_COMP) logs --tail=0 --follow

bash: ## Connect to the PHP FPM container
	@$(PHP_CONT) bash

database-up: ## Spin up dev database
	docker-compose run --rm php sh -c "\
		APP_ENV='dev'; \
		composer install; \
		bin/console about; \
		bin/console do:da:cr --if-not-exists && bin/console do:sch:upd --force; \
		bin/console do:fix:lo --no-interaction; \
	"

## —— Tests 🧪 ———————————————————————————————————————————————————————————————
test-unit: up ## Run php unit on fresh container
	docker-compose exec php sh -c "\
		APP_ENV='test'; \
		bin/console about; \
		bin/console do:da:dr --force; \
		bin/console do:da:cr --if-not-exists && bin/console do:sch:upd --force; \
		bin/console do:sch:val; \
		bin/phpunit; \
	"

test-behat: up ## Run behat on fresh container
	docker-compose exec php sh -c "\
		APP_ENV='test'; \
		bin/console about; \
		bin/console do:da:dr --force; \
		bin/console do:da:cr --if-not-exists && bin/console do:sch:upd --force; \
		bin/console do:sch:val; \
		vendor/bin/behat; \
	"

test: up ## Run all tests on fresh container
	docker-compose exec php sh -c "\
		APP_ENV='test'; \
		bin/console about; \
		bin/console do:da:dr --force; \
		bin/console do:da:cr --if-not-exists && bin/console do:sch:upd --force; \
		bin/console do:sch:val; \
		vendor/bin/behat; \
		bin/phpunit; \
	"
