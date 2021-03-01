<?php

namespace Civi\Osdi;

use Civi\Osdi\Exception\InvalidArgumentException;
use Civi\Osdi\Generic\OsdiObject;

interface RemoteObjectInterface {

  public function getNamespace(): string;

  public function getType(): string;

  public function getOwnUrl(RemoteSystemInterface $system);

  /**
   * @param string|null $fieldName
   * @return mixed
   * @throws InvalidArgumentException
   */
  public function getOriginal(string $fieldName);

  /**
   * @param string $key
   * @return mixed|null
   */
  public function getAltered(string $key);

  public function getAllAltered(): array;

  /**
   * @param string $fieldName
   * @param mixed $val
   * @throws InvalidArgumentException
   */
  public function set(string $fieldName, $val);

  /**
   * @param string $fieldName
   * @param mixed $val
   * @throws InvalidArgumentException
   */
  public function appendTo(string $fieldName, $val);

  /**
   * @param string $fieldName
   * @throws InvalidArgumentException
   */
  public function clearField(string $fieldName);

  public function getFieldsToClearBeforeWriting(): array;

}