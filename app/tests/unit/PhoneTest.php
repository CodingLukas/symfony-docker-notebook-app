<?php


namespace App\Tests\unit;

use App\Entity\Phone;
use PHPUnit\Framework\TestCase;

class PhoneTest extends TestCase
{
    public function testSettingProductNumber()
    {
        $phone = new Phone();
        $number = "+37061158523";

        $phone->setNumber($number);

        $this->assertEquals($number, $phone->getNumber());
    }

    public function testSettingProductName()
    {
        $phone = new Phone();
        $name = "Tom";

        $phone->setName($name);

        $this->assertEquals($name, $phone->getName());
    }
}
