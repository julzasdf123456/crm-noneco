<?php

namespace App\Models;

use Eloquent as Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * Class DemandLetterMonths
 * @package App\Models
 * @version May 19, 2023, 8:33 am PST
 *
 * @property string $DemandLetterId
 * @property string $ServicePeriod
 * @property string $AccountNumber
 * @property number $NetAmount
 * @property number $Surcharge
 * @property number $Interest
 * @property number $TotalAmountDue
 * @property string $Notes
 * @property string $Status
 */
class DemandLetterMonths extends Model
{
    // use SoftDeletes;

    use HasFactory;

    public $table = 'Billing_DemandLetterMonths';
    
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';


    protected $dates = ['deleted_at'];

    protected $primaryKey = 'id';

    public $incrementing = false;

    public $fillable = [
        'id',
        'DemandLetterId',
        'ServicePeriod',
        'AccountNumber',
        'NetAmount',
        'Surcharge',
        'Interest',
        'TotalAmountDue',
        'Notes',
        'Status'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'string',
        'DemandLetterId' => 'string',
        'ServicePeriod' => 'date',
        'AccountNumber' => 'string',
        'NetAmount' => 'decimal:2',
        'Surcharge' => 'decimal:2',
        'Interest' => 'decimal:2',
        'TotalAmountDue' => 'decimal:2',
        'Notes' => 'string',
        'Status' => 'string'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        'DemandLetterId' => 'required|string|max:255',
        'ServicePeriod' => 'nullable',
        'AccountNumber' => 'nullable|string|max:255',
        'NetAmount' => 'nullable|numeric',
        'Surcharge' => 'nullable|numeric',
        'Interest' => 'nullable|numeric',
        'TotalAmountDue' => 'nullable|numeric',
        'Notes' => 'nullable|string|max:1000',
        'Status' => 'nullable|string|max:255',
        'created_at' => 'nullable',
        'updated_at' => 'nullable'
    ];

    
}
