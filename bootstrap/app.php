<?php

require __DIR__.'/../vendor/autoload.php';

use BackupManager\Config\Config;
use BackupManager\Filesystems;
use BackupManager\Databases;
use BackupManager\Compressors;
use BackupManager\Manager;

$storageConfig = Config::fromPhpFile(__DIR__.'/../config/storage.php');
$databasesConfig = Config::fromPhpFile(__DIR__.'/../config/databases.php');

$filesystemsConfig = $storageConfig->getItems();
$filesystemsConfig['local'] = [
    'type' => "Local",
    'root' => "/tmp",
];

$filesystems = new Filesystems\FilesystemProvider(new Config($filesystemsConfig));
$filesystems->add(new Filesystems\DropboxV2Filesystem);
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
