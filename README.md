# URL shortening API
_A job interview task_

## Project Request
Create a small web service exposing URL shortening functions. One should be able to create, read, and delete shortened URLs.
The API functions will be exposed under the '/api' path while accessing a shortened URL at the root level will cause redirection to the shortened URL.

## Description
The project is realised with symfony 5.2, php 8.0, postgres, api-platform and docker

![UI](public/img/UI.png?raw=true "UI")

**Docker Containers**
![Docker Containers](public/img/docker_containers.png?raw=true "Docker Containers")

## Installation
1. Clone the project from the repo
    ```
    git clone git@github.com:fantomas/tiny-url-api.git
    ```
2. enter the project `cd tiny-url-api`
3. run docker containers `docker-compose up -d`

## Usage
1. You can go to https://localhost/api (should accept initially the SSL certificate)
2. Some urls are preloaded with fixtures, and you can see them by calling GET `api/urls`
3. You can try https://localhost/todor or https://localhost/test for redirections
4. You can get inside the php container with `docker exec -it tiny-url-api_php_1 sh`

## Tests
![phpunit](public/img/phpunit.png?raw=true "phpunit")

## Bonus points
1. counter for url visits is implemented. I also have implemented database optimistic lock. Otherwise, in case of heavy traffic we may miss some url hits.
2. added API endpoint to read shortened URL redirections count GET `/api/urls/{id}/visits`
3. added API endpoint to edit shortened URL PUT `/api/urls/{id}`
4. added some validations for the user data

## Notes
These are only for demo purposes
1. A DB reset and apply of fixtures will happen every time the container is up
2. phpunit tests are using the same DB

