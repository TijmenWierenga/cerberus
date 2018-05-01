init: keys build server
build:
	UID=$$(id -u) docker-compose build
	docker volume create --driver local mongo-data
keys:
	mkdir keys
	openssl genrsa -out keys/private.key 2048
	openssl rsa -in keys/private.key -pubout -out keys/public.key
	chmod 660 keys/private.key keys/public.key
server:
	docker-compose up -d
test:
	docker run -it --rm -v $$(pwd):/var/www/html -w /var/www/html cerberus/php:7.2 bin/phpunit

.SILENT: keys server test init build
.PHONY: server test
