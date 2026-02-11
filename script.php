<?php

/**
 * @package    Com_Accommodation_manager
 * @author     Altea Software Srl <web@altea.it>
 * @copyright  Copyright (C) 2019. Tutti i diritti riservati.
 * @license    GNU General Public License versione 2 o successiva; vedi LICENSE.txt
 */

defined('_JEXEC') or die();

use \Joomla\CMS\Factory;
use \Joomla\CMS\Filesystem\Folder;
use \Joomla\CMS\Language\Text;
use \Joomla\CMS\Installer\Installer;
use \Joomla\CMS\Installer\InstallerScript;

/**
 * Updates the database structure of the component
 *
 * @version  Release: 3.4.0
 * @author   Altea Software Srl <web@altea.it>
 * @since    3.0.0
 */
class com_accommodation_managerInstallerScript extends InstallerScript
{
	/**
	 * The title of the component (printed on installation and uninstallation messages)
	 *
	 * @var string
	 */
	protected $extension = 'Accommodation Manager';

	/**
	 * The minimum Joomla! version required to install this extension
	 *
	 * @var   string
	 */
	protected $minimumJoomla = '4.0';

	/**
	 * Method to install the component
	 *
	 * @param   mixed $parent Object who called this method.
	 *
	 * @return void
	 *
	 * @since 0.2b
	 */
	public function install($parent)
	{
		// Tables are created by sql/install.mysql.utf8.sql (via manifest).
		// Schema upgrades handled by upgradeSchema() in postflight.
		$this->installPlugins($parent);
		$this->installModules($parent);
	}

	/**
	 * Method to update the DB of the component
	 *
	 * @param   mixed $parent Object who started the upgrading process
	 *
	 * @return void
	 *
	 * @since 0.2b
     * @throws Exception
	 */
	private function installDb($parent)
	{
		$installation_folder = $parent->getParent()->getPath('source');

		$app = Factory::getApplication();

		if (function_exists('simplexml_load_file') && file_exists($installation_folder . '/installer/structure.xml'))
		{
			$component_data = simplexml_load_file($installation_folder . '/installer/structure.xml');

			// Check if there are tables to import.
			foreach ($component_data->children() as $table)
			{
				$this->processTable($app, $table);
			}
		}
		else
		{
			if (!function_exists('simplexml_load_file'))
			{
				$app->enqueueMessage(Text::_('This script needs \'simplexml_load_file\' to update the component'));
			}
			else
			{
				$app->enqueueMessage(Text::_('Structure file was not found.'));
			}
		}
	}

	/**
	 * Process a table definition from the structure XML.
	 *
	 * @param   \Joomla\CMS\Application\CMSApplication  $app    Application object
	 * @param   \SimpleXMLElement                        $table  Table to process
	 *
	 * @return  void
	 *
	 * @since   0.2b
	 */
	private function processTable($app, $table)
	{
		$table_added = false;

		if (isset($table['action'])) {
			$table_added = match ((string) $table['action']) {
				'add'    => $this->processTableAdd($app, $table),
				'change' => $this->processTableChange($app, $table),
				'remove' => $this->processTableRemove($app, $table),
				default  => false,
			};
		}

		if (!$table_added && $this->existsTable($table['table_name'])) {
			$this->executeFieldsUpdating($app, $table);
		}
	}

	/**
	 * Handle 'add' action for a table.
	 *
	 * @param   \Joomla\CMS\Application\CMSApplication  $app    Application object
	 * @param   \SimpleXMLElement                        $table  Table definition
	 *
	 * @return  bool  True if the table was created
	 *
	 * @since   3.2.0
	 */
	private function processTableAdd($app, $table): bool
	{
		if ($this->existsTable($table['table_name'])) {
			return false;
		}

		return $this->createTable($app, $table);
	}

	/**
	 * Handle 'change' (rename) action for a table.
	 *
	 * @param   \Joomla\CMS\Application\CMSApplication  $app    Application object
	 * @param   \SimpleXMLElement                        $table  Table definition
	 *
	 * @return  bool  True if the table was created as new
	 *
	 * @since   3.2.0
	 */
	private function processTableChange($app, $table): bool
	{
		$db = Factory::getDbo();

		if ($this->existsTable($table['old_name']) && !$this->existsTable($table['new_name'])) {
			try {
				$db->renameTable($table['old_name'], $table['new_name']);
				$app->enqueueMessage(
					Text::sprintf('Table `%s` was successfully renamed to `%s`', $table['old_name'], $table['new_name'])
				);
			} catch (\Exception $ex) {
				$app->enqueueMessage(
					Text::sprintf('There was an error renaming the table `%s`. Error: %s', $table['old_name'], $ex->getMessage()),
					'error'
				);
			}

			return false;
		}

		if (!$this->existsTable($table['table_name'])) {
			return $this->createTable($app, $table);
		}

		return false;
	}

	/**
	 * Handle 'remove' action for a table.
	 *
	 * @param   \Joomla\CMS\Application\CMSApplication  $app    Application object
	 * @param   \SimpleXMLElement                        $table  Table definition
	 *
	 * @return  bool  Always false (removed tables are not "added")
	 *
	 * @since   3.2.0
	 */
	private function processTableRemove($app, $table): bool
	{
		$db = Factory::getDbo();

		try {
			$db->dropTable((string) $table['table_name'], true);
			$app->enqueueMessage(
				Text::sprintf('Table `%s` was successfully deleted', $table['table_name'])
			);
		} catch (\Exception $ex) {
			$app->enqueueMessage(
				Text::sprintf('There was an error deleting Table `%s`. Error: %s', $table['table_name'], $ex->getMessage()),
				'error'
			);
		}

		return false;
	}

