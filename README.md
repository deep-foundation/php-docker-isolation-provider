# php-docker-isolation-provider

## Quick Start
```php
function fn($data) {
    //your code
}
```


## Information about handler parameters
- `/healthz` - GET - 200 - Health check endpoint
    - Response:
        - `{}`
- `/init` - GET - 200 - Initialization endpoint
    - Response:
        - `{}`
- `/call` and `/http-call` - GET - 200 - Call executable code of handler in this isolation provider
    - Request:
        - body:
            - params:
                - jwt: STRING - Deeplinks send this token, for create gql and deep client
                - code: STRING - Code of handler
                - data: {} - Data for handler execution from deeplinks
                  > If this is type handler
                    - oldLink - from deeplinks, link before transaction
                    - newLink - from deeplinks, link after transaction
                    - promiseId - from deeplinks, promise id
    - Response:
        - `{ resolved?: any; rejected?: any; }` - If resolved or rejected is not null, then it's result of execution


## Information about $data in function fn

- `$data['deep']` - Deep Client instance
- `$data['data']` - Data for handler execution from deeplinks



## Examples
```php
function fn($data) {
    $result = sprintf("Processed data: %s, deep: %s", print_r($data['data']), 
      print_r($data['deep']));
    return $result;
}
```

```php
function fn($data) {
  return $data['deep']->select(1);
}
```

```php
function fn($data) {
    $new_record = array(
        "type_id" => 58,
        "from_id" => 0,
        "to_id" => 0
    );
    return $data['deep']->insert($new_record);
}
```


## Install/Build Deep Client PHP extension implemented in C++
```bash
pip install -r requirements.txt

apt-get install autoconf cmake make automake libtool git libboost-all-dev libssl-dev g++
#apt-get install libboost-python1.74-dev
cmake .
make
```


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
