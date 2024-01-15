ps: docker-ps
up: docker-up
down: docker-down
restart: down up
pull: docker-pull
build: docker-build
exec: docker_exec
e: docker_exec
i: composer_install
u: composer_update

docker-ps:
	docker compose ps

docker-up:
	docker compose up -d

docker-build:
	docker compose up -d --build

docker-pull:
	docker compose pull

#docker-build:
#	docker compose build --pull

docker-down:
	docker compose down --remove-orphans

docker-down-clear:
	docker compose down -v --remove-orphans

composer_install:
	docker compose exec json_rpc_server composer i

composer_update:
	docker compose exec json_rpc_server composer u

docker_exec:
	docker compose exec json_rpc_server bash
