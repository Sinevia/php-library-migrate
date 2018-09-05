# PHP Library Migrate #

## How to Use ##

1. Create a migrationn directory to hold your migration files

2. Create the migration files with the following format YYYY_MM_DD_SEQNCE_ClassName (i.e. 2018_09_05_000001_CreateSettingsTable).

```
class CreateSettingsTable {
    /**
     * Run the migrations.
     *
     * @return void
     */
    function up() {
        // YOUR CODE HERE
    }
    
    /**
     * Reverse the migrations.
     *
     * @return void
     */
    function down() {
        // YOUR CODE HERE
    }
}
```

3. Create a migrate.php file at a location of your preference (this example uses "app/migrate.php").
The migrations will be sorted alphabetically in ascending order and run

```
require_once dirname(__DIR__) . '/vendor/autoload.php';
Sinevia\Migrate::setDirectoryMigration(__DIR__.'/Migrations');
Sinevia\Migrate::setDatabase(db());
Sinevia\Migrate::up();
```
Run the migrations:

```
php app/migrate.php
```
