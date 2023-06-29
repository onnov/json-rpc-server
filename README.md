<!-- TODO [![Build Status](https://travis-ci.org/onnov/json-rpc-server.svg?branch=master)](https://travis-ci.org/onnov/json-rpc-server) -->

[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/onnov/json-rpc-server/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/onnov/json-rpc-server/?branch=master)


# Json RPC 2.0 server

## Install

[Composer](https://getcomposer.org) (recommended)
Use Composer to install this library from Packagist: onnov/json-rpc-server

Run the following command from your project directory to add the dependency:
```bash
composer require onnov/json-rpc-server
```

Alternatively, to load the dev-master branch, add the dependency directly to the composer.json file.

composer.json
```
 "repositories": [
        {
            "type": "git",
            "url": "git@github.com:onnov/json-rpc-server.git"
        }
    ],
    "require": {
        "onnov/json-rpc-server": "dev-master"
    },
```

## Use

Для автозагрузки в symfony нужно добавить:
```
  Onnov\JsonRpcServer\JsonRpcHandler:
    autowire: true
```
в файл services.yaml

### Авторизация
Json RPC server не занимается авторизацией.
Если нужна авторизация, авторизуйте пользователя любым способом,
результат авторизации передайте в метод JsonRpcHandler::run,
3-м параметром **$resultAuth** в виде true или false.

Данный флаг предусмотрен только для того, что бы
Json RPC server выдал стандартный ответ при отсутствии авторизации.

Если авторизация не требуется, просто передайте true
3-м параметром **$resultAuth**.

---

Если API использует авторизацию, но несколько методов должны быть доступны
без авторизации, такие методы как **login** или **authCheck**
эти методы можно перечислить в 4-м параметре **$methodsWithoutAuth** в виде массива:
```php
$methodsWithoutAuth = ['login', 'authCheck'];
```
Эти методы будут доступны независимо от авторизации.

### Фабрика с методами
Создаем фабрику с помощью Интерфейса ApiFactoryInterface
в методе getSubscribedServices перечисляем все используемые классы
```
public static function getSubscribedServices(): array
    {
        return [
            'Bankruptcy' => Bankruptcy::class,
            'Bankrupt' => Bankrupt::class,
        ];
    }
```

### Запуск обработчика json rpc
Создаем объект JsonRpcHandler
передаем в метод run(
        ApiFactoryInterface $apiFactory,
        string $json,
        bool $resultAuth,
        array $methodsWithoutAuth = [],
        bool $responseSchemaCheck = false
)

метод возвращает строку json

```
use Onnov\JsonRpcServer\JsonRpcHandler;
use Onnov\JsonRpcServer\Model\ResultAuthModel;

$authRes = (new ResultAuthModel())->setSuccess(true);
$apiFactory = new ApiFactory()

$server = new JsonRpcHandler();
$res = $server->run(
    $apiFactory,
    $json,
    $resultAuth,
    $methodsWithoutAuth
    $responseSchemaCheck
)

```

### JSON RPC методы
API Методы создаются с помощью Интерфейса `ApiMethodInterface.php`. 
Каджый класс с используемыми методами должен быть описан в ApiFactory.

jsonRPC запрос может выглядеть так:
```
{
  "jsonrpc": "2.0",
  "method": "Auth.check",
  "params": null,
  "id": 911
}
```
Ответ формируется в зависимости от того, что вернет метод API.
Все, что возвращают методы попадает в `result`

---

# Validation

https://opis.io/json-schema