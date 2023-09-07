<?php

namespace App\Models;

use Eloquent as Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * Class DemandLetters
 * @package App\Models
 * @version May 19, 2023, 8:32 am PST
 *
 * @property string $AccountNumber
 * @property string $UserId
 * @property string $Status
 * @property string $DateSent
 * @property string $Notes
 */
class DemandLetters extends Model
{
    // use SoftDeletes;

    use HasFactory;

    public $table = 'Billing_DemandLetters';
    
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';


    protected $dates = ['deleted_at'];

    protected $primaryKey = 'id';

    public $incrementing = false;

    public $fillable = [
        'id',
        'AccountNumber',
        'UserId',
        'Status',
        'DateSent',
        'Notes'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'string',
        'AccountNumber' => 'string',
        'UserId' => 'string',
        'Status' => 'string',
        'DateSent' => 'date',
        'Notes' => 'string'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        'AccountNumber' => 'nullable|string|max:255',
        'UserId' => 'nullable|string|max:255',
        'Status' => 'nullable|string|max:255',
        'DateSent' => 'nullable',
        'Notes' => 'nullable|string|max:1000',
        'created_at' => 'nullable',
        'updated_at' => 'nullable'
    ];

    
}
