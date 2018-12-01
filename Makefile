build:
	docker build -t gogcart .

start:
	docker run --name gogcart -d -p 8080:8080 gogcart

stop:
	docker kill gogcart
	docker rm gogcart

restart-data:
	docker exec gogcart sh -c "scripts/restart_data.sh"

build-dev:
	docker build -f Dockerfile.dev -t gogcart-dev .

test:
	docker run gogcart-dev php "bin/phpunit"
