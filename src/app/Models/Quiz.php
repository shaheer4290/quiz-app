<?php

namespace App\Models;

use Cviebrock\EloquentSluggable\Sluggable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Quiz extends Model
{
    use HasFactory;
    use Sluggable;

    public const STATUS_DRAFT = 'draft';

    public const STATUS_PUBLISHED = 'published';

    public const MAXIMUM_QUESTIONS_COUNT = 10;

    public function getRouteKeyName()
    {
        return 'slug';
    }

    /**
     * Return the sluggable configuration array for this model.
     *
     * @return array
     */
    public function sluggable(): array
    {
        return [
            'slug' => [
                'source' => 'title',
            ],
        ];
    }

    public function isPublished(): bool
    {
        return $this->status == self::STATUS_PUBLISHED;
    }

    public function User()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function Questions()
    {
        return $this->hasMany(QuizQuestion::class);
    }
}
