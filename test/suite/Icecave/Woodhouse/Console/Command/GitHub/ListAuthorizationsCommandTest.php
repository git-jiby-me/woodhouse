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
        $this->_clientFactory = Phake::mock('Icecave\Woodhouse\GitHub\GitHubClientFactory');
        $this->_client = Phake::mock('Icecave\Woodhouse\GitHub\GitHubClient');
        $this->_command = Phake::partialMock(
            __NAMESPACE__ . '\ListAuthorizationsCommand',
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

        $this->_authorization0 = new stdClass;
        $this->_authorization0->id = 111;
        $this->_authorization0->token = 'a1a1a1a1a1a1a1a1a1a1a1a1a1a1a1a1a1a1a1a1';
        $this->_authorization0->scopes = array('pip', 'pop');
        $this->_authorization0->app = new stdClass;
        $this->_authorization0->app->name = 'foo';
        $this->_authorization0->app->url = 'bar';
        $this->_authorization0->note = null;
        $this->_authorization0->note_url = null;
        $this->_authorization1 = new stdClass;
        $this->_authorization1->id = 222;
        $this->_authorization1->token = 'b2b2b2b2b2b2b2b2b2b2b2b2b2b2b2b2b2b2b2b2';
        $this->_authorization1->scopes = array();
        $this->_authorization1->app = new stdClass;
        $this->_authorization1->app->name = 'baz';
        $this->_authorization1->app->url = 'qux';
        $this->_authorization1->note = 'doom';
        $this->_authorization1->note_url = 'splat';
        $this->_authorizations = array(
            $this->_authorization0,
            $this->_authorization1,
        );

        Phake::when($this->_clientFactory)
            ->create(Phake::anyParameters())
            ->thenReturn($this->_client)
        ;
        Phake::when($this->_client)
            ->authorizationsMatching(Phake::anyParameters())
            ->thenReturn($this->_authorizations)
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
222: b2b2b2b2b2b2b2b2b2b2b2b2b2b2b2b2b2b2b2b2 <info>baz []</info> <comment>splat</comment>

EOD;
    }

    public function testConstructor()
    {
        $this->assertSame($this->_clientFactory, $this->_command->clientFactory());
    }

    public function testConstructorDefaults()
    {
        $this->_command = new ListAuthorizationsCommand;

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

        $this->assertSame('github:list-auth', $this->_command->getName());
        $this->assertSame('List current GitHub authorizations.', $this->_command->getDescription());
        $this->assertEquals($expectedInputDefinition, $this->_command->getDefinition());
    }

    public function testExecute()
    {
        $input = new StringInput('github:list-auth --username ping --password pong --name pang --url peng');
        $exitCode = $this->_command->run($input, $this->_outputInterface);

        $this->assertSame(0, $exitCode);
        $this->assertSame($this->_expectedOutput, $this->_output);
        Phake::inOrder(
            Phake::verify($this->_clientFactory)->create('ping', 'pong'),
            Phake::verify($this->_client)->authorizationsMatching('pang', 'peng')
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
        $input = new StringInput('github:list-auth --name pang --url peng');
        $exitCode = $this->_command->run($input, $this->_outputInterface);

        $this->assertSame(0, $exitCode);
        $this->assertSame($this->_expectedOutput, $this->_output);
        Phake::inOrder(
            Phake::verify($this->_clientFactory)->create('ping', 'pong'),
            Phake::verify($this->_client)->authorizationsMatching('pang', 'peng')
        );
    }

    public function testExecuteOverridePatterns()
    {
        $input = new StringInput('github:list-auth --username ping --password pong --name pang');
        $exitCode = $this->_command->run($input, $this->_outputInterface);

        $this->assertSame(0, $exitCode);
        $this->assertSame($this->_expectedOutput, $this->_output);
        Phake::inOrder(
            Phake::verify($this->_clientFactory)->create('ping', 'pong'),
            Phake::verify($this->_client)->authorizationsMatching('pang', null)
        );
    }

    public function testExecuteDefaultPatterns()
    {
        $input = new StringInput('github:list-auth --username ping --password pong');
        $exitCode = $this->_command->run($input, $this->_outputInterface);

        $this->assertSame(0, $exitCode);
        $this->assertSame($this->_expectedOutput, $this->_output);
        Phake::inOrder(
            Phake::verify($this->_clientFactory)->create('ping', 'pong'),
            Phake::verify($this->_client)->authorizationsMatching(
                null,
                '/^https\:\/\/github\.com\/IcecaveStudios\/woodhouse$/'
            )
        );
    }
}
