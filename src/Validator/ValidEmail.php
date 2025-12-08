<?php
namespace App\Validator;

use Attribute;
use Symfony\Component\Validator\Constraint;

#[Attribute]
class ValidEmail extends Constraint
{
    public string $message = 'Email "{{ value }}" некоректний!';
}
