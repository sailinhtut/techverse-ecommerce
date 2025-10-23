<?php

namespace App\Inventory\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Category extends Model
{
    protected $table = 'categories';

    protected $fillable = [
        'name',
        'slug',
        'description',
        'parent_id',
    ];

    protected function casts(): array
    {
        return [
            'parent_id' => 'integer',
        ];
    }

    public function parent()
    {
        return $this->belongsTo(Category::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(Category::class, 'parent_id');
    }

    public function jsonResponse(array $eager_list = []): array
    {
        $response = [
            'id' => $this->id,
            'name' => $this->name,
            'slug' => $this->slug,
            'description' => $this->description,
            'parent_id' => $this->parent_id,
            'children' => $this->children->map(fn($child) => $child->jsonResponse())->all(),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];

        if (in_array('parent', $eager_list) && !is_null($this->parent_id)) {
            $response['parent'] = $this->parent->jsonResponse();
        }

        return $response;
    }

    protected static function boot()
    {
        parent::boot();

        static::saving(function ($category) {
            if ($category->isDirty('name')) {
                $base = Str::slug($category->name);
                $slug = $base;
                $i = 1;
                while (self::where('slug', $slug)
                    ->where('id', '!=', $category->id)
                    ->exists()
                ) {
                    $slug = "{$base}-" . $i++;
                }
                $category->slug = $slug;
            }
        });
    }
}
