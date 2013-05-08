<?php
namespace AltregoTests;

use Altrego\Altrego,
    AltregoTests\Fixture;

class AltregoTest extends AltregoTestCase
{

    public function testCanCreateAltregoInstance()
    {
        $alterEgo = Altrego::create(new Fixture\Foo());

        $this->assertInstanceOf('Altrego\Altrego', $alterEgo);
    }

    public function testCanAccessPrivateVariable()
    {
        $alterEgo = Altrego::create(new Fixture\Foo());

        $this->assertEquals('This is a private variable', $alterEgo->priv);
    }

    public function testCanUpdatePrivateVariable()
    {
        $alterEgo = Altrego::create(new Fixture\Foo());
        $updatedText = 'Updated text!"£$%^&*()';
        $alterEgo->priv = $updatedText;

        $this->assertEquals($updatedText, $alterEgo->priv);
    }

    public function testCanExecutePrivateMethod()
    {
        $alterEgo = Altrego::create(new Fixture\Foo());

        $echoText = 'Echo text!"£$%^&*()';
        $this->assertEquals($echoText, $alterEgo->privFunc($echoText));
    }

    public function testCanExecutePrivateMethodWithArrayParams()
    {
        $alterEgo = Altrego::create(new Fixture\Foo());

        $echoText = array('Echo', 'text!', '"£$%^&*()');
        $this->assertEquals($echoText, $alterEgo->privFunc($echoText));
    }

    public function testCanSetPrivateArrayEntry()
    {
        $alterEgo = Altrego::create(new Fixture\Foo());

        $alterEgo->privArray = array();

        $this->assertCount(0, $alterEgo->privArray);

        $newValue = 'New Value!"£$%^&*(';
        $alterEgo->privArray[] = $newValue;
        $this->assertContains($newValue, $alterEgo->privArray);

        $this->assertCount(1, $alterEgo->privArray);
    }

    public function testCanSetPrivateAssociativeArrayEntry()
    {
        $alterEgo = Altrego::create(new Fixture\Foo());

        $alterEgo->privArray = array();

        $key = 'key-123';
        $value = 'value-456';
        $alterEgo->privArray[$key] = $value;

        $this->assertArrayHasKey($key, $alterEgo->privArray);
        $this->assertEquals($value, $alterEgo->privArray[$key]);

        unset($alterEgo->privArray[$key]);

        $this->assertArrayNotHasKey($key, $alterEgo->privArray);
        $this->assertCount(0, $alterEgo->privArray);
    }

    public function testCanCallPrivateStaticMethod()
    {
        $alterEgo = Altrego::create(new Fixture\Foo());

        $arguments = 'Staic Call !"£$%';
        $this->assertEquals($arguments, Altrego::callStatic($alterEgo, 'privStatFunc', $arguments));

        $arrayArguments = array('Static',  'Call!', '"£$%');
        $this->assertEquals($arguments, Altrego::callStatic($alterEgo, 'privStatFunc', $arguments));
    }

    public function testObjectMaintainsState()
    {
        $object = new Fixture\Foo();
        $alterEgo = Altrego::create($object);

        $this->assertEquals($object, $alterEgo->getObject());
    }


}