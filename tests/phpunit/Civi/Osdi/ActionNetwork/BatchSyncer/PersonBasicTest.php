<?php

namespace Civi\Osdi\ActionNetwork\BatchSyncer;

use Civi;
use Civi\Osdi\Factory;
use Civi\Osdi\LocalObject\PersonBasic as LocalPerson;
use CRM_OSDI_ActionNetwork_TestUtils;
use PHPUnit;

/**
 * Test \Civi\Osdi\RemoteSystemInterface
 *
 * @group headless
 */
class PersonBasicTest extends PHPUnit\Framework\TestCase implements
    \Civi\Test\HeadlessInterface,
    \Civi\Test\TransactionalInterface {

  /**
   * @var array{Contact: array, OptionGroup: array, OptionValue: array, CustomGroup: array, CustomField: array}
   */
  private static $createdEntities = [];

  /**
   * @var \Civi\Osdi\ActionNetwork\RemoteSystem
   */
  public static $system;

  /**
   * @var \Civi\Osdi\ActionNetwork\Mapper\Reconciliation2022May001
   */
  public static $mapper;

  public function setUpHeadless(): \Civi\Test\CiviEnvBuilder {
    return \Civi\Test::headless()
      ->installMe(__DIR__)
      ->apply();
  }

  public static function setUpBeforeClass(): void {
    self::$system = CRM_OSDI_ActionNetwork_TestUtils::createRemoteSystem();
    parent::setUpBeforeClass();
  }

  public function setUp(): void {
    parent::setUp();
  }

  public function tearDown(): void {
    parent::tearDown();
  }

  public static function tearDownAfterClass(): void {
    foreach (self::$createdEntities as $type => $ids) {
      foreach ($ids as $id) {
        civicrm_api4($type, 'delete', [
          'where' => [['id', '=', $id]],
          'checkPermissions' => FALSE,
        ]);
      }
    }

    parent::tearDownAfterClass();
  }

  public function testBatchSyncFromANDoesNotRunConcurrently() {
    $remotePerson = new \Civi\Osdi\ActionNetwork\Object\Person(self::$system);
    $remotePerson->emailAddress->set($email = "syncjobtest-no-concurrent@null.org");
    $remotePerson->save();

    $justBeforePersonWasModified =
      \Civi\Osdi\ActionNetwork\RemoteSystem::formatDateTime(
      strtotime($remotePerson->modifiedDate->get()) - 1);

    Civi::settings()->add([
      'osdiClient.syncJobProcessId' => getmypid(),
      'osdiClient.syncJobActNetModTimeCutoff' => $justBeforePersonWasModified,
      'osdiClient.syncJobEndTime' => NULL,
    ]);

    $singleSyncer = Factory::singleton('SingleSyncer', 'Person', self::$system);
    $batchSyncer = Factory::singleton('BatchSyncer', 'Person', $singleSyncer);

    $batchSyncer->batchSyncFromRemote();
    $syncedContactCount = \Civi\Api4\Email::get(FALSE)
      ->addWhere('email', '=', $email)
      ->execute()->count();

    self::assertEquals(0, $syncedContactCount);

    Civi::settings()->set('osdiClient.syncJobProcessId', 9999999999999);
    sleep(1);
    $batchSyncer->batchSyncFromRemote();

    $syncedContactCount = \Civi\Api4\Email::get(FALSE)
      ->addWhere('email', '=', $email)
      ->execute()->count();

    self::assertEquals(1, $syncedContactCount);
  }

  public function testBatchSyncFromAN() {
    $localPeople = $this->setUpBatchSyncFromAN();

    $syncStartTime = time();

    $singleSyncer = Factory::singleton('SingleSyncer', 'Person', self::$system);
    $batchSyncer = Factory::singleton('BatchSyncer', 'Person', $singleSyncer);

    $batchSyncer->batchSyncFromRemote();

    $this->assertBatchSyncFromAN($localPeople, $syncStartTime);
  }

  public function testBatchSyncFromCivi() {
    [$remotePeople, $maxRemoteModTimeBeforeSync] = $this->setUpBatchSyncFromCivi();

    $syncStartTime = time();

    $singleSyncer = Factory::singleton('SingleSyncer', 'Person', self::$system);
    $batchSyncer = Factory::singleton('BatchSyncer', 'Person', $singleSyncer);

    $batchSyncer->batchSyncFromLocal();

    $this->assertBatchSyncFromCivi($remotePeople, $syncStartTime, $maxRemoteModTimeBeforeSync);
  }

  public function testBatchSyncViaApiCall() {
    $localPeople = $this->setUpBatchSyncFromAN();
    [$remotePeople, $maxRemoteModTimeBeforeSync] = $this->setUpBatchSyncFromCivi();

    $syncStartTime = time();
    sleep(1);

    $result = civicrm_api3('Job', 'osdiclientbatchsynccontacts',
      ['debug' => 1, 'api_token' => ACTION_NETWORK_TEST_API_TOKEN]);

    sleep(1);

    self::assertEquals(0, $result['is_error']);
    $this->assertBatchSyncFromAN($localPeople, $syncStartTime);
    $this->assertBatchSyncFromCivi($remotePeople, $syncStartTime, $maxRemoteModTimeBeforeSync);
  }

  private function setUpBatchSyncFromAN(): array {
    $twoSecondsAgo = self::$system::formatDateTime(time() - 2);

    Civi::settings()->add([
      'osdiClient.syncJobProcessId' => 99999999999999,
      'osdiClient.syncJobActNetModTimeCutoff' => $twoSecondsAgo,
      'osdiClient.syncJobStartTime' => strtotime("2000-11-11 00:00:00"),
      'osdiClient.syncJobEndTime' => strtotime("2000-11-11 00:00:11"),
    ]);

    $testTime = time();

    for ($i = 1; $i < 5; $i++) {
      $remotePerson = new \Civi\Osdi\ActionNetwork\Object\Person(self::$system);
      $remotePerson->emailAddress->set($email = "syncJobFromANTest$i@null.org");
      $remotePerson->givenName->set('Sync Job Test');
      $remotePerson->familyName->set($lastName = "$i $testTime");
      $remotePeople[$i] = $remotePerson->save();
      $remoteModTime = strtotime($remotePerson->modifiedDate->get());

      $localPerson = new LocalPerson();
      $localPerson->emailEmail->set($email);
      $localPerson->firstName->set('Unsynced');
      $localPerson->lastName->set($lastName);
      $localPeople[$i] = $localPerson->save();
      $localModTime = strtotime($localPerson->modifiedDate->get());

      $syncState = new \Civi\Osdi\PersonSyncState();
      $syncState->setSyncOrigin(\Civi\Osdi\PersonSyncState::ORIGIN_REMOTE);
      $syncState->setRemotePersonId($remotePerson->getId());
      $syncState->setContactId($localPerson->getId());
      $syncState->setRemotePreSyncModifiedTime($remoteModTime - 10);
      $syncState->setRemotePostSyncModifiedTime($remoteModTime);
      $syncState->setLocalPreSyncModifiedTime($localModTime - 10);
      $syncState->setLocalPostSyncModifiedTime($localModTime);
      $syncState->save();
      /** @var \Civi\Osdi\PersonSyncState[] $syncStates */
      $syncStates[$i] = $syncState;

      usleep(400000);
    }

    usleep(700000);
    $remotePeople[3]->languageSpoken->set('es');
    $remotePeople[3]->save();

    self::assertGreaterThan(
      $syncStates[3]->getRemotePostSyncModifiedTime(),
      strtotime($remotePeople[3]->modifiedDate->get())
    );

    // "find" results can lag. we wait for them to catch up
    $foundByModDate = FALSE;
    for ($i = 0; $i < 5; $i++) {
      $searchResults = self::$system->find('osdi:people', [
        ['modified_date', 'gt', $testTime],
      ]);
      foreach ($searchResults as $remotePerson) {
        if ($remotePerson->getId() === $remotePeople[3]->getId()) {
          $foundByModDate = TRUE;
          break 2;
        }
      }
      sleep(1);
    }

    self::assertTrue($foundByModDate);

    return $localPeople;
  }

  /**
   * @param \Civi\Osdi\LocalObject\Person\N2F[] $localPeople
   * @param int $syncStartTime
   *
   * @return void
   */
  private function assertBatchSyncFromAN(array $localPeople, int $syncStartTime): void {
    foreach ($localPeople as $i => $localPerson) {
      $localPerson->load();
      if ($i === 3) {
        self::assertEquals('Sync Job Test', $localPerson->firstName->get());
        self::assertGreaterThanOrEqual($syncStartTime, strtotime($localPerson->modifiedDate->get()));
        self::assertLessThan($syncStartTime + 60, strtotime($localPerson->modifiedDate->get()));
      }
      else {
        self::assertEquals('Unsynced', $localPerson->firstName->get());
        self::assertLessThan($syncStartTime, strtotime($localPerson->modifiedDate->get()));
      }
    }
  }

  private function setUpBatchSyncFromCivi(): array {
    $twoSecondsAgo = self::$system::formatDateTime(time() - 2);

    Civi::settings()->add([
      'osdiClient.syncJobProcessId' => 99999999999999,
      'osdiClient.syncJobCiviModTimeCutoff' => $twoSecondsAgo,
      'osdiClient.syncJobStartTime' => strtotime("2000-11-11 00:00:00"),
      'osdiClient.syncJobEndTime' => strtotime("2000-11-11 00:00:11"),
    ]);

    $testTime = time();

    /** @var \Civi\Osdi\LocalObject\PersonBasic[] $localPeople */
    for ($i = 1; $i < 5; $i++) {
      $localPerson = new LocalPerson();
      $localPerson->emailEmail->set($email = "syncJobFromCiviTest$i@null.org");
      $localPerson->firstName->set('Sync Job Test');
      $localPerson->lastName->set($lastName = "$i $testTime");
      $localPeople[$i] = $localPerson->save();
      $localModTime = strtotime($localPerson->modifiedDate->get());

      $remotePerson = new \Civi\Osdi\ActionNetwork\Object\Person(self::$system);
      $remotePerson->emailAddress->set($email);
      $remotePerson->givenName->set('test (not yet synced)');
      $remotePerson->familyName->set($lastName);
      $remotePeople[$i] = $remotePerson->save();
      $remoteModTime = strtotime($remotePerson->modifiedDate->get());

      $syncState = new \Civi\Osdi\PersonSyncState();
      $syncState->setSyncOrigin(\Civi\Osdi\PersonSyncState::ORIGIN_REMOTE);
      $syncState->setRemotePersonId($remotePerson->getId());
      $syncState->setContactId($localPerson->getId());
      $syncState->setLocalPreSyncModifiedTime($localModTime - 10);
      $syncState->setLocalPostSyncModifiedTime($localModTime);
      $syncState->setRemotePreSyncModifiedTime($remoteModTime - 10);
      $syncState->setRemotePostSyncModifiedTime($remoteModTime);
      $syncState->save();

      usleep(400000);
    }

    usleep(700000);
    $localPeople[3]->preferredLanguage->set('es');
    $localPeople[3]->save();

    return array($remotePeople, strtotime($remotePeople[4]->modifiedDate->get()));
  }

  private function assertBatchSyncFromCivi($remotePeople, int $syncStartTime, $maxRemoteModTimeBeforeSync): void {
    foreach ($remotePeople as $i => $remotePerson) {
      /** @var \Civi\Osdi\ActionNetwork\Object\Person $remotePerson */
      $remotePerson->load();
      if ($i == 3) {
        self::assertEquals('Sync Job Test', $remotePerson->givenName->get());
        self::assertGreaterThanOrEqual($syncStartTime, strtotime($remotePerson->modifiedDate->get()));
        self::assertLessThan($syncStartTime + 60, strtotime($remotePerson->modifiedDate->get()));
      }
      else {
        self::assertEquals('test (not yet synced)', $remotePerson->givenName->get());
        self::assertLessThanOrEqual($maxRemoteModTimeBeforeSync, strtotime($remotePerson->modifiedDate->get()));
      }
    }
  }

}