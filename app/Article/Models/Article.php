<?php


namespace App\Article\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

class Article extends Model
{
    protected $table = 'articles';

    protected $with = [];

    protected $fillable = [
        'title',
        'slug',
        'description',
        'content',
        'tags',
        'image',
        'status',
        'is_featured',
        'published_at',
        'view_count',
    ];

    protected function casts(): array
    {
        return [
            'is_featured'  => 'boolean',
            'published_at' => 'datetime',
            'view_count'   => 'integer',
            'tags'         => 'array',
        ];
    }


    public function jsonResponse(array $eager_list = []): array
    {
        $image = getDownloadableLink($this->image);

        $response = [
            'id'            => $this->id,
            'title'         => $this->title,
            'slug'          => $this->slug,
            'description'   => $this->description,
            'content'       => $this->content,
            'tags'          => $this->tags,
            'image'         => $image,
            'status'        => $this->status,
            'is_featured'   => $this->is_featured,
            'published_at'  => $this->published_at,
            'view_count'    => $this->view_count,
            'created_at'    => $this->created_at,
            'updated_at'    => $this->updated_at,
        ];

        return $response;
    }

    protected static function boot()
    {
        parent::boot();

        static::saving(function ($article) {
            if ($article->isDirty('title')) {
                $base = Str::slug($article->title);
                $slug = $base;
                $i = 1;

                while (
                    self::where('slug', $slug)
                    ->where('id', '!=', $article->id)
                    ->exists()
                ) {
                    $slug = "{$base}-{$i}";
                    $i++;
                }

                $article->slug = $slug;
            }
        });

        static::saved(fn() => Cache::flush());
        static::deleted(fn() => Cache::flush());
    }
}
