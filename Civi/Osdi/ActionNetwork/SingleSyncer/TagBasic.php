<?php

namespace Civi\Osdi\ActionNetwork\SingleSyncer;

use Civi\Osdi\LocalObjectInterface;
use Civi\Osdi\LocalRemotePair;
use Civi\Osdi\RemoteObjectInterface;
use Civi\Osdi\RemoteSystemInterface;
use Civi\Osdi\Result\FetchOldOrFindNewMatch as OldOrNewMatchResult;
use Civi\Osdi\SingleSyncerInterface;
use Civi\Osdi\Util;

class TagBasic extends AbstractSingleSyncer implements SingleSyncerInterface {

  public function __construct(RemoteSystemInterface $remoteSystem) {
    $this->remoteSystem = $remoteSystem;
  }

  public function getSavedMatch(
    LocalRemotePair $pair,
    int $syncProfileId = NULL
  ): ?LocalRemotePair {
    $side = $pair->isOriginLocal() ? 'local' : 'remote';
    $objectId = $pair->getOriginObject()->getId();
    $syncProfileId = $syncProfileId ?? $this->getSyncProfile()['id'] ?? 'null';
    $savedMatches = self::getOrSetAllSavedMatches()[$syncProfileId] ?? [];
    return $savedMatches[$side][$objectId] ?? NULL;
  }

  protected function getLocalObjectClass(): string {
    return \Civi\Osdi\LocalObject\TagBasic::class;
  }

  protected function getRemoteObjectClass(): string {
    return \Civi\Osdi\ActionNetwork\Object\Tag::class;
  }

  public function makeLocalObject($id = NULL): LocalObjectInterface {
    return new \Civi\Osdi\LocalObject\TagBasic($id);
  }

  public function makeRemoteObject($id = NULL): RemoteObjectInterface {
    $tag = new \Civi\Osdi\ActionNetwork\Object\Tag($this->getRemoteSystem());
    if ($id) {
      $tag->setId($id);
    }
    return $tag;
  }

  public function saveMatch(LocalRemotePair $pair): LocalRemotePair {
    $localObject = $pair->getLocalObject();
    $remoteObject = $pair->getRemoteObject();
    $localId = $localObject->getId();
    $remoteId = $remoteObject->getId();
    $savedMatches = $this->getOrSetAllSavedMatches();
    $syncProfileId = $this->getSyncProfile()['id'] ?? 'null';

    if ($oldMatchForLocal = $savedMatches[$syncProfileId]['local'][$localId] ?? NULL) {
      $oldMatchRemoteId = $oldMatchForLocal->getRemoteObject()->getId();
      unset($savedMatches[$syncProfileId]['remote'][$oldMatchRemoteId]);
      unset($savedMatches['persistable'][$syncProfileId][$localId]);
    }
    if ($oldMatchForRemote = $savedMatches[$syncProfileId]['remote'][$remoteId] ?? NULL) {
      $oldMatchLocalId = $oldMatchForRemote->getLocalObject()->getId();
      unset($savedMatches[$syncProfileId]['local'][$oldMatchLocalId]);
      unset($savedMatches['persistable'][$syncProfileId][$oldMatchLocalId]);
    }

    $pair = new LocalRemotePair($localObject, $remoteObject);
    $savedMatches[$syncProfileId]['local'][$localId] = $pair;
    $savedMatches[$syncProfileId]['remote'][$remoteId] = $pair;
    $savedMatches['persistable'][$syncProfileId][$localId] = $remoteId;

    $this->getOrSetAllSavedMatches($savedMatches);
    return $pair;
  }

  /**
   * @param \Civi\Osdi\LocalRemotePair $pair
   *
   * @return \Civi\Osdi\LocalRemotePair
   */
  protected function saveSyncStateIfNeeded(LocalRemotePair $pair) {
    /** @var \Civi\Osdi\Result\FetchOldOrFindNewMatch $r */
    $r = $pair->getResultStack()->getLastOfType(OldOrNewMatchResult::class);
    if ($r->isStatus($r::FETCHED_SAVED_MATCH)) {
      return $pair;
    }
    return $this->saveMatch($pair);
  }

  protected function typeCheckLocalObject(LocalObjectInterface $object): \Civi\Osdi\LocalObject\TagBasic {
    Util::assertClass($object, \Civi\Osdi\LocalObject\TagBasic::class);
    /** @var \Civi\Osdi\LocalObject\TagBasic $object */
    return $object;
  }

  protected function typeCheckRemoteObject(RemoteObjectInterface $object): \Civi\Osdi\ActionNetwork\Object\Tag {
    Util::assertClass($object, \Civi\Osdi\ActionNetwork\Object\Tag::class);
    /** @var \Civi\Osdi\ActionNetwork\Object\Tag $object */
    return $object;
  }

  private function getOrSetAllSavedMatches($replacement = NULL): array {
    static $matchArray = NULL;

    if (is_array($replacement)) {
      $matchArray = $replacement;
      \Civi::cache('long')
        ->set('osdi-client:tag-match', $matchArray['persistable']);
    }

    elseif (is_null($matchArray)) {
      $idArray = \Civi::cache('long')->get('osdi-client:tag-match', []);
      $matchArray = ['persistable' => $idArray];
      foreach ($idArray as $syncProfileId => $matches) {
        foreach ($matches as $localId => $remoteId) {
          $localObject = $this->makeLocalObject($localId);
          $remoteObject = $this->makeRemoteObject($remoteId);
          $pair = new LocalRemotePair($localObject, $remoteObject);
          $matchArray[$syncProfileId]['local'][$localId] = $pair;
          $matchArray[$syncProfileId]['remote'][$remoteId] = $pair;
        }
      }
    }
    return $matchArray;
  }

}