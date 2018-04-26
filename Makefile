keys:
	mkdir keys
	openssl genrsa -out keys/private.key 2048
	openssl rsa -in keys/private.key -pubout -out keys/public.key
	chmod 660 keys/private.key keys/public.key
server:
	docker run -itd --rm -v $$(pwd):/app -w /app/public -p 8080:8080 php:7.2-alpine -S 0.0.0.0:8080
unittest:
	docker run -it --rm -v $$(pwd):/app -w /app php:7.2-alpine bin/phpunit

.SILENT: keys server unittest
.PHONY: server unittest
