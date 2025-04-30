include .env

ROOT_DIR := $(shell dirname $(realpath $(firstword $(MAKEFILE_LIST))))
PHP      := docker-compose exec -T php
BENCHMARK := benchmark.php

all: build up composer-install

build:
	docker-compose --env-file .env build

up:
	docker-compose --env-file .env up -d

down:
	docker-compose --env-file .env down

restart: down up

composer-install:
	$(PHP) composer install

composer-require:
	$(PHP) composer require $(package)

benchmark:
	$(PHP) php /app/bin/$(BENCHMARK) $(filter-out $@,$(MAKECMDGOALS))

benchmarks:
	$(PHP) php /app/bin/$(BENCHMARK) run-all

%:
	@:
