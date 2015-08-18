<?php

namespace Icecave\Woodhouse\Console\Command\GitHub;

use Icecave\Woodhouse\Console\Application;
use Phake;
use PHPUnit_Framework_TestCase;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputDefinition;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\StringInput;

/**
 * @covers \Icecave\Woodhouse\Console\Command\GitHub\DeleteAuthorizationCommand
 * @covers \Icecave\Woodhouse\Console\Command\GitHub\AbstractGitHubCommand
 */
class DeleteAuthorizationCommandTest extends PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $this->clientFactory = Phake::mock('Icecave\Woodhouse\GitHub\GitHubClientFactory');
        $this->client = Phake::mock('Icecave\Woodhouse\GitHub\GitHubClient');
        $this->command = Phake::partialMock(
            __NAMESPACE__ . '\DeleteAuthorizationCommand',
            $this->clientFactory
        );

        $this->application = Phake::partialMock(
            'Icecave\Woodhouse\Console\Application',
            '/path/to/vendors'
        );
        $this->command->setApplication($this->application);

        $this->helperSet = Phake::mock('Symfony\Component\Console\Helper\HelperSet');
        $this->dialogHelper = Phake::mock('Symfony\Component\Console\Helper\DialogHelper');
        $this->hiddenInputHelper = Phake::mock('Icecave\Woodhouse\Console\Helper\HiddenInputHelper');
        Phake::when($this->helperSet)
            ->get('dialog')
            ->thenReturn($this->dialogHelper);
        Phake::when($this->helperSet)
            ->get('hidden-input')
            ->thenReturn($this->hiddenInputHelper);
        $this->command->setHelperSet($this->helperSet);

        Phake::when($this->clientFactory)
            ->create(Phake::anyParameters())
            ->thenReturn($this->client);

        $this->output = '';
        $this->outputInterface = Phake::mock('Symfony\Component\Console\Output\OutputInterface');
        $that = $this;
        Phake::when($this->outputInterface)
            ->writeln(Phake::anyParameters())
            ->thenReturnCallback(
                function ($data) use ($that) {
                    $that->output .= $data . "\n";
                }
            );

        $this->expectedOutput = <<<'EOD'
Authorization deleted.

EOD;
    }

    public function testConstructor()
    {
        $this->assertSame($this->clientFactory, $this->command->clientFactory());
    }

    public function testConstructorDefaults()
    {
        $this->command = new DeleteAuthorizationCommand();

        $this->assertInstanceOf(
            'Icecave\Woodhouse\GitHub\GitHubClientFactory',
            $this->command->clientFactory()
        );
    }

    public function testClientUserAgent()
    {
        Phake::verify($this->clientFactory)->setUserAgent($this->application->getName() . '/' . $this->application->getVersion());
    }

    public function testConfigure()
    {
        $expectedInputDefinition = new InputDefinition();
        $expectedInputDefinition->addOption(new InputOption(
            'username',
            'u',
            InputOption::VALUE_REQUIRED,
            'A GitHub username to use for API authentication.'
        ));
        $expectedInputDefinition->addOption(new InputOption(
            'password',
            'p',
            InputOption::VALUE_REQUIRED,
            'A GitHub password to use for API authentication.'
        ));
        $expectedInputDefinition->addArgument(new InputArgument(
            'id',
            InputArgument::REQUIRED,
            'The ID of the authorization to delete.'
        ));

        $this->assertSame('github:delete-auth', $this->command->getName());
        $this->assertSame('Delete a GitHub authorization.', $this->command->getDescription());
        $this->assertEquals($expectedInputDefinition, $this->command->getDefinition());
    }

    public function testExecute()
    {
        $input = new StringInput('github:delete-auth 111 --username ping --password pong');
        $exitCode = $this->command->run($input, $this->outputInterface);

        $this->assertSame(0, $exitCode);
        $this->assertSame($this->expectedOutput, $this->output);
        Phake::inOrder(
            Phake::verify($this->clientFactory)->create('ping', 'pong'),
            Phake::verify($this->client)->deleteAuthorization(111)
        );
    }

    public function testExecuteInteractiveCredentials()
    {
        Phake::when($this->dialogHelper)
            ->ask(Phake::anyParameters())
            ->thenReturn('ping');
        Phake::when($this->hiddenInputHelper)
            ->askHiddenResponse(Phake::anyParameters())
            ->thenReturn('pong');
        $input = new StringInput('github:delete-auth 111');
        $exitCode = $this->command->run($input, $this->outputInterface);

        $this->assertSame(0, $exitCode);
        $this->assertSame($this->expectedOutput, $this->output);
        Phake::inOrder(
            Phake::verify($this->clientFactory)->create('ping', 'pong'),
            Phake::verify($this->client)->deleteAuthorization(111)
        );
    }
}
