<?php
/**
 * @version    3.2.0
 * @package    Com_Accommodation_manager
 * @author     Altea Software Srl <web@altea.it>
 * @copyright  Copyright (C) 2026. Tutti i diritti riservati.
 * @license    GNU General Public License versione 2 o successiva; vedi LICENSE.txt
 */

namespace Accomodationmanager\Component\Accommodation_manager\Site\Helper;

defined('_JEXEC') or die;

use Joomla\CMS\Factory;

/**
 * Frontend helper for Accommodation Manager
 *
 * @since  3.2.0
 */
class Accommodation_managerHelper
{
	/**
	 * Valid language suffixes matching DB column naming
	 *
	 * @var array
	 */
	private static array $validLanguages = ['de', 'it', 'en', 'fr', 'es'];

	/**
	 * Gets the language suffix for the current frontend language.
	 * Maps Joomla language tag (e.g. 'de-DE') to DB column suffix (e.g. 'de').
	 *
	 * @return  string  Language suffix (de, it, en, fr, es)
	 *
	 * @since   3.2.0
	 */
	public static function getLanguageSuffix(): string
	{
		$langTag  = Factory::getLanguage()->getTag();
		$langCode = strtolower(substr($langTag, 0, 2));

		if (in_array($langCode, self::$validLanguages, true))
		{
			return $langCode;
		}

		return 'de';
	}
}
