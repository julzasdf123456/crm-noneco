<?php

namespace App\Models;

use Eloquent as Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * Class Readings
 * @package App\Models
 * @version January 25, 2022, 11:10 am PST
 *
 * @property string $AccountNumber
 * @property string $ServicePeriod
 * @property string|\Carbon\Carbon $ReadingTimestamp
 * @property string $KwhUsed
 * @property string $DemandKwhUsed
 * @property string $Notes
 * @property string $Latitude
 * @property string $Longitude
 */
class Readings extends Model
{
    // use SoftDeletes;

    use HasFactory;

    public $table = 'Billing_Readings';
    
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';


    protected $dates = ['deleted_at'];

    protected $primaryKey = 'id';

    public $incrementing = false;

    public $fillable = [
        'id',
        'AccountNumber',
        'ServicePeriod',
        'ReadingTimestamp',
        'KwhUsed',
        'DemandKwhUsed',
        'Notes',
        'Latitude',
        'Longitude',
        'FieldStatus', // OVERREADING, STUCK-UP, NOT IN USE, NO DISPLAY
        'MeterReader',
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'string',
        'AccountNumber' => 'string',
        'ServicePeriod' => 'string',
        'ReadingTimestamp' => 'datetime',
        'KwhUsed' => 'string',
        'DemandKwhUsed' => 'string',
        'Notes' => 'string',
        'Latitude' => 'string',
        'Longitude' => 'string',
        'FieldStatus' => 'string',
        'MeterReader' => 'string'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        'id' => 'string',
        'AccountNumber' => 'nullable|string|max:255',
        'ServicePeriod' => 'nullable',
        'ReadingTimestamp' => 'nullable',
        'KwhUsed' => 'nullable|string|max:255',
        'DemandKwhUsed' => 'nullable|string|max:255',
        'Notes' => 'nullable|string|max:3000',
        'Latitude' => 'nullable|string|max:60',
        'Longitude' => 'nullable|string|max:60',
        'created_at' => 'nullable',
        'updated_at' => 'nullable',
        'FieldStatus' => 'nullable|string',
        'MeterReader' => 'nullable|string'
    ];

    public static function getDaysBetweenDates($from, $to) {
        $from = strtotime($from); 
        $to = strtotime($to);
        $datediff = $to - $from;

        return round($datediff / (60 * 60 * 24));
    }
}
