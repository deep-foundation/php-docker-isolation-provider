# php-docker-isolation-provider

## Restart nginx
```bash
sudo systemctl restart nginx
```


## Restart docker

```bash
docker build -t php-docker-isolation-provider .

docker run -d -p 39090:39090 -e PORT=39090 php-docker-isolation-provider

docker ps
```

## Stop docker

```bash
docker stop $(docker ps -aq) && docker rm $(docker ps -aq)
```