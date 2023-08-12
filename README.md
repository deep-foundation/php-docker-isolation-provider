# php-docker-isolation-provider

## Restart nginx
```bash
sudo systemctl restart nginx
```


## Local restart docker

```bash
docker build -t php-docker-isolation-provider .

docker run -d -p 39101:39101 -e PORT=39101 php-docker-isolation-provider

docker ps
```

## Stop docker

```bash
docker stop $(docker ps -aq) && docker rm $(docker ps -aq)
```

## Start docker js

```bash
docker pull deepf/js-docker-isolation-provider:main
docker run -d -p 39090:39090 -e PORT=39090 deepf/js-docker-isolation-provider:main
```


## Tests
```bash
php8.1 vendor/bin/phpunit tests/Feature/CallTest.php
```
