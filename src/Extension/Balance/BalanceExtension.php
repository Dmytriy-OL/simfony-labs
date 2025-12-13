<?php

namespace App\Extension\Balance;

use App\Entity\Balance\Balance;
use App\Extension\UserRelationExtension;

class BalanceExtension extends UserRelationExtension
{
    public function getResourceClass(): string
    {
        return Balance::class;
    }
}
