<?php

namespace App\Repositories;

use App\Models\DemandLetterMonths;
use App\Repositories\BaseRepository;

/**
 * Class DemandLetterMonthsRepository
 * @package App\Repositories
 * @version May 19, 2023, 8:33 am PST
*/

class DemandLetterMonthsRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
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
     * Return searchable fields
     *
     * @return array
     */
    public function getFieldsSearchable()
    {
        return $this->fieldSearchable;
    }

    /**
     * Configure the Model
     **/
    public function model()
    {
        return DemandLetterMonths::class;
    }
}