	/**
	 * Create a table from its XML definition.
	 *
	 * @param   \Joomla\CMS\Application\CMSApplication  $app    Application object
	 * @param   \SimpleXMLElement                        $table  Table definition
	 *
	 * @return  bool  True on success
	 *
	 * @since   3.2.0
	 */
	private function createTable($app, $table): bool
	{
		$db = Factory::getDbo();
		$create_statement = $this->generateCreateTableStatement($table);
		$db->setQuery($create_statement);

		try {
			$db->execute();
			$app->enqueueMessage(
				Text::sprintf('Table `%s` has been successfully created', (string) $table['table_name'])
			);

			return true;
		} catch (\Exception $ex) {
			$app->enqueueMessage(
				Text::sprintf('There was an error creating the table `%s`. Error: %s', (string) $table['table_name'], $ex->getMessage()),
				'error'
			);

			return false;
		}
	}

	/**
	 * Checks if a certain exists on the current database
	 *
	 * @param   string $table_name Name of the table
	 *
	 * @return boolean True if it exists, false if it does not.
	 */
	private function existsTable($table_name)
	{
		$db = Factory::getDbo();

		$table_name = str_replace('#__', $db->getPrefix(), (string) $table_name);

		return in_array($table_name, $db->getTableList());
	}

	/**
	 * Generates a 'CREATE TABLE' statement for the tables passed by argument.
	 *
	 * @param   SimpleXMLElement $table Table of the database
	 *
	 * @return string 'CREATE TABLE' statement
	 */
	private function generateCreateTableStatement($table)
	{
		$create_table_statement = '';

		if (isset($table->field))
		{
			$fields = $table->children();

			$fields_definitions = array();
			$indexes            = array();

			$db = Factory::getDbo();

			foreach ($fields as $field)
			{
				$field_definition = $this->generateColumnDeclaration($field);

				if ($field_definition !== false)
				{
					$fields_definitions[] = $field_definition;
				}

				if ($field['index'] == 'index')
				{
					$indexes[] = $field['field_name'];
				}
			}

			foreach ($indexes as $index)
			{
				$fields_definitions[] = Text::sprintf(
					'INDEX %s (%s ASC)',
					$db->quoteName((string) $index), $index
				);
			}

			// Avoid duplicate PK definition
            if (strpos(implode(',', $fields_definitions), 'PRIMARY KEY') === false)
            {
                $fields_definitions[] = 'PRIMARY KEY (`id`)';
            }

			$create_table_statement = Text::sprintf(
				'CREATE TABLE IF NOT EXISTS %s (%s)',
				$table['table_name'],
				implode(',', $fields_definitions)
			);

			if(isset($table['storage_engine']) && !empty($table['storage_engine']))
			{
				$create_table_statement .= " ENGINE=" . $table['storage_engine'];
			}
			if(isset($table['collation']))
			{
				$create_table_statement .= " DEFAULT COLLATE=" . $table['collation'];
			}
		}
		return $create_table_statement;
	}

	/**
	 * Generate a column declaration
	 *
	 * @param   SimpleXMLElement $field Field data
	 *
	 * @return string Column declaration
	 */
	private function generateColumnDeclaration($field)
	{
		$db        = Factory::getDbo();
		$col_name  = $db->quoteName((string) $field['field_name']);
		$data_type = $this->getFieldType($field);

		if ($data_type !== false)
		{
			$default_value = (isset($field['default'])) ? 'DEFAULT ' . $field['default'] : '';

			$other_data = '';

			if (isset($field['is_autoincrement']) && $field['is_autoincrement'] == 1)
			{
				$other_data .= ' AUTO_INCREMENT PRIMARY KEY';
			}

			$comment_value = (isset($field['description'])) ? 'COMMENT ' . $db->quote((string) $field['description']) : '';

			if(strtolower($field['field_type']) == 'datetime' || strtolower($field['field_type']) == 'text')
			{
				return Text::sprintf(
					'%s %s %s %s %s', $col_name, $data_type,
					$default_value, $other_data, $comment_value
				);
			}

			if((isset($field['required']) && $field['required'] == 1)  || $field['field_name'] == 'id')
			{
				return Text::sprintf(
					'%s %s NOT NULL %s %s %s', $col_name, $data_type,
					$default_value, $other_data, $comment_value
				);
			}

			return Text::sprintf(
				'%s %s NULL %s %s %s', $col_name, $data_type,
				$default_value, $other_data, $comment_value
			);
			
		}

		return false;
	}

	/**
	 * Generates SQL field type of a field.
	 *
	 * @param   SimpleXMLElement $field Field information
	 *
	 * @return  mixed SQL string data type, false on failure.
	 */
	private function getFieldType($field)
	{
		$data_type = (string) $field['field_type'];

		if (isset($field['field_length']) && ($this->allowsLengthField($data_type) || $data_type == 'ENUM'))
		{
			$data_type .= '(' . (string) $field['field_length'] . ')';
		}

		return (!empty($data_type)) ? $data_type : false;
	}

	/**
	 * Check if a SQL type allows length values.
	 *
	 * @param   string $field_type SQL type
	 *
	 * @return boolean True if it allows length values, false if it does not.
	 */
	private function allowsLengthField($field_type)
	{
		$allow_length = array(
			'INT',
			'VARCHAR',
			'CHAR',
			'TINYINT',
			'SMALLINT',
			'MEDIUMINT',
			'INTEGER',
			'BIGINT',
			'FLOAT',
			'DOUBLE',
			'DECIMAL',
			'NUMERIC'
		);

		return (in_array((string) $field_type, $allow_length));
	}

	/**
	 * Updates all the fields related to a table.
	 *
	 * @param   CMSApplication  $app   Application Object
	 * @param   SimpleXMLElement $table Table information.
	 *
	 * @return void
	 */
	private function executeFieldsUpdating($app, $table)
	{
		if (isset($table->field))
		{
			foreach ($table->children() as $field)
			{
				$table_name = (string) $table['table_name'];

				$this->processField($app, $table_name, $field);
			}
		}
	}

