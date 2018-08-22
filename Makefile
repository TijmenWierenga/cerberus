init: keys app-key build server
build:
	USER_ID=$$(id -u) docker-compose -f docker-compose.yml -f docker-compose.dev.yml build
	docker volume create --driver local mongo-data
app-key:
	mkdir -p keys
	cat /dev/urandom | env LC_CTYPE=C tr -dc 'a-zA-Z0-9' | fold -w 32 | head -n 1 > keys/app-key
	chmod 660 keys/app-key
keys:
	mkdir -p keys
	openssl genrsa -out keys/private.key 2048
	openssl rsa -in keys/private.key -pubout -out keys/public.key
	chmod 660 keys/private.key keys/public.key
server:
	USER_ID=$$(id -u) docker-compose -f docker-compose.yml -f docker-compose.dev.yml up -d
stop:
	USER_ID=$$(id -u) docker-compose -f docker-compose.yml -f docker-compose.dev.yml down
unit-test:
	docker run -it --rm -v $$(pwd):/var/www/html -w /var/www/html cerberus/php:7.2 bin/phpunit --testsuite unit
	docker run -it --rm -v $$(pwd):/var/www/html -w /var/www/html cerberus/php:7.2 vendor/bin/phpstan analyze src --level 7
	docker run -it --rm -v $$(pwd):/var/www/html -w /var/www/html cerberus/php:7.2 vendor/bin/phpcs src --standard=PSR2
functional-test:
	docker-compose -f docker-compose.test.yml up -d mongo
	-docker-compose -f docker-compose.test.yml run functional_test
	docker-compose -f docker-compose.test.yml down --volumes
integration-test:
	docker-compose -f docker-compose.test.yml up -d mongo
	-docker-compose -f docker-compose.test.yml run integration_test
	docker-compose -f docker-compose.test.yml down --volumes
test: unit-test functional-test integration-test
create_client:
	docker-compose run -w /var/www/html server bin/console oauth:client:create default -g password -g refresh_token -g auth_code -g client_credentials -g implicit https://www.tijmenwierenga.nl/callback

.SILENT: keys server unit-test functional-test integration-test init build stop create_client test
.PHONY: server test
