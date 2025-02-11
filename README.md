# Epoch SoftDelete

A Laravel package that introduces epoch-based soft deletes to handle unique constraints in multi-tenant applications. This package works seamlessly with Laravel's `SoftDeletes` trait, allowing you to enforce unique constraints while using soft deletes without conflicts.

## 🚀 Features

- **Better Handling of Unique Constraints**: The epoch-based approach ensures unique constraints are enforced on soft-deleted records without causing conflicts. Users can delete records and recreate them without facing duplicate entry errors.
- **Consistent Developer Experience**: Works just like Laravel's default `SoftDeletes` trait. You only need to adjust the `deleted_at` column type to an integer.
- **Default Value Handling**: Non-deleted records have a default `deleted_at` value of `0`, ensuring clarity and consistency in your database.
- **Backward Compatibility**: The package does not modify any existing features or behaviors, and it fully supports Laravel's soft delete functionality.

## 📌 Requirements

- php 8.1+
- Laravel 10, or 11

## 📥 Installation

You can install the package via Composer:

```bash
composer require ismayil-dev/epoch-softdelete
```
## ⚙️ Configuration

**Step 1.** Register the Service Provider\
After installing the package, you need to manually register the service provider in your Laravel app.\
Open `config/app.php` and add the following line inside the `providers` array:
```php
'providers' => [
    // Other service providers...
    IsmayilDev\EpochSoftDelete\EpochSoftDeleteServiceProvider::class,
],
```

**Step 2.** Adjust your `deleted_at` column:\
For new projects, you can use the built-in macro for migrations:
```php
Schema::table('your_table', function (Blueprint $table) {
    $table->epochSoftDeletes();
});
```

**Step 3.** Apply the `EpochSoftDeletes` trait:\
In your models, use the EpochSoftDeletes trait instead of the default SoftDeletes trait:
```php
use IsmayilDev\EpochSoftDelete\EpochSoftDeletes;

class YourModel extends Model
{
    use EpochSoftDeletes;

    // Other model code...
}
```
**Step 4.** Run migrations:\
After modifying your migration files, run the migration to update the database schema:
```bash
php artisan migrate
```

## 🛠 Usage
Once configured, **continue using Laravel’s soft delete functionality as usual** — no extra changes needed!
```php
YourModel::find(1)->delete();
YourModel::withTrashed()->get();
YourModel::onlyTrashed()->get();
YourModel::find(1)->restore();
// etc.
```

### 📌 `deleted_at` Auto-Casting

When retrieving soft-deleted models, the `deleted_at` column automatically casts to a **Carbon** instance, making it easier to work with.
This means you can perform all Carbon operations on `deleted_at` just like in Laravel’s default soft deletes. 🎯

### ⚙️ Handling Existing Projects
If you're integrating Epoch SoftDelete into an existing project, you must update your existing data before changing the column type.

### Steps to Migrate Existing Projects
#### 1. Convert existing deleted_at values:\
If a record has a `deleted_at` date, convert it to an **epoch timestamp**.\
If `deleted_at` is `NULL`, update it to `0` to indicate the record is not deleted.
#### 2. Update your database schema:\
Modify the `deleted_at` column to be an **integer** (epoch timestamp) instead of `timestamp` or `NULL`.
```php
Schema::table('your_table', function (Blueprint $table) {
    $table->integer('deleted_at')->default(0)->change();
});
```
#### 3. Apply the EpochSoftDeletes trait in your model.
#### 4. Run migrations to finalize the changes.

⚠️ **Important:** If you do not convert your existing `deleted_at` values before running migrations, queries may not work as expected.

## 🎯 Why Use This Package?
* Prevents Unique Constraint Conflicts: No duplicate entry errors when re-creating soft-deleted records.
* Zero Learning Curve: Works exactly like Laravel’s default SoftDeletes.
* Ideal for Multi-Tenant & High-Integrity Systems: Ensures smooth database management in complex applications.

## 📢 Contributing
We welcome contributions! 🎉

If you find a bug, have a feature request, or want to improve the package:
* Open an issue with details about the problem or feature request.
* Submit a pull request (PR) with improvements or fixes.

Before submitting a PR:
* Make sure your changes follow Laravel and PSR coding standards.
* Run tests to ensure everything works as expected.

Feel free to contribute and help improve Epoch SoftDelete! 🚀

# 📜 License

This package is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).

