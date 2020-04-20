# Json RPC 2.0 server

## Install

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
Если нужна авторизация, авторизируем любым способом,
результат авторизации записываем в объект ResultAuthModel.

если успех setSuccess(true);
иначе setSuccess(false);

Если авторизация не требуется, просто создаем объект так:
$authRes = (new ResultAuthModel())->setSuccess(true);


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

### Запуск обработкика json rpc
Создаем объект JsonRpcHandler
передаем в метод run(
ApiFactoryInterface $apiFactory,
        string $json,
        bool $resultAuth,
        array $methodsWithoutAuth = [],
        bool $responseSchemaCheck = false
)

метод возвращает объект строку json

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
ответ формируется в зависимости от того, что вернет метод API.
Все, что возвращают методы попадает в `result`

