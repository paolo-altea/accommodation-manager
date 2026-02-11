<?php
/**
 * @version    3.2.0
 * @package    Com_Accommodation_manager
 * @author     Altea Software Srl <web@altea.it>
 * @copyright  Copyright (C) 2026. Tutti i diritti riservati.
 * @license    GNU General Public License versione 2 o successiva; vedi LICENSE.txt
 */

namespace Accomodationmanager\Component\Accommodation_manager\Site\Service;

defined('_JEXEC') or die;

use Accomodationmanager\Component\Accommodation_manager\Site\Helper\Accommodation_managerHelper;
use Joomla\CMS\Application\SiteApplication;
use Joomla\CMS\Component\Router\RouterView;
use Joomla\CMS\Component\Router\RouterViewConfiguration;
use Joomla\CMS\Component\Router\Rules\MenuRules;
use Joomla\CMS\Component\Router\Rules\NomenuRules;
use Joomla\CMS\Component\Router\Rules\StandardRules;
use Joomla\CMS\Factory;
use Joomla\CMS\Filter\OutputFilter;
use Joomla\CMS\Menu\AbstractMenu;
use Joomla\Database\DatabaseInterface;

/**
 * SEF Router for com_accommodation_manager frontend views.
 *
 * View hierarchy:
 *   categories  (list of all categories)
 *   rooms       (list of all rooms)
 *   category    (rooms filtered by category, key=id)
 *     └── room  (single room detail, key=id)
 *   rates       (rates grid)
 *
 * Generates language-aware slugs for category and room URLs.
 * Falls back to room_name / room_category_title when localized title is empty.
 *
 * @since  3.2.0
 */
class Router extends RouterView
{
	/**
	 * @var  array|null  Cached rooms lookup
	 */
	private ?array $roomsLookup = null;

	/**
	 * @var  array|null  Cached categories lookup
	 */
	private ?array $categoriesLookup = null;

	public function __construct(SiteApplication $app, AbstractMenu $menu)
	{
		// Categories list view (keyed so that layout variants are found in lookup)
		$categories = new RouterViewConfiguration('categories');
		$categories->setKey('id');
		$this->registerView($categories);

		// Rooms list view (all rooms, no category filter)
		$rooms = new RouterViewConfiguration('rooms');
		$this->registerView($rooms);

		// Category view (child of categories list)
		$category = new RouterViewConfiguration('category');
		$category->setKey('id')->setParent($categories);
		$this->registerView($category);

		// Room detail view (child of rooms list)
		$room = new RouterViewConfiguration('room');
		$room->setKey('id')->setParent($rooms);
		$this->registerView($room);

		// Rates grid view
		$rates = new RouterViewConfiguration('rates');
		$this->registerView($rates);

		parent::__construct($app, $menu);

		$this->attachRule(new MenuRules($this));
		$this->attachRule(new StandardRules($this));
		$this->attachRule(new NomenuRules($this));
	}

	/**
	 * Build segment for a category (ID → slug).
	 *
	 * @param   int    $id     Category ID
	 * @param   array  $query  Query string parameters
	 *
	 * @return  array  Single-element array [id => slug]
	 *
	 * @since   3.2.0
	 */
	public function getCategorySegment($id, $query): array
	{
		$categories = $this->loadCategories();

		if (!isset($categories[$id]))
		{
			return [(int) $id => (string) $id];
		}

		$lang = Accommodation_managerHelper::getLanguageSuffix();
		$slug = $this->buildSlug($categories[$id], 'room_category_name_' . $lang, 'room_category_title');

		return [(int) $id => $slug];
	}

	/**
	 * Parse segment back to category ID (slug → ID).
	 *
	 * @param   string  $segment  URL segment
	 * @param   array   $query    Query string parameters
	 *
	 * @return  int  Category ID, or 0 if not found
	 *
	 * @since   3.2.0
	 */
	public function getCategoryId($segment, $query): int
	{
		$categories = $this->loadCategories();
		$lang       = Accommodation_managerHelper::getLanguageSuffix();
		$nameCol    = 'room_category_name_' . $lang;

		// First pass: match against slugified localized name
		foreach ($categories as $cat)
		{
			$name = $cat->$nameCol ?? '';

			if ($name !== '')
			{
				$slug = OutputFilter::stringURLSafe($name);

				if ($slug === $segment)
				{
					return (int) $cat->id;
				}
			}
		}

		// Second pass: match against slugified backend title
		foreach ($categories as $cat)
		{
			$slug = OutputFilter::stringURLSafe($cat->room_category_title);

			if ($slug === $segment)
			{
				return (int) $cat->id;
			}
		}

		return 0;
	}

	/**
	 * Build segment for the categories list view.
	 *
	 * The categories view shows all categories (no individual ID).
	 * Always returns key=0 to match the menu item lookup entry
	 * (buildLookup defaults to 0 for menu items without a key value).
	 *
	 * @param   int    $id     Passed ID (ignored)
	 * @param   array  $query  Query string parameters
	 *
	 * @return  array  Single-element array [0 => '']
	 *
	 * @since   3.2.0
	 */
	public function getCategoriesSegment($id, $query): array
	{
		return [0 => ''];
	}

