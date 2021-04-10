# URL shortening API
_A job interview task_

## Project Request
Create a small web service exposing URL shortening functions. One should be able to create, read, and delete shortened URLs.
The API functions will be exposed under the '/api' path while accessing a shortened URL at the root level will cause redirection to the shortened URL.

## Description
The project is realised with Symfony 5.2, PHP 8.0, api-platform and Docker

![UI](public/img/UI.png?raw=true "UI")

**Docker**
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
2. Some urls are preloaded with fixtures, and you can see them by calling GET `api/urls`. A DB reset and fixtures will be applied on every container up and this is solely for demo purpose.
3. The possibility for updating an url is disabled by requirements
4. You can try https://localhost/todor or https://localhost/test for redirections
5. You can get inside the php container with `docker exec -it tiny-url-api_php_1 sh`

## Notes


