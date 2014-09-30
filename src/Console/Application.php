<?php
namespace Icecave\Woodhouse\Console;

use Icecave\Woodhouse\PackageInfo;
use Symfony\Component\Console\Application as SymfonyApplication;

class Application extends SymfonyApplication
{
    /**
     * @param string $vendorPath The path to the composer vendor folder.
     */
    public function __construct($vendorPath)
    {
        $this->vendorPath = $vendorPath;

        parent::__construct(PackageInfo::NAME, PackageInfo::VERSION);

        $this->getHelperSet()->set(new Helper\HiddenInputHelper());
        $this->add(new Command\GitHub\CreateAuthorizationCommand());
        $this->add(new Command\GitHub\DeleteAuthorizationCommand());
        $this->add(new Command\GitHub\ListAuthorizationsCommand());
        $this->add(new Command\PublishCommand());
    }

    public function vendorPath()
    {
        return $this->vendorPath;
    }

    private $vendorPath;
}
