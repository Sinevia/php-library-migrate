<?php
require_once dirname(__DIR__) . '/vendor/autoload.php';
require_once dirname(__DIR__) . '/php/boot.php';

defined('MIGRATIONS_DIR') OR define('MIGRATIONS_DIR', PHP_DIR . '/database/migrations/');

MigrationPlugin::createTables();

$migrationFiles = scandir(MIGRATIONS_DIR, SCANDIR_SORT_ASCENDING);
foreach ($migrationFiles as $migrationFile) {
    if (in_array($migrationFile, ['.', '..'])) {
        continue; // shortcuts
    }
    $ext = pathinfo($migrationFile, PATHINFO_EXTENSION);
    if ($ext != 'php') {
        echo " - File $migrationFile no '.php' extension. SKIPPED.\n<br />";
        continue;
    }
    $name = substr(substr($migrationFile, 18), 0, -4);

    $classNameWithSpaces = \Sinevia\StringUtils::camelize(str_replace('_', ' ', $name));
    $className = str_replace(' ', '', $classNameWithSpaces);
    var_dump($name);
    var_dump($className);
    
    if (MigrationPlugin::getMigration($migrationFile) != null) {
        echo " - File $migrationFile ALREADY processed. SKIPPED.\n<br />";
        continue;
    }
    
    echo " - Processing file $migrationFile class $className ...\n<br />";    
    
    var_dump($migrationFile);
    require_once MIGRATIONS_DIR.'/'.$migrationFile;
    
    $class = new $className;
    $class->up();
    
    MigrationPlugin::createMigration([
        'Migration'=>$migrationFile,
        'CreatedAt'=>date('Y-m-d'),
        'UpdatedAt'=>date('Y-m-d'),
    ]);
}
