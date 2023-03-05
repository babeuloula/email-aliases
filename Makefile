#-include docker/.env

CONTAINER ?= php
.SILENT: shell reset analyse phpunit
.DEFAULT_GOAL := help

help:
	@grep -E '(^[a-zA-Z_-]+:.*?##.*$$)|(^##)' $(MAKEFILE_LIST) | awk 'BEGIN {FS = ":.*?## "}; {printf "\033[32m%-30s\033[0m %s\n", $$1, $$2}' | sed -e 's/\[32m##/[33m/'

##
## Project
##---------------------------------------------------------------------------

install: ## Install the project
install: hooks
	cd ./docker && ./install.sh

start: ## Start the project
start: hooks
	cd ./docker && ./start.sh

stop: ## Stop the project
stop:
	cd ./docker && ./stop.sh

restart: ## Restart the project
restart: stop start

hooks:
	echo "#!/bin/bash" > .git/hooks/pre-commit
	echo "make check" >> .git/hooks/pre-commit
	chmod +x .git/hooks/pre-commit

shell: ## Connect to PHP container
shell:
	cd ./docker && docker compose exec $(CONTAINER) bash

clear-cache:
	bin/console cache:clear --no-warmup

##
## Database
##---------------------------------------------------------------------------

reset: ## Reset the database (only on container)
reset:
	make doctrine-migrations;

doctrine-migrations: ## Execute all migrations (only on container)
doctrine-migrations:
	bin/console doctrine:migration:migrate --allow-no-migration --no-interaction --all-or-nothing

##
## Code quality
##---------------------------------------------------------------------------

check:
	cd ./docker && docker compose exec -T php make lint
	cd ./docker && docker compose exec -T php make analyse

lint: ## Execute PHPCS
lint:
	vendor/bin/phpcs -p

fixer: ## Execute PHPCS fixer
fixer:
	./vendor/bin/phpcbf -p

analyse: ## Execute PHPStan
analyse:
	vendor/bin/phpstan analyse
