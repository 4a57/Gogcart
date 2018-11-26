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

## Running the tests

You can run test on running docker container:
```bash
make test
```

## Built With

* [Symfony 4](https://symfony.com/) - Web framework
* [Api Platform](https://api-platform.com/) - API framework for Symfony
* [RoadRunner](https://github.com/spiral/roadrunner) - PHP server
* [Docker](https://www.docker.com/) - Containerization

