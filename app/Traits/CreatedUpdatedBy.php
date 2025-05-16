<?php

namespace App\Traits;

use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Model;

trait CreatedUpdatedBy
{
    public static function bootCreatedUpdatedBy()
    {
        // Event listener untuk 'creating' (sebelum model disimpan pertama kali)
        static::creating(function (Model $model) {
            if (Auth::check()) {
                $model->created_by = Auth::id();
                // updated_by juga bisa diisi saat creating, atau hanya saat updating
                $model->updated_by = Auth::id();
            }
        });

        // Event listener untuk 'updating' (sebelum model yang sudah ada diupdate)
        static::updating(function (Model $model) {
            if (Auth::check()) {
                $model->updated_by = Auth::id();
            }
        });
    }
}