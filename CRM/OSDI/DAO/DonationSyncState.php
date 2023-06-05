<?php

/**
 * @package CRM
 * @copyright CiviCRM LLC https://civicrm.org/licensing
 *
 * Generated from osdi-client/xml/schema/CRM/OSDI/DonationSyncState.xml
 * DO NOT EDIT.  Generated by CRM_Core_CodeGen
 * (GenCodeChecksum:adf8f817be5124b573f414b21541ec28)
 */
use CRM_OSDI_ExtensionUtil as E;

/**
 * Database access object for the DonationSyncState entity.
 */
class CRM_OSDI_DAO_DonationSyncState extends CRM_Core_DAO {
  const EXT = E::LONG_NAME;
  const TABLE_ADDED = '';

  /**
   * Static instance to hold the table name.
   *
   * @var string
   */
  public static $_tableName = 'civicrm_osdi_donation_sync_state';

  /**
   * Should CiviCRM log any modifications to this table in the civicrm_log table.
   *
   * @var bool
   */
  public static $_log = TRUE;

  /**
   * Unique DonationSyncState ID
   *
   * @var int|string|null
   *   (SQL type: int unsigned)
   *   Note that values will be retrieved from the database as a string.
   */
  public $id;

  /**
   * FK to Contribution
   *
   * @var int|string|null
   *   (SQL type: int unsigned)
   *   Note that values will be retrieved from the database as a string.
   */
  public $contribution_id;

  /**
   * FK to OSDI Sync Profile
   *
   * @var int|string|null
   *   (SQL type: int unsigned)
   *   Note that values will be retrieved from the database as a string.
   */
  public $sync_profile_id;

  /**
   * FK to identifier field on remote system
   *
   * @var string|null
   *   (SQL type: varchar(255))
   *   Note that values will be retrieved from the database as a string.
   */
  public $remote_donation_id;

  /**
   * Whether the donation source was local (CiviCRM) or remote
   *
   * @var string|null
   *   (SQL type: varchar(12))
   *   Note that values will be retrieved from the database as a string.
   */
  public $source;

  /**
   * Date and time of the last sync
   *
   * @var string|null
   *   (SQL type: timestamp)
   *   Note that values will be retrieved from the database as a string.
   */
  public $sync_time;

  /**
   * Status code of the last sync, from \Civi\Osdi\Result\Sync
   *
   * @var string|null
   *   (SQL type: varchar(32))
   *   Note that values will be retrieved from the database as a string.
   */
  public $sync_status;

  /**
   * Class constructor.
   */
  public function __construct() {
    $this->__table = 'civicrm_osdi_donation_sync_state';
    parent::__construct();
  }

