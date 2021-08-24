<?php

namespace App\Models;

use Eloquent as Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * Class ServiceConnections
 * @package App\Models
 * @version July 21, 2021, 6:12 am UTC
 *
 * @property string $MemberConsumerId
 * @property string $DateOfApplication
 * @property string $ServiceAccountName
 * @property integer $AccountCount
 * @property string $Sitio
 * @property string $Barangay
 * @property string $Town
 * @property string $ContactNumber
 * @property string $EmailAddress
 * @property string $AccountType
 * @property string $AccountOrganization
 * @property string $OrganizationAccountNumber
 * @property string $IsNIHE
 * @property string $AccountApplicationType
 * @property string $ConnectionApplicationType
 * @property string $Status
 * @property string $Notes
 */
class ServiceConnections extends Model
{
    // use SoftDeletes;

    use HasFactory;

    public $table = 'CRM_ServiceConnections';
    
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';

    
    protected $primaryKey = 'id';

    public $incrementing = false;


    protected $dates = ['deleted_at'];



    public $fillable = [
        'id',
        'MemberConsumerId',
        'DateOfApplication',
        'ServiceAccountName',
        'AccountCount',
        'Sitio',
        'Barangay',
        'Town',
        'ContactNumber',
        'EmailAddress',
        'AccountType',
        'AccountOrganization',
        'OrganizationAccountNumber',
        'IsNIHE',
        'AccountApplicationType',
        'ConnectionApplicationType',
        'BuildingType',
        'Status',
        'Notes'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'string',
        'MemberConsumerId' => 'string',
        'DateOfApplication' => 'date',
        'ServiceAccountName' => 'string',
        'AccountCount' => 'integer',
        'Sitio' => 'string',
        'Barangay' => 'string',
        'Town' => 'string',
        'ContactNumber' => 'string',
        'EmailAddress' => 'string',
        'AccountType' => 'string',
        'AccountOrganization' => 'string',
        'OrganizationAccountNumber' => 'string',
        'IsNIHE' => 'string',
        'AccountApplicationType' => 'string',
        'ConnectionApplicationType' => 'string',
        'BuildingType' => 'string',
        'Status' => 'string',
        'Notes' => 'string'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        'id' => 'required|string',
        'MemberConsumerId' => 'nullable|string|max:255',
        'DateOfApplication' => 'nullable',
        'ServiceAccountName' => 'nullable|string|max:255',
        'AccountCount' => 'nullable|integer',
        'Sitio' => 'nullable|string|max:1000',
        'Barangay' => 'nullable|string|max:10',
        'Town' => 'nullable|string|max:10',
        'ContactNumber' => 'nullable|string|max:500',
        'EmailAddress' => 'nullable|string|max:800',
        'AccountType' => 'nullable|string|max:100',
        'AccountOrganization' => 'nullable|string|max:100',
        'OrganizationAccountNumber' => 'nullable|string|max:100',
        'IsNIHE' => 'nullable|string|max:255',
        'AccountApplicationType' => 'nullable|string|max:100',
        'ConnectionApplicationType' => 'nullable|string|max:100',
        'BuildingType' => 'nullable|string',
        'Status' => 'nullable|string|max:100',
        'Notes' => 'nullable|string|max:2000',
        'created_at' => 'nullable',
        'updated_at' => 'nullable'
    ];

    public static function getAccountCount($consumerId) {
        $sc = ServiceConnections::where('MemberConsumerId', $consumerId)->get();

        if ($sc == null) {
            return 0;
        } else {
            return count($sc);
        }
    }

    public static function getContactInfo($serviceConnections) {
        if ($serviceConnections->ContactNumber==null && $serviceConnections->EmailAddress==null) {
            return 'not specified';
        } elseif ($serviceConnections->ContactNumber==null && $serviceConnections->EmailAddress!=null) {
            return $serviceConnections->EmailAddress;
        } elseif ($serviceConnections->ContactNumber!=null && $serviceConnections->EmailAddress==null) {
            return $serviceConnections->ContactNumber;
        } else {
            return $serviceConnections->ContactNumber . ' | ' . $serviceConnections->EmailAddress;
        }
    }

    public static function getAddress($serviceConnections) {
        if ($serviceConnections->Sitio==null && ($serviceConnections->Barangay!=null && $serviceConnections->Town!=null)) {
            return $serviceConnections->Barangay . ', ' . $serviceConnections->Town;
        } elseif($serviceConnections->Sitio!=null && ($serviceConnections->Barangay!=null && $serviceConnections->Town!=null)) {
            return $serviceConnections->Sitio . ', ' . $serviceConnections->Barangay . ', ' . $serviceConnections->Town;
        }
    }
}
