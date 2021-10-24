<?php

namespace App\Validator;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class IsValidOwner extends Constraint
{
    /*
     * Any public properties become valid options for the annotation.
     * Then, use these in your validator class.
     */
    public $message = 'Nie mogę ustawić innego użytkownika';
    public $anonymousMessage = "Nie możesz ustawić właściciela, jeśli nie jesteś uwierzytelniony. Nara.";
}
