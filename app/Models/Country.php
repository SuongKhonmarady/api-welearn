<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Country extends Model
{
    /** The table associated with the model.
     *
     * @var string
     */
    protected $table = 'countries';

    protected $fillable = ['name', 'code'];

    /**
     * Get the scholarships for the country.
     */
    public function scholarships()
    {
        return $this->hasMany(Scholarship::class);
    }
}
