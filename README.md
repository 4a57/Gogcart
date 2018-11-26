# Gogcart

Gogkart will provide new a completely new experience for your shop customers! At least on the April Fool's Day.

## Getting Started

The Gogcart is dockerized and to simplify database is SQLite and stored in file so all what you need to run this application is docker.

### Prerequisites

To run project you need docker. You can get this [here](https://www.docker.com/get-started).

### Installing

1. Clone repository to your computer:
    ```bash
    git clone git@gitlab.com:JacekWisniewski/gogcart.git
    ```
1. Create your `.env` file based on `.env.dist`
    ```bash
    cp .env.dist .env
    ```
1. Build docker image and run container:
    ```bash
    make build
    make start
    ```
1. Create database file and load fixtures:
    ```bash
    make restart-data
    ```

Now you can open `http://localhost:8080` to see API documentation and start to work with the API.
Swagger documentation UI allow to try requests directly from a browser.

### Note
Note that the application works based on API resources. In requests body you should use full resource id in REST convention like `/products/2` instead of just entity id like `2`.
For example to add product to cart you should make request like:

```bash
curl -X POST "http://localhost:8080/carts/1/products" -H "accept: application/json" -d '{"id": "products/1"}'
```

instead of:

```bash
curl -X POST "http://localhost:8080/carts/1/products" -H "accept: application/json" -d '{"id": "1"}'
```

## Running the tests

You can run test on running docker container:
```bash
make test
```

## Deployment
Dockerfile is not ready to use on production environment - it's only for development.

## Built With

* [Symfony 4](https://symfony.com/) - Web framework
* [Api Platform](https://api-platform.com/) - API framework for Symfony
* [RoadRunner](https://github.com/spiral/roadrunner) - PHP server
* [Docker](https://www.docker.com/) - Containerization
