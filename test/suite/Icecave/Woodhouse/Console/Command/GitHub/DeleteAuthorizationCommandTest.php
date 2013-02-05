<?php
namespace Icecave\Woodhouse\Console\Command\GitHub;

use Icecave\Woodhouse\Console\Application;
use PHPUnit_Framework_TestCase;
use Phake;
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
        $this->_clientFactory = Phake::mock('Icecave\Woodhouse\GitHub\GitHubClientFactory');
        $this->_client = Phake::mock('Icecave\Woodhouse\GitHub\GitHubClient');
        $this->_command = Phake::partialMock(
            __NAMESPACE__ . '\DeleteAuthorizationCommand',
            $this->_clientFactory
        );

        $this->_application = Phake::partialMock(
            'Icecave\Woodhouse\Console\Application',
            '/path/to/vendors'
        );
        $this->_command->setApplication($this->_application);

        $this->_helperSet = Phake::mock('Symfony\Component\Console\Helper\HelperSet');
        $this->_dialogHelper = Phake::mock('Symfony\Component\Console\Helper\DialogHelper');
        $this->_hiddenInputHelper = Phake::mock('Icecave\Woodhouse\Console\Helper\HiddenInputHelper');
        Phake::when($this->_helperSet)
            ->get('dialog')
            ->thenReturn($this->_dialogHelper)
        ;
        Phake::when($this->_helperSet)
            ->get('hidden-input')
            ->thenReturn($this->_hiddenInputHelper)
        ;
        $this->_command->setHelperSet($this->_helperSet);

        Phake::when($this->_clientFactory)
            ->create(Phake::anyParameters())
            ->thenReturn($this->_client)
        ;

        $this->_output = '';
        $this->_outputInterface = Phake::mock('Symfony\Component\Console\Output\OutputInterface');
        $that = $this;
        Phake::when($this->_outputInterface)
            ->writeln(Phake::anyParameters())
            ->thenGetReturnByLambda(function ($data) use ($that) {
                $that->_output .= $data . "\n";
            })
        ;

        $this->_expectedOutput = <<<'EOD'
Authorization deleted.

EOD;
    }

    public function testConstructor()
    {
        $this->assertSame($this->_clientFactory, $this->_command->clientFactory());
    }

    public function testConstructorDefaults()
    {
        $this->_command = new DeleteAuthorizationCommand;

        $this->assertInstanceOf(
            'Icecave\Woodhouse\GitHub\GitHubClientFactory',
            $this->_command->clientFactory()
        );
    }

    public function testConfigure()
    {
        $expectedInputDefinition = new InputDefinition;
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

        $this->assertSame('github:delete-auth', $this->_command->getName());
        $this->assertSame('Delete a GitHub authorization.', $this->_command->getDescription());
        $this->assertEquals($expectedInputDefinition, $this->_command->getDefinition());
    }

    public function testExecute()
    {
        $input = new StringInput('github:delete-auth 111 --username ping --password pong');
        $exitCode = $this->_command->run($input, $this->_outputInterface);

        $this->assertSame(0, $exitCode);
        $this->assertSame($this->_expectedOutput, $this->_output);
        Phake::inOrder(
            Phake::verify($this->_clientFactory)->create('ping', 'pong'),
            Phake::verify($this->_client)->deleteAuthorization(111)
        );
    }

    public function testExecuteInteractiveCredentials()
    {
        Phake::when($this->_dialogHelper)
            ->ask(Phake::anyParameters())
            ->thenReturn('ping')
        ;
        Phake::when($this->_hiddenInputHelper)
            ->askHiddenResponse(Phake::anyParameters())
            ->thenReturn('pong')
        ;
        $input = new StringInput('github:delete-auth 111');
        $exitCode = $this->_command->run($input, $this->_outputInterface);

        $this->assertSame(0, $exitCode);
        $this->assertSame($this->_expectedOutput, $this->_output);
        Phake::inOrder(
            Phake::verify($this->_clientFactory)->create('ping', 'pong'),
            Phake::verify($this->_client)->deleteAuthorization(111)
        );
    }
}
