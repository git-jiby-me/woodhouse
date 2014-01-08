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
 * @covers \Icecave\Woodhouse\Console\Command\GitHub\ListAuthorizationsCommand
 * @covers \Icecave\Woodhouse\Console\Command\GitHub\AbstractGitHubCommand
 */
class ListAuthorizationsCommandTest extends PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $this->clientFactory = Phake::mock('Icecave\Woodhouse\GitHub\GitHubClientFactory');
        $this->client = Phake::mock('Icecave\Woodhouse\GitHub\GitHubClient');
        $this->command = Phake::partialMock(
            __NAMESPACE__ . '\ListAuthorizationsCommand',
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

        $this->authorization0 = new stdClass;
        $this->authorization0->id = 111;
        $this->authorization0->token = 'a1a1a1a1a1a1a1a1a1a1a1a1a1a1a1a1a1a1a1a1';
        $this->authorization0->scopes = array('pip', 'pop');
        $this->authorization0->app = new stdClass;
        $this->authorization0->app->name = 'foo';
        $this->authorization0->app->url = 'bar';
        $this->authorization0->note = null;
        $this->authorization0->note_url = null;
        $this->authorization1 = new stdClass;
        $this->authorization1->id = 222;
        $this->authorization1->token = 'b2b2b2b2b2b2b2b2b2b2b2b2b2b2b2b2b2b2b2b2';
        $this->authorization1->scopes = array();
        $this->authorization1->app = new stdClass;
        $this->authorization1->app->name = 'baz';
        $this->authorization1->app->url = 'qux';
        $this->authorization1->note = 'doom';
        $this->authorization1->note_url = 'splat';
        $this->authorizations = array(
            $this->authorization0,
            $this->authorization1,
        );

        Phake::when($this->clientFactory)
            ->create(Phake::anyParameters())
            ->thenReturn($this->client)
        ;
        Phake::when($this->client)
            ->authorizationsMatching(Phake::anyParameters())
            ->thenReturn($this->authorizations)
        ;

        $this->output = '';
        $this->outputInterface = Phake::mock('Symfony\Component\Console\Output\OutputInterface');
        $that = $this;
        Phake::when($this->outputInterface)
            ->writeln(Phake::anyParameters())
            ->thenGetReturnByLambda(
                function ($data) use ($that) {
                    $that->output .= $data . "\n";
                }
            );

        $this->expectedOutput = <<<'EOD'
111: a1a1a1a1a1a1a1a1a1a1a1a1a1a1a1a1a1a1a1a1 <info>foo [pip, pop]</info> <comment>bar</comment>
222: b2b2b2b2b2b2b2b2b2b2b2b2b2b2b2b2b2b2b2b2 <info>baz []</info> <comment>splat</comment>

EOD;
    }

    public function testConstructor()
    {
        $this->assertSame($this->clientFactory, $this->command->clientFactory());
    }

    public function testConstructorDefaults()
    {
        $this->command = new ListAuthorizationsCommand;

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
            'name',
            null,
            InputOption::VALUE_REQUIRED,
            'Restrict to authorizations with a specific name (PCRE pattern).'
        ));
        $expectedInputDefinition->addOption(new InputOption(
            'url',
            null,
            InputOption::VALUE_REQUIRED,
            'Restrict to authorizations with a specific URL (PCRE pattern).'
        ));

        $this->assertSame('github:list-auth', $this->command->getName());
        $this->assertSame('List current GitHub authorizations.', $this->command->getDescription());
        $this->assertEquals($expectedInputDefinition, $this->command->getDefinition());
    }

    public function testExecute()
    {
        $input = new StringInput('github:list-auth --username ping --password pong --name pang --url peng');
        $exitCode = $this->command->run($input, $this->outputInterface);

        $this->assertSame(0, $exitCode);
        $this->assertSame($this->expectedOutput, $this->output);
        Phake::inOrder(
            Phake::verify($this->clientFactory)->create('ping', 'pong'),
            Phake::verify($this->client)->authorizationsMatching('pang', 'peng')
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
        $input = new StringInput('github:list-auth --name pang --url peng');
        $exitCode = $this->command->run($input, $this->outputInterface);

        $this->assertSame(0, $exitCode);
        $this->assertSame($this->expectedOutput, $this->output);
        Phake::inOrder(
            Phake::verify($this->clientFactory)->create('ping', 'pong'),
            Phake::verify($this->client)->authorizationsMatching('pang', 'peng')
        );
    }

    public function testExecuteOverridePatterns()
    {
        $input = new StringInput('github:list-auth --username ping --password pong --name pang');
        $exitCode = $this->command->run($input, $this->outputInterface);

        $this->assertSame(0, $exitCode);
        $this->assertSame($this->expectedOutput, $this->output);
        Phake::inOrder(
            Phake::verify($this->clientFactory)->create('ping', 'pong'),
            Phake::verify($this->client)->authorizationsMatching('pang', null)
        );
    }

    public function testExecuteDefaultPatterns()
    {
        $input = new StringInput('github:list-auth --username ping --password pong');
        $exitCode = $this->command->run($input, $this->outputInterface);

        $this->assertSame(0, $exitCode);
        $this->assertSame($this->expectedOutput, $this->output);
        Phake::inOrder(
            Phake::verify($this->clientFactory)->create('ping', 'pong'),
            Phake::verify($this->client)->authorizationsMatching(
                null,
                '/^https\:\/\/github\.com\/IcecaveStudios\/woodhouse$/'
            )
        );
    }
}
