# Simple Command Bus for PHP 8

![Code Coverage](https://github.com/ericksonreyes/command-bus/raw/master/coverage_badge.svg)
[![Build](https://github.com/ericksonreyes/command-bus/actions/workflows/merge.yaml/badge.svg?branch=master)](https://github.com/ericksonreyes/command-bus/actions/workflows/merge.yaml)

Nothing fancy. I just created my command bus that I've been copy-pasting over and over again. I usually move most of
the business or application logic away from framework controllers (MVC) and put it into commands and handlers.

But I don't want to couple the command handler with the framework controller. I want to be able to assign and switch
them via a dependency injection library.

## Installation

```shell
composer require ericksonreyes/command-bus
```

### Example (Lumen + Symfony Dependency Injection)

Symfony Service Container Configuration

```yaml
services:

  uuid_generator:
    class: App\Services\UuidGenerator

  user_repository:
    class: App\Repositories\UserRepository

  user_registration_service:
    class: Application\Users\Service\UserRegistrationService
    arguments:
    - '@user_repository'

```

Lumen Controller

```php
namespace App\Http\Controllers;

use App\Repository\UserRepository;
use Illuminate\Support\Facades\URL;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller as BaseController;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Application\Users\UseCase\RegisterUser;

/**
* Class UserRegistrationController
 * @package App\Http\Controllers
 */
class UserRegistrationController extends BaseController {

    /**
     * @param \Symfony\Component\DependencyInjection\ContainerInterface $container
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
    */
    public function store(
        ContainerInterface $container, 
        Request $request
    ): Response {
        try {
            $url = URL::current();
            $id = $uuidGenerator->generate(prefix: 'user-');
            $uuidGenerator = $container->get('uuid_generator');
            $handler = $container->get('user_registration_service');
            
            $command = new RegisterUser(
                $id,
                $request->get('email'),
                $request->get('password'),
                $request->get('password_confirmation')
            );
            $handler->handleThis($command);
            
            return \response([
                '_embedded' => [
                    '_links' => [
                        'self' => [
                            'href' => url("{$url}/{$id}")
                        ]
                    ],
                    'id' => $id                            
                ]
            ], 201);
        }
        catch (\Exception $exception) {
            $httpCode = 500;
            if ($exception->getCode() >= 400 && $exception->getCode() < 600) {
                $httpCode =$exception->getCode();
            }
            
            return \response([
                '_error' => [
                    'code' => get_class($exception),
                    'message' => $exception->getMessage()            
                ]
            ], $httpCode);
        }
    }
   
}
```

### Author

* Erickson
  Reyes ([GitHub](https://github.com/ericksonreyes), [GitLab](https://gitlab.com/ericksonreyes/), [LinkedIn](https://www.linkedin.com/in/ericksonreyes/)
  and [Packagist](http://packagist.org/users/ericksonreyes/)).

### License

See [LICENSE](LICENSE)

### Gitlab

This project is also available in [GitLab](https://gitlab.com/ericksonreyes/command-bus) 