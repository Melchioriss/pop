.PHONY: start
start: erase build up db ## clean current environment, recreate dependencies and spin up again

.PHONY: reup
reup: stop up

.PHONY: stop
stop: ## stop environment
	docker-compose stop

.PHONY: rebuild
rebuild: start ## same as start

.PHONY: erase
erase: ## stop and delete containers, clean volumes.
	docker-compose stop
	docker-compose rm -v -f
	docker-compose down --volumes --remove-orphans
	rm -rf ./var
	rm -rf ./vendor

.PHONY: build
build: ## build environment and initialize composer and project dependencies
	docker-compose build
	docker-compose run --rm php sh -lc 'xoff;COMPOSER_MEMORY_LIMIT=-1 composer install'

.PHONY: composer-update
composer-update: ## Update project dependencies
	docker-compose run --rm php sh -lc 'xoff;COMPOSER_MEMORY_LIMIT=-1 composer update'

.PHONY: up
up: ## spin up environment
	docker-compose up -d

.PHONY: test
test: # run tests in exists environment
	docker-compose exec php sh -lc "./vendor/bin/phpunit $(with)"

.PHONY: style
style: ## executes php analizers
	docker-compose run --rm php sh -lc './vendor/bin/phpstan analyse -l 6 -c phpstan.neon src tests'

.PHONY: cs
cs: ## executes php cs fixer
	docker-compose run --rm php sh -lc './vendor/bin/php-cs-fixer --no-interaction --diff -v fix'

.PHONY: cs-check
cs-check: ## executes php cs fixer in dry run mode
	docker-compose run --rm php sh -lc './vendor/bin/php-cs-fixer --no-interaction --dry-run --diff -v fix'

.PHONY: layer
layer: ## Check issues with layers
	docker-compose run --rm php sh -lc 'php bin/deptrac.phar analyze --formatter-graphviz=0'

.PHONY: db
db: ## recreate database
	docker-compose exec php sh -lc './bin/console d:d:d --force'
	docker-compose exec php sh -lc './bin/console d:d:c'
	docker-compose exec php sh -lc './bin/console d:s:u --force'
	docker-compose exec php sh -lc './bin/console d:m:m -n'

.PHONY: schema-validate
schema-validate: ## validate database schema
	docker-compose exec php sh -lc './bin/console d:s:v'

.PHONY: enable_xdebug
enable_xdebug:
	docker-compose exec php sh -lc 'xon'

.PHONY: disable_xdebug
disable_xdebug:
	docker-compose exec php sh -lc 'xoff'

# TODO: fix duplication?
.PHONY: restart_backend
restart_backend:
	docker-compose restart php

.PHONY: restart_backend_again
restart_backend_again:
	docker-compose restart php

.PHONY: wait_for_it
wait_for_it:
	echo "Press <ENTER> to finish"
	@read anything

.PHONY: debug
debug: xon wait_for_it xoff

.PHONY: xon
xon: enable_xdebug restart_backend

.PHONY: xoff
xoff: disable_xdebug restart_backend_again

.PHONY: sh
sh: ## gets inside a container, use 's' variable to select a service. make s=php sh
	docker-compose exec $(s) sh -l

.PHONY: logs
logs: ## look for 's' service logs, make s=php logs
	docker-compose logs -f $(s)

.PHONY: help
help: ## Display this help message
	@cat $(MAKEFILE_LIST) | grep -e "^[a-zA-Z_\-]*: *.*## *" | awk 'BEGIN {FS = ":.*?## "}; {printf "\033[36m%-30s\033[0m %s\n", $$1, $$2}'

.PHONY: cache-cleanup
cache-cleanup:
	docker-compose exec php console cache:clear $(for)

.PHONY: admin
admin:
	docker-compose exec php console user:roles:add $(them) ADMIN

.PHONY: schema-update
schema-update:
	docker-compose exec php console doctrine:schema:update --force
