<?php

use CRM_Osdi_ExtensionUtil as E;
use Civi\Test\HeadlessInterface;
use Civi\Test\HookInterface;
use Civi\Test\TransactionalInterface;
use Jsor\HalClient\HalClient;
use Jsor\HalClient\HalResource;

/**
 * Unit tests for OsdiPerson class
 *
 * @group headless
 */
class CRM_OSDI_Generic_OsdiPersonTest extends \PHPUnit\Framework\TestCase implements HeadlessInterface, HookInterface, TransactionalInterface {

  public function setUpHeadless() {
    // Civi\Test has many helpers, like install(), uninstall(), sql(), and sqlFile().
    // See: https://docs.civicrm.org/dev/en/latest/testing/phpunit/#civitest
    return \Civi\Test::headless()
        ->installMe(__DIR__)
        ->apply();
  }

  public function setUp() {
    parent::setUp();
  }

  public function tearDown() {
    parent::tearDown();
  }

  public function makeSystem() {
    return new Civi\Osdi\Mock\RemoteSystem();
  }

  public function makeBlankOsdiPerson() {
    return new Civi\Osdi\Generic\OsdiPerson();
  }

  public function makeExistingOsdiPerson() {
    $client = new HalClient('');
    $personResource = new HalResource($client, [
        'id' => 'generic001',
        'given_name' => 'Testy',
        'family_name' => 'McTest',
        'email_addresses' => [
            [
                "primary" => true,
                "address" => "testy@test.net",
                "status" => "subscribed",
            ],
        ],
        'phone_numbers' => [
            [
                'primary' => 'true',
                'number' => '12024444444',
                'number_type' => 'Mobile',
                'status' => 'subscribed',
            ],
        ],
        'identifiers' => ['other_system:999']
    ]);
    return new Civi\Osdi\Generic\OsdiPerson($personResource);
  }

  public function expected($key) {
    $expected = [
        'existingPersonUrl' => 'http://te.st/people/generic001',
        'existingPersonId' => 'generic001',
        'existingPersonIdException' => 'Cannot change the id of the Civi\Osdi\Generic\OsdiPerson '
            .'whose id is already set to "generic001".',
    ];
    return $expected[$key];
  }

  public function testOverwriteScalarField() {
    $person = $this->makeExistingOsdiPerson();
    $person->set('given_name', 'Mr. Zest');
    $this->assertEquals('Mr. Zest', $person->getAltered('given_name'));
  }

  public function testAlterWrongFieldThrowsException() {
    $person = $this->makeBlankOsdiPerson();
    $this->expectException(\Civi\Osdi\Exception\InvalidArgumentException::class);
    $person->set('definitely * not * a * valid * field', 'foo');
  }

  public function testOverwriteMultiValueField() {
    $person = $this->makeExistingOsdiPerson();
    $newEmail = [
        'primary' => 'false',
        'address' => 'second@email.address',
        'status' => 'subscribed'
    ];
    $person->set('email_addresses', [$newEmail]);
    $this->assertContains('email_addresses', $person->getFieldsToClearBeforeWriting());
    $this->assertEquals($newEmail, $person->getAltered('email_addresses')[0]);
  }

  public function testAppendToMultiValueField() {
    $person = $this->makeExistingOsdiPerson();
    $originalIdentifier = $person->getOriginal('identifiers')[0] ?? NULL;
    $this->assertNotNull($originalIdentifier);
    $newIdentifier = 'biminy:bomboulash';
    $person->appendTo('identifiers', $newIdentifier);
    $this->assertEquals($originalIdentifier, $person->getOriginal('identifiers')[0]);
    $this->assertEquals($newIdentifier, $person->getAltered('identifiers')[0]);
  }

  public function testClearMultiValueField() {
    $person = $this->makeExistingOsdiPerson();
    $originalPhone = $person->getOriginal('phone_numbers')[0] ?? NULL;
    $this->assertNotNull($originalPhone);
    $person->clearField('phone_numbers');
    $this->assertEquals($originalPhone, $person->getOriginal('phone_numbers')[0]);
    $this->assertContains('phone_numbers', $person->getFieldsToClearBeforeWriting());
  }

  public function testClearWrongFieldThrowsException() {
    $person = $this->makeExistingOsdiPerson();
    $this->expectException(\Civi\Osdi\Exception\InvalidArgumentException::class);
    $this->expectExceptionMessage('Cannot clear field "given_name"');
    $person->clearField('given_name');
  }

  public function testGetIdFromExistingPerson() {
    $person = $this->makeExistingOsdiPerson();
    $this->assertEquals($this->expected('existingPersonId'), $person->getId());
  }

  public function testSetIdWhenBlank() {
    $person = $this->makeBlankOsdiPerson();
    $person->setId("A");
    $this->assertEquals("A", $person->getId());
  }

  public function testOverwriteIdThrowsException() {
    $person = $this->makeExistingOsdiPerson();
    $this->expectExceptionMessage($this->expected('existingPersonIdException'));
    $person->setId("A");
  }

  public function testGetUrl() {
    $person = $this->makeExistingOsdiPerson();
    $system = $this->makeSystem();
    $this->assertEquals($this->expected('existingPersonUrl'), $person->getOwnUrl($system));
  }

}
