# HELP
# This will output the help for each task
.PHONY: help

help: ## This help
	@awk 'BEGIN {FS = ":.*?## "} /^[a-zA-Z_-]+:.*?## / {printf "\033[36m%-30s\033[0m %s\n", $$1, $$2}' $(MAKEFILE_LIST)

.DEFAULT_GOAL := help

# Install & config setup
init: ## Install

clear:
	./vendor/bin/sail artisan route:clear
	./vendor/bin/sail artisan view:clear
	./vendor/bin/sail artisan cache:clear

start-local:
	./vendor/bin/sail artisan serve & npm run dev

start-docker:
	./vendor/bin/sail up -d && ./vendor/bin/sail npm run dev

stop-docker:
	./vendor/bin/sail stop

test:
	./vendor/bin/sail artisan test

biome:
	biome check --write --unsafe ./resources/js
