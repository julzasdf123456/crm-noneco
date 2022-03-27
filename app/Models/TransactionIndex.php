<?php

namespace App\Models;

use Eloquent as Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * Class TransactionIndex
 * @package App\Models
 * @version February 10, 2022, 9:10 am PST
 *
 * @property string $TransactionNumber
 * @property string $PaymentTitle
 * @property string $PaymentDetails
 * @property string $ORNumber
 * @property string $ORDate
 * @property string $SubTotal
 * @property string $VAT
 * @property string $Total
 * @property string $Notes
 * @property string $UserId
 * @property string $ServiceConnectionId
 * @property string $TicketId
 * @property string $ObjectId
 * @property string $Source
 */
class TransactionIndex extends Model
{
    // use SoftDeletes;

    use HasFactory;

    public $table = 'Cashier_TransactionIndex';
    
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';


    protected $dates = ['deleted_at'];

    protected $primaryKey = 'id';

    public $incrementing = false;

    public $fillable = [
        'id',
        'TransactionNumber',
        'PaymentTitle',
        'PaymentDetails',
        'ORNumber',
        'ORDate',
        'SubTotal',
        'VAT',
        'Total',
        'Notes',
        'UserId',
        'ServiceConnectionId',
        'TicketId',
        'ObjectId',
        'Source',
        'PaymentUsed'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'string',
        'TransactionNumber' => 'string',
        'PaymentTitle' => 'string',
        'PaymentDetails' => 'string',
        'ORNumber' => 'string',
        'ORDate' => 'date',
        'SubTotal' => 'string',
        'VAT' => 'string',
        'Total' => 'string',
        'Notes' => 'string',
        'UserId' => 'string',
        'ServiceConnectionId' => 'string',
        'TicketId' => 'string',
        'ObjectId' => 'string',
        'Source' => 'string',
        'PaymentUsed' => 'string'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        'id' => 'string',
        'TransactionNumber' => 'nullable|string|max:255',
        'PaymentTitle' => 'nullable|string|max:500',
        'PaymentDetails' => 'nullable|string|max:2000',
        'ORNumber' => 'nullable|string|max:255',
        'ORDate' => 'nullable',
        'SubTotal' => 'nullable|string|max:255',
        'VAT' => 'nullable|string|max:255',
        'Total' => 'nullable|string|max:255',
        'Notes' => 'nullable|string|max:1500',
        'UserId' => 'nullable|string|max:255',
        'created_at' => 'nullable',
        'updated_at' => 'nullable',
        'ServiceConnectionId' => 'nullable|string|max:50',
        'TicketId' => 'nullable|string|max:50',
        'ObjectId' => 'nullable|string|max:50',
        'Source' => 'nullable|string|max:50',
        'PaymentUsed' => 'nullable|string',
    ];

    public static function getReconnectionFeeId() {
        return '1645770252818-S6Soo3slq1KNMJU2tcYIoLjXb2XtUI';
    }
}