	/**
	 * Process a certain field.
	 *
	 * @param   \Joomla\CMS\Application\CMSApplication  $app         Application object
	 * @param   string                                   $table_name  The table name
	 * @param   \SimpleXMLElement                        $field       Field information
	 *
	 * @return  void
	 *
	 * @since   0.2b
	 */
	private function processField($app, $table_name, $field)
	{
		if (!isset($field['action'])) {
			$this->addFieldWithMessage($app, $table_name, $field, 'added');
			return;
		}

		match ((string) $field['action']) {
			'add'    => $this->addFieldWithMessage($app, $table_name, $field, 'added'),
			'change' => $this->processFieldChange($app, $table_name, $field),
			'remove' => $this->processFieldRemove($app, $table_name, $field),
			default  => null,
		};
	}

	/**
	 * Handle 'change' action for a field (rename or modify).
	 *
	 * @param   \Joomla\CMS\Application\CMSApplication  $app         Application object
	 * @param   string                                   $table_name  The table name
	 * @param   \SimpleXMLElement                        $field       Field information
	 *
	 * @return  void
	 *
	 * @since   3.2.0
	 */
	private function processFieldChange($app, $table_name, $field)
	{
		if (isset($field['old_name']) && isset($field['new_name'])
			&& $this->existsField($table_name, $field['old_name'])
			&& !$this->existsField($table_name, $field['new_name'])
		) {
			$this->renameField($app, $table_name, $field);
			return;
		}

		$this->addFieldWithMessage($app, $table_name, $field, 'modified');
	}

	/**
	 * Rename a field column.
	 *
	 * @param   \Joomla\CMS\Application\CMSApplication  $app         Application object
	 * @param   string                                   $table_name  The table name
	 * @param   \SimpleXMLElement                        $field       Field information
	 *
	 * @return  void
	 *
	 * @since   3.2.0
	 */
	private function renameField($app, $table_name, $field)
	{
		$db = Factory::getDbo();

		$renaming_statement = Text::sprintf(
			'ALTER TABLE %s CHANGE %s %s %s',
			$table_name,
			$db->quoteName($field['old_name']->__toString()),
			$db->quoteName($field['new_name']->__toString()),
			$this->getFieldType($field)
		);
		$db->setQuery($renaming_statement);

		try {
			$db->execute();
			$app->enqueueMessage(
				Text::sprintf('Field `%s` has been successfully modified', $field['old_name'])
			);
		} catch (\Exception $ex) {
			$app->enqueueMessage(
				Text::sprintf('There was an error modifying the field `%s`. Error: %s', $field['field_name'], $ex->getMessage()),
				'error'
			);
		}
	}

	/**
	 * Handle 'remove' action for a field.
	 *
	 * @param   \Joomla\CMS\Application\CMSApplication  $app         Application object
	 * @param   string                                   $table_name  The table name
	 * @param   \SimpleXMLElement                        $field       Field information
	 *
	 * @return  void
	 *
	 * @since   3.2.0
	 */
	private function processFieldRemove($app, $table_name, $field)
	{
		if (!$this->existsField($table_name, $field['field_name'])) {
			return;
		}

		$db = Factory::getDbo();

		$drop_statement = Text::sprintf(
			'ALTER TABLE %s DROP COLUMN %s',
			$table_name, $field['field_name']
		);
		$db->setQuery($drop_statement);

		try {
			$db->execute();
			$app->enqueueMessage(
				Text::sprintf('Field `%s` has been successfully deleted', $field['field_name'])
			);
		} catch (\Exception $ex) {
			$app->enqueueMessage(
				Text::sprintf('There was an error deleting the field `%s`. Error: %s', $field['field_name'], $ex->getMessage()),
				'error'
			);
		}
	}

	/**
	 * Add/modify a field and enqueue the appropriate message.
	 *
	 * @param   \Joomla\CMS\Application\CMSApplication  $app         Application object
	 * @param   string                                   $table_name  The table name
	 * @param   \SimpleXMLElement                        $field       Field information
	 * @param   string                                   $verb        Action verb for messages ('added' or 'modified')
	 *
	 * @return  void
	 *
	 * @since   3.2.0
	 */
	private function addFieldWithMessage($app, $table_name, $field, string $verb)
	{
		$result = $this->addField($table_name, $field);

		if ($result === true) {
			$app->enqueueMessage(
				Text::sprintf('Field `%s` has been successfully ' . $verb, $field['field_name'])
			);
		} elseif ($result !== false) {
			$app->enqueueMessage(
				Text::sprintf('There was an error with the field `%s`. Error: %s', $field['field_name'], $result),
				'error'
			);
		}
	}

	/**
	 * Add a field if it does not exists or modify it if it does.
	 *
	 * @param   string           $table_name Table name
	 * @param   SimpleXMLElement $field      Field Information
	 *
	 * @return mixed Constant on success(self::$MODIFIED | self::$NOT_MODIFIED), error message if an error occurred
	 */
	private function addField($table_name, $field)
	{
		$db = Factory::getDbo();

		$query_generated = false;

		// Check if the field exists first to prevent issues adding the field
		if ($this->existsField($table_name, $field['field_name']))
		{
			if ($this->needsToUpdate($table_name, $field))
			{
				$change_statement = $this->generateChangeFieldStatement($table_name, $field);
				$db->setQuery($change_statement);
				$query_generated = true;
			}
		}
		else
		{
			$add_statement = $this->generateAddFieldStatement($table_name, $field);
			$db->setQuery($add_statement);
			$query_generated = true;
		}

		if ($query_generated)
		{
			try
			{
				$db->execute();

				return true;
			} catch (Exception $ex)
			{
				return $ex->getMessage();
			}
		}

		return false;
	}

	/**
	 * Checks if a field exists on a table
	 *
	 * @param   string $table_name Table name
	 * @param   string $field_name Field name
	 *
	 * @return boolean True if exists, false if it do
	 */
	private function existsField($table_name, $field_name)
	{
		$db = Factory::getDbo();

		return in_array((string) $field_name, array_keys($db->getTableColumns($table_name)));
	}

