<?php

namespace App\Contracts;

interface GenderableUndeclaredIncluded extends Genderable
{
    const GENDER__UNDECLARED = 'O';
    const GENDER__UNDECLARED__FULL = 'Undeclared';
}
