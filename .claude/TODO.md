# TODO - Accommodation Manager Component (Joomla 5/6)

---

## BACKEND

---

### FASE 1 - Interfacce Admin (Liste)

Revisione delle liste nel backend per verificare usabilità e consistenza:

- [x] **1.1** Rates (griglia tariffe) - Rivista struttura tabella, paginazione, formato date (2026-02-05)
- [x] **1.2** Rate Periods - Formato date corretto (2026-02-05)
- [x] **1.3** Rooms Manager - Lista OK, form edit rifatto (2026-02-05)
- [x] **1.4** Room Categories - Lista riordinata colonne, Bootstrap 5 (2026-02-05)
- [x] **1.5** Rate Typologies - Aggiunto rate_typology_title, Bootstrap 5 responsive (2026-02-05)

### FASE 2 - Interfacce Admin (Form Edit)

Revisione dei form di editing:

- [x] **2.1** Form edit Room - Modifiche campi e layout (2026-02-05)
  - [x] **2.1.1** Aggiungere campi alt multilingua per room_floor_plan (alt_de, alt_it, alt_en, alt_fr, alt_es) (2026-02-05)
  - [x] **2.1.2** Aggiungere campi alt multilingua per room_thumbnail (alt_de, alt_it, alt_en, alt_fr, alt_es) (2026-02-05)
  - [x] **2.1.3** Aggiungere campo "prezzo a partire da" (room_price_from) (2026-02-05)
  - [x] **2.1.4** Rimuovere campo room_pano (2026-02-05)
  - [x] **2.1.5** Convertire room_gallery in subform ripetibile (media, media_mobile, alt_de/it/en/fr/es) (2026-02-05)
  - [x] **2.1.6** Riorganizzare layout form (tab per sezione/lingua, sidebar stato) (2026-02-05)
  - [x] **2.1.7** Eseguire SQL update per aggiungere nuove colonne al database (2026-02-05)
- [x] **2.2** Form edit Rate Period - Bootstrap 5, validazione date start/end (2026-02-05)
- [x] **2.3** Form edit Room Category - Bootstrap 5, tab per lingua, sidebar stato (2026-02-05)
- [x] **2.4** Form edit Rate Typology - Aggiunto rate_typology_title obbligatorio (2026-02-05)

### FASE 3 - Funzionalità rotte

- [x] **3.1** Fixare ricerca testo in ListModel: aggiunto WHERE mancante in ManagerrateperiodsModel e ManagerratetypologiesModel (2026-02-05)
- [x] **3.2** ~~Fixare ForeignKey fields in `managerrate.xml`~~ - Rimosso: form edit singola rate non usato, rates si modificano nella griglia (2026-02-05)
- [x] **3.3** Fixare bug `strrpos()` in Table bind(): `!= false` -> `!== false` in RoommanagerTable e RoommanagercategoryTable (2026-02-05)
- [x] **3.4** Uniformare language key: controller usavano `NO_ELEMENT_SELECTED`, cambiato in `NO_ITEM_SELECTED` che già esiste (2026-02-05)

### FASE 4 - API deprecate Joomla 3 → Joomla 5

- [x] **4.1** Sostituire `Factory::getUser()` con `Factory::getApplication()->getIdentity()` in tutti i file admin (2026-02-05)
- [x] **4.2** Sostituire `Factory::getDbo()` con `$this->getDatabase()` o `Factory::getContainer()->get('DatabaseDriver')` (2026-02-05)
- [x] **4.3** Rimuovere `jimport('joomla.filter.output')` da tutti i 4 AdminModel (2026-02-05)
- [x] **4.4** Rimuovere uso di `Sidebar` HTML Helper da tutte le 4 list View (2026-02-05)
- [x] **4.5** Sostituire `$table->getError()` con try/catch nei metodi `duplicate()` dei 4 AdminModel (2026-02-05)
- [x] **4.6** Già OK - SQL usa `DEFAULT NULL` per checked_out_time (2026-02-05)

### FASE 5 - Database schema

- [x] **5.1** Fixare tipi colonna sbagliati (2026-02-05):
  - `rates.rate`: VARCHAR(255) → DECIMAL(10,2) NULL (NULL = non disponibile)
  - `rooms.room_surface`: VARCHAR(255) → VARCHAR(50) (mantiene VARCHAR per valori range "20-24")
  - `rooms.room_people`: VARCHAR(255) → VARCHAR(20) (mantiene VARCHAR per valori range "2-4")
  - `rate_periods.period_start/period_end`: DATETIME → DATE
- [x] **5.2** Aggiunte colonne `created`, `modified`, `modified_by` a tutte le 5 tabelle (2026-02-05)
- [x] **5.3** Rimosso codice ACL inutile dalle 4 Table classes (non serve controllo granulare) (2026-02-05)
- [x] **5.4** Aggiunta colonna `version_note` a tutte le 5 tabelle per supporto versioning (2026-02-05)
- [x] **5.5** Aggiungere indici secondari (2026-02-05):
  - `rooms`: idx_room_category, idx_state_ordering ✓
  - `room_categories`: idx_room_category_parent, idx_state_ordering ✓
  - `rates`: idx_room_id, idx_period_id, idx_typology_id, idx_state ✓
  - `rate_periods`: idx_state_ordering ✓
  - `rate_typologies`: idx_state_ordering ✓
- [x] **5.6** Verificato: `room_floor_plan` e `room_thumbnail` sono già VARCHAR(255) - OK (2026-02-05)
- [x] **5.7** Aggiornata collation a `utf8mb4_unicode_ci` in install e update SQL (2026-02-05)
- [x] **5.8** Creare sistema update SQL funzionale: creato `sql/updates/mysql/3.1.0.sql` (2026-02-05)
- [x] **5.9** Aggiunto cleanup in uninstall SQL: content_types, assets, ucm_content, action_logs (2026-02-05)

### FASE 6 - Performance

- [x] **6.1** ~~Eliminare N+1 queries in `ManagerratesModel::getItems()`~~ - Risolto: griglia usa query dedicate (getPeriods/getRooms/getTypologies/getRatesGrid), rimosso getListQuery/getItems (2026-02-05)
- [x] **6.2** Eliminare N+1 queries in `RoomsmanagerModel::getItems()` - Usato valore già recuperato dal JOIN invece di N query (2026-02-05)
- [x] **6.3** Eliminare N+1 queries in `ManagerroomcategoriesModel::getItems()` - Usato valore già recuperato dal JOIN invece di N query (2026-02-05)
- [x] **6.4** Rimuovere jQuery dependency da `joomla.asset.json` - admin.js era già vanilla JS, rimossa dipendenza + aggiornata versione a 3.0.0 (2026-02-05)

### FASE 7 - Gestione lingue

**Decisione**: Manteniamo l'approccio a colonne separate (`*_de`, `*_it`, `*_en`, `*_fr`, `*_es`) ma rendiamo la lista lingue configurabile.

- [x] **7.1** Creare configurazione centralizzata per lista lingue supportate (config.xml + Helper::LANGUAGES) (2026-02-05)
- [x] **7.2** Aggiornare edit templates per usare la lista lingue configurata (tab dinamici) (2026-02-05)
- [x] **7.3** Aggiornare list templates per usare la lista lingue configurata (colonne dinamiche) (2026-02-05)
- [x] **7.4** ~~Creare script per aggiungere nuova lingua~~ → Spostato a POST-RELEASE

### FASE 8 - Pulizia codice morto

- [x] **8.1** Rimuovere 5 Field classes orfane (mai usate nei form XML): `ModifiedbyField.php`, `TimecreatedField.php`, `TimeupdatedField.php`, `NestedparentField.php`, `FilemultipleField.php` - sia da admin che da site (2026-02-05)
- [x] **8.2** Rimuovere `listhelper.php` - NON PRESENTE (già rimosso o mai esistito) (2026-02-05)
- [x] **8.3** Rimuovere `getSortFields()` da tutte le 4 list View (2026-02-05)
- [x] **8.4** Rimuovere variabile `$date` inutilizzata da tutti i 4 Table bind() (2026-02-05)
- [x] **8.5** Rimuovere doppia assegnazione `$task` da tutti i 4 Table bind() (2026-02-05)
- [x] **8.6** Rimuovere placeholder `//XXX_CUSTOM_TABLE_FUNCTION` da tutte le 4 Table classes (2026-02-05)
- [x] **8.7** Rimuovere import `use` inutilizzati: TagsHelper, ParameterType, ArrayHelper, Text, Form, Accommodation_managerHelper (2026-02-05)
- [x] **8.8** Rimuovere override `store()` inutile da tutte le Table (chiama solo parent con default) (2026-02-05)
- [x] **8.9** Rimuovere override `delete()` inutile da tutte le Table (load + parent::delete senza logica aggiuntiva) (2026-02-05)
- [x] **8.10** Rimuovere binding `params`/`metadata` da Table bind() (colonne inesistenti nel DB) + import Registry (2026-02-05)
- [x] **8.11** Rimuovere `$app` inutilizzato da `getForm()` in tutti i 4 AdminModel (2026-02-05)
- [x] **8.12** Sostituire `@$table->ordering` (error suppression) con check esplicito in tutti i AdminModel prepareTable() (2026-02-05)
- [x] **8.13** Rimuovere tutti i 12 file `index.html` (convenzione Joomla 3, non necessaria) (2026-02-05)