	/**
	 * Check if a field needs to be updated.
	 *
	 * @param   string           $table_name Table name
	 * @param   SimpleXMLElement $field      Field information
	 *
	 * @return boolean True if the field has to be updated, false otherwise
	 */
	private function needsToUpdate($table_name, $field)
	{

		if(!isset($field['action']) || $field['field_name'] == 'id')
		{
			return false;
		}
		
		$db = Factory::getDbo();

		$query = Text::sprintf(
			'SHOW FULL COLUMNS FROM `%s` WHERE Field LIKE %s', $table_name, $db->quote((string) $field['field_name'])
		);
		$db->setQuery($query);

		$field_info = $db->loadObject();

		if (strripos($field_info->Type, $this->getFieldType($field)) === false)
		{
			return true;
		}
		else
		{
			return false;
		}
	}

	/**
	 * Generates an change column statement
	 *
	 * @param   string           $table_name Table name
	 * @param   SimpleXMLElement $field      Field Information
	 *
	 * @return string Change column statement
	 */
	private function generateChangeFieldStatement($table_name, $field)
	{
		$column_declaration = $this->generateColumnDeclaration($field);

		return Text::sprintf('ALTER TABLE %s MODIFY %s', $table_name, $column_declaration);
	}

	/**
	 * Generates an add column statement
	 *
	 * @param   string           $table_name Table name
	 * @param   SimpleXMLElement $field      Field Information
	 *
	 * @return string Add column statement
	 */
	private function generateAddFieldStatement($table_name, $field)
	{
		$column_declaration = $this->generateColumnDeclaration($field);

		return Text::sprintf('ALTER TABLE %s ADD %s', $table_name, $column_declaration);
	}

	/**
	 * Get a manifest element (plugins or modules) from the parent installer.
	 *
	 * @param   mixed   $parent  Installer parent object
	 * @param   string  $name    Element name ('plugins' or 'modules')
	 *
	 * @return  \SimpleXMLElement|null
	 *
	 * @since   3.2.0
	 */
	private function getManifestElement($parent, string $name)
	{
		if (method_exists($parent, 'getManifest'))
		{
			return $parent->getManifest()->$name;
		}

		return $parent->get('manifest')->$name;
	}

	/**
	 * Installs plugins for this component
	 *
	 * @param   mixed $parent Object who called the install/update method
	 *
	 * @return void
	 */
	private function installPlugins($parent)
	{
		$installation_folder = $parent->getParent()->getPath('source');
		$app                 = Factory::getApplication();

		$plugins = $this->getManifestElement($parent, 'plugins');

		if ($plugins && count($plugins->children()))
		{
			$db    = Factory::getDbo();
			$query = $db->getQuery(true);

			foreach ($plugins->children() as $plugin)
			{
				$pluginName  = (string) $plugin['plugin'];
				$pluginGroup = (string) $plugin['group'];
				$path        = $installation_folder . '/plugins/' . $pluginGroup . '/' . $pluginName;
				$installer   = new Installer;

				if (!$this->isAlreadyInstalled('plugin', $pluginName, $pluginGroup))
				{
					$result = $installer->install($path);
				}
				else
				{
					$result = $installer->update($path);
				}

				if ($result)
				{
					$app->enqueueMessage('Plugin ' . $pluginName . ' was installed successfully');
				}
				else
				{
					$app->enqueueMessage('There was an issue installing the plugin ' . $pluginName,
						'error');
				}

				$query
					->clear()
					->update('#__extensions')
					->set('enabled = 1')
					->where(
						array(
							'type LIKE ' . $db->quote('plugin'),
							'element LIKE ' . $db->quote($pluginName),
							'folder LIKE ' . $db->quote($pluginGroup)
						)
					);
				$db->setQuery($query);
				$db->execute();
			}
		}
	}

	/**
	 * Check if an extension is already installed in the system
	 *
	 * @param   string $type   Extension type
	 * @param   string $name   Extension name
	 * @param   mixed  $folder Extension folder(for plugins)
	 *
	 * @return boolean
	 */
	private function isAlreadyInstalled($type, $name, $folder = null)
	{
		$result = false;

		switch ($type)
		{
			case 'plugin':
				$result = file_exists(JPATH_PLUGINS . '/' . $folder . '/' . $name);
				break;
			case 'module':
				$result = file_exists(JPATH_SITE . '/modules/' . $name);
				break;
		}

		return $result;
	}

	/**
	 * Installs plugins for this component
	 *
	 * @param   mixed $parent Object who called the install/update method
	 *
	 * @return void
	 */
	private function installModules($parent)
	{
		$installation_folder = $parent->getParent()->getPath('source');
		$app                 = Factory::getApplication();

		$modules = $this->getManifestElement($parent, 'modules');

		if (!empty($modules))
		{

			if (count($modules->children()))
			{
				foreach ($modules->children() as $module)
				{
					$moduleName = (string) $module['module'];
					$path       = $installation_folder . '/modules/' . $moduleName;
					$installer  = new Installer;

					if (!$this->isAlreadyInstalled('module', $moduleName))
					{
						$result = $installer->install($path);
					}
					else
					{
						$result = $installer->update($path);
					}

					if ($result)
					{
						$app->enqueueMessage('Module ' . $moduleName . ' was installed successfully');
					}
					else
					{
						$app->enqueueMessage('There was an issue installing the module ' . $moduleName,
							'error');
					}
				}
			}
		}
	}

	/**
	 * Method to update the component
	 *
	 * @param   mixed $parent Object who called this method.
	 *
	 * @return void
	 */
	public function update($parent)
	{
		// Schema upgrades are handled by upgradeSchema() in postflight.
		// installDb() uses installer/structure.xml which no longer exists.
		$this->installPlugins($parent);
		$this->installModules($parent);
	}

