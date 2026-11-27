## cvtic-micro-framework

Bonjour,

Voici les modifications de votre code :

- Dans `Request.php`, `parent::parse_str()` est remplacé par `parse_str()`. Avant, les requêtes PUT, DELETE et PATCH plantaient.
- Dans `HttpMethodNotImplemented.php` et `InvalidViewImplementation.php`, `extends Exception` est remplacé par `extends \Exception`. Sans le `\`, PHP cherchait une classe qui n'existe pas et plantait.

### installer les dépendances
```sh
./scripts/composer install
```

### lancer les tests unitaires
```sh
./vendor/bin/phpunit tests
```