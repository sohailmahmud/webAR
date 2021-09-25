<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CustomMarker extends Model
{
    
    /*
    |--------------------------------------------------------------------------
    | Attributes
    |--------------------------------------------------------------------------
    |
    | - id
    | - image1
    | - pattern1
    | - image2
    | - pattern2
    | - thumb1
    | - thumb2
    | - thumb3
    | - thumb4
    | - user_id
    |
    */

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'custom_markers';


    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;



    /**
     * Get the user of custom marker
     */
    public function user()
    {
        return $this->belongsTo('App\User');
    }


}
