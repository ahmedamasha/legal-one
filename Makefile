# Makefile for managing Docker and Symfony tasks

# Variables
DOCKER_COMPOSE = docker-compose
EXEC_PHP = $(DOCKER_COMPOSE) exec app
SYMFONY = $(EXEC_PHP)  php bin/console

# Targets
.PHONY: up down bash test migrate

# Start Docker containers and run migrations
up: docker-up migrate

# Start Docker containers
docker-up:
	$(DOCKER_COMPOSE) build
	$(DOCKER_COMPOSE) up 

# Stop Docker containers
down:
	$(DOCKER_COMPOSE) down

# Access the Symfony app container bash shell
bash:
	$(EXEC_PHP) bash

# Run Symfony tests
test:
	$(EXEC_PHP) bash -c 'cd /var/www/html && ./vendor/bin/phpunit'

# Run Symfony migrations
migrate:
	$(SYMFONY) doctrine:migrations:migrate --no-interaction

# Run Symfony migrations
producer:
	$(SYMFONY) app:log-producer

# Run Consumer
consumer:
	$(SYMFONY) app:log-consumer