### FASE 9 - Refactoring strutturale

- [x] **9.1** Estrarre base class comune per i 4 list Controller: `BaseListController` (2026-02-05)
- [x] **9.2** Estrarre base class comune per i 4 AdminModel: `BaseItemModel` (2026-02-05)
- [x] **9.3** Estrarre base class comune per le 4 Table classes: `BaseTable` (2026-02-05)
- [x] **9.4** Estrarre base class comune per le 4 list HtmlView + 4 edit HtmlView: `BaseListView`, `BaseEditView` (2026-02-05)
- [x] **9.5** Spostare query SQL raw in `prepareTable()` a Query Builder (2026-02-05)
- [x] **9.6** Aggiungere check `core.edit.own` nelle edit View - verifica created_by per permettere edit propri item (2026-02-05)
- [x] **9.7** Convertire costanti globali MODIFIED/NOT_MODIFIED in script.php a class constants (self::MODIFIED, self::NOT_MODIFIED) (2026-02-05)

---

## CONFIGURAZIONE & BUILD

---

### FASE 10 - Manifest e configurazione

- [x] **10.1** Rimuovere manifest duplicato da `src/administrator/components/com_accommodation_manager/accommodation_manager.xml` (2026-02-05)
- [x] **10.2** Rimuovere `script.php` duplicato dalla cartella admin (2026-02-05)
- [x] **10.3** Fixare `creationDate` nel manifest: 2.1.1 → 2026-02 (2026-02-05)
- [x] **10.4** Allineare versioni: manifest e joomla.asset.json → 3.1.0 (2026-02-05)
- [x] **10.5** Aggiornare `version="4.0"` a `version="5.0"` nel tag `<extension>` del manifest (2026-02-05)
- [x] **10.6** Rimossa cartella `presets/` (copia irrilevante di com_content) (2026-02-05)
- [x] **10.7** Pulito `config.xml`: rimosso fieldset vuoto e commenti boilerplate (2026-02-05)
- [x] **10.8** Aggiunti in `config.xml` campi link multilingua (request_link_*, booking_link_*) (2026-02-05)
- [x] **10.9** Fixato `joomla.asset.json`: rimosso prefisso "CVS:", versione 3.0.0 (2026-02-05)
- [x] **10.10** ACL granulare: non necessario, basta permesso generico componente (2026-02-05)
- [x] **10.11** Fixata language key errata nei filter form: `COM_USERS_FILTER_SEARCH_DESC` → `JGLOBAL_FILTER_SEARCH_DESC` (2026-02-05)
- [x] **10.12** Rimosse language key duplicate `COM_ACCOMMODATION_MANAGER_XML_DESCRIPTION` e `COMPONENT_DESC` (2026-02-05)
- [x] **10.13** Aggiungere filtro per categoria in Rooms list (2026-02-05)
- [x] **10.15** Aggiungere filtro per parent category in Room Categories list (2026-02-05)
- [x] **10.16** Mostrare "No Parent" invece di "0" nella colonna Parent della lista Room Categories (2026-02-05)
- [x] **10.17** Rimosso updateservers (component-creator) dal manifest (2026-02-05)

### FASE 11 - Build e packaging

- [x] **11.1** Creare `build/build.sh` per generare ZIP del componente (2026-02-05)
- [x] **11.2** Creare `.gitignore` appropriato (dist/, *.zip, .DS_Store, .idea/) (2026-02-05)
- [x] **11.3** Git repository inizializzato (2026-02-05)
- [x] **11.4** ~~Package manifest~~ → Spostato a FASE 22 (dopo moduli/plugin)
- [x] **11.5** ~~Package install script~~ → Spostato a FASE 22
- [x] **11.6** ~~Language files package~~ → Spostato a FASE 22

### FASE 11b - Code Review Fix (pre-frontend)

Problemi identificati durante code review del backend:

**Priorità Alta - Joomla 6 Compatibility:**
- [x] **11b.1** Sostituire `Factory::getApplication()->close()` con JSON response in `ManagerratesController::saveOrderAjax()` (2026-02-06)
- [x] **11b.2** Aggiungere `protected string $typeAlias = '';` in `BaseTable.php` (PHP 8.2+ dynamic property warning) (2026-02-06)
- [x] **11b.3** Sostituire `echo "1"` con JSON response in `BaseListController::saveOrderAjax()` (2026-02-06)

**Priorità Media - Code Smell:**
- [x] **11b.4** Refactor `script.php::processTable()` - estratti metodi processTableAdd/Change/Remove/createTable (2026-02-06)
- [x] **11b.5** Refactor `script.php::processField()` - estratti metodi processFieldChange/renameField/processFieldRemove/addFieldWithMessage (2026-02-06)
- [x] **11b.6** Fix `Factory::getUser()` deprecato in `CreatedbyField.php` - usa UserFactoryInterface + htmlspecialchars output (2026-02-06)

**Priorità Bassa - Cleanup:**
- [x] **11b.7** Rimuovere `AssociationServiceTrait` inutilizzato da `Accommodation_managerComponent.php` + import morti (2026-02-06)
- [x] **11b.8** Rimosso `Helper::getFiles()` dead code da admin e site + fixato `Factory::getUser()` in site Helper (2026-02-06)
- [x] **11b.9** Rimosso `ForeignKeyField::getAttribute()` - duplica metodo nativo Joomla (2026-02-06)
- [x] **11b.10** Fix `catch (ExecutionFailureException $e)` → `catch (\Exception $e)` in `ForeignkeyField.php` (2026-02-06)

---

## FRONTEND

Riferimento analizzato: lo script custom `static/room_rate` usato in produzione al posto del frontend Joomla

---

### FASE 12 - View Categories List (elenco categorie camere)

- [x] **12.0** Pulizia frontend: rimosso tutto il codice CRUD auto-generato (controller, model, view, template, form, field) mai usato in produzione (2026-02-06)
- [x] **12.1** Model CategoriesModel: query categorie pubblicate con titoli multilingua, immagine, descrizione (2026-02-06)
- [x] **12.2** View + template: lista categorie con titolo, immagine, descrizione, link alla lista camere filtrata (2026-02-06)
- [x] **12.3** Helper frontend con getLanguageSuffix() per mappare lingua Joomla a suffisso colonna DB (2026-02-06)
- [x] **12.4** Router SEF riscritto, DisplayController aggiornato, menu item XML (2026-02-06)
- [x] **12.5** Language files frontend riscritti (solo key necessarie) + sys.ini per menu type picker (2026-02-06)

### Backend fix minori (da fare dopo frontend)

- [x] **B.1** Room Categories edit: spostare i campi alt immagine nel tab Basic insieme all'immagine (2026-02-06)
- [x] **B.2** Frontend CSS/JS: asset per-view registrati in `joomla.asset.json`, caricati condizionalmente da config params (load_css/load_js) (2026-02-06)
  - categories-slider.css/js (con dependency Swiper CDN)
  - gallery-slider.css/js (con dependency Swiper CDN)
  - category-filter.js
  - rates-grid.css/js
- [x] **B.3** Configurazione component con tab per view (2026-02-06):
  - Componente: unito lingue abilitate con hr separator
  - **Categories**: immagine, descrizione, bottone link
  - **Rooms**: toggle sezioni (superficie, persone, prezzo, intro, descrizione, planimetria, galleria, video, tariffe), split per categoria, Swiper.js, bottoni richiesta/prenotazione
  - **Room detail**: tariffe condizionali, bottoni condizionali ai link configurati
  - **Rates**: nascondi periodi passati, dividi per stagione estate/inverno con date inizio configurabili

---

### FASE 13 - View Rooms List (elenco camere)

- [x] **13.1** Model RoomsModel: query camere con categoria, prezzo, thumbnail, titoli multilingua, gallery JSON, filtro per category_id (2026-02-06)
- [x] **13.2** View + template: lista camere con `<section>`, `<picture>` per gallery, `data-category`, page heading, unique room_name constraint (2026-02-06)

### FASE 14 - View Room Detail (dettaglio camera)

- [x] **14.1** Model RoomModel: singola camera con tutti i campi, gallery JSON decode (2026-02-06)
- [x] **14.2** View + template: `<article>` con `<h1>` titolo camera, tutte le info, gallery con `<picture>` (2026-02-06)

### FASE 15 - View Rates (griglia tariffe)

- [x] **15.1** Model RatesModel: getPeriods/getRooms/getTypologies/getRatesGrid con 3D array (2026-02-06)
- [x] **15.2** Template: tabella HTML con periodi rowspan, tipologie righe, camere colonne, page heading (2026-02-06)
- [x] **15.3** Formattazione prezzi: `number_format` + euro, NULL → dash (2026-02-06)

### FASE 16 - Applicare config ai template

- [x] **16.1** Categories template: params condizionano immagine, descrizione, bottone link (2026-02-06)
- [x] **16.2** Rooms template: params condizionano tutte le sezioni + layout split (default flat / grouped by category) + category filter JS (2026-02-06)
- [x] **16.3** Rates template/model: nascondi periodi passati, split per stagione con raggruppamento per anno (Summer 2026, Winter 2026/27) (2026-02-06)
- [x] **16.4** Room detail template: bottoni richiesta/prenotazione condizionali ai link configurati (2026-02-06)

### FASE 17 - Frontend CSS/JS e Swiper

- [x] **17.1** CSS/JS per-view con toggle load_css/load_js in config: categories-slider, gallery-slider, rates-grid, category-filter (2026-02-06)
- [x] **17.2** Swiper.js integrato: categories slider (layout alternativo), gallery slider per rooms e room detail (2026-02-06)
- [x] **17.3** rates-grid.js: zebra striping, period group hover, sticky columns (all viewports), scroll hints, border-separate fix. category-filter.js: filtro client-side per categoria (2026-02-10)

### FASE 18 - Router SEF

- [x] **18.1** Router SEF riscritto: slug multilingua per categories e rooms, gerarchia categories→category e rooms→room (2026-02-10)
- [x] **18.2** View Category creata: Model, HtmlView, template, menu item XML con selezione categoria (2026-02-10)
- [x] **18.3** Fix lookup MenuRules: categories con setKey('id') + getCategoriesSegment/Id per layout variants (slider) (2026-02-10)
- [x] **18.4** Language keys frontend/admin per category view in tutte e 3 le lingue (2026-02-10)

### FASE 20 - Moduli frontend

Moduli Joomla per richiamare le view dei dati in posizioni del template:

- [x] **20.1** `mod_accommodation_categories` - Modulo categorie camere con title_tag (p/h2/h3), show/hide image/description/button, ordinamento (ordering/titolo generico/nome lingua + ASC/DESC) (2026-02-10)
- [x] **20.2** `mod_accommodation_rooms` - Modulo elenco camere con filtro categoria, show/hide per ogni sezione, Swiper gallery, ordinamento (ordering/titolo lingua/nome interno + ASC/DESC), pulsanti request/booking (2026-02-10)
- [x] **20.3** `mod_accommodation_rates` - Modulo griglia tariffe con CSS/JS componente, season grouping, config letto da componente (2026-02-10)

### FASE 22 - Package manifest

Pacchetto installabile unico con componente + moduli:

- [x] **22.1** Creare `pkg_accommodation_manager.xml` (package manifest) (2026-02-10)
- [x] **22.2** ~~Package install script~~ - Non necessario, componente ha gia' script.php e moduli non richiedono script (2026-02-10)
- [x] **22.3** Popolare `language/` root con file sys.ini a livello pacchetto (EN, DE, IT) (2026-02-10)
- [x] **22.4** Aggiornare `build/build.sh` per generare ZIP del package completo (2026-02-10)

### FASE 23 - Rates grid in Rooms/Room views

Griglia tariffe per singola camera visualizzabile nella lista camere e nel dettaglio camera:

- [x] **23.1** Estratto rendering griglia in Joomla Layout condivisi: `layouts/rates/grid.php` (multi-room) e `layouts/rates/room-grid.php` (single-room) (2026-02-10)
- [x] **23.2** Helper: aggiunto `buildSeasonGroups()` per centralizzare logica season grouping (2026-02-10)
- [x] **23.3** RatesModel: aggiunto filtro opzionale `$roomIds` a `getRatesGrid()` (2026-02-10)
- [x] **23.4** HtmlView Rooms/Room: caricano dati tariffe via `bootComponent()->getMVCFactory()->createModel('Rates')` (2026-02-10)
- [x] **23.5** Config: toggle `rooms_show_rates` (default off) nel fieldset Rooms (2026-02-10)
- [x] **23.6** Templates rates/rooms/room: usano `LayoutHelper::render()` invece di duplicare HTML (2026-02-10)
- [x] **23.7** Modulo rates: riscritto per usare layout condiviso + `Factory::getLanguage()->load()` per chiavi componente (2026-02-10)

### FASE 24 - Estrarre gallery in Joomla Layout condiviso

Blocco gallery (~50 righe) con Swiper, `<picture>`, `getimagesize()` fallback, mobile source duplicato tra rooms e room templates:

- [x] **24.1** Creare `layouts/room/gallery.php` — layout condiviso per rendering gallery (Swiper + plain) (2026-02-10)
- [x] **24.2** Aggiornare `tmpl/rooms/default.php` per usare `LayoutHelper::render('room.gallery', ...)` (2026-02-10)
- [x] **24.3** Aggiornare `tmpl/room/default.php` per usare `LayoutHelper::render('room.gallery', ...)` (2026-02-10)
- [x] **24.4** Aggiornare `mod_accommodation_rooms/tmpl/default.php` per usare layout condiviso gallery (2026-02-10)

### Pre-rilascio

- [x] **PR.3** Analisi overkill generale completata (2026-02-10)

#### PR.3a — Priorità Alta: Eliminare duplicazione codice

- [x] **PR.3a.1** Estratto `Accommodation_managerHelper::decodeGalleryItems($json, $lang)` — sostituito in RoomsModel, CategoryModel, RoomModel, RoomsHelper (2026-02-10)
- [x] **PR.3a.2** Module helpers: RatesHelper riscritto per delegare a RatesModel via bootComponent (eliminata DatabaseAwareInterface, -70 righe). RoomsHelper usa decodeGalleryItems(). CategoriesHelper invariato (query specifica modulo per ordering/filter/limit) (2026-02-10)

#### PR.3b — Priorità Media: Pulizia dead code

- [x] **PR.3b.1** `script.php`: rimossi `preflight()` e `postflight()` vuoti (2026-02-10)
- [x] **PR.3b.2** `script.php`: estratto `getManifestElement($parent, $name)` — sostituisce 4 blocchi if/else identici (2026-02-10)
- [x] **PR.3b.3** Estratto `Accommodation_managerHelper::buildRoomBaseQuery($db)` — 19 colonne SELECT + category JOIN + state=1, usato da RoomsModel, CategoryModel, RoomModel (2026-02-10)
- [x] **PR.3b.4** Estratto `Accommodation_managerHelper::loadRatesData($params, $roomIds)` — restituisce [periods, typologies, ratesGrid], usato da Rooms/HtmlView e Room/HtmlView (2026-02-10)

#### PR.3c — Priorità Bassa: Semplificazione base classes

- [x] **PR.3c.1** `BaseItemModel`: rimosso `getItem()` proxy — chiamava solo parent (2026-02-10)
- [x] **PR.3c.2** `script.php`: rimossi `MODIFIED`/`NOT_MODIFIED` constants, sostituiti con `true`/`false` (2026-02-10)
- [x] **PR.3c.3** `Router.php`: creato `buildSlug($item, $localizedCol, $fallbackCol)` generico, usato da categories e rooms. Rimosso `buildRoomSlug()` (2026-02-10)
- [x] **PR.3c.4** Fix `mod_accommodation_rates` language: caricamento lingua componente da path corretto (`JPATH_SITE/components/com_accommodation_manager`) (2026-02-10)

#### PR.3d — Valutati e mantenuti (nessun intervento)

- **BaseTable**: KEEP — hook `processBind()` usato da 3/4 subclasses, audit fields condivisi
- **BaseListController**: KEEP — `duplicate()` + `saveOrderAjax()` giustificano DRY
- **BaseListView / BaseEditView**: funzionano, semplificazione ha rischio > beneficio per 4 subclass ciascuno
- **Config load_css/load_js**: KEEP — utili per chi fa override CSS/JS custom nel template
- **Config 12 display toggles Rooms**: KEEP — pattern standard Joomla, ogni toggle ha showon conditions
- **Config 5 toggle lingue separati**: KEEP — più chiaro per l'utente di un multi-select

---

- [x] **PR.1** Audit utilizzo HTMLHelper e funzioni native Joomla (2026-02-11):
  - CSS inline estratto da managerrates/default.php → admin-rates.css via WebAssetManager
  - onclick inline rimossi → data-task attribute + handler in admin.js
  - date() PHP nativo in Helper → Joomla\CMS\Date\Date
  - Cast (int) per $item->id e $item->ordering in tutte le 4 liste admin
  - Cast (int) per $catId in rooms/default.php
  - Fix caricamento CSS managerrates: registerAndUseStyle hardcoded → useStyle da joomla.asset.json
- [x] **PR.2** Traduzioni fr-FR e es-ES (2026-02-11):
  - Joomla fa fallback automatico a en-GB se il file .ini della lingua attiva non esiste
  - Creati 20 file .ini (10 per lingua) per componente admin/site + 3 moduli
  - Il componente ora supporta tutte e 5 le lingue (de, it, en, fr, es) sia nei dati DB che nelle label interfaccia
- [x] **PR.4** Checklist per aggiungere nuova lingua (2026-02-11):
  - Creato `docs/ADD-LANGUAGE.md` con checklist completa e autosufficiente (44 language keys esplicite, tutti i form XML, SQL, config, manifest)
  - Refactoring: Router.php e RoomsmanagerModel.php ora dinamici (leggono da costanti LANGUAGES/VALID_LANGUAGES)
  - **NON serve toccare** (dinamici via `getLanguageSuffix()`): Router, RoomsmanagerModel, Models frontend, Module helpers, Template frontend/layouts, Template admin edit
- [x] **PR.5** Template frontend modulari con Joomla Layout (2026-02-11):
  - Creati 6 layout: `room/thumbnail`, `room/info`, `room/floor-plan`, `room/actions`, `room/detail-link`, `category/item`
  - Fix bug: gallery inline in `rooms/grouped.php` e `category/default.php` → usano `room.gallery` layout
  - Aggiornati 6 template componente + 2 template modulo per usare i layout condivisi
  - Parametro `langPrefix` per supportare prefissi lingua diversi (componente vs modulo)

---

## MIGRAZIONE DATI (da fare a fine progetto)

---

### FASE 19 - Script migrazione dati

#### Contesto

Il vecchio componente Joomla 3 era nella cartella `com_accommodation_manager_four` ma registrato in `#__extensions` come `com_accommodation_manager` (stesso element del nuovo). Le tabelle DB sono le stesse (`#__accommodation_manager_*`).

Quando si installa il nuovo pacchetto (v3.3.0) sopra il vecchio (v2.1.1):
- Il manifest ora usa `<schemas><schemapath>` per SQL versionati (fix 2026-02-11)
- `script.php` postflight esegue `upgradeSchema()` che aggiunge colonne mancanti, modifica tipi, aggiunge indici e popola rate_typology_title — gestisce il caso in cui le tabelle esistevano già dal vecchio componente
- `script.php` postflight esegue `removeLegacyComponent()` che rimuove `com_accommodation_manager_four` (DB + filesystem) senza triggerare il suo uninstall SQL (che farebbe DROP TABLE)

**Lo script migrazione serve solo per convertire i DATI** nei 2 casi dove il contenuto non è compatibile con il nuovo formato.

#### Conversioni dati necessarie

- [x] **19.1** `room_gallery` — convertito in `migrateData()` postflight: scansiona cartella immagini, crea JSON subform (2026-02-11)
- [x] **19.2** `rates.rate` — convertito in `migrateData()` postflight: "--" e non-numerici → NULL prima del MODIFY a DECIMAL (2026-02-11)
- [x] **19.3** `rate_typology_title` — gestito da `upgradeSchema()` postflight: popola da COALESCE(de, it, en, fr, es) (2026-02-11)

#### Implementazione

Tutto integrato nel `script.php` postflight (v3.4.0), eseguito automaticamente durante l'installazione del pacchetto:

1. `migrateData()` — converte dati (rate "--"→NULL, gallery path→JSON, svuota pano)
2. `upgradeSchema()` — ALTER TABLE, MODIFY COLUMN, DROP room_pano, indici, popola rate_typology_title
3. `removeLegacyComponent()` — rimuove vecchio com_accommodation_manager_four

#### Task operativi

- [x] **19.4** `room_pano` — colonna rimossa: dati svuotati in migrateData(), DROP COLUMN in upgradeSchema() (2026-02-11)
- [x] **19.5** ~~Backup pre-migrazione~~ — non necessario, rimosso (2026-02-11)
- [x] **19.6** ~~Script PHP separato~~ — integrato nel postflight, non serve script esterno (2026-02-11)
- [x] **19.7** ~~Test su DB produzione~~ — non necessario, rimosso (2026-02-11)

---

## BUG FIX

---

### Backend — Administrator

