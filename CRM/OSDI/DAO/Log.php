<?php

/**
 * @package CRM
 * @copyright CiviCRM LLC https://civicrm.org/licensing
 *
 * Generated from osdi-client/xml/schema/CRM/OSDI/OsdiLog.xml
 * DO NOT EDIT.  Generated by CRM_Core_CodeGen
 * (GenCodeChecksum:0f54c2166a494cbf26f722d74080c10b)
 */
use CRM_OSDI_ExtensionUtil as E;

/**
 * Database access object for the Log entity.
 */
class CRM_OSDI_DAO_Log extends CRM_Core_DAO {
  const EXT = E::LONG_NAME;
  const TABLE_ADDED = '';

  /**
   * Static instance to hold the table name.
   *
   * @var string
   */
  public static $_tableName = 'civicrm_osdi_log';

  /**
   * Should CiviCRM log any modifications to this table in the civicrm_log table.
   *
   * @var bool
   */
  public static $_log = FALSE;

  /**
   * Unique OsdiLog ID
   *
   * @var int|string|null
   *   (SQL type: int unsigned)
   *   Note that values will be retrieved from the database as a string.
   */
  public $id;

  /**
   * Class that created this log entry
   *
   * @var string
   *   (SQL type: varchar(127))
   *   Note that values will be retrieved from the database as a string.
   */
  public $creator;

  /**
   * @var string|null
   *   (SQL type: varchar(127))
   *   Note that values will be retrieved from the database as a string.
   */
  public $entity_table;

  /**
   * FK to PersonSyncState, DonationSyncState, etc
   *
   * @var int|string|null
   *   (SQL type: int unsigned)
   *   Note that values will be retrieved from the database as a string.
   */
  public $entity_id;

  /**
   * When the log entry was created
   *
   * @var string|null
   *   (SQL type: timestamp)
   *   Note that values will be retrieved from the database as a string.
   */
  public $created_date;

  /**
   * Log context
   *
   * @var string|null
   *   (SQL type: longtext)
   *   Note that values will be retrieved from the database as a string.
   */
  public $details;

  /**
   * Class constructor.
   */
  public function __construct() {
    $this->__table = 'civicrm_osdi_log';
    parent::__construct();
  }

  /**
   * Returns localized title of this entity.
   *
   * @param bool $plural
   *   Whether to return the plural version of the title.
   */
  public static function getEntityTitle($plural = FALSE) {
    return $plural ? E::ts('OSDI Log Entries') : E::ts('OSDI Log Entry');
  }

  /**
   * Returns foreign keys and entity references.
   *
   * @return array
   *   [CRM_Core_Reference_Interface]
   */
  public static function getReferenceColumns() {
    if (!isset(Civi::$statics[__CLASS__]['links'])) {
      Civi::$statics[__CLASS__]['links'] = static::createReferenceColumns(__CLASS__);
      Civi::$statics[__CLASS__]['links'][] = new CRM_Core_Reference_Dynamic(self::getTableName(), 'entity_id', NULL, 'id', 'entity_table');
      CRM_Core_DAO_AllCoreTables::invoke(__CLASS__, 'links_callback', Civi::$statics[__CLASS__]['links']);
    }
    return Civi::$statics[__CLASS__]['links'];
  }

