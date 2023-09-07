<?php

namespace App\Repositories;

use App\Models\DemandLetters;
use App\Repositories\BaseRepository;

/**
 * Class DemandLettersRepository
 * @package App\Repositories
 * @version May 19, 2023, 8:32 am PST
*/

class DemandLettersRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'AccountNumber',
        'UserId',
        'Status',
        'DateSent',
        'Notes'
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
        return DemandLetters::class;
    }
}
