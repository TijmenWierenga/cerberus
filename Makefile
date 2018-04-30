init: keys create_docker_volumes server
create_docker_volumes:
	docker volume create --driver local mongo-data
keys:
	mkdir keys
	openssl genrsa -out keys/private.key 2048
	openssl rsa -in keys/private.key -pubout -out keys/public.key
	chmod 660 keys/private.key keys/public.key
server:
	docker-compose up -d
test:
	docker run -it --rm -v $$(pwd):/app -w /app php:7.2-alpine bin/phpunit

.SILENT: keys server test init create_docker_volumes
.PHONY: server test
