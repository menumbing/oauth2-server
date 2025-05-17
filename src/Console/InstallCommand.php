<?php

declare(strict_types=1);

namespace Menumbing\OAuth2\Server\Console;

use Hyperf\Command\Command;

/**
 * @author  Iqbal Maulana <iq.bluejack@gmail.com>
 */
class InstallCommand extends Command
{
    protected ?string $signature = 'install:oauth2-server
            {--force : Overwrite keys they already exist}
            {--length=4096 : The length of the private key}';

    protected string $description = 'Run the commands necessary to prepare OAuth2 server for use';

    public function handle(): void
    {
        $this->call('vendor:publish', ['package' => 'menumbing/orm']);
        $this->call('vendor:publish', ['package' => 'hyperf-extension/hashing']);
        $this->call('vendor:publish', ['package' => 'menumbing/auth']);

        $this->call('gen:oauth2-keys', [
            '--force'  => $this->option('force'),
            '--length' => $this->option('length'),
        ]);

        $this->call('vendor:publish', ['package' => 'menumbing/oauth2-server', '--id' => 'config', '--force']);
        $this->call('vendor:publish', ['package' => 'menumbing/oauth2-server', '--id' => 'migration', '--force']);

        $this->call('migrate');

        $this->info('OAuth2 server installed successfully.');
    }
}