	/**
	 * Parse segment back to categories ID (always 0).
	 *
	 * @param   string  $segment  URL segment
	 * @param   array   $query    Query string parameters
	 *
	 * @return  int  Always 0
	 *
	 * @since   3.2.0
	 */
	public function getCategoriesId($segment, $query): int
	{
		return 0;
	}

	/**
	 * Build segment for a single room (ID → slug).
	 *
	 * @param   int    $id     Room ID
	 * @param   array  $query  Query string parameters
	 *
	 * @return  array  Single-element array [id => slug]
	 *
	 * @since   3.2.0
	 */
	public function getRoomSegment($id, $query): array
	{
		$rooms = $this->loadRooms();

		if (!isset($rooms[$id]))
		{
			return [(int) $id => (string) $id];
		}

		$lang = Accommodation_managerHelper::getLanguageSuffix();
		$slug = $this->buildSlug($rooms[$id], 'room_title_' . $lang, 'room_name');

		return [(int) $id => $slug];
	}

	/**
	 * Parse segment back to room ID (slug → ID).
	 *
	 * @param   string  $segment  URL segment
	 * @param   array   $query    Query string parameters
	 *
	 * @return  int  Room ID, or 0 if not found
	 *
	 * @since   3.2.0
	 */
	public function getRoomId($segment, $query): int
	{
		$rooms = $this->loadRooms();
		$lang  = Accommodation_managerHelper::getLanguageSuffix();
		$titleCol = 'room_title_' . $lang;

		// First pass: match against slugified localized title
		foreach ($rooms as $room)
		{
			$title = $room->$titleCol ?? '';

			if ($title !== '')
			{
				$slug = OutputFilter::stringURLSafe($title);

				if ($slug === $segment)
				{
					return (int) $room->id;
				}
			}
		}

		// Second pass: match against slugified room_name
		foreach ($rooms as $room)
		{
			$slug = OutputFilter::stringURLSafe($room->room_name);

			if ($slug === $segment)
			{
				return (int) $room->id;
			}
		}

		return 0;
	}

	/**
	 * Build a URL slug from a localised column with a fallback column.
	 *
	 * @param   object  $item          DB row object
	 * @param   string  $localizedCol  Localised column name (e.g. room_title_de)
	 * @param   string  $fallbackCol   Fallback column name (e.g. room_name)
	 *
	 * @return  string  URL-safe slug
	 *
	 * @since   3.2.0
	 */
	private function buildSlug(object $item, string $localizedCol, string $fallbackCol): string
	{
		$name = $item->$localizedCol ?? '';

		if ($name !== '')
		{
			$slug = OutputFilter::stringURLSafe($name);

			if ($slug !== '')
			{
				return $slug;
			}
		}

		return OutputFilter::stringURLSafe($item->$fallbackCol);
	}

	/**
	 * Load all published categories for slug lookup.
	 *
	 * @return  array  Categories indexed by ID
	 *
	 * @since   3.2.0
	 */
	private function loadCategories(): array
	{
		if ($this->categoriesLookup !== null)
		{
			return $this->categoriesLookup;
		}

		$db = Factory::getContainer()->get(DatabaseInterface::class);

		$columns = [
			$db->quoteName('id'),
			$db->quoteName('room_category_title'),
		];

		foreach (Accommodation_managerHelper::VALID_LANGUAGES as $lang)
		{
			$columns[] = $db->quoteName('room_category_name_' . $lang);
		}

		$query = $db->getQuery(true)
			->select($columns)
			->from($db->quoteName('#__accommodation_manager_room_categories'))
			->where($db->quoteName('state') . ' = 1');

		$db->setQuery($query);
		$this->categoriesLookup = $db->loadObjectList('id') ?: [];

		return $this->categoriesLookup;
	}

	/**
	 * Load all published rooms for slug lookup.
	 *
	 * @return  array  Rooms indexed by ID
	 *
	 * @since   3.2.0
	 */
	private function loadRooms(): array
	{
		if ($this->roomsLookup !== null)
		{
			return $this->roomsLookup;
		}

		$db = Factory::getContainer()->get(DatabaseInterface::class);

		$columns = [
			$db->quoteName('id'),
			$db->quoteName('room_name'),
		];

		foreach (Accommodation_managerHelper::VALID_LANGUAGES as $lang)
		{
			$columns[] = $db->quoteName('room_title_' . $lang);
		}

		$query = $db->getQuery(true)
			->select($columns)
			->from($db->quoteName('#__accommodation_manager_rooms'))
			->where($db->quoteName('state') . ' = 1');

		$db->setQuery($query);
		$this->roomsLookup = $db->loadObjectList('id') ?: [];

		return $this->roomsLookup;
	}
}