- [x] **BF.1** `view=managerrateperiod`: la validazione date scatta anche sul pulsante "Chiudi", deve attivarsi solo al salvataggio (2026-02-12)
- [x] **BF.2** Menu ordine errato: "Tipologie Tariffa" deve venire prima di "Rates" (prima rate typologies, poi rates) (2026-02-12)
- [x] **BF.3** Menu: traduzioni mancanti — alcune voci sono tradotte, altre no (2026-02-12)
- [x] **BF.4** Tutte le `<thead>` delle tabelle liste admin non sono tradotte (language keys mancanti) (2026-02-12)
- [x] **BF.5** Rates grid admin: gli input dei prezzi non sono centrati come le intestazioni `<thead>` (2026-02-12)
- [x] **BF.6** `room_code` deve essere univoco (vincolo UNIQUE nel DB + validazione lato server) (2026-02-12)
- [x] **BF.7** Roommanager gallery e campi room — problemi multipli: (2026-02-12)
  - [x] **BF.7a** Il tasto "-" (rimuovi immagine) nel subform gallery non funziona — creato sublayout `gallery_section.php` mancante (2026-02-12)
  - [x] **BF.7b** Non salva più di 3 immagini nella gallery — causa: PHP `max_input_vars=1000` insufficiente per form pesante, non è bug componente (2026-02-12)
  - [x] **BF.7c** Rimuovere testo errato "Insert the path to the folder where the specific room images are specified" — corretto in en-GB, fr-FR, es-ES (2026-02-12)
  - [x] **BF.7d** Ripensare `room_surface`, `room_people` e `room_price_from` — `room_surface` INT + unità m² da language key, `room_price_from` DECIMAL + format string da language key (es. "a partire da %s €"), `room_people` invariato VARCHAR (2026-02-12)

### Frontend

- [x] **BF.8** ~~Problema con la scrittura degli URL~~ — Non è un bug: il menu item deve puntare a "Accommodation Manager > Rooms" (non a un articolo con modulo dentro). Il router SEF richiede un menu item del componente come anchor point. (2026-02-12)
- [x] **BF.9** Rates view responsive: desktop tabella con scroll orizzontale, mobile (<=768px) cards raggruppate per periodo via JS — tabella nascosta, cards generate da `rates-grid.js`, stesse custom properties CSS, fallback senza JS = tabella scrollabile (2026-02-12)

---

## POST-RELEASE

---

- [ ] **POST.1** Documentazione componente nel Help button: utilizzare il pulsante Help nella toolbar di configurazione Joomla per linkare/mostrare una documentazione completa del componente (parametri, CSS custom properties, JS custom events, struttura DB, ecc.) — [GitHub Issue #1](https://github.com/paolo-altea/accomodation_manager/issues/1)
- [x] **POST.2** Gallery alt lingua non filtrate per lingue attive nel backend: nella schermata gallery del roommanager, i campi alt nelle varie lingue non vengono filtrati per le lingue attive nel config come avviene nelle altre schermate — [GitHub Issue #2](https://github.com/paolo-altea/accomodation_manager/issues/2) (2026-02-24)
- [x] **POST.3** Recuperare info su camere e categorie all'interno di script esterni — [GitHub Issue #3](https://github.com/paolo-altea/accomodation_manager/issues/3) (2026-02-24)
- [ ] **POST.4** Pulsanti booking/request: passare room_code invece di room_name nell'URL — da definire formato parametro e compatibilità sistema esterno — [GitHub Issue #4](https://github.com/paolo-altea/accomodation_manager/issues/4)

---

## Completati (storico)

- [x] Rimosso codice morto edit singola rate: managerrate.xml, ManagerrateController, ManagerrateModel, ManagerrateTable, View/Managerrate, tmpl/managerrate (2026-02-05)
- [x] Rimosso getListQuery/getItems da ManagerratesModel - sostituiti da metodi griglia (2026-02-05)
- [x] Sicurezza 1.1: Rimuovere `ajax/update.php` - RCE, path traversal, CSRF bypass (2026-02-05)
- [x] Sicurezza 1.2: Riscrivere `tmpl/managerrates/default.php` - MVC fix + CSRF (2026-02-05)
- [x] Sicurezza 1.3: Fixare `filter="raw"` nei form XML (2026-02-05)
- [x] Sicurezza 1.4: Fixare `filter="JComponentHelper::filterText"` nei form XML (2026-02-05)
- [x] Fixare `$this->transitions` undefined in tutte le 5 list View (2026-02-05)
- [x] Rimuovere riferimento al plugin Finder dal manifest (2026-02-05)
- [x] Rimuovere plugin Finder legacy (2026-02-04)