	/**
	 * Runs after install or update. Removes the legacy
	 * com_accommodation_manager_four component if present.
	 *
	 * The old component (Joomla 3 era) used the same DB tables
	 * (#__accommodation_manager_*) but a different element name.
	 * Its uninstall SQL contains DROP TABLE statements that would
	 * destroy shared data, so we remove it manually without
	 * triggering the Joomla uninstaller.
	 *
	 * @param   string  $type    install, update, or discover_install
	 * @param   mixed   $parent  Installer parent object
	 *
	 * @return  void
	 *
	 * @since   3.3.0
	 */
	public function postflight($type, $parent)
	{
		$this->migrateData();
		$this->upgradeSchema();
		$this->removeLegacyComponent();
	}

	/**
	 * Migrate data from the legacy Joomla 3 format.
	 *
	 * Runs BEFORE upgradeSchema() so that data conversions happen while
	 * columns still have the old types (e.g. rate is still VARCHAR when
	 * we convert "--" to NULL, gallery is widened to TEXT before storing
	 * JSON).
	 *
	 * Safe to run multiple times — each step checks current state before
	 * acting.
	 *
	 * @return  void
	 *
	 * @since   3.3.0
	 */
	private function migrateData(): void
	{
		$db  = Factory::getDbo();
		$app = Factory::getApplication();
		$t   = '#__accommodation_manager_';

		// ── 1. rates.rate: convert "--" and non-numeric values to NULL ──
		// Must run while column is still VARCHAR, before MODIFY to DECIMAL
		// (MariaDB would silently convert "--" to 0.00 instead of NULL)

		try
		{
			$columns = $db->getTableColumns($t . 'rates');
			$rateType = $columns['rate'] ?? '';

			if (stripos($rateType, 'char') !== false)
			{
				// Old column is NOT NULL — allow NULL first
				$db->setQuery("ALTER TABLE `{$t}rates` MODIFY COLUMN `rate` VARCHAR(255) NULL DEFAULT NULL");
				$db->execute();

				// Now convert non-numeric values ("--", etc.) to NULL
				$db->setQuery(
					"UPDATE `{$t}rates` SET `rate` = NULL " .
					"WHERE `rate` IS NOT NULL AND TRIM(`rate`) != '' " .
					"AND TRIM(`rate`) NOT REGEXP '^[0-9]+(\\\\.[0-9]+)?$'"
				);
				$db->execute();
				$count = $db->getAffectedRows();

				if ($count > 0)
				{
					$app->enqueueMessage(
						Text::sprintf('Migration: %d non-numeric rate values converted to NULL.', $count)
					);
				}
			}
		}
		catch (\Exception $e)
		{
			$app->enqueueMessage('Migration rates: ' . $e->getMessage(), 'warning');
		}

		// ── 2. room_gallery: convert directory path to JSON subform ──
		// Old format: "images/rooms/suite/" (directory with images)
		// New format: [{"image":"images/rooms/suite/1.jpg","image_mobile":"","alt_de":"",…}]

		try
		{
			if ($this->existsField($t . 'rooms', 'room_gallery'))
			{
				// Ensure column is TEXT first (old schema had VARCHAR(255))
				$columns = $db->getTableColumns($t . 'rooms');

				if (stripos($columns['room_gallery'] ?? '', 'varchar') !== false)
				{
					$db->setQuery("ALTER TABLE `{$t}rooms` MODIFY COLUMN `room_gallery` TEXT NULL");
					$db->execute();
				}

				// Find rows with non-empty, non-JSON gallery values
				$query = $db->getQuery(true)
					->select([$db->quoteName('id'), $db->quoteName('room_gallery')])
					->from($db->quoteName($t . 'rooms'))
					->where($db->quoteName('room_gallery') . ' IS NOT NULL')
					->where($db->quoteName('room_gallery') . " != ''")
					->where($db->quoteName('room_gallery') . " NOT LIKE " . $db->quote('[%'));
				$db->setQuery($query);
				$rows = $db->loadObjectList();

				$converted = 0;

				foreach ($rows as $row)
				{
					$galleryJson = $this->convertGalleryPathToJson($row->room_gallery);

					$update = $db->getQuery(true)
						->update($db->quoteName($t . 'rooms'))
						->set($db->quoteName('room_gallery') . ' = ' . $db->quote($galleryJson))
						->where($db->quoteName('id') . ' = ' . (int) $row->id);
					$db->setQuery($update);
					$db->execute();
					$converted++;
				}

				if ($converted > 0)
				{
					$app->enqueueMessage(
						Text::sprintf('Migration: %d room galleries converted to JSON.', $converted)
					);
				}
			}
		}
		catch (\Exception $e)
		{
			$app->enqueueMessage('Migration gallery: ' . $e->getMessage(), 'warning');
		}

		// ── 3. room_pano: clear data (column will be dropped by upgradeSchema) ──

		try
		{
			if ($this->existsField($t . 'rooms', 'room_pano'))
			{
				$db->setQuery(
					"UPDATE `{$t}rooms` SET `room_pano` = '' " .
					"WHERE `room_pano` IS NOT NULL AND `room_pano` != ''"
				);
				$db->execute();
				$count = $db->getAffectedRows();

				if ($count > 0)
				{
					$app->enqueueMessage(
						Text::sprintf('Migration: %d room panorama values cleared.', $count)
					);
				}
			}
		}
		catch (\Exception $e)
		{
			$app->enqueueMessage('Migration pano: ' . $e->getMessage(), 'warning');
		}
	}

