<?php

namespace App\Models;

use Eloquent as Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * Class MemberConsumers
 * @package App\Models
 * @version July 16, 2021, 2:01 am UTC
 *
 * @property string $MembershipType
 * @property string $FirstName
 * @property string $MiddleName
 * @property string $LastName
 * @property string $Suffix
 * @property string $OrganizationName
 * @property string $Birthdate
 * @property string $Sitio
 * @property string $Barangay
 * @property string $Town
 * @property string $ContactNumbers
 * @property string $EmailAddress
 * @property string $DateApplied
 * @property string $DateOfPMS
 * @property string $DateApproved
 * @property string $CivilStatus
 * @property string $Religion
 * @property string $Citizenship
 * @property string $ApplicationStatus
 * @property string $Notes
 * @property string $Trashed
 */
class MemberConsumers extends Model
{
    // use SoftDeletes;

    use HasFactory;

    public $table = 'CRM_MemberConsumers';

    protected $primaryKey = 'Id';

    public $incrementing = false;
    
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';


    protected $dates = ['deleted_at'];



    public $fillable = [
        'Id',
        'MembershipType',
        'FirstName',
        'MiddleName',
        'LastName',
        'Suffix',
        'OrganizationName',
        'Gender',
        'Birthdate',
        'Sitio',
        'Barangay',
        'Town',
        'ContactNumbers',
        'EmailAddress',
        'DateApplied',
        'DateOfPMS',
        'DateApproved',
        'CivilStatus',
        'Religion',
        'Citizenship',
        'ApplicationStatus',
        'Notes',
        'Trashed'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'Id' => 'string',
        'MembershipType' => 'string',
        'FirstName' => 'string',
        'MiddleName' => 'string',
        'LastName' => 'string',
        'Suffix' => 'string',
        'OrganizationName' => 'string',
        'Gender' => 'string',
        'Birthdate' => 'date',
        'Sitio' => 'string',
        'Barangay' => 'string',
        'Town' => 'string',
        'ContactNumbers' => 'string',
        'EmailAddress' => 'string',
        'DateApplied' => 'date',
        'DateOfPMS' => 'date',
        'DateApproved' => 'date',
        'CivilStatus' => 'string',
        'Religion' => 'string',
        'Citizenship' => 'string',
        'ApplicationStatus' => 'string',
        'Notes' => 'string',
        'Trashed' => 'string'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        'Id' => 'string',
        'MembershipType' => 'required|string|max:255',
        'FirstName' => 'nullable|string|max:300',
        'MiddleName' => 'nullable|string|max:300',
        'LastName' => 'nullable|string|max:300',
        'Suffix' => 'nullable|string|max:50',
        'Gender' => 'nullable|string|max:50',
        'OrganizationName' => 'nullable|string|max:1000',
        'Birthdate' => 'nullable',
        'Sitio' => 'nullable|string|max:1000',
        'Barangay' => 'nullable|string|max:50',
        'Town' => 'nullable|string|max:50',
        'ContactNumbers' => 'nullable|string|max:300',
        'EmailAddress' => 'nullable|string|max:300',
        'DateApplied' => 'nullable',
        'DateOfPMS' => 'nullable',
        'DateApproved' => 'nullable',
        'CivilStatus' => 'nullable|string|max:255',
        'Religion' => 'nullable|string|max:255',
        'Citizenship' => 'nullable|string|max:255',
        'ApplicationStatus' => 'nullable|string|max:255',
        'Notes' => 'nullable|string|max:2000',
        'Trashed' => 'nullable|string|max:5',
        'created_at' => 'nullable',
        'updated_at' => 'nullable'
    ];

    public static function serializeMemberName($memberconsumer) {
        if ($memberconsumer->MembershipType == '1626404083011') { // GET ID OF THE DESIRED JURIDICAL TYPE
            return $memberconsumer->OrganizationName;
        } else {
            return $memberconsumer->FirstName . ' ' . $memberconsumer->LastName . ' ' . $memberconsumer->Suffix;
        }
    }

    public static function serializeMemberNameFormal($memberconsumer) {
        if ($memberconsumer->MembershipType == '1626404083011') { // GET ID OF THE DESIRED JURIDICAL TYPE
            return $memberconsumer->OrganizationName;
        } else {
            return $memberconsumer->LastName . ', ' . $memberconsumer->FirstName . ' ' . $memberconsumer->Suffix;
        }
    }
}
