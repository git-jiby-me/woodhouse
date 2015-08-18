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
        $this->clientFactory = Phake::mock('Icecave\Woodhouse\GitHub\GitHubClientFactory');
        $this->client = Phake::mock('Icecave\Woodhouse\GitHub\GitHubClient');
        $this->command = Phake::partialMock(
            __NAMESPACE__ . '\CreateAuthorizationCommand',
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
            ->thenReturn($this->dialogHelper)
        ;
        Phake::when($this->helperSet)
            ->get('hidden-input')
            ->thenReturn($this->hiddenInputHelper)
        ;
        $this->command->setHelperSet($this->helperSet);

        $this->authorization = new stdClass();
        $this->authorization->id = 111;
        $this->authorization->token = 'a1a1a1a1a1a1a1a1a1a1a1a1a1a1a1a1a1a1a1a1';
        $this->authorization->scopes = array('pip', 'pop');
        $this->authorization->app = new stdClass();
        $this->authorization->app->name = 'foo';
        $this->authorization->app->url = 'bar';
        $this->authorization->note = null;
        $this->authorization->note_url = null;

        Phake::when($this->clientFactory)
            ->create(Phake::anyParameters())
            ->thenReturn($this->client)
        ;
        Phake::when($this->client)
            ->createAuthorization(Phake::anyParameters())
            ->thenReturn($this->authorization)
        ;

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
111: a1a1a1a1a1a1a1a1a1a1a1a1a1a1a1a1a1a1a1a1 <info>foo [pip, pop]</info> <comment>bar</comment>

EOD;
    }

    public function testConstructor()
    {
        $this->assertSame($this->clientFactory, $this->command->clientFactory());
    }

    public function testConstructorDefaults()
    {
        $this->command = new CreateAuthorizationCommand();

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

        $this->assertSame('github:create-auth', $this->command->getName());
        $this->assertSame('Create a new GitHub authorization.', $this->command->getDescription());
        $this->assertEquals($expectedInputDefinition, $this->command->getDefinition());
    }

    public function testExecute()
    {
        $input = new StringInput('github:create-auth --username ping --password pong --scope pip --scope pop --name pang --url peng');
        $exitCode = $this->command->run($input, $this->outputInterface);

        $this->assertSame(0, $exitCode);
        $this->assertSame($this->expectedOutput, $this->output);
        Phake::inOrder(
            Phake::verify($this->clientFactory)->create('ping', 'pong'),
            Phake::verify($this->client)->createAuthorization(
                array('pip', 'pop'),
                'pang',
                'peng'
            )
        );
    }

    public function testExecuteInteractiveCredentials()
    {
        Phake::when($this->dialogHelper)
            ->ask(Phake::anyParameters())
            ->thenReturn('ping')
        ;
        Phake::when($this->hiddenInputHelper)
            ->askHiddenResponse(Phake::anyParameters())
            ->thenReturn('pong')
        ;
        $input = new StringInput('github:create-auth --scope pip --scope pop --name pang --url peng');
        $exitCode = $this->command->run($input, $this->outputInterface);

        $this->assertSame(0, $exitCode);
        $this->assertSame($this->expectedOutput, $this->output);
        Phake::inOrder(
            Phake::verify($this->clientFactory)->create('ping', 'pong'),
            Phake::verify($this->client)->createAuthorization(
                array('pip', 'pop'),
                'pang',
                'peng'
            )
        );
    }

    public function testExecuteOverrideDetails()
    {
        $input = new StringInput('github:create-auth --username ping --password pong --name pang');
        $exitCode = $this->command->run($input, $this->outputInterface);

        $this->assertSame(0, $exitCode);
        $this->assertSame($this->expectedOutput, $this->output);
        Phake::inOrder(
            Phake::verify($this->clientFactory)->create('ping', 'pong'),
            Phake::verify($this->client)->createAuthorization(
                array('repo'),
                'pang',
                null
            )
        );
    }

    public function testExecuteDefaultDetails()
    {
        $input = new StringInput('github:create-auth --username ping --password pong');
        $exitCode = $this->command->run($input, $this->outputInterface);

        $this->assertSame(0, $exitCode);
        $this->assertSame($this->expectedOutput, $this->output);
        Phake::inOrder(
            Phake::verify($this->clientFactory)->create('ping', 'pong'),
            Phake::verify($this->client)->createAuthorization(
                array('repo'),
                'Woodhouse',
                'https://github.com/IcecaveStudios/woodhouse'
            )
        );
    }
}
