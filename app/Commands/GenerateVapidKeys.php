<?php

namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;
use Minishlink\WebPush\VAPID;

class GenerateVapidKeys extends BaseCommand
{
    protected $group       = 'WebPush';
    protected $name        = 'webpush:generate-keys';
    protected $description = 'Generate VAPID keys for Web Push notifications';

    public function run(array $params)
    {
        CLI::write('Generating VAPID keys...', 'yellow');
        CLI::newLine();

        try {
            // Generate VAPID keys
            $keys = VAPID::createVapidKeys();

            CLI::write('VAPID keys generated successfully!', 'green');
            CLI::newLine();

            CLI::write('Public Key:', 'yellow');
            CLI::write($keys['publicKey'], 'white');
            CLI::newLine();

            CLI::write('Private Key:', 'yellow');
            CLI::write($keys['privateKey'], 'white');
            CLI::newLine();

            CLI::write('Add these keys to your app/Config/WebPush.php file:', 'cyan');
            CLI::newLine();

            CLI::write("public string \$publicKey = '{$keys['publicKey']}';", 'white');
            CLI::write("public string \$privateKey = '{$keys['privateKey']}';", 'white');
            CLI::newLine();

            CLI::write('WARNING: Keep your private key secret!', 'red');
            CLI::write('Never commit it to version control or share it publicly.', 'red');
            CLI::newLine();
        } catch (\Exception $e) {
            CLI::error('Failed to generate VAPID keys: ' . $e->getMessage());
            return 1;
        }

        return 0;
    }
}
