<?php
namespace Icecave\Woodhouse\Console;

use Icecave\Woodhouse\TypeCheck\TypeCheck;
use Symfony\Component\Console\Application as SymfonyApplication;

class Application extends SymfonyApplication
{
    public function __construct() {
        $this->typeCheck = TypeCheck::get(__CLASS__, func_get_args());

        parent::__construct('Typhoon', 'DEV');

        // $this->add(new Command\PublishCommand);
    }

    private $typeCheck;
}
