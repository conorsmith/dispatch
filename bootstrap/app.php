<?php

require __DIR__.'/../vendor/autoload.php';

use BigName\BackupManager\Config\Config;
use BigName\BackupManager\Filesystems;
use BigName\BackupManager\Databases;
use BigName\BackupManager\Compressors;
use BigName\BackupManager\Manager;

$storageConfig = Config::fromPhpFile(__DIR__.'/../config/storage.php');
$databasesConfig = Config::fromPhpFile(__DIR__.'/../config/databases.php');

$filesystemsConfig = $storageConfig->getItems();
$filesystemsConfig['local'] = [
    'type' => "Local",
    'root' => "/tmp",
];

$filesystems = new Filesystems\FilesystemProvider(new Config($filesystemsConfig));
$filesystems->add(new Filesystems\DropboxFilesystem);
$filesystems->add(new Filesystems\LocalFilesystem);

$databases = new Databases\DatabaseProvider($databasesConfig);
$databases->add(new Databases\MysqlDatabase);

$compressors = new Compressors\CompressorProvider;
$compressors->add(new Compressors\GzipCompressor);
$compressors->add(new Compressors\NullCompressor);

$manager = new Manager($filesystems, $databases, $compressors);

$app = new \Symfony\Component\Console\Application();

$app->add(new \Tercet\Dispatch\BackupCommand($manager, $storageConfig, $databasesConfig));

return $app;