	/**
	 * Convert a gallery directory path to JSON subform format.
	 *
	 * Scans the directory for image files and builds a JSON array
	 * with one entry per image, ready for the repeatable subform.
	 *
	 * @param   string  $path  Directory or file path (relative to JPATH_ROOT)
	 *
	 * @return  string  JSON string, or empty string if no images found
	 *
	 * @since   3.3.0
	 */
	private function convertGalleryPathToJson(string $path): string
	{
		$path = trim($path);

		// Strip Joomla media fragment (images/x.jpg#joomlaImage://…)
		if (strpos($path, '#') !== false)
		{
			$path = explode('#', $path)[0];
		}

		$fullPath = JPATH_ROOT . '/' . ltrim($path, '/');
		$items    = [];
		$allowed  = ['jpg', 'jpeg', 'png', 'gif', 'webp', 'avif'];

		if (is_dir($fullPath))
		{
			$files = @scandir($fullPath);

			if ($files !== false)
			{
				sort($files);

				foreach ($files as $file)
				{
					if ($file === '.' || $file === '..')
					{
						continue;
					}

					$ext = strtolower(pathinfo($file, PATHINFO_EXTENSION));

					if (in_array($ext, $allowed))
					{
						$items[] = [
							'image'        => rtrim($path, '/') . '/' . $file,
							'image_mobile' => '',
							'alt_de'       => '',
							'alt_it'       => '',
							'alt_en'       => '',
							'alt_fr'       => '',
							'alt_es'       => '',
						];
					}
				}
			}
		}
		elseif (is_file($fullPath))
		{
			$items[] = [
				'image'        => $path,
				'image_mobile' => '',
				'alt_de'       => '',
				'alt_it'       => '',
				'alt_en'       => '',
				'alt_fr'       => '',
				'alt_es'       => '',
			];
		}

		if (empty($items))
		{
			return '';
		}

		return json_encode($items, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
	}

	/**
	 * Ensure the database schema matches the expected state.
	 *
	 * When upgrading from the legacy Joomla 3 component, the tables
	 * already exist with the old schema. The CREATE TABLE IF NOT EXISTS
	 * in install.mysql.utf8.sql is skipped. This method adds any
	 * missing columns, modifies column types, adds indexes, and
	 * populates rate_typology_title from existing language data.
	 *
	 * Safe to run multiple times — checks before each change.
	 *
	 * @return  void
	 *
	 * @since   3.3.0
	 */
	private function upgradeSchema(): void
	{
		$db  = Factory::getDbo();
		$app = Factory::getApplication();
		$t   = '#__accommodation_manager_';

		// ── ADD COLUMN (skipped if column already exists) ──

		$addColumns = [
			// 3.0.0 — rooms
			[$t . 'rooms', 'room_price_from',        "VARCHAR(255) NULL DEFAULT ''"],
			[$t . 'rooms', 'room_floor_plan_alt_de',  "VARCHAR(255) NULL DEFAULT ''"],
			[$t . 'rooms', 'room_floor_plan_alt_it',  "VARCHAR(255) NULL DEFAULT ''"],
			[$t . 'rooms', 'room_floor_plan_alt_en',  "VARCHAR(255) NULL DEFAULT ''"],
			[$t . 'rooms', 'room_floor_plan_alt_fr',  "VARCHAR(255) NULL DEFAULT ''"],
			[$t . 'rooms', 'room_floor_plan_alt_es',  "VARCHAR(255) NULL DEFAULT ''"],
			[$t . 'rooms', 'room_thumbnail_alt_de',   "VARCHAR(255) NULL DEFAULT ''"],
			[$t . 'rooms', 'room_thumbnail_alt_it',   "VARCHAR(255) NULL DEFAULT ''"],
			[$t . 'rooms', 'room_thumbnail_alt_en',   "VARCHAR(255) NULL DEFAULT ''"],
			[$t . 'rooms', 'room_thumbnail_alt_fr',   "VARCHAR(255) NULL DEFAULT ''"],
			[$t . 'rooms', 'room_thumbnail_alt_es',   "VARCHAR(255) NULL DEFAULT ''"],

			// 3.0.0 — rate_typologies
			[$t . 'rate_typologies', 'rate_typology_title', "VARCHAR(255) NOT NULL DEFAULT ''"],

			// 3.1.0 — audit columns (all 5 tables)
			[$t . 'rooms',            'created',      'DATETIME NULL DEFAULT NULL'],
			[$t . 'rooms',            'modified_by',  'INT(11) NULL DEFAULT 0'],
			[$t . 'rooms',            'modified',     'DATETIME NULL DEFAULT NULL'],
			[$t . 'rooms',            'version_note', "VARCHAR(255) NULL DEFAULT ''"],
			[$t . 'room_categories',  'created',      'DATETIME NULL DEFAULT NULL'],
			[$t . 'room_categories',  'modified_by',  'INT(11) NULL DEFAULT 0'],
			[$t . 'room_categories',  'modified',     'DATETIME NULL DEFAULT NULL'],
			[$t . 'room_categories',  'version_note', "VARCHAR(255) NULL DEFAULT ''"],
			[$t . 'rate_periods',     'created',      'DATETIME NULL DEFAULT NULL'],
			[$t . 'rate_periods',     'modified_by',  'INT(11) NULL DEFAULT 0'],
			[$t . 'rate_periods',     'modified',     'DATETIME NULL DEFAULT NULL'],
			[$t . 'rate_periods',     'version_note', "VARCHAR(255) NULL DEFAULT ''"],
			[$t . 'rates',            'created',      'DATETIME NULL DEFAULT NULL'],
			[$t . 'rates',            'modified_by',  'INT(11) NULL DEFAULT 0'],
			[$t . 'rates',            'modified',     'DATETIME NULL DEFAULT NULL'],
			[$t . 'rates',            'version_note', "VARCHAR(255) NULL DEFAULT ''"],
			[$t . 'rate_typologies',  'created',      'DATETIME NULL DEFAULT NULL'],
			[$t . 'rate_typologies',  'modified_by',  'INT(11) NULL DEFAULT 0'],
			[$t . 'rate_typologies',  'modified',     'DATETIME NULL DEFAULT NULL'],
			[$t . 'rate_typologies',  'version_note', "VARCHAR(255) NULL DEFAULT ''"],

			// 3.2.0 — room_categories image
			[$t . 'room_categories', 'room_category_image',        "VARCHAR(255) NULL DEFAULT ''"],
			[$t . 'room_categories', 'room_category_image_alt_de', "VARCHAR(255) NULL DEFAULT ''"],
			[$t . 'room_categories', 'room_category_image_alt_it', "VARCHAR(255) NULL DEFAULT ''"],
			[$t . 'room_categories', 'room_category_image_alt_en', "VARCHAR(255) NULL DEFAULT ''"],
			[$t . 'room_categories', 'room_category_image_alt_fr', "VARCHAR(255) NULL DEFAULT ''"],
			[$t . 'room_categories', 'room_category_image_alt_es', "VARCHAR(255) NULL DEFAULT ''"],

			// 3.3.0 — room_class
			[$t . 'rooms', 'room_class', "VARCHAR(255) NULL DEFAULT ''"],
		];

		$columnsAdded = 0;

		foreach ($addColumns as [$table, $column, $type])
		{
			if (!$this->existsField($table, $column))
			{
				try
				{
					$db->setQuery("ALTER TABLE `{$table}` ADD COLUMN `{$column}` {$type}");
					$db->execute();
					$columnsAdded++;
				}
				catch (\Exception $e)
				{
					$app->enqueueMessage("Schema: could not add {$column}: " . $e->getMessage(), 'warning');
				}
			}
		}

		// ── MODIFY COLUMN (safe to re-run) ──

		$modifyColumns = [
			[$t . 'rooms',        'room_gallery',  'TEXT NULL'],
			[$t . 'rates',        'rate',          'DECIMAL(10,2) NULL DEFAULT NULL'],
			[$t . 'rooms',        'room_surface',  "VARCHAR(50) NULL DEFAULT ''"],
			[$t . 'rooms',        'room_people',   "VARCHAR(20) NULL DEFAULT ''"],
			[$t . 'rate_periods', 'period_start',  'DATE NOT NULL'],
			[$t . 'rate_periods', 'period_end',    'DATE NOT NULL'],
		];

		foreach ($modifyColumns as [$table, $column, $type])
		{
			if ($this->existsField($table, $column))
			{
				try
				{
					$db->setQuery("ALTER TABLE `{$table}` MODIFY COLUMN `{$column}` {$type}");
					$db->execute();
				}
				catch (\Exception $e)
				{
					$app->enqueueMessage("Schema: could not modify {$column}: " . $e->getMessage(), 'warning');
				}
			}
		}

		// ── ADD INDEXES (silently ignore duplicates) ──

		$indexes = [
			[$t . 'rooms',           'idx_room_category',        '(`room_category`)'],
			[$t . 'rooms',           'idx_state_ordering',       '(`state`, `ordering`)'],
			[$t . 'room_categories', 'idx_room_category_parent', '(`room_category_parent`)'],
			[$t . 'room_categories', 'idx_state_ordering',       '(`state`, `ordering`)'],
			[$t . 'rates',           'idx_room_id',              '(`room_id`)'],
			[$t . 'rates',           'idx_period_id',            '(`period_id`)'],
			[$t . 'rates',           'idx_typology_id',          '(`typology_id`)'],
			[$t . 'rates',           'idx_state',                '(`state`)'],
			[$t . 'rate_periods',    'idx_state_ordering',       '(`state`, `ordering`)'],
			[$t . 'rate_typologies', 'idx_state_ordering',       '(`state`, `ordering`)'],
		];

		foreach ($indexes as [$table, $name, $columns])
		{
			try
			{
				$db->setQuery("ALTER TABLE `{$table}` ADD INDEX `{$name}` {$columns}");
				$db->execute();
			}
			catch (\Exception $e)
			{
				// Index already exists — expected
			}
		}

		// Unique index on room_name
		try
		{
			$db->setQuery("ALTER TABLE `{$t}rooms` ADD UNIQUE INDEX `idx_room_name` (`room_name`)");
			$db->execute();
		}
		catch (\Exception $e)
		{
			// Already exists
		}

		// ── DROP obsolete columns ──

		if ($this->existsField($t . 'rooms', 'room_pano'))
		{
			try
			{
				$db->setQuery("ALTER TABLE `{$t}rooms` DROP COLUMN `room_pano`");
				$db->execute();
				$app->enqueueMessage('Dropped obsolete column room_pano.');
			}
			catch (\Exception $e)
			{
				$app->enqueueMessage('Could not drop room_pano: ' . $e->getMessage(), 'warning');
			}
		}

		// ── UTF-8MB4 conversion ──

		$allTables = ['rooms', 'room_categories', 'rate_periods', 'rates', 'rate_typologies'];

		foreach ($allTables as $table)
		{
			try
			{
				$db->setQuery("ALTER TABLE `{$t}{$table}` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
				$db->execute();
			}
			catch (\Exception $e)
			{
				// Already utf8mb4
			}
		}

		// ── Populate rate_typology_title from first non-empty language column ──

		try
		{
			$db->setQuery(
				"UPDATE `{$t}rate_typologies` " .
				"SET `rate_typology_title` = COALESCE(" .
					"NULLIF(`rate_typology_de`, ''), " .
					"NULLIF(`rate_typology_it`, ''), " .
					"NULLIF(`rate_typology_en`, ''), " .
					"NULLIF(`rate_typology_fr`, ''), " .
					"NULLIF(`rate_typology_es`, ''), " .
					"'') " .
				"WHERE `rate_typology_title` = '' OR `rate_typology_title` IS NULL"
			);
			$db->execute();
		}
		catch (\Exception $e)
		{
			$app->enqueueMessage('Could not populate rate_typology_title: ' . $e->getMessage(), 'warning');
		}

		if ($columnsAdded > 0)
		{
			$app->enqueueMessage(
				Text::sprintf('Database schema upgraded: %d columns added.', $columnsAdded)
			);
		}
	}

	/**
	 * Remove the legacy com_accommodation_manager_four component
	 * without triggering its uninstall SQL (which would DROP the
	 * shared tables).
	 *
	 * Cleans up: #__extensions, #__menu, #__assets, #__content_types,
	 * and filesystem directories.
	 *
	 * @return  void
	 *
	 * @since   3.3.0
	 */
	private function removeLegacyComponent(): void
	{
		$db  = Factory::getDbo();
		$app = Factory::getApplication();

		$oldElement = 'com_accommodation_manager_four';

		// Check if the old component exists in #__extensions
		$query = $db->getQuery(true)
			->select($db->quoteName('extension_id'))
			->from($db->quoteName('#__extensions'))
			->where($db->quoteName('element') . ' = ' . $db->quote($oldElement))
			->where($db->quoteName('type') . ' = ' . $db->quote('component'));
		$db->setQuery($query);
		$oldExtensionId = (int) $db->loadResult();

		if (!$oldExtensionId)
		{
			return;
		}

		$app->enqueueMessage(
			Text::sprintf('Removing legacy component "%s" (ID %d)...', $oldElement, $oldExtensionId)
		);

		try
		{
			// 1. Remove admin menu items linked to the old component
			$query = $db->getQuery(true)
				->delete($db->quoteName('#__menu'))
				->where($db->quoteName('component_id') . ' = ' . $oldExtensionId);
			$db->setQuery($query);
			$db->execute();

			// 2. Remove ACL assets
			$query = $db->getQuery(true)
				->delete($db->quoteName('#__assets'))
				->where($db->quoteName('name') . ' LIKE ' . $db->quote($oldElement . '%'));
			$db->setQuery($query);
			$db->execute();

			// 3. Remove content type mappings
			$query = $db->getQuery(true)
				->delete($db->quoteName('#__content_types'))
				->where($db->quoteName('type_alias') . ' LIKE ' . $db->quote($oldElement . '.%'));
			$db->setQuery($query);
			$db->execute();

			// 4. Remove the old Finder plugin extension record (if any)
			// The plugin element is "accommodation_manager_fourroomsmanager" (no "com_" prefix)
			$oldElementNoPrefix = str_replace('com_', '', $oldElement);
			$query = $db->getQuery(true)
				->delete($db->quoteName('#__extensions'))
				->where($db->quoteName('element') . ' LIKE ' . $db->quote($oldElementNoPrefix . '%'))
				->where($db->quoteName('type') . ' = ' . $db->quote('plugin'));
			$db->setQuery($query);
			$db->execute();

			// 5. Remove the old component extension record
			$query = $db->getQuery(true)
				->delete($db->quoteName('#__extensions'))
				->where($db->quoteName('extension_id') . ' = ' . $oldExtensionId);
			$db->setQuery($query);
			$db->execute();

			// 6. Remove filesystem directories
			$foldersToRemove = [
				JPATH_ADMINISTRATOR . '/components/' . $oldElement,
				JPATH_SITE . '/components/' . $oldElement,
				JPATH_SITE . '/media/' . $oldElement,
				JPATH_PLUGINS . '/finder/' . $oldElementNoPrefix . 'roomsmanager',
			];

			foreach ($foldersToRemove as $folder)
			{
				if (is_dir($folder))
				{
					Folder::delete($folder);
				}
			}

			$app->enqueueMessage(
				Text::sprintf('Legacy component "%s" removed successfully.', $oldElement)
			);
		}
		catch (\Exception $e)
		{
			$app->enqueueMessage(
				Text::sprintf('Error removing legacy component "%s": %s', $oldElement, $e->getMessage()),
				'warning'
			);
		}
	}

	/**
	 * Method to uninstall the component
	 *
	 * @param   mixed $parent Object who called this method.
	 *
	 * @return void
	 */
	public function uninstall($parent)
	{
		$this->uninstallPlugins($parent);
		$this->uninstallModules($parent);
	}

	/**
	 * Uninstalls plugins
	 *
	 * @param   mixed $parent Object who called the uninstall method
	 *
	 * @return void
	 */
	private function uninstallPlugins($parent)
	{
		$app     = Factory::getApplication();

		$plugins = $this->getManifestElement($parent, 'plugins');

		if ($plugins && count($plugins->children()))
		{
			$db    = Factory::getDbo();
			$query = $db->getQuery(true);

			foreach ($plugins->children() as $plugin)
			{
				$pluginName  = (string) $plugin['plugin'];
				$pluginGroup = (string) $plugin['group'];
				$query
					->clear()
					->select('extension_id')
					->from('#__extensions')
					->where(
						array(
							'type LIKE ' . $db->quote('plugin'),
							'element LIKE ' . $db->quote($pluginName),
							'folder LIKE ' . $db->quote($pluginGroup)
						)
					);
				$db->setQuery($query);
				$extension = $db->loadResult();

				if (!empty($extension))
				{
					$installer = new Installer;
					$result    = $installer->uninstall('plugin', $extension);

					if ($result)
					{
						$app->enqueueMessage('Plugin ' . $pluginName . ' was uninstalled successfully');
					}
					else
					{
						$app->enqueueMessage('There was an issue uninstalling the plugin ' . $pluginName,
							'error');
					}
				}
			}
		}
	}

	/**
	 * Uninstalls plugins
	 *
	 * @param   mixed $parent Object who called the uninstall method
	 *
	 * @return void
	 */
	private function uninstallModules($parent)
	{
		$app = Factory::getApplication();

		$modules = $this->getManifestElement($parent, 'modules');

		if (!empty($modules))
		{

			if (count($modules->children()))
			{
				$db    = Factory::getDbo();
				$query = $db->getQuery(true);

				foreach ($modules->children() as $plugin)
				{
					$moduleName = (string) $plugin['module'];
					$query
						->clear()
						->select('extension_id')
						->from('#__extensions')
						->where(
							array(
								'type LIKE ' . $db->quote('module'),
								'element LIKE ' . $db->quote($moduleName)
							)
						);
					$db->setQuery($query);
					$extension = $db->loadResult();

					if (!empty($extension))
					{
						$installer = new Installer;
						$result    = $installer->uninstall('module', $extension);

						if ($result)
						{
							$app->enqueueMessage('Module ' . $moduleName . ' was uninstalled successfully');
						}
						else
						{
							$app->enqueueMessage('There was an issue uninstalling the module ' . $moduleName,
								'error');
						}
					}
				}
			}
		}
	}

}
