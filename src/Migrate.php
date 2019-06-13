<?php

namespace Sinevia;

class Migrate {

    public static $verbose = true;
    public static $db = null;
    public static $directoryMigrations = null;
    public static $tableMigration = 'snv_migrations_migration';
    public static $tableMigrationSchema = array(
        array("Id", "STRING", "NOT NULL PRIMARY KEY"),
        array("Migration", "STRING"),
        array("CreatedAt", "STRING"),
        array("UpdatedAt", "STRING"),
    );

    public static function getDirectory() {
        return self::$directoryMigrations;
    }

    public static function setDirectoryMigration($directoryMigration) {
        self::$directoryMigrations = $directoryMigration;
    }

    /**
     * @return \Sinevia\SqlDb
     */
    public static function getDatabase() {
        // try to load from function db() if exists
        //if (self::$db == null AND function_exists('db')) {
        //    self::$db = db();
        //}
        return self::$db;
    }

    /**
     * 
     * @param \Sinevia\SqlDb $db
     */
    public static function setDatabase($db) {
        if (get_class($db) != 'Sinevia\SqlDb') {
            throw new \RuntimeException('Expected database class of type Sinevia\SqlDb received ' . get_class($db));
        }

        self::$db = $db;
    }

    public static function createTables() {
        if (self::getTableMigration()->exists() == false) {
            self::getTableMigration()->create(self::$tableMigrationSchema);
        }
    }

    public static function getTableMigration() {
        return self::getDatabase()->table(self::$tableMigration);
    }

    public static function getMigration($migration) {
        $result = self::getTableMigration()->where('Migration', '=', $migration)->selectOne();
        return $result;
    }

    public static function createMigration($data) {
        $data['Id'] = date('Y-m-d-H-i-s-') . rand(1000, 9999);
        $result = self::getTableMigration()->insert($data);
        return $result;
    }

    public static function up() {
        $cli = defined('STDIN') ? true : false;
        if (is_null(self::getDirectory())) {
            throw new \RuntimeException('Migrations directory not set');
        }
        if (file_exists(self::getDirectory()) == false) {
            throw new \RuntimeException('Migrations directory DOES NOT exist at "' . self::getDirectory() . '"');
        }
        if (is_null(self::getDatabase())) {
            throw new \RuntimeException('Database not set');
        }
        $migrationFiles = scandir(self::getDirectory(), SCANDIR_SORT_ASCENDING);
        foreach ($migrationFiles as $migrationFile) {
            if (in_array($migrationFile, ['.', '..'])) {
                continue; // shortcuts
            }
            $ext = pathinfo($migrationFile, PATHINFO_EXTENSION);
            if ($ext != 'php') {
                if (self::$verbose) {
                    echo " - File $migrationFile no '.php' extension. SKIPPED.\n<br />";
                }
                continue;
            }
            $name = substr(substr($migrationFile, 16), 0, -4);
            $classNameWithSpaces = self::camelize(str_replace('_', ' ', $name));
            $className = str_replace(' ', '', $classNameWithSpaces);
            // DEBUG: var_dump($name);
            // DEBUG: var_dump($className);

            if (self::getMigration($migrationFile) != null) {
                if (self::$verbose) {
                    echo " - File $migrationFile ALREADY processed. SKIPPED.\n" . ($cli ? '' : '<br />');
                }
                continue;
            }

            if (self::$verbose) {
                echo " - Processing file $migrationFile class $className ...\n" . ($cli ? '' : '<br />');
            }

            // DEBUG: var_dump($migrationFile);
            require_once self::getDirectory() . '/' . $migrationFile;

            $class = new $className;
            $class->up();

            self::createMigration([
                'Migration' => $migrationFile,
                'CreatedAt' => date('Y-m-d'),
                'UpdatedAt' => date('Y-m-d'),
            ]);
        }
    }

    private static function camelize($string, $separator = " ", $remove_separator = false) {
        $string = str_replace($separator, " ", $string);
        $ucstring = ucwords($string);
        if ($remove_separator) {
            $string = str_replace(" ", "", $ucstring);
        } else {
            $string = str_replace(" ", $separator, $ucstring);
        }
        return $string;
    }

}
