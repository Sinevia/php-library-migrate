<?php
namespace Sinevia;

class Migrate {
    public static $tableMigration = 'snv_migrations_migration';
    public static $tableMigrationSchema = array(
        array("Id", "STRING", "NOT NULL PRIMARY KEY"),
        array("Migration", "STRING"),
        array("CreatedAt", "STRING"),
        array("UpdatedAt", "STRING"),
    );

    /**
     * @return \Sinevia\SqlDb
     */
    public static function getDatabase() {
        return db();
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
        $data['Id'] = date('Y-m-d-H-i-s-'). rand(1000, 9999);
        $result = self::getTableMigration()->insert($data);
        return $result;
    }
}