  /**
   * Returns localized title of this entity.
   *
   * @param bool $plural
   *   Whether to return the plural version of the title.
   */
  public static function getEntityTitle($plural = FALSE) {
    return $plural ? E::ts('Donation Sync States') : E::ts('Donation Sync State');
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
      Civi::$statics[__CLASS__]['links'][] = new CRM_Core_Reference_Basic(self::getTableName(), 'contribution_id', 'civicrm_contribution', 'id');
      Civi::$statics[__CLASS__]['links'][] = new CRM_Core_Reference_Basic(self::getTableName(), 'sync_profile_id', 'civicrm_osdi_sync_profile', 'id');
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
          'description' => E::ts('Unique DonationSyncState ID'),
          'required' => TRUE,
          'where' => 'civicrm_osdi_donation_sync_state.id',
          'table_name' => 'civicrm_osdi_donation_sync_state',
          'entity' => 'DonationSyncState',
          'bao' => 'CRM_OSDI_DAO_DonationSyncState',
          'localizable' => 0,
          'html' => [
            'type' => 'Number',
          ],
          'readonly' => TRUE,
          'add' => NULL,
        ],
        'contribution_id' => [
          'name' => 'contribution_id',
          'type' => CRM_Utils_Type::T_INT,
          'title' => E::ts('Local Contribution ID'),
          'description' => E::ts('FK to Contribution'),
          'import' => TRUE,
          'where' => 'civicrm_osdi_donation_sync_state.contribution_id',
          'export' => TRUE,
          'table_name' => 'civicrm_osdi_donation_sync_state',
          'entity' => 'DonationSyncState',
          'bao' => 'CRM_OSDI_DAO_DonationSyncState',
          'localizable' => 0,
          'FKClassName' => 'CRM_Contribute_DAO_Contribution',
          'html' => [
            'type' => 'EntityRef',
            'label' => E::ts("Contribution"),
          ],
          'add' => NULL,
        ],
        'sync_profile_id' => [
          'name' => 'sync_profile_id',
          'type' => CRM_Utils_Type::T_INT,
          'title' => E::ts('OSDI Sync Profile ID'),
          'description' => E::ts('FK to OSDI Sync Profile'),
          'import' => TRUE,
          'where' => 'civicrm_osdi_donation_sync_state.sync_profile_id',
          'export' => TRUE,
          'table_name' => 'civicrm_osdi_donation_sync_state',
          'entity' => 'DonationSyncState',
          'bao' => 'CRM_OSDI_DAO_DonationSyncState',
          'localizable' => 0,
          'FKClassName' => 'CRM_OSDI_DAO_SyncProfile',
          'add' => NULL,
        ],
        'remote_donation_id' => [
          'name' => 'remote_donation_id',
          'type' => CRM_Utils_Type::T_STRING,
          'title' => E::ts('Remote Donation Identifier'),
          'description' => E::ts('FK to identifier field on remote system'),
          'maxlength' => 255,
          'size' => CRM_Utils_Type::HUGE,
          'import' => TRUE,
          'where' => 'civicrm_osdi_donation_sync_state.remote_donation_id',
          'export' => TRUE,
          'default' => NULL,
          'table_name' => 'civicrm_osdi_donation_sync_state',
          'entity' => 'DonationSyncState',
          'bao' => 'CRM_OSDI_DAO_DonationSyncState',
          'localizable' => 0,
          'add' => NULL,
        ],
        'source' => [
          'name' => 'source',
          'type' => CRM_Utils_Type::T_STRING,
          'title' => E::ts('Source'),
          'description' => E::ts('Whether the donation source was local (CiviCRM) or remote'),
          'maxlength' => 12,
          'size' => CRM_Utils_Type::TWELVE,
          'import' => TRUE,
          'where' => 'civicrm_osdi_donation_sync_state.source',
          'export' => TRUE,
          'table_name' => 'civicrm_osdi_donation_sync_state',
          'entity' => 'DonationSyncState',
          'bao' => 'CRM_OSDI_DAO_DonationSyncState',
          'localizable' => 0,
          'add' => NULL,
        ],
        'sync_time' => [
          'name' => 'sync_time',
          'type' => CRM_Utils_Type::T_TIMESTAMP,
          'title' => E::ts('Sync Time'),
          'description' => E::ts('Date and time of the last sync'),
          'import' => TRUE,
          'where' => 'civicrm_osdi_donation_sync_state.sync_time',
          'export' => TRUE,
          'default' => NULL,
          'table_name' => 'civicrm_osdi_donation_sync_state',
          'entity' => 'DonationSyncState',
          'bao' => 'CRM_OSDI_DAO_DonationSyncState',
          'localizable' => 0,
          'add' => NULL,
        ],
        'sync_status' => [
          'name' => 'sync_status',
          'type' => CRM_Utils_Type::T_STRING,
          'title' => E::ts('Status of Last Sync'),
          'description' => E::ts('Status code of the last sync, from \Civi\Osdi\Result\Sync'),
          'maxlength' => 32,
          'size' => CRM_Utils_Type::MEDIUM,
          'import' => TRUE,
          'where' => 'civicrm_osdi_donation_sync_state.sync_status',
          'export' => TRUE,
          'default' => NULL,
          'table_name' => 'civicrm_osdi_donation_sync_state',
          'entity' => 'DonationSyncState',
          'bao' => 'CRM_OSDI_DAO_DonationSyncState',
          'localizable' => 0,
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
    $r = CRM_Core_DAO_AllCoreTables::getImports(__CLASS__, 'osdi_donation_sync_state', $prefix, []);
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
    $r = CRM_Core_DAO_AllCoreTables::getExports(__CLASS__, 'osdi_donation_sync_state', $prefix, []);
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
      'index_sync_profile_id' => [
        'name' => 'index_sync_profile_id',
        'field' => [
          0 => 'sync_profile_id',
        ],
        'localizable' => FALSE,
        'sig' => 'civicrm_osdi_donation_sync_state::0::sync_profile_id',
      ],
      'index_remote_donation_id' => [
        'name' => 'index_remote_donation_id',
        'field' => [
          0 => 'remote_donation_id',
        ],
        'localizable' => FALSE,
        'sig' => 'civicrm_osdi_donation_sync_state::0::remote_donation_id',
      ],
      'index_sync_time' => [
        'name' => 'index_sync_time',
        'field' => [
          0 => 'sync_time',
        ],
        'localizable' => FALSE,
        'sig' => 'civicrm_osdi_donation_sync_state::0::sync_time',
      ],
      'index_sync_status' => [
        'name' => 'index_sync_status',
        'field' => [
          0 => 'sync_status',
        ],
        'localizable' => FALSE,
        'sig' => 'civicrm_osdi_donation_sync_state::0::sync_status',
      ],
    ];
    return ($localize && !empty($indices)) ? CRM_Core_DAO_AllCoreTables::multilingualize(__CLASS__, $indices) : $indices;
  }

}
