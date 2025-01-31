# Epoch Soft Deletes

This package solves a common issue in some software applications where soft-deleted records can cause duplicate entry problems due to unique constraints. `EpochSoftDeletes` trait that extends Laravel's soft delete functionality by storing the `deleted_at` column as an epoch timestamp.

**Benefits to End Users:**

- **Better Handling of Unique Constraints:** The epoch-based approach allows unique constraints to be enforced on soft-deleted records without causing conflicts. Users can delete records without facing errors when recreating similar records later.
- **Consistent Developer Experience:** `EpochSoftDeletes` works just like Laravel's default `SoftDeletes` trait. Developers only need to adjust the `deleted_at` column type to an integer. Everything else — soft deleting, restoring, querying — remains the same.
- **Default Value Handling:** Non-deleted records have a default `deleted_at` value of `0`, ensuring clarity and consistency in the database.


**Why It Doesn’t Break Existing Features:** This addition does not modify any existing features or behaviors. It simply offers an alternative trait for developers who need more control over unique constraints when using soft deletes. The default soft delete functionality remains fully backward-compatible.

**How It Makes Building Web Applications Easier:** The `EpochSoftDeletes` trait simplifies the management of soft-deleted records in scenarios where unique constraints need strict enforcement. By preventing soft-deleted records from causing conflicts with new entries, it streamlines data management and reduces errors in software and high-integrity systems.
