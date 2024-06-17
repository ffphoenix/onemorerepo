# Test App
### List of changes
- Created `docker` environment with `composer`
- Created [App.php](src%2FApp.php) with refactored code
- Created  `tests` -> [AppTest.php](tests%2FAppTest.php). Added couple cases with reflection.

#### Total time: ~6h 
#### Where: 
- docker + composer ~1.5h (currently I mostly work with `Node.JS`) 
- refactoring ~3h 
- phpunit ~1.5h 

### Start project
```bash
docker compose up
```

### Install composer
```bash
docker exec -ti --user=root php-gateway composer install 
```

### Run TestAPP 
```bash
docker exec -ti php-gateway php src/RunApp.php input.txt
```

### Run tests for TestAPP
```bash
docker exec -ti --user=root php-gateway composer test
```