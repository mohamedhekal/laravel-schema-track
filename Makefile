.PHONY: help install test test-coverage analyse format format-check cs-fix cs-check clean

help: ## Show this help message
	@echo 'Usage: make [target]'
	@echo ''
	@echo 'Targets:'
	@awk 'BEGIN {FS = ":.*?## "} /^[a-zA-Z_-]+:.*?## / {printf "  %-15s %s\n", $$1, $$2}' $(MAKEFILE_LIST)

install: ## Install dependencies
	composer install

test: ## Run tests
	vendor/bin/phpunit

test-coverage: ## Run tests with coverage
	vendor/bin/phpunit --coverage-html build/coverage

analyse: ## Run static analysis
	vendor/bin/phpstan analyse

format: ## Format code with Laravel Pint
	vendor/bin/pint

format-check: ## Check code formatting
	vendor/bin/pint --test

cs-fix: ## Fix code style with PHP CS Fixer
	vendor/bin/php-cs-fixer fix

cs-check: ## Check code style
	vendor/bin/php-cs-fixer fix --dry-run --diff

clean: ## Clean build artifacts
	rm -rf build/
	rm -rf vendor/
	rm -rf storage/schema-track/
	rm -rf storage/schema-track-test/

ci: ## Run CI checks
	make format-check
	make cs-check
	make analyse
	make test

setup: ## Setup development environment
	make install
	make format
	make test 