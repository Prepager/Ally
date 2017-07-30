<?php

namespace ZapsterStudios\Ally\Tests\Command;

use org\bovigo\vfs\vfsStream as Stream;
use ZapsterStudios\Ally\Tests\TestCase;

class InstallationTest extends TestCase
{
    /**
     * root directory.
     *
     * @var vfsStreamDirectory
     */
    protected $root;

    /**
     * Set up VFS stream.
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();

        $structure = [
            '.env' => 'ENV_EXAMPLE=false',
            '.env.example' => 'ENV_EXAMPLE=true',

            'routes' => [],
            'factories' => [],
            'migrations' => [],
            'Exceptions' => [],
        ];

        $this->root = Stream::setup('tests', null, $structure);
    }

    /**
     * Get the installation stubs folder dir.
     *
     * @param  string  $path
     * @return string
     */
    protected function stubContent($path)
    {
        $base = explode('/tests/', __DIR__)[0].'/install-stubs/';

        return file_get_contents(realpath($base.$path));
    }

    /**
     * Assert file was published.
     *
     * @param  string  $file
     * @param  string  $content
     * @param  bool  $raw
     * @return void
     */
    public function assertPublished($file, $content, $raw = false)
    {
        $root = $this->root;

        $this->assertTrue($root->hasChild($file));
        $this->assertSame($root->getChild($file)->getContent(), ($raw ? $content : $this->stubContent($content)));
    }

    /**
     * @test
     * @group Command
     */
    public function canRunInstallation()
    {
        $this->artisan('ally:install', [
            '--force' => true,
            '--testing' => Stream::url('tests'),
        ]);

        // PublishConfig
        $this->assertPublished('auth.php', 'config/auth.php');
        $this->assertPublished('services.php', 'config/services.php');

        // PublishDatabase
        $dbUsers = '2014_10_12_000000_create_users_table.php';
        $dbTeams = '2017_06_08_165123_create_teams_table.php';

        $this->assertPublished('migrations/'.$dbUsers, 'database/migrations/'.$dbUsers);
        $this->assertPublished('migrations/'.$dbTeams, 'database/migrations/'.$dbTeams);
        $this->assertPublished('factories/ModelFactory.php', 'database/factories/ModelFactory.php');

        // PublishEnv
        $envFile = PHP_EOL.PHP_EOL;
        $envFile .= file_get_contents(realpath(__DIR__.'/../../.env.example'));

        $this->assertPublished('.env', 'ENV_EXAMPLE=false'.$envFile, true);
        $this->assertPublished('.env.example', 'ENV_EXAMPLE=true'.$envFile, true);

        // PublishExceptions
        $this->assertPublished('Exceptions/Handler.php', 'app/Exceptions/Handler.php');

        // PublishModels
        $this->assertPublished('Team.php', 'app/Team.php');
        $this->assertPublished('User.php', 'app/User.php');

        // PublishRoutes
        $this->assertPublished('routes/api.php', 'routes/api.php');
        $this->assertPublished('routes/web.php', 'routes/web.php');
    }
}
