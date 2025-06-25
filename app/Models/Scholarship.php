<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Scholarship extends Model
{
    /** The table associated with the model.
     *
     * @var string
     */
    protected $table = 'scholarships';
    protected $fillable = [
        'title', 'slug', 'description', 'link', 'official_link','post_at', 'deadline',
        'eligibility', 'host_country', 'host_university',
        'program_duration', 'degree_offered'
    ];
    

    protected $hidden = [
        'created_at',
        'updated_at',
    ];

    /**
     * Boot method to automatically generate slug
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($scholarship) {
            if (empty($scholarship->slug)) {
                $scholarship->slug = static::generateUniqueSlug($scholarship->title);
            }
        });

        static::updating(function ($scholarship) {
            if ($scholarship->isDirty('title') && empty($scholarship->slug)) {
                $scholarship->slug = static::generateUniqueSlug($scholarship->title);
            }
        });
    }

    /**
     * Generate a unique slug from title
     */
    public static function generateUniqueSlug($title)
    {
        $slug = Str::slug($title);
        $originalSlug = $slug;
        $counter = 1;

        while (static::where('slug', $slug)->exists()) {
            $slug = $originalSlug . '-' . $counter;
            $counter++;
        }

        return $slug;
    }

    /**
     * Get the route key for the model.
     */
    public function getRouteKeyName()
    {
        return 'slug';
    }

    /**
     * Find model by slug or ID
     */
    public static function findBySlugOrId($identifier)
    {
        return static::where('slug', $identifier)->orWhere('id', $identifier)->firstOrFail();
    }

    /**
     * Get the URL for this scholarship
     */
    public function getUrlAttribute()
    {
        return url($this->slug);
    }

    /**
     * Get the API URL for this scholarship
     */
    public function getApiUrlAttribute()
    {
        return url("api/scholarship/{$this->slug}");
    }

    /**
     * Get the country that the scholarship belongs to.
     */
    // public function country()
    // {
    //     return $this->belongsTo(Country::class);
    // }
}
