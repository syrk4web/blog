<?php
// tests/Entyty/UserTest.php
namespace App\Tests\Form\Type;

use App\Entity\User;
use Symfony\Component\Form\Test\TypeTestCase;
use Symfony\Component\Validator\Validation;

class UserTest extends TypeTestCase
{
    /** @test */
    public function invalidPassword(): void
    {
        $user = new User();
        // get User entity function loadValidatorMetadata to validate
        $validator = Validation::createValidatorBuilder()->addMethodMapping('loadValidatorMetadata')->getValidator();
        // get pair of username and password to validate using loop
        $data = [
            ['username' => 'admin', 'password' => 'admin'],
            ['username' => 'admin', 'password' => '<pddzS4'],
            ['username' => 'admin', 'password' => 'abc'],
            ['username' => 'admin', 'password' => '123'],
        ];
        // loop to validate
        foreach ($data as $value) {
            $user->setUsername($value['username']);
            $user->setPassword($value['password']);
            $errors = $validator->validate($user);
            $this->assertCount(1, $errors);
        }             
    }
}