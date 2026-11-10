## cvtic-micro-framework

Boilerplate du projet micro-framework à réaliser en groupe.

### licences
Vous retrouverez dans le dossier _LICENSES/_, toutes les licences des 
bibliothèques utilisées dans ce micro-framework.  
Remerciement au projet Symfony de partager ses bibliothèques vraiment pratique.  
Remerciement au projet favicon.io pour la petite icône bien rigolote.  

### qu'est ce qu'on utilise ici
Déjà, on utilise une version de PHP qui est `>=8.2`.  
Ensuite, on utilise les bibliothèques suivantes et rien d'autre :
- [Symfony HTTP Foundation](https://symfony.com/doc/current/components/http_foundation.html)
- [Twig by Symfony](https://twig.symfony.com/doc/3.x/api.html)

Pour finir, histoire de tester que le code marche bien, on utilise [PHPUnit](https://docs.phpunit.de/en/11.4/index.html).

Vous avez aussi, directement dans le repo, deux scripts PHP à votre disposition :
- [composer](https://getcomposer.org/doc/00-intro.md) - Un gestionnaire de projet PHP
- [pretty-php](https://github.com/lkrms/pretty-php) - Un outil de mise en page, avec une opinion, pour le code PHP

Je vous invite **chaleureusement** à lire les documentations de tout ces petits
projets, histoire de savoir avec quoi vous allez travailler :)

### installer les dépendences
Il suffit de demander à `composer` de le faire :
```sh
./scripts/composer install
```
Vous devriez ensuite avoir un dossier _vendor_, avec plusieurs sous-dossier.  
Vous pouvez maintenant lancer les tests unitaires du projet (il n'y en a pas, mais ça ne fait rien) :
```sh
./vendor/bin/phpunit tests
```
La commande devrait se terminer sans erreurs.

### quelques bonnes lectures
Toujours bien d'apprendre :)

#### PHP
- HTTP header always text/html even when manually set  
https://code.whatever.social/questions/8028957/how-to-fix-headers-already-sent-error-in-php
- How to add local path composer's requirements  
https://aschmelyun.com/blog/installing-a-local-composer-package-in-your-php-project/
- Secure PHP application  
https://www.php.net/manual/en/security.php
- PHP "the right way"  
https://phptherightway.com/
- PHP OOP ressources  
https://github.com/marcelgsantos/learning-oop-in-php

#### Git
- https://learngitbranching.js.org/
- https://git-scm.com/book/en/v2
- https://git-rebase.io/
- gitworkflows(7)

### favicon
J'ai téléchargé un favicon, depuis https://favicon.io, pour rendre les onglets de nos navigateurs plus jolis :)
Pour l'ajouter à vos pages, il suffit de rajouter le snippet suivant dans vos `<head>`.
```html
<link rel="icon" type="image/png" sizes="32x32" href="/static/favicon/favicon-32x32.png">
<link rel="icon" type="image/png" sizes="16x16" href="/static/favicon/favicon-16x16.png">
<link rel="manifest" href="/static/favicon/site.webmanifest">
```
