<?php
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
        $this->loader->addPath($this->mainPath.'/'.$tag, $tag);
    }
}
?>