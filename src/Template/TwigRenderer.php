<?php
namespace Framework312\Template;

use Framework312\Template\Renderer;
use Twig\Environment;
use Twig\Loader\FilesystemLoader;  
class TwigRenderer implements Renderer {
    private $loader;
    private $twig;
    private $mainPath;
    public function __construct(string $templatesPath) { 
        $this->loader = new FilesystemLoader($templatesPath);
        $this->twig = new Environment($this->loader);
        $this->mainPath = $templatesPath;
        $this->loader->addPath($templatesPath,"global");
    }
    public function render(mixed $data, string $template) : string {
        return $this->twig->render($template, $data);
    }
    public function register(string $tag) : void {
        // 'App\Views\Book' -> 'Book' : le dossier porte le nom court de la classe
        $tag = substr(strrchr('\\' . $tag, '\\'), 1);
        $path = $this->mainPath . '/' . $tag;

        // @Book/show.twig -> templates/Book/show.twig
        $this->loader->addPath($path, $tag);
        // show.twig -> cherché d'abord dans templates/Book/, puis dans templates/
        $this->loader->prependPath($path);
    }
}
?>