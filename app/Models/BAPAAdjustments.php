<?php

namespace App\Models;

use Eloquent as Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * Class BAPAAdjustments
 * @package App\Models
 * @version May 25, 2022, 10:58 am PST
 *
 * @property string $BAPAName
 * @property string $ServicePeriod
 * @property string $DiscountPercentage
 * @property string $DiscountAmount
 * @property string $NumberOfConsumers
 * @property string $SubTotal
 * @property string $NetAmount
 * @property string $UserId
 * @property string $Route
 */
class BAPAAdjustments extends Model
{
    // use SoftDeletes;

    use HasFactory;

    public $table = 'Cashier_BAPAAdjustments';
    
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';


    protected $dates = ['deleted_at'];

    protected $primaryKey = 'id';

    public $incrementing = false;

    public $fillable = [
        'id',
        'BAPAName',
        'ServicePeriod',
        'DiscountPercentage',
        'DiscountAmount',
        'NumberOfConsumers',
        'SubTotal',
        'NetAmount',
        'UserId',
        'Route',
        'Status'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'string',
        'BAPAName' => 'string',
        'ServicePeriod' => 'string',
        'DiscountPercentage' => 'string',
        'DiscountAmount' => 'string',
        'NumberOfConsumers' => 'string',
        'SubTotal' => 'string',
        'NetAmount' => 'string',
        'UserId' => 'string',
        'Route' => 'string',
        'Status' => 'string'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        'id' => 'string',
        'BAPAName' => 'nullable|string|max:500',
        'ServicePeriod' => 'nullable',
        'DiscountPercentage' => 'nullable|string|max:255',
        'DiscountAmount' => 'nullable|string|max:255',
        'NumberOfConsumers' => 'nullable|string|max:255',
        'SubTotal' => 'nullable|string|max:255',
        'NetAmount' => 'nullable|string|max:255',
        'UserId' => 'nullable|string|max:255',
        'Route' => 'nullable|string|max:255',
        'created_at' => 'nullable',
        'updated_at' => 'nullable',
        'Status' => 'nullable|string|max:255',
    ];

    
}
