<?php
namespace Icecave\Woodhouse\Console\Command\GitHub;

use Icecave\Woodhouse\Console\Application;
use PHPUnit_Framework_TestCase;
use Phake;
use stdClass;
use Symfony\Component\Console\Input\InputDefinition;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\StringInput;

/**
 * @covers \Icecave\Woodhouse\Console\Command\GitHub\CreateAuthorizationCommand
 * @covers \Icecave\Woodhouse\Console\Command\GitHub\AbstractGitHubCommand
 */
class CreateAuthorizationCommandTest extends PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $this->_clientFactory = Phake::mock('Icecave\Woodhouse\GitHub\GitHubClientFactory');
        $this->_client = Phake::mock('Icecave\Woodhouse\GitHub\GitHubClient');
        $this->_command = Phake::partialMock(
            __NAMESPACE__ . '\CreateAuthorizationCommand',
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

        $this->_authorization = new stdClass;
        $this->_authorization->id = 111;
        $this->_authorization->token = 'a1a1a1a1a1a1a1a1a1a1a1a1a1a1a1a1a1a1a1a1';
        $this->_authorization->scopes = array('pip', 'pop');
        $this->_authorization->app = new stdClass;
        $this->_authorization->app->name = 'foo';
        $this->_authorization->app->url = 'bar';
        $this->_authorization->note = null;
        $this->_authorization->note_url = null;

        Phake::when($this->_clientFactory)
            ->create(Phake::anyParameters())
            ->thenReturn($this->_client)
        ;
        Phake::when($this->_client)
            ->createAuthorization(Phake::anyParameters())
            ->thenReturn($this->_authorization)
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
111: a1a1a1a1a1a1a1a1a1a1a1a1a1a1a1a1a1a1a1a1 <info>foo [pip, pop]</info> <comment>bar</comment>

EOD;
    }

    public function testConstructor()
    {
        $this->assertSame($this->_clientFactory, $this->_command->clientFactory());
    }

    public function testConstructorDefaults()
    {
        $this->_command = new CreateAuthorizationCommand;

        $this->assertInstanceOf(
            'Icecave\Woodhouse\GitHub\GitHubClientFactory',
            $this->_command->clientFactory()
        );
    }

    public function testClientUserAgent()
    {
        Phake::verify($this->_clientFactory)->setUserAgent($this->_application->getName() . '/' . $this->_application->getVersion());
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
        $expectedInputDefinition->addOption(new InputOption(
            'scope',
            's',
            InputOption::VALUE_IS_ARRAY | InputOption::VALUE_OPTIONAL,
            'One or more access scopes.',
            array('repo')
        ));
        $expectedInputDefinition->addOption(new InputOption(
            'name',
            null,
            InputOption::VALUE_REQUIRED,
            'The name to use when creating the authorization.'
        ));
        $expectedInputDefinition->addOption(new InputOption(
            'url',
            null,
            InputOption::VALUE_REQUIRED,
            'The URL to use when creating the authorization.'
        ));

        $this->assertSame('github:create-auth', $this->_command->getName());
        $this->assertSame('Create a new GitHub authorization.', $this->_command->getDescription());
        $this->assertEquals($expectedInputDefinition, $this->_command->getDefinition());
    }

    public function testExecute()
    {
        $input = new StringInput('github:create-auth --username ping --password pong --scope pip --scope pop --name pang --url peng');
        $exitCode = $this->_command->run($input, $this->_outputInterface);

        $this->assertSame(0, $exitCode);
        $this->assertSame($this->_expectedOutput, $this->_output);
        Phake::inOrder(
            Phake::verify($this->_clientFactory)->create('ping', 'pong'),
            Phake::verify($this->_client)->createAuthorization(
                array('pip', 'pop'),
                'pang',
                'peng'
            )
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
        $input = new StringInput('github:create-auth --scope pip --scope pop --name pang --url peng');
        $exitCode = $this->_command->run($input, $this->_outputInterface);

        $this->assertSame(0, $exitCode);
        $this->assertSame($this->_expectedOutput, $this->_output);
        Phake::inOrder(
            Phake::verify($this->_clientFactory)->create('ping', 'pong'),
            Phake::verify($this->_client)->createAuthorization(
                array('pip', 'pop'),
                'pang',
                'peng'
            )
        );
    }

    public function testExecuteOverrideDetails()
    {
        $input = new StringInput('github:create-auth --username ping --password pong --name pang');
        $exitCode = $this->_command->run($input, $this->_outputInterface);

        $this->assertSame(0, $exitCode);
        $this->assertSame($this->_expectedOutput, $this->_output);
        Phake::inOrder(
            Phake::verify($this->_clientFactory)->create('ping', 'pong'),
            Phake::verify($this->_client)->createAuthorization(
                array('repo'),
                'pang',
                null
            )
        );
    }

    public function testExecuteDefaultDetails()
    {
        $input = new StringInput('github:create-auth --username ping --password pong');
        $exitCode = $this->_command->run($input, $this->_outputInterface);

        $this->assertSame(0, $exitCode);
        $this->assertSame($this->_expectedOutput, $this->_output);
        Phake::inOrder(
            Phake::verify($this->_clientFactory)->create('ping', 'pong'),
            Phake::verify($this->_client)->createAuthorization(
                array('repo'),
                'Woodhouse',
                'https://github.com/IcecaveStudios/woodhouse'
            )
        );
    }
}
