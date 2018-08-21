<?= "<?php\n" ?>

namespace <?= $namespace; ?>;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Doctrine\ORM\EntityManagerInterface;

class <?= $class_name; ?> extends Controller
{

    protected $em;

    /**
     *
     */
    public function __construct(EntityManagerInterface $em)
    {
        // add your dependencies
        $this->em = $em;
    }

    /**
     * @Route("<?= $route_path ?>", name="<?= $route_name ?>")
     */
    public function __invoke()
    {
<?php if ($twig_installed) {
    ?>
        return $this->render('<?= $template_name ?>', [
            'controller_name' => '<?= $class_name ?>',
        ]);
<?php
} else {
        ?>
        return $this->json([
            'message' => 'Welcome to your new controller!',
            'path' => '<?= $relative_path; ?>',
        ]);
<?php
    } ?>
    }
}