  /**
   * Returns all the column names of this table
   *
   * @return array
   */
  public static function &fields() {
    if (!isset(Civi::$statics[__CLASS__]['fields'])) {
      Civi::$statics[__CLASS__]['fields'] = [
        'id' => [
          'name' => 'id',
          'type' => CRM_Utils_Type::T_INT,
          'title' => E::ts('ID'),
          'description' => E::ts('Unique OsdiLog ID'),
          'required' => TRUE,
          'usage' => [
            'import' => FALSE,
            'export' => FALSE,
            'duplicate_matching' => FALSE,
            'token' => FALSE,
          ],
          'where' => 'civicrm_osdi_log.id',
          'table_name' => 'civicrm_osdi_log',
          'entity' => 'Log',
          'bao' => 'CRM_OSDI_DAO_Log',
          'localizable' => 0,
          'html' => [
            'type' => 'Number',
          ],
          'readonly' => TRUE,
          'add' => NULL,
        ],
        'creator' => [
          'name' => 'creator',
          'type' => CRM_Utils_Type::T_STRING,
          'title' => E::ts('Creator'),
          'description' => E::ts('Class that created this log entry'),
          'required' => TRUE,
          'maxlength' => 127,
          'size' => CRM_Utils_Type::HUGE,
          'usage' => [
            'import' => FALSE,
            'export' => FALSE,
            'duplicate_matching' => FALSE,
            'token' => FALSE,
          ],
          'where' => 'civicrm_osdi_log.creator',
          'table_name' => 'civicrm_osdi_log',
          'entity' => 'Log',
          'bao' => 'CRM_OSDI_DAO_Log',
          'localizable' => 0,
          'add' => NULL,
        ],
        'entity_table' => [
          'name' => 'entity_table',
          'type' => CRM_Utils_Type::T_STRING,
          'title' => E::ts('Entity Table'),
          'maxlength' => 127,
          'size' => CRM_Utils_Type::HUGE,
          'usage' => [
            'import' => FALSE,
            'export' => FALSE,
            'duplicate_matching' => FALSE,
            'token' => FALSE,
          ],
          'where' => 'civicrm_osdi_log.entity_table',
          'table_name' => 'civicrm_osdi_log',
          'entity' => 'Log',
          'bao' => 'CRM_OSDI_DAO_Log',
          'localizable' => 0,
          'pseudoconstant' => [
            'optionGroupName' => 'osdi_log_used_for',
            'optionEditPath' => 'civicrm/admin/options/osdi_log_used_for',
          ],
          'add' => NULL,
        ],
        'entity_id' => [
          'name' => 'entity_id',
          'type' => CRM_Utils_Type::T_INT,
          'title' => E::ts('Entity'),
          'description' => E::ts('FK to PersonSyncState, DonationSyncState, etc'),
          'usage' => [
            'import' => FALSE,
            'export' => FALSE,
            'duplicate_matching' => FALSE,
            'token' => FALSE,
          ],
          'where' => 'civicrm_osdi_log.entity_id',
          'table_name' => 'civicrm_osdi_log',
          'entity' => 'Log',
          'bao' => 'CRM_OSDI_DAO_Log',
          'localizable' => 0,
          'add' => NULL,
        ],
        'created_date' => [
          'name' => 'created_date',
          'type' => CRM_Utils_Type::T_TIMESTAMP,
          'title' => E::ts('Created Date'),
          'description' => E::ts('When the log entry was created'),
          'usage' => [
            'import' => FALSE,
            'export' => FALSE,
            'duplicate_matching' => FALSE,
            'token' => FALSE,
          ],
          'where' => 'civicrm_osdi_log.created_date',
          'default' => 'CURRENT_TIMESTAMP',
          'table_name' => 'civicrm_osdi_log',
          'entity' => 'Log',
          'bao' => 'CRM_OSDI_DAO_Log',
          'localizable' => 0,
          'add' => NULL,
        ],
        'details' => [
          'name' => 'details',
          'type' => CRM_Utils_Type::T_LONGTEXT,
          'title' => E::ts('Details'),
          'description' => E::ts('Log context'),
          'usage' => [
            'import' => FALSE,
            'export' => FALSE,
            'duplicate_matching' => FALSE,
            'token' => FALSE,
          ],
          'where' => 'civicrm_osdi_log.details',
          'table_name' => 'civicrm_osdi_log',
          'entity' => 'Log',
          'bao' => 'CRM_OSDI_DAO_Log',
          'localizable' => 0,
          'serialize' => self::SERIALIZE_JSON,
          'add' => NULL,
        ],
      ];
      CRM_Core_DAO_AllCoreTables::invoke(__CLASS__, 'fields_callback', Civi::$statics[__CLASS__]['fields']);
    }
    return Civi::$statics[__CLASS__]['fields'];
  }

  /**
   * Return a mapping from field-name to the corresponding key (as used in fields()).
   *
   * @return array
   *   Array(string $name => string $uniqueName).
   */
  public static function &fieldKeys() {
    if (!isset(Civi::$statics[__CLASS__]['fieldKeys'])) {
      Civi::$statics[__CLASS__]['fieldKeys'] = array_flip(CRM_Utils_Array::collect('name', self::fields()));
    }
    return Civi::$statics[__CLASS__]['fieldKeys'];
  }

  /**
   * Returns the names of this table
   *
   * @return string
   */
  public static function getTableName() {
    return self::$_tableName;
  }

  /**
   * Returns if this table needs to be logged
   *
   * @return bool
   */
  public function getLog() {
    return self::$_log;
  }

  /**
   * Returns the list of fields that can be imported
   *
   * @param bool $prefix
   *
   * @return array
   */
  public static function &import($prefix = FALSE) {
    $r = CRM_Core_DAO_AllCoreTables::getImports(__CLASS__, 'osdi_log', $prefix, []);
    return $r;
  }

  /**
   * Returns the list of fields that can be exported
   *
   * @param bool $prefix
   *
   * @return array
   */
  public static function &export($prefix = FALSE) {
    $r = CRM_Core_DAO_AllCoreTables::getExports(__CLASS__, 'osdi_log', $prefix, []);
    return $r;
  }

  /**
   * Returns the list of indices
   *
   * @param bool $localize
   *
   * @return array
   */
  public static function indices($localize = TRUE) {
    $indices = [
      'index_creator' => [
        'name' => 'index_creator',
        'field' => [
          0 => 'creator',
        ],
        'localizable' => FALSE,
        'sig' => 'civicrm_osdi_log::0::creator',
      ],
      'index_entity_table' => [
        'name' => 'index_entity_table',
        'field' => [
          0 => 'entity_table',
        ],
        'localizable' => FALSE,
        'sig' => 'civicrm_osdi_log::0::entity_table',
      ],
      'index_entity_id' => [
        'name' => 'index_entity_id',
        'field' => [
          0 => 'entity_id',
        ],
        'localizable' => FALSE,
        'sig' => 'civicrm_osdi_log::0::entity_id',
      ],
      'index_created_date' => [
        'name' => 'index_created_date',
        'field' => [
          0 => 'created_date',
        ],
        'localizable' => FALSE,
        'sig' => 'civicrm_osdi_log::0::created_date',
      ],
    ];
    return ($localize && !empty($indices)) ? CRM_Core_DAO_AllCoreTables::multilingualize(__CLASS__, $indices) : $indices;
  }

}
