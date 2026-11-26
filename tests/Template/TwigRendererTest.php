<?php declare(strict_types=1);

namespace Framework312\Tests\Template;

use Framework312\Template\TwigRenderer;
use PHPUnit\Framework\TestCase;
use Twig\Error\LoaderError;

final class TwigRendererTest extends TestCase
{
    private string $dossier;

    // Avant chaque test : un dossier de templates
    //   templates/accueil.twig
    //   templates/Book/show.twig
    protected function setUp(): void
    {
        $this->dossier = sys_get_temp_dir() . '/twig_test_' . uniqid();
        mkdir($this->dossier . '/Book', 0777, true);
        file_put_contents($this->dossier . '/accueil.twig', 'Bonjour {{ nom }}');
        file_put_contents($this->dossier . '/Book/show.twig', 'Livre {{ titre }}');
    }

    // Après chaque test : on supprime le dossier de templates
    protected function tearDown(): void
    {
        unlink($this->dossier . '/Book/show.twig');
        unlink($this->dossier . '/accueil.twig');
        rmdir($this->dossier . '/Book');
        rmdir($this->dossier);
    }

    // ---------- __construct() ----------
    // Teste que le constructeur crée bien un TwigRenderer avec un dossier existant
    public function testConstructeur1(): void
    {
        $renderer = new TwigRenderer($this->dossier);

        $this->assertInstanceOf(TwigRenderer::class, $renderer);
    }
    // Teste que le constructeur renvoie une erreur avec un dossier inexistant
    public function testConstructeur2(): void
    {
        $this->expectException(LoaderError::class);

        new TwigRenderer($this->dossier . '/inexistant');
    }

    // ---------- render() ----------
    // Vérifie que render() remplit correctement le template avec les données fournies
    public function testRender1(): void
    {
        $renderer = new TwigRenderer($this->dossier);

        $html = $renderer->render(['nom' => 'Mael'], 'accueil.twig');

        $this->assertSame('Bonjour Mael', $html);
    }
    // Vérifie que render() lève une erreur si le template n'existe pas
    public function testRender2(): void
    {
        $renderer = new TwigRenderer($this->dossier);

        $this->expectException(LoaderError::class);

        $renderer->render([], 'inexistant.twig');
    }

    // ---------- register() ----------
    // Vérifie que register() rend les templates du sous-dossier accessibles
    public function testRegister1(): void
    {
        $renderer = new TwigRenderer($this->dossier);

        $renderer->register('Book');

        $this->assertSame('Livre Dune', $renderer->render(['titre' => 'Dune'], '@Book/show.twig'));
    }
    // Vérifie que register() lève une erreur si le sous-dossier n'existe pas
    public function testRegister2(): void
    {
        $renderer = new TwigRenderer($this->dossier);

        $this->expectException(LoaderError::class);

        $renderer->register('Auteur');
    }
}