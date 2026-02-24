# Accommodation Manager — API per accesso dati

> Questo documento va integrato nella documentazione del componente (Help button) quando verr&agrave; risolta la [GitHub Issue #1](https://github.com/paolo-altea/accomodation_manager/issues/1).

## Panoramica

Il componente espone metodi statici per accedere ai dati di camere e categorie da qualsiasi contesto PHP all'interno di Joomla (articoli, template override, plugin, moduli, file PHP custom).

I dati vengono restituiti gi&agrave; tradotti nella lingua corrente del sito.

## Metodi disponibili

| Metodo | Ritorna | Query DB |
|--------|---------|----------|
| `getCategoriesWithRooms()` | Array di categorie, ognuna con `->rooms` | 2 |
| `getRooms()` | Array di tutte le camere | 1 |
| `getCategories()` | Array di tutte le categorie | 1 |
| `getRoom(int $id)` | Singola camera o `null` | 1 |

## Uso da articolo Joomla (Sourcerer o simili)

All'interno di un articolo, con un'estensione che permette l'esecuzione di PHP (es. Sourcerer), inserire:

```php
\Joomla\CMS\Factory::getApplication()->bootComponent('com_accommodation_manager');

$categories = \Accomodationmanager\Component\Accommodation_manager\Site\Helper\Accommodation_managerHelper::getCategoriesWithRooms();

foreach ($categories as $category) :
    echo '<h2>' . htmlspecialchars($category->name) . '</h2>';

    if (!empty($category->description)) :
        echo '<p>' . $category->description . '</p>';
    endif;

    echo '<ul>';
    foreach ($category->rooms as $room) :
        echo '<li>' . htmlspecialchars($room->title) . '</li>';
    endforeach;
    echo '</ul>';
endforeach;
```

## Uso da file PHP (template override, plugin, script custom)

In un file PHP che viene caricato nel contesto Joomla (es. un template override o un file incluso tramite `require`):

```php
<?php
defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Accomodationmanager\Component\Accommodation_manager\Site\Helper\Accommodation_managerHelper;

// Boot del componente (registra il namespace per l'autoloader)
Factory::getApplication()->bootComponent('com_accommodation_manager');

// Categorie con camere annidate
$categories = Accommodation_managerHelper::getCategoriesWithRooms();

foreach ($categories as $category) {
    echo '<h2>' . htmlspecialchars($category->name) . '</h2>';

    foreach ($category->rooms as $room) {
        echo '<div class="room">';
        echo '<h3>' . htmlspecialchars($room->title) . '</h3>';
        echo '<p>' . $room->intro . '</p>';

        if ($room->room_surface) {
            echo '<p>Superficie: ' . (int) $room->room_surface . ' m&sup2;</p>';
        }

        if ($room->room_price_from) {
            echo '<p>A partire da: ' . number_format((float) $room->room_price_from, 2, ',', '.') . ' &euro;</p>';
        }

        echo '</div>';
    }
}
```

## Propriet&agrave; disponibili

### Oggetto categoria

| Propriet&agrave; | Tipo | Descrizione |
|----------|------|-------------|
| `id` | int | ID categoria |
| `name` | string | Nome nella lingua corrente |
| `description` | string | Descrizione nella lingua corrente |
| `image` | string | Path immagine categoria |
| `image_alt` | string | Alt text immagine nella lingua corrente |
| `ordering` | int | Ordinamento |
| `rooms` | array | Camere associate (solo con `getCategoriesWithRooms()`) |

### Oggetto camera

| Propriet&agrave; | Tipo | Descrizione |
|----------|------|-------------|
| `id` | int | ID camera |
| `room_name` | string | Nome interno (non tradotto) |
| `room_code` | string | Codice univoco camera |
| `room_category` | int | ID categoria di appartenenza |
| `room_surface` | int | Superficie in m&sup2; |
| `room_people` | string | Numero persone (es. "2-4") |
| `room_price_from` | decimal | Prezzo a partire da |
| `room_class` | string | Classe CSS custom |
| `title` | string | Titolo nella lingua corrente |
| `intro` | string | Testo introduttivo nella lingua corrente |
| `description` | string | Descrizione completa nella lingua corrente |
| `thumbnail` | string | Path immagine thumbnail |
| `thumbnail_alt` | string | Alt text thumbnail nella lingua corrente |
| `floor_plan` | string | Path immagine planimetria |
| `floor_plan_alt` | string | Alt text planimetria nella lingua corrente |
| `gallery` | string | JSON raw della gallery |
| `gallery_items` | array | Gallery decodificata (oggetti con `image`, `image_mobile`, `alt`) |
| `video` | string | URL video |
| `ordering` | int | Ordinamento |
| `category_name` | string | Nome categoria nella lingua corrente |
| `category_description` | string | Descrizione categoria nella lingua corrente |

## Note

- **`bootComponent` &egrave; obbligatorio**: le classi dei componenti di terze parti non sono registrate nell'autoloader globale di Joomla. Il boot registra il namespace e rende disponibili le classi.
- **Lingua automatica**: tutti i campi multilingua vengono restituiti nella lingua attiva del sito. Non serve specificare la lingua.
- **Solo dati pubblicati**: i metodi restituiscono solo record con `state = 1`.
- **Negli articoli** (Sourcerer/eval): usare il namespace completo con `\` iniziale, il `use` potrebbe non funzionare.
- **Nei file PHP** (template, plugin): si pu&ograve; usare `use` normalmente, posizionandolo in cima al file.
