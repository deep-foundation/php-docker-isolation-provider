# php-docker-isolation-provider

## Restart nginx
```bash
sudo systemctl restart nginx
```


## Local restart docker
```bash
docker build -t php-docker-isolation-provider .
docker run -d -p 39103:39103 -e PORT=39103 php-docker-isolation-provider
docker ps
```

#### or
```bash
docker run -it php-docker-isolation-provider
```

## Check open ports
```bash
netstat -tuln
curl http://localhost:39100
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
