<?php
/**
 * @version    3.1.0
 * @package    Com_Accommodation_manager
 * @author     Altea Software Srl <web@altea.it>
 * @copyright  Copyright (C) 2024. Tutti i diritti riservati.
 * @license    GNU General Public License versione 2 o successiva; vedi LICENSE.txt
 */

namespace Accomodationmanager\Component\Accommodation_manager\Administrator\Helper;

defined('_JEXEC') or die;

use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Factory;
use Joomla\CMS\Object\CMSObject;

/**
 * Accommodation_manager helper.
 *
 * @since  2.0.1
 */
class Accommodation_managerHelper
{
	/**
	 * All supported languages with their labels
	 *
	 * @var array
	 */
	public const LANGUAGES = [
		'de' => 'Deutsch',
		'it' => 'Italiano',
		'en' => 'English',
		'fr' => 'Français',
		'es' => 'Español',
	];

	/**
	 * Gets the enabled languages from component configuration
	 *
	 * @return  array  Array of enabled language codes (e.g., ['de', 'it', 'en'])
	 *
	 * @since   3.1.0
	 */
	public static function getEnabledLanguages(): array
	{
		$params = ComponentHelper::getParams('com_accommodation_manager');
		$enabled = [];

		foreach (array_keys(self::LANGUAGES) as $lang)
		{
			// Default to enabled (1) if not set
			if ($params->get('lang_' . $lang, 1))
			{
				$enabled[] = $lang;
			}
		}

		// Ensure at least one language is always enabled
		if (empty($enabled))
		{
			$enabled = ['de'];
		}

		return $enabled;
	}

	/**
	 * Gets the enabled languages with their labels
	 *
	 * @return  array  Associative array of enabled languages (e.g., ['de' => 'Deutsch', 'it' => 'Italiano'])
	 *
	 * @since   3.1.0
	 */
	public static function getEnabledLanguagesWithLabels(): array
	{
		$enabled = self::getEnabledLanguages();
		$result = [];

		foreach ($enabled as $lang)
		{
			$result[$lang] = self::LANGUAGES[$lang];
		}

		return $result;
	}

	/**
	 * Gets the files attached to an item
	 *
	 * @param   int     $pk     The item's id
	 * @param   string  $table  The table's name
	 * @param   string  $field  The field's name
	 *
	 * @return  array  The files
	 */
	public static function getFiles($pk, $table, $field)
	{
		$db = Factory::getContainer()->get('DatabaseDriver');
		$query = $db->getQuery(true);

		$query
			->select($field)
			->from($table)
			->where('id = ' . (int) $pk);

		$db->setQuery($query);

		return explode(',', $db->loadResult());
	}

	/**
	 * Gets a list of the actions that can be performed.
	 *
	 * @return  CMSObject
	 *
	 * @since   2.0.1
	 */
	public static function getActions()
	{
		$user   = Factory::getApplication()->getIdentity();
		$result = new CMSObject;

		$assetName = 'com_accommodation_manager';

		$actions = array(
			'core.admin', 'core.manage', 'core.create', 'core.edit', 'core.edit.own', 'core.edit.state', 'core.delete'
		);

		foreach ($actions as $action)
		{
			$result->set($action, $user->authorise($action, $assetName));
		}

		return $result;
	}
}

