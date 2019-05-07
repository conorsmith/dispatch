<?php

namespace Tercet\Dispatch;

use BackupManager\Config\Config;
use BackupManager\Filesystems\Destination;
use BackupManager\Manager;
use Carbon\Carbon;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class BackupCommand extends Command
{
    private $backupManager;
    private $storageConfig;
    private $databasesConfig;

    public function __construct(
        Manager $backupManager,
        Config $storageConfig,
        Config $databasesConfig
    ) {
        parent::__construct();
        $this->backupManager = $backupManager;
        $this->storageConfig = $storageConfig;
        $this->databasesConfig = $databasesConfig;
    }

    protected function configure()
    {
        $this->setName('backup')
            ->setDescription('Backs up your databases');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $storage = array_keys($this->storageConfig->getItems());
        $databases = array_keys($this->databasesConfig->getItems());

        $target = array_pop($storage);

        $path = Carbon::now()->format("Y-M-d Hi") . "/";

        foreach ($databases as $database) {
            $this->backupManager->makeBackup()->run(
                $database,
                [new Destination($target, $path . $database . '.sql')],
                'gzip'
            );
            $output->writeln(sprintf("Backed up database '%s' to %s", $database, ucfirst($target)));
        }

        $output->writeln("<info>Backup complete!</info>");
    }
}
