<?php

namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;
use Config\Services;

class MigrateFileCommand extends BaseCommand
{
    /**
     * The Command's Group
     *
     * @var string
     */
    protected $group = 'Database';

    /**
     * The Command's Name
     *
     * @var string
     */
    protected $name = 'migrate:file';

    /**
     * The Command's Description
     *
     * @var string
     */
    protected $description = 'Migrates a single migration file.';

    /**
     * The Command's Usage
     *
     * @var string
     */
    protected $usage = 'migrate:file [arguments] [options]';

    /**
     * The Command's Arguments
     *
     * @var array
     */
    protected $arguments = [
        'name' => 'The valid migration file path beginning from the ROOTPATH. For example: php spark migrate:file "app\Database\Migrations\2022-02-16-101819_AddBlogMigration.php"'
    ];

    /**
     * The Command's Options
     *
     * @var array
     */
    protected $options = [
        '--namespace' => 'Set migration namespace. Default: "App".',
        '--dbgroup' => 'Set database group. Default: "default".',
    ];

    /**
     * Actually execute a command.
     *
     * @param array $params
     */
    public function run(array $params)
    {
        CLI::write("Running migration...", 'yellow');

        $message = "";
        $paramsSize = count($params);

        if (!$paramsSize) {
            $message = 'Too few arguments passed. Missing "migration file path."';
        } else if ($paramsSize > 1) {
            $message = 'Too many arguments passed.';
        }

        if ($paramsSize !== 1) {
            CLI::write(sprintf('Invalid Params: %s', $message), 'red');
            CLI::newLine();
            $this->showHelp();
            return;
        }

        $runner = Services::migrations();
        $namespace = ($params['namespace'] ?? CLI::getOption('namespace')) ?: "App";
        $dbgroup = ($params['dbgroup'] ?? CLI::getOption('dbgroup')) ?: "default";

        try {
            if (!$runner->force(ROOTPATH . $params[0], $namespace, $dbgroup)) {
                CLI::error(lang('Migrations.generalFault'), 'light_gray', 'red'); // @codeCoverageIgnore
            }

            $messages = $runner->getCliMessages();

            foreach ($messages as $message) {
                CLI::write($message);
            }

            CLI::write('Done migration.', 'green');

            // @codeCoverageIgnoreStart
        } catch (\Throwable $e) {
            $this->showError($e);
            // @codeCoverageIgnoreEnd
        }
    }
}
