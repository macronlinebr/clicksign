<?php

namespace Cyberlpkf\Clicksign\Models;

use App\Scopes\ApiScope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Schema;
use MichaelAChrisco\ReadOnly\ReadOnlyTrait;
use Staudenmeir\EloquentJsonRelations\HasJsonRelationships;

class Api extends Model
{
    use HasJsonRelationships, ReadOnlyTrait;

    protected $table = 'api';

    protected $casts = [
        'credencial' => 'json',
    ];

    protected $dates = [
        'created_at',
        'updated_at',
    ];

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);

        if (Schema::hasColumn($this->table, 'delete_at'))
            $this->dates = array_merge($this->dates, ['deleted_at']);
    }

    public static function boot()
    {
        parent::boot();

        return static::addGlobalScope('deleted_at', function (Builder $builder) {
            if (Schema::hasColumn($this->table, 'delete_at'))
                $builder->whereNull('deleted_at');

        });
    }
}