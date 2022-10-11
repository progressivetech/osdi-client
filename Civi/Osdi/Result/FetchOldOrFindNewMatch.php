<?php

namespace Civi\Osdi\Result;

class FetchOldOrFindNewMatch extends AbstractResult {

  use SimpleErrorTrait;

  const ERROR = 'error';

  const FETCHED_SAVED_MATCH = 'fetched saved match';

  const FOUND_NEW_MATCH = 'found new match';

  const NO_MATCH_FOUND = 'no match found';

  public function hasMatch() {
    return in_array($this->statusCode, [self::FETCHED_SAVED_MATCH, self::FOUND_NEW_MATCH]);
  }

}
