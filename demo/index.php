<?php declare(strict_types=1);

// Démo du micro-framework : lancer depuis le dossier demo/ avec
//   php -S localhost:8000 index.php
// puis ouvrir http://localhost:8000/

require __DIR__ . '/../vendor/autoload.php';

use Framework312\Router\Request;
use Framework312\Router\SimpleRouter;
use Framework312\Router\View\HTMLView;
use Framework312\Router\View\JSONView;
use Framework312\Router\View\TemplateView;
use Framework312\Template\TwigRenderer;

// Fausse "base de données" pour l'exemple
const LIVRES = [
    '1' => ['titre' => 'Dune', 'auteur' => 'Frank Herbert'],
    '2' => ['titre' => 'Fondation', 'auteur' => 'Isaac Asimov'],
];

// 1. HTMLView : le verbe renvoie directement du HTML
class Accueil extends HTMLView {
    protected function get(Request $request): string {
        return '<h1>Micro-framework 312</h1>
            <ul>
              <li><a href="/book/1">/book/1</a> (TemplateView)</li>
              <li><a href="/book/2">/book/2</a> (TemplateView)</li>
              <li><a href="/book/99">/book/99</a> (livre inconnu)</li>
              <li><a href="/api/book/1">/api/book/1</a> (JSONView)</li>
              <li><a href="/nimporte/quoi">/nimporte/quoi</a> (404)</li>
            </ul>';
    }
}

// 2. TemplateView : le verbe renvoie le template + les données
class Book extends TemplateView {
    protected function get(Request $request): array {
        $id = $request->attributes->get('id');
        $livre = LIVRES[$id] ?? null;

        return [
            'template' => 'show.twig',
            'data'     => ['id' => $id, 'livre' => $livre],
        ];
    }

    protected function post(Request $request): array {
        return [
            'template' => 'saved.twig',
            'data'     => ['id' => $request->attributes->get('id'), 'note' => $request->request->get('note')],
        ];
    }
}

// 3. JSONView : le verbe renvoie un tableau, encodé en JSON
class BookApi extends JSONView {
    protected function get(Request $request): array {
        $id = $request->attributes->get('id');
        return ['id' => $id, 'livre' => LIVRES[$id] ?? null];
    }
}

$engine = new TwigRenderer(__DIR__ . '/templates');
$router = new SimpleRouter($engine);

$router->register('/', 'Accueil');
$router->register('/book/:id', 'Book');
$router->register('/api/book/:id', 'BookApi');

$router->serve();
