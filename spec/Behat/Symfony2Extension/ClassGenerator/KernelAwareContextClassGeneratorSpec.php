<?php

namespace spec\Behat\Symfony2Extension\ClassGenerator;

use PhpSpec\ObjectBehavior;

class KernelAwareContextClassGeneratorSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType('Behat\Symfony2Extension\ClassGenerator\KernelAwareContextClassGenerator');
    }

    function it_is_a_class_generator()
    {
        $this->shouldHaveType('Behat\Behat\Context\ClassGenerator\ContextClassGenerator');
    }

    function it_supports_any_class()
    {
        $this->supportsClassname('test\classname')->shouldBe(true);
    }

    function it_generates_classes_in_the_global_namespace()
    {
        $code = <<<'PHP'
<?php

use Behat\Behat\Context\TurnipAcceptingContext;
use Behat\Gherkin\Node\PyStringNode;
use Behat\Gherkin\Node\TableNode;
use Behat\Symfony2Extension\Context\KernelAwareContext;
use Symfony\Component\HttpKernel\KernelInterface;

/**
 * Behat context class.
 */
class TestContext implements TurnipAcceptingContext, KernelAwareContext
{
    /**
     * @var KernelInterface
     */
    private $kernel;

    /**
     * Initializes context. Every scenario gets it's own context object.
     *
     * @param array $parameters Suite parameters (set them up through behat.yml)
     */
    public function __construct(array $parameters)
    {
    }

    /**
     * Sets Kernel instance.
     * This method will be automatically called by Symfony2Extension ContextInitializer.
     *
     * @param KernelInterface $kernel
     */
    public function setKernel(KernelInterface $kernel)
    {
        $this->kernel = $kernel;
    }
}

PHP;
        $this->generateClass('TestContext')->shouldReturn($code);
    }

    function it_generates_namespaced_classes()
    {
        $code = <<<'PHP'
<?php

namespace Test;

use Behat\Behat\Context\TurnipAcceptingContext;
use Behat\Gherkin\Node\PyStringNode;
use Behat\Gherkin\Node\TableNode;
use Behat\Symfony2Extension\Context\KernelAwareContext;
use Symfony\Component\HttpKernel\KernelInterface;

/**
 * Behat context class.
 */
class TestContext implements TurnipAcceptingContext, KernelAwareContext
{
    /**
     * @var KernelInterface
     */
    private $kernel;

    /**
     * Initializes context. Every scenario gets it's own context object.
     *
     * @param array $parameters Suite parameters (set them up through behat.yml)
     */
    public function __construct(array $parameters)
    {
    }

    /**
     * Sets Kernel instance.
     * This method will be automatically called by Symfony2Extension ContextInitializer.
     *
     * @param KernelInterface $kernel
     */
    public function setKernel(KernelInterface $kernel)
    {
        $this->kernel = $kernel;
    }
}

PHP;
        $this->generateClass('Test\TestContext')->shouldReturn($code);
    }
}
