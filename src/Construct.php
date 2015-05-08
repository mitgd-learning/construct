<?php namespace JonathanTorres\Construct;

use Illuminate\Filesystem\Filesystem;
use JonathanTorres\Construct\Str;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class Construct extends Command
{

    /**
     * The filesystem instance.
     *
     * @var \Illuminate\Filesystem\Filesystem
     **/
    protected $file;

    /**
     * String helper.
     *
     * @var \JonathanTorres\Construct\Str
     **/
    protected $str;

    /**
     * Folder to store source files.
     *
     * @var string
     **/
    protected $srcPath = 'src';

    /**
     * Entered project name.
     *
     * @var string
     **/
    protected $projectName;

    /**
     * Camel case version of vendor name.
     * ex: JonathanTorres
     *
     * @var string
     **/
    protected $vendorUpper;

    /**
     * Lower case version of vendor name.
     * ex: jonathantorres
     *
     * @var string
     **/
    protected $vendorLower;

    /**
     * Camel case version of project name.
     * ex: Construct
     *
     * @var string
     **/
    protected $projectUpper;

    /**
     * Lower case version of project name.
     * ex: construct
     *
     * @var string
     **/
    protected $projectLower;

    /**
     * Initialize.
     *
     * @param \Illuminate\Filesystem\Filesystem $file
     * @param \JonathanTorres\Construct\Str $str
     *
     * @return void
     **/
    public function __construct(Filesystem $file, Str $str)
    {
        parent::__construct();

        $this->file = $file;
        $this->str = $str;
    }

    /**
     * Command configuration.
     *
     * @return void
     **/
    protected function configure()
    {
        $this->setName('generate');
        $this->setDescription('Generate a basic PHP project.');
        $this->addArgument('name', InputArgument::REQUIRED, 'The vendor/project name.');
    }

    /**
     * Execute command.
     *
     * @param \Symfony\Component\Console\Input\InputInterface $input
     * @param \Symfony\Component\Console\Output\OutputInterface $output
     *
     * @return void
     **/
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->projectName = $input->getArgument('name');

        if (!$this->str->isValid($this->projectName)) {
            $output->writeln('"' . $this->projectName . '" is not a valid project name, please use "vendor/project"');
            return false;
        }

        $this->saveNames();
        $this->root();
        $this->src();
        $this->readme();
        $this->gitignore();
        $this->phpunit();
        $this->travis();
        $this->composer();
        $this->projectClass();
        $this->projectTest();

        $output->writeln('Project "' . $this->projectName . '" created.');
    }

    /**
     * Save versions of project names.
     *
     * @return void
     **/
    protected function saveNames()
    {
        $names = $this->str->split($this->projectName);

        $this->vendorLower = $this->str->toLower($names['vendor']);
        $this->vendorUpper = $this->str->toStudly($names['vendor']);
        $this->projectLower = $this->str->toLower($names['project']);
        $this->projectUpper = $this->str->toStudly($names['project']);
    }

    /**
     * Create project root folder.
     *
     * @return void
     **/
    protected function root()
    {
        $this->file->makeDirectory($this->projectLower);
    }

    /**
     * Create 'src' folder.
     *
     * @return void
     **/
    protected function src()
    {
        $this->file->makeDirectory($this->projectLower . '/' . $this->srcPath);
    }

    /**
     * Generate gitignore file.
     *
     * @return void
     **/
    protected function gitignore()
    {
        $this->file->copy(__DIR__ . '/stubs/gitignore.txt', $this->projectLower . '/' . '/.gitignore');
    }

    /**
     * Generate README file.
     *
     * @return void
     **/
    protected function readme()
    {
        $file = $this->file->get(__DIR__ . '/stubs/README.txt');
        $content = str_replace('{project_upper}', $this->projectUpper, $file);

        $this->file->put($this->projectLower . '/' . '/README.md', $content);
    }

    /**
     * Generate phpunit file.
     *
     * @return void
     **/
    protected function phpunit()
    {
        $file = $this->file->get(__DIR__ . '/stubs/phpunit.txt');
        $content = str_replace('{project_upper}', $this->projectUpper, $file);

        $this->file->put($this->projectLower . '/' . '/phpunit.xml', $content);
    }

    /**
     * Generate .travis.yml file.
     *
     * @return void
     **/
    protected function travis()
    {
        $this->file->copy(__DIR__ . '/stubs/travis.txt', $this->projectLower . '/' . '/.travis.yml');
    }

    /**
     * Generate composer file.
     *
     * @return void
     **/
    protected function composer()
    {
        $file = $this->file->get(__DIR__ . '/stubs/composer.txt');
        $stubs = ['{project_upper}', '{project_lower}', '{vendor_lower}', '{vendor_upper}'];
        $values = [$this->projectUpper, $this->projectLower, $this->vendorLower, $this->vendorUpper];

        $content = str_replace($stubs, $values, $file);

        $this->file->put($this->projectLower . '/' . '/composer.json', $content);
    }

    /**
     * Generate project class file.
     *
     * @return void
     **/
    protected function projectClass()
    {
        $file = $this->file->get(__DIR__ . '/stubs/Project.txt');
        $content = str_replace(['{project_upper}', '{vendor_upper}'], [$this->projectUpper, $this->vendorUpper], $file);

        $this->file->put($this->projectLower . '/' . $this->srcPath . '/' . $this->projectUpper . '.php', $content);
    }

    /**
     * Generate project test file.
     *
     * @return void
     **/
    protected function projectTest()
    {
        $file = $this->file->get(__DIR__ . '/stubs/ProjectTest.txt');
        $stubs = ['{project_upper}', '{project_lower}', '{vendor_upper}'];
        $values = [$this->projectUpper, $this->projectLower, $this->vendorUpper];

        $content = str_replace($stubs, $values, $file);

        $this->file->makeDirectory($this->projectLower . '/tests');
        $this->file->put($this->projectLower . '/tests/' . $this->projectUpper . 'Test.php', $content);
    }
}