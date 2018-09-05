# php-library-migrate

Create a migrate.php file at a location of your preference (this example uses "app/migrate.php").

```
require_once dirname(__DIR__) . '/vendor/autoload.php';
Sinevia\Migrate::setDirectoryMigration(__DIR__.'/Migrations');
Sinevia\Migrate::setDatabase(db());
Sinevia\Migrate::run();
```
Run the migrations:

```
php app/migrate.php
```
