<?php

namespace App\Models\Scopes;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;

class TenantScope implements Scope
{
    /**
     * Apply the scope to a given Eloquent query builder.
     */
    public function apply(Builder $builder, Model $model): void
    {
        if (auth()->check()) {
            $user = auth()->user();

            // Super Admin melihat semua data
            if ($user->hasRole('Super Admin')) {
                return;
            }

            // Jika role adalah Admin Entitas, filter berdasarkan entity_id
            if ($user->hasRole('Admin Entitas') && $user->entity_id) {
                // Periksa apakah model memiliki kolom 'entity_id'
                if (in_array('entity_id', $model->getFillable()) || $model->getConnection()->getSchemaBuilder()->hasColumn($model->getTable(), 'entity_id')) {
                    $builder->where($model->getTable() . '.entity_id', $user->entity_id);
                } elseif (in_array('user_id', $model->getFillable()) || $model->getConnection()->getSchemaBuilder()->hasColumn($model->getTable(), 'user_id')) {
                    // Jika tidak ada entity_id, tapi ada user_id (misal: tabel Attendance, Bap, Leave)
                    $builder->whereHas('user', function ($q) use ($user) {
                        $q->where('entity_id', $user->entity_id);
                    });
                }
            }
            
            // Jika role adalah Principal, filter data agar hanya memuat entitas/store/absensi 
            // yang berkaitan dengan principal yang dipegang user
            // (Logika lebih spesifik bisa ditambahkan sesuai kebutuhan, misal model Store)
            if ($user->hasRole('Principal')) {
                $principalIds = $user->principals->pluck('id')->toArray();
                if (!empty($principalIds)) {
                    if (in_array('principal_id', $model->getFillable()) || $model->getConnection()->getSchemaBuilder()->hasColumn($model->getTable(), 'principal_id')) {
                        $builder->whereIn($model->getTable() . '.principal_id', $principalIds);
                    } elseif (in_array('user_id', $model->getFillable()) || $model->getConnection()->getSchemaBuilder()->hasColumn($model->getTable(), 'user_id')) {
                        // Jika tidak ada principal_id, tapi ada user_id
                        $builder->whereHas('user', function ($q) use ($principalIds) {
                            $q->whereHas('principals', function ($q2) use ($principalIds) {
                                $q2->whereIn('principals.id', $principalIds);
                            });
                        });
                    }
                }
            }
        }
    }
}
