# PHP-PDO
A complete database toolkit written in PHP to handle PDO statements

## Installation
Use the package manager [composer](https://getcomposer.org) to install the library
```bash
composer require riculum/php-pdo
```

## Initial setup

### Credentials
The basic database settings can be set through environment variables. Add a `.env` file in the root of your project. Make sure the `.env` file is added to your `.gitignore` so it is not checked-in the code. By default, the library looks for the following variables:

* DB_HOST
* DB_NAME
* DB_USERNAME
* DB_PASSWORD

More information how to use environment variables [here](https://github.com/vlucas/phpdotenv)

### Configuration
Import vendor/autoload.php and load the `.env` settings
```php
require_once 'vendor/autoload.php';

use Database\Core\Database as DB;

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();
```

## Statements
The `select` statement is used to return an array containing all the result set rows
```php
try {
    $user = DB::select('SELECT * FROM my_table WHERE firstname = ?', ['John']);
} catch (PDOException $e) {
    echo $e->getMessage();
}
```

The `single` statement is used to fetch the next row from a result set
```php
try {
    $user = DB::single('SELECT * FROM my_table WHERE firstname = ?', ['John']);
} catch (PDOException $e) {
    echo $e->getMessage();
}
```

The `insert` statement is used to insert new records in a table
```php
try {
    $id = DB::insert('INSERT INTO my_table (firstname, lastname) VALUES (?,?)', ['John', 'Doe']);
} catch (PDOException $e) {
    echo $e->getMessage();
}
```

The `insertAssoc` statement is used to insert new records in a table using an associative array
```php
$data = [
    'firstname' => 'John',
    'lastname' => 'Doe'
];

try {
    $id = DB::insertAssoc('my_table', $data);
} catch (PDOException $e) {
    echo $e->getMessage();
}
```

The `update` statement is used to modify the existing records in a table
```php
try {
    DB::update('UPDATE my_table SET firstname = ? WHERE lastname = ?', ['John', 'Doe']);
} catch (PDOException $e) {
    echo $e->getMessage();
}
```

The `delete` statement is used to delete existing records in a table
```php
try {
    DB::delete('DELETE FROM my_table WHERE firstname = ?', ['John']);
} catch (PDOException $e) {
    echo $e->getMessage();
}
```

Use `statement` for other operations not mentioned
```php
try {
    DB::statement('DROP TABLE my_table');
} catch (PDOException $e) {
    echo $e->getMessage();
}
```

### Transactions
Transaction are used to run a series of operations within an entity. If an exception is thrown between `beginTransaction` and `commit`, the transaction will automatically be rolled back. 
```php
$data = [
    'firstname' => 'John',
    'lastname' => 'Doe'
];

try {
    DB::beginTransaction();
    $id = DB::insertAssoc('my_table', $data);
    DB::update('UPDATE my_table SET firstname = ?, lastname = ? WHERE id = ?', ['Jane', 'Doe', $id]);
    DB::commit();
} catch (PDOException $e) {
    echo $e->getMessage();
}
```
