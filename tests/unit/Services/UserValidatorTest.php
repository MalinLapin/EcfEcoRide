<?php
use PHPUnit\Framework\TestCase;
use App\services\UserValidator;

class UserValidatorTest extends TestCase
{
    /**
     * Méthode de test de validation du format d'email.
     * 
     * @return void
     */
    public function testValidateEmail():void
    {
        $this->assertTrue(UserValidator::validateEmail('toto@ecoride.fr'));
        $this->assertFalse(UserValidator::validateEmail('notanemail'));
    }

    /**
     * Méthode de test de validation de la complexiter du Mdp
     * 
     * @return void
     */
    public function testValidatePasswordStrength():void
    {
        $this->assertTrue(UserValidator::validatePasswordStrength('Abcd1234!avde'));
        $this->assertFalse(UserValidator::validatePasswordStrength('abc'));
    }
}