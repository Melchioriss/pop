include .env
export

.PHONY: from-scratch # clean start
from-scratch: erase build up db

.PHONY: reup
reup: stop up

.PHONY: stop
stop:
	docker-compose stop

.PHONY: erase ## erase everything except sources
erase:
	sudo chown -R $(shell id -un):$(shell id -gn) ./
	docker-compose stop
	docker-compose rm -v -f
	docker-compose down --volumes --remove-orphans
	rm -rf ./var
	rm -rf ./vendor
	rm -rf ./node_modules

.PHONY: build-docker
build-docker:
	 docker-compose build

.PHONY: build
build: build-docker composer-install

.PHONY: composer-update
composer-update:
	docker-compose run --rm php sh -lc 'COMPOSER_MEMORY_LIMIT=-1 composer update -vvv'

.PHONY: composer-install
composer-install:
	docker-compose run --rm php sh -lc 'COMPOSER_MEMORY_LIMIT=-1 composer install -vvv'

.PHONY: composer-require
composer-require:
	docker-compose run --rm php sh -lc 'COMPOSER_MEMORY_LIMIT=-1 composer require $(this) -vvv'

.PHONY: up
up:
	docker-compose -f ./docker-compose.yml -f ./docker-compose.dev.yml up -d

.PHONY: up-pure
up-pure:
	docker-compose up -d

.PHONY: test
test:
	docker-compose exec php simple-phpunit $(with)

.PHONY: style
style:
	docker-compose run --rm php phpstan analyse -l 6 -c phpstan.neon backend tests

.PHONY: cs
cs: ## executes php cs fixer
	docker-compose run --rm php php-cs-fixer --no-interaction --diff -v fix

.PHONY: cs-check
cs-check: ## executes php cs fixer in dry run mode
	docker-compose run --rm php php-cs-fixer --no-interaction --dry-run --diff -v fix

.PHONY: deptrac
deptrac: ## Check issues with layers
	docker-compose run --rm php php ./bin/deptrac.phar analyze --formatter-graphviz=0

.PHONY: db
db: ## recreate database
	docker-compose exec php console d:d:d --force
	docker-compose exec php console d:d:c
	docker-compose exec php console d:m:m -n

.PHONY: schema-validate
schema-validate: ## validate database schema
	docker-compose exec php console d:s:v

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

.PHONY: member
member:
	docker-compose exec php console user:group:add $(them) $(of)

.PHONY: games
games:
	docker-compose exec php console steam:games:import

.PHONY: schema-update
schema-update:
	docker-compose exec php console doctrine:schema:update --force

.PHONY: migration
migration:
	docker-compose exec php console doctrine:migrations:generate

.PHONY: migration-diff
migration-diff:
	docker-compose exec php console doctrine:migrations:diff

.PHONY: migrate
migrate:
	docker-compose exec php console doctrine:migrations:migrate --no-interaction

.PHONY: import-pop
import-pop:
	docker-compose exec php console steam:group:import PoPSG

.PHONY: query
query:
	docker-compose exec php console doctrine:query:sql $(this)

.PHONY: restart-frontend
restart-frontend:
	docker-compose restart frontend

.PHONY: update-sources
update-sources:
	git pull --ff-only origin "$(shell git rev-parse --abbrev-ref HEAD)"

.PHONY: update
update: update-sources restart-frontend build migrate

.PHONY: dump-database
dump-database:
	docker-compose exec php sqlite3 ./var/pop.db .dump > $(to)

.PHONY: import-database-dump
import-database-dump:
	docker-compose run --rm php rm -f ./var/pop.db
	docker exec -i $(shell docker-compose ps -q php) sqlite3 ./var/pop.db < $(from)

.PHONY: enter
enter:
	docker-compose exec $(in) sh -l

.PHONY: enter-database
enter-database:
	docker-compose exec php sqlite3 ./var/pop.db

.PHONY: execute
execute:
	docker-compose exec php console $(command)

.PHONY: sqlite-dump
sqlite-dump:
	curl -s https://raw.githubusercontent.com/dumblob/mysql2sqlite/d14d22ad7029cdf4d11825ee3c96922e8fbb0122/mysql2sqlite \
	| awk -f - $(from) > $(from)ite.sql
