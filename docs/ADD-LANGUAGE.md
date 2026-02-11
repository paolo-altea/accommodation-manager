# Checklist: Aggiungere una nuova lingua

## Architettura multilingua del componente

Accommodation Manager **non usa** le Joomla Associations o tabelle di traduzione. Ogni campo tradotto ha una colonna per lingua nel database con suffisso `_de`, `_it`, `_en`, `_fr`, `_es` (es. `room_title_de`, `room_title_it`, ecc.).

Il frontend determina la lingua attiva con `getLanguageSuffix()` che prende i primi 2 caratteri del tag Joomla (es. `de-DE` -> `de`) e li usa come suffisso per selezionare la colonna corretta.

Aggiungere una nuova lingua significa: creare le colonne DB, i campi nei form XML, e le chiavi nei file .ini.

---

## Placeholder usati in questa checklist

| Placeholder | Significato | Esempio |
|---|---|---|
| `{LANG}` | Codice ISO 639-1 (minuscolo, 2 caratteri) | `pt` |
| `{LANG_UPPER}` | Stesso codice in MAIUSCOLO | `PT` |
| `{LOCALE}` | Locale Joomla completo | `pt-PT` |
| `{NomeLingua}` | Nome della lingua nella lingua stessa | `Portugues` |

I primi 2 caratteri di `{LOCALE}` devono corrispondere a `{LANG}` (il mapping in `getLanguageSuffix()` usa `substr($tag, 0, 2)`).

---

## Suggerimento operativo

Per ogni passo che richiede di "aggiungere dopo `_es`", il modo piu semplice e copiare il blocco `_es` esistente e sostituire `_es` / `_ES` / `es` con `_{LANG}` / `_{LANG_UPPER}` / `{LANG}`.

File di riferimento da usare come template: le chiavi `_ES` / `es-ES` esistenti.

---

## 1. Database -- Colonne nuova lingua

### 1a. File di update SQL

Creare `src/administrator/components/com_accommodation_manager/sql/updates/mysql/X.X.X.sql` con:

```sql
-- Rooms: titoli, intro, descrizioni, alt text
ALTER TABLE `#__accommodation_manager_rooms`
    ADD COLUMN `room_title_{LANG}` VARCHAR(255) DEFAULT '' AFTER `room_title_es`,
    ADD COLUMN `room_intro_{LANG}` TEXT AFTER `room_intro_es`,
    ADD COLUMN `room_description_{LANG}` TEXT AFTER `room_description_es`,
    ADD COLUMN `room_floor_plan_alt_{LANG}` VARCHAR(255) DEFAULT '' AFTER `room_floor_plan_alt_es`,
    ADD COLUMN `room_thumbnail_alt_{LANG}` VARCHAR(255) DEFAULT '' AFTER `room_thumbnail_alt_es`;

-- Categories: nomi, descrizioni, alt immagine
ALTER TABLE `#__accommodation_manager_room_categories`
    ADD COLUMN `room_category_name_{LANG}` VARCHAR(255) DEFAULT '' AFTER `room_category_name_es`,
    ADD COLUMN `room_category_description_{LANG}` TEXT AFTER `room_category_description_es`,
    ADD COLUMN `room_category_image_alt_{LANG}` VARCHAR(255) DEFAULT '' AFTER `room_category_image_alt_es`;

-- Rate periods: titoli periodo
ALTER TABLE `#__accommodation_manager_rate_periods`
    ADD COLUMN `period_title_{LANG}` VARCHAR(255) DEFAULT '' AFTER `period_title_es`;

-- Rate typologies: titoli tipologia
ALTER TABLE `#__accommodation_manager_rate_typologies`
    ADD COLUMN `rate_typology_{LANG}` VARCHAR(255) DEFAULT '' AFTER `rate_typology_es`;
```

### 1b. File di install SQL

Aggiungere le stesse colonne in `src/administrator/components/com_accommodation_manager/sql/install.mysql.utf8.sql` nelle rispettive tabelle, dopo le colonne `*_es`.

---

## 2. Configurazione componente

File: `src/administrator/components/com_accommodation_manager/config.xml`

### 2a. Toggle lingua (nel fieldset `component`, dopo `lang_es`)

```xml
<field
    name="lang_{LANG}"
    type="radio"
    default="1"
    label="COM_ACCOMMODATION_MANAGER_CONFIG_LANG_{LANG_UPPER}"
    layout="joomla.form.field.radio.switcher"
    >
    <option value="0">JNO</option>
    <option value="1">JYES</option>
</field>
```

### 2b. Link richiesta (nel fieldset `links`, dopo `request_link_es`)

```xml
<field
    name="request_link_{LANG}"
    type="url"
    label="COM_ACCOMMODATION_MANAGER_CONFIG_REQUEST_LINK_{LANG_UPPER}"
    description="COM_ACCOMMODATION_MANAGER_CONFIG_REQUEST_LINK_DESC"
    filter="url"
/>
```

### 2c. Link prenotazione (nel fieldset `links`, dopo `booking_link_es`)

```xml
<field
    name="booking_link_{LANG}"
    type="url"
    label="COM_ACCOMMODATION_MANAGER_CONFIG_BOOKING_LINK_{LANG_UPPER}"
    description="COM_ACCOMMODATION_MANAGER_CONFIG_BOOKING_LINK_DESC"
    filter="url"
/>
```

---

## 3. Form XML backend

### 3a. Room Manager

File: `src/administrator/components/com_accommodation_manager/forms/roommanager.xml`

Aggiungere i seguenti campi (copiare quelli `_es` e sostituire il suffisso):
- `room_title_{LANG}` (type="text")
- `room_intro_{LANG}` (type="editor")
- `room_description_{LANG}` (type="editor")
- `room_floor_plan_alt_{LANG}` (type="text")
- `room_thumbnail_alt_{LANG}` (type="text")

### 3b. Room Category

File: `src/administrator/components/com_accommodation_manager/forms/roommanagercategory.xml`

Aggiungere:
- `room_category_name_{LANG}` (type="text")
- `room_category_description_{LANG}` (type="editor")
- `room_category_image_alt_{LANG}` (type="text")

### 3c. Rate Period

File: `src/administrator/components/com_accommodation_manager/forms/managerrateperiod.xml`

Aggiungere:
- `period_title_{LANG}` (type="text")

### 3d. Rate Typology

File: `src/administrator/components/com_accommodation_manager/forms/managerratetypology.xml`

Aggiungere:
- `rate_typology_{LANG}` (type="text")

### 3e. Gallery subform

File: `src/administrator/components/com_accommodation_manager/forms/roommanager_gallery.xml`

Aggiungere:
- `alt_{LANG}` (type="text")

---

## 4. Helper PHP -- Costanti lingua

### 4a. Admin Helper

File: `src/administrator/components/com_accommodation_manager/src/Helper/Accommodation_managerHelper.php`

Aggiungere alla costante `LANGUAGES`:

```php
public const LANGUAGES = [
    'de' => 'Deutsch',
    'it' => 'Italiano',
    'en' => 'English',
    'fr' => 'Francais',
    'es' => 'Espanol',
    '{LANG}' => '{NomeLingua}',  // <-- aggiungere
];
```

### 4b. Site Helper

File: `src/components/com_accommodation_manager/src/Helper/Accommodation_managerHelper.php`

Aggiungere alla costante `VALID_LANGUAGES`:

```php
public const VALID_LANGUAGES = ['de', 'it', 'en', 'fr', 'es', '{LANG}'];
```

---

## 5. Language keys -- File .ini admin esistenti

Aggiungere le seguenti chiavi in **tutte le 5 lingue esistenti** (de-DE, it-IT, en-GB, fr-FR, es-ES).

Path: `src/administrator/components/com_accommodation_manager/language/{EXISTING_LOCALE}/com_accommodation_manager.ini`

### 5a. Chiavi di configurazione (3 chiavi)

```ini
COM_ACCOMMODATION_MANAGER_CONFIG_LANG_{LANG_UPPER}="{NomeLingua} ({LANG_UPPER})"
COM_ACCOMMODATION_MANAGER_CONFIG_REQUEST_LINK_{LANG_UPPER}="Request Link ({LANG_UPPER})"
COM_ACCOMMODATION_MANAGER_CONFIG_BOOKING_LINK_{LANG_UPPER}="Booking Link ({LANG_UPPER})"
```

### 5b. Tab contenuto (1 chiave)

```ini
COM_ACCOMMODATION_MANAGER_TAB_CONTENT_{LANG_UPPER}="Content ({LANG_UPPER})"
```

### 5c. Intestazioni colonne nelle liste backend (7 chiavi)

```ini
COM_ACCOMMODATION_MANAGER_ROOMSMANAGER_ROOM_TITLE_{LANG_UPPER}="Room_title_{LANG}"
COM_ACCOMMODATION_MANAGER_ROOMSMANAGER_ROOM_INTRO_{LANG_UPPER}="Room_intro_{LANG}"
COM_ACCOMMODATION_MANAGER_ROOMSMANAGER_ROOM_DESCRIPTION_{LANG_UPPER}="Room_description_{LANG}"
COM_ACCOMMODATION_MANAGER_MANAGERROOMCATEGORIES_ROOM_CATEGORY_NAME_{LANG_UPPER}="Room_category_name_{LANG}"
COM_ACCOMMODATION_MANAGER_MANAGERROOMCATEGORIES_ROOM_CATEGORY_DESCRIPTION_{LANG_UPPER}="Room_category_description_{LANG}"
COM_ACCOMMODATION_MANAGER_MANAGERRATEPERIODS_PERIOD_TITLE_{LANG_UPPER}="Name ({LANG})"
COM_ACCOMMODATION_MANAGER_MANAGERRATETYPOLOGIES_RATE_TYPOLOGY_{LANG_UPPER}="Name ({LANG})"
```

### 5d. Label ordinamento ASC/DESC (14 chiavi)

```ini
COM_ACCOMMODATION_MANAGER_ROOM_TITLE_{LANG_UPPER}_DESC="Room_title_{LANG} Descending"
COM_ACCOMMODATION_MANAGER_ROOM_TITLE_{LANG_UPPER}_ASC="Room_title_{LANG} Ascending"
COM_ACCOMMODATION_MANAGER_ROOM_INTRO_{LANG_UPPER}_DESC="Room_intro_{LANG} Descending"
COM_ACCOMMODATION_MANAGER_ROOM_INTRO_{LANG_UPPER}_ASC="Room_intro_{LANG} Ascending"
COM_ACCOMMODATION_MANAGER_ROOM_DESCRIPTION_{LANG_UPPER}_DESC="Room_description_{LANG} Descending"
COM_ACCOMMODATION_MANAGER_ROOM_DESCRIPTION_{LANG_UPPER}_ASC="Room_description_{LANG} Ascending"
COM_ACCOMMODATION_MANAGER_ROOM_CATEGORY_NAME_{LANG_UPPER}_DESC="Room_category_name_{LANG} Descending"
COM_ACCOMMODATION_MANAGER_ROOM_CATEGORY_NAME_{LANG_UPPER}_ASC="Room_category_name_{LANG} Ascending"
COM_ACCOMMODATION_MANAGER_ROOM_CATEGORY_DESCRIPTION_{LANG_UPPER}_DESC="Room_category_description_{LANG} Descending"
COM_ACCOMMODATION_MANAGER_ROOM_CATEGORY_DESCRIPTION_{LANG_UPPER}_ASC="Room_category_description_{LANG} Ascending"
COM_ACCOMMODATION_MANAGER_PERIOD_TITLE_{LANG_UPPER}_DESC="Name ({LANG}) Descending"
COM_ACCOMMODATION_MANAGER_PERIOD_TITLE_{LANG_UPPER}_ASC="Name ({LANG}) Ascending"
COM_ACCOMMODATION_MANAGER_RATE_TYPOLOGY_{LANG_UPPER}_DESC="Name ({LANG}) Descending"
COM_ACCOMMODATION_MANAGER_RATE_TYPOLOGY_{LANG_UPPER}_ASC="Name ({LANG}) Ascending"
```

### 5e. Label campi form -- Room Manager (6 chiavi)

```ini
COM_ACCOMMODATION_MANAGER_FORM_LBL_ROOMMANAGER_ROOM_TITLE_{LANG_UPPER}="Room Title ({LANG})"
COM_ACCOMMODATION_MANAGER_FORM_DESC_ROOMMANAGER_ROOM_TITLE_{LANG_UPPER}="{NomeLingua} Room Name"
COM_ACCOMMODATION_MANAGER_FORM_LBL_ROOMMANAGER_ROOM_INTRO_{LANG_UPPER}="Room Short Description ({LANG})"
COM_ACCOMMODATION_MANAGER_FORM_DESC_ROOMMANAGER_ROOM_INTRO_{LANG_UPPER}="Short Description"
COM_ACCOMMODATION_MANAGER_FORM_LBL_ROOMMANAGER_ROOM_DESCRIPTION_{LANG_UPPER}="Room Description ({LANG})"
COM_ACCOMMODATION_MANAGER_FORM_DESC_ROOMMANAGER_ROOM_DESCRIPTION_{LANG_UPPER}="Description"
```

### 5f. Label campi form -- Room Category (4 chiavi)

```ini
COM_ACCOMMODATION_MANAGER_FORM_LBL_ROOMMANAGERCATEGORY_ROOM_CATEGORY_NAME_{LANG_UPPER}="Category Name ({LANG})"
COM_ACCOMMODATION_MANAGER_FORM_DESC_ROOMMANAGERCATEGORY_ROOM_CATEGORY_NAME_{LANG_UPPER}=""
COM_ACCOMMODATION_MANAGER_FORM_LBL_ROOMMANAGERCATEGORY_ROOM_CATEGORY_DESCRIPTION_{LANG_UPPER}="Category Description ({LANG})"
COM_ACCOMMODATION_MANAGER_FORM_DESC_ROOMMANAGERCATEGORY_ROOM_CATEGORY_DESCRIPTION_{LANG_UPPER}=""
```

### 5g. Label campi form -- Category Image Alt (2 chiavi)

```ini
COM_ACCOMMODATION_MANAGER_FORM_LBL_IMAGE_ALT_{LANG_UPPER}="Alt Text ({LANG_UPPER})"
COM_ACCOMMODATION_MANAGER_FORM_DESC_IMAGE_ALT_{LANG_UPPER}="Alternative text for accessibility ({NomeLingua})"
```

### 5h. Label campi form -- Rate Period (2 chiavi)

```ini
COM_ACCOMMODATION_MANAGER_FORM_LBL_MANAGERRATEPERIOD_PERIOD_TITLE_{LANG_UPPER}="Period Title ({LANG})"
COM_ACCOMMODATION_MANAGER_FORM_DESC_MANAGERRATEPERIOD_PERIOD_TITLE_{LANG_UPPER}=""
```

### 5i. Label campi form -- Rate Typology (2 chiavi)

```ini
COM_ACCOMMODATION_MANAGER_FORM_LBL_MANAGERRATETYPOLOGY_RATE_TYPOLOGY_{LANG_UPPER}="Rate Typology ({LANG})"
COM_ACCOMMODATION_MANAGER_FORM_DESC_MANAGERRATETYPOLOGY_RATE_TYPOLOGY_{LANG_UPPER}=""
```

### 5j. Label campi form -- Alt text media (3 chiavi)

```ini
COM_ACCOMMODATION_MANAGER_FORM_LBL_ROOMMANAGER_FLOOR_PLAN_ALT_{LANG_UPPER}="Alt Text ({LANG_UPPER})"
COM_ACCOMMODATION_MANAGER_FORM_LBL_ROOMMANAGER_THUMBNAIL_ALT_{LANG_UPPER}="Alt Text ({LANG_UPPER})"
COM_ACCOMMODATION_MANAGER_FORM_LBL_GALLERY_ALT_{LANG_UPPER}="Alt Text ({LANG_UPPER})"
```

**Totale: 44 chiavi da aggiungere per ciascuno dei 5 file .ini admin esistenti.**

---

## 6. Language keys -- File .sys.ini admin esistenti

Path: `src/administrator/components/com_accommodation_manager/language/{EXISTING_LOCALE}/com_accommodation_manager.sys.ini`

Aggiungere in tutti e 5 i file:

```ini
COM_ACCOMMODATION_MANAGER_CONFIG_LANG_{LANG_UPPER}="{NomeLingua} ({LANG_UPPER})"
```

---

## 7. File di lingua per la nuova lingua

Creare i file .ini per il locale `{LOCALE}` copiando quelli `en-GB` come base e traducendo tutte le stringhe.

### 7a. Componente admin (2 file)

```
src/administrator/components/com_accommodation_manager/language/{LOCALE}/com_accommodation_manager.ini
src/administrator/components/com_accommodation_manager/language/{LOCALE}/com_accommodation_manager.sys.ini
```

### 7b. Componente site (2 file)

```
src/components/com_accommodation_manager/language/{LOCALE}/com_accommodation_manager.ini
src/components/com_accommodation_manager/language/{LOCALE}/com_accommodation_manager.sys.ini
```

### 7c. Modulo Rooms (2 file)

```
src/modules/mod_accommodation_rooms/language/{LOCALE}/mod_accommodation_rooms.ini
src/modules/mod_accommodation_rooms/language/{LOCALE}/mod_accommodation_rooms.sys.ini
```

### 7d. Modulo Categories (2 file)

```
src/modules/mod_accommodation_categories/language/{LOCALE}/mod_accommodation_categories.ini
src/modules/mod_accommodation_categories/language/{LOCALE}/mod_accommodation_categories.sys.ini
```

### 7e. Modulo Rates (2 file)

```
src/modules/mod_accommodation_rates/language/{LOCALE}/mod_accommodation_rates.ini
src/modules/mod_accommodation_rates/language/{LOCALE}/mod_accommodation_rates.sys.ini
```

**Totale: 10 file .ini da creare e tradurre.**

---

## 8. Manifest e build

### 8a. Manifest componente

File: `accommodation_manager.xml`

Aggiungere la cartella lingua nelle sezioni `<languages>` (admin e site):

```xml
<language tag="{LOCALE}">language/{LOCALE}/com_accommodation_manager.ini</language>
<language tag="{LOCALE}">language/{LOCALE}/com_accommodation_manager.sys.ini</language>
```

### 8b. Manifest moduli

Aggiungere le stesse righe `<language>` nei 3 file manifest dei moduli:
- `src/modules/mod_accommodation_rooms/mod_accommodation_rooms.xml`
- `src/modules/mod_accommodation_categories/mod_accommodation_categories.xml`
- `src/modules/mod_accommodation_rates/mod_accommodation_rates.xml`

### 8c. Build script

File: `build/build.sh`

Aggiungere la copia delle nuove cartelle lingua `{LOCALE}/` per componente e moduli.

### 8d. Package language (se presente)

Verificare se `pkg_accommodation_manager.xml` ha file sys.ini a livello pacchetto e aggiungere `{LOCALE}` se necessario.

---

## 9. Sync per test locale

> **Nota**: questo passo e specifico dell'ambiente di sviluppo. Non necessario per la distribuzione del pacchetto.

Copiare tutti i file modificati/creati in `/projects/base/` per test immediato su `base.test`.

---

## File che NON servono modifiche

Grazie ai refactoring, questi file si adattano automaticamente leggendo le costanti `LANGUAGES` / `VALID_LANGUAGES`:

- **Router.php** -- colonne generate dinamicamente da `VALID_LANGUAGES`
- **RoomsmanagerModel.php** -- filter_fields generati da `LANGUAGES`
- **Models frontend** (RoomModel, RoomsModel, RatesModel, CategoriesModel) -- usano `getLanguageSuffix()`
- **Module helpers** (RoomsHelper, CategoriesHelper) -- usano `getLanguageSuffix()`
- **Template frontend e layouts** -- usano `getLanguageSuffix()`
- **Template admin edit** -- tab lingua generati dinamicamente da `Helper::LANGUAGES`
