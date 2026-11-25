<?php

namespace App\Models\Concerns;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;

/**
 * Fills created_by / updated_by from the signed-in admin without exposing those
 * columns to mass assignment.
 */
trait TracksBlame
{
    /** @var array<string, array<int, string>> */
    protected static array $blameColumns = [];

    protected static function bootTracksBlame(): void
    {
        static::creating(function (Model $model) {
            if (! $adminId = Auth::guard('admin')->id()) {
                return;
            }

            if (static::tableHasColumn($model, 'created_by') && empty($model->created_by)) {
                $model->created_by = $adminId;
            }

            if (static::tableHasColumn($model, 'updated_by') && empty($model->updated_by)) {
                $model->updated_by = $adminId;
            }
        });

        static::updating(function (Model $model) {
            if (! $adminId = Auth::guard('admin')->id()) {
                return;
            }

            if (static::tableHasColumn($model, 'updated_by')) {
                $model->updated_by = $adminId;
            }
        });
    }

    protected static function tableHasColumn(Model $model, string $column): bool
    {
        $table = $model->getTable();

        if (! isset(static::$blameColumns[$table])) {
            static::$blameColumns[$table] = Schema::getColumnListing($table);
        }

        return in_array($column, static::$blameColumns[$table], true);
    }
}
