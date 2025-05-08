<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Scholarship extends Model
{
    /** The table associated with the model.
     *
     * @var string
     */
    protected $table = 'scholarships';
    protected $fillable = [
        'title', 'description', 'link', 'official_link','post_at', 'deadline',
        'eligibility', 'host_country', 'host_university',
        'program_duration', 'degree_offered'
    ];
    

    protected $hidden = [
        'created_at',
        'updated_at',
    ];

    /**
     * Get the country that the scholarship belongs to.
     */
    // public function country()
    // {
    //     return $this->belongsTo(Country::class);
    // }
}
