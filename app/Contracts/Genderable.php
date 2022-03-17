<?php

namespace App\Contracts;

interface Genderable
{
    const GENDER__MALE = 'M';
    const GENDER__FEMALE = 'F';
    const GENDER__MALE__FULL = 'Male';
    const GENDER__FEMALE__FULL = 'Female';

    /**
     * Returns genders allowed to the model
     * @return array
     */
    public static function getGenders(): array;

    /**
     * Returns the full string of the abbreviated gender code
     * Ex.: 'M' => 'Male'
     * @param $abbreviated
     * @return string|null
     */
    public static function getFullGender($abbreviated): ?string;
}
