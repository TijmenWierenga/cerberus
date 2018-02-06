server:
	docker run -itd --rm -v $$(pwd):/app -w /app/public -p 8080:8080 php:7.2-alpine -S 0.0.0.0:8080
unittest:
	docker run -it --rm -v $$(pwd):/app -w /app php:7.2-alpine vendor/bin/phpunit