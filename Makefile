.PHONY: up d b ps restart logs build app1 nginx mysql localstack

up:
	docker-compose up -d

d:
	docker-compose down

b:
	docker-compose build --no-cache

build:
	docker-compose build --no-cache

restart:
	docker-compose down && docker-compose up -d
	
logs:
	docker-compose logs -f

ps:
	docker-compose ps

app1:
	docker-compose exec -it app1 bash

nginx:
	docker-compose exec -it nginx bash

mysql:
	docker-compose exec -it mysql bash

localstack:
	docker-compose exec -it localstack bash
