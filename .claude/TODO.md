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
- [ ] **7.4** Creare script per aggiungere nuova lingua (SQL + config + form + template) - DA FARE DOPO FASE 12

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

Riferimento analizzato: `/Users/paolodaponte/projects/base/static/room_rate` (script custom usato in produzione al posto del frontend Joomla)

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

### Pre-rilascio

- [ ] **PR.1** Audit utilizzo HTMLHelper e funzioni native Joomla: verificare tutto il codice (template, model, view, controller) per individuare casi in cui si costruisce output a mano invece di usare helper nativi Joomla (es. `HTMLHelper::_('image')`, `HTMLHelper::_('date')`, `HTMLHelper::_('grid.*')`, `Text::_()`, `Route::_()`, ecc.)
- [ ] **PR.2** Strategia traduzioni per lingue non installate: il componente prevede 5 lingue (de, it, en, fr, es) con colonne DB dedicate, ma i file `.ini` esistono solo per en-GB, de-DE, it-IT. Decidere come gestire fr-FR e es-ES (creare i file? fallback a en-GB? generare automaticamente?). Valutare anche cosa succede se una lingua è abilitata nel componente (`config.xml`) ma il language pack Joomla corrispondente non è installato nel sito.
- [ ] **PR.3** Analisi overkill generale: revisione dell'intero componente per individuare complessità non necessaria, over-engineering, astrazioni premature, opzioni di configurazione eccessive, o codice ridondante. Semplificare dove possibile mantenendo solo ciò che serve realmente.

---

## MIGRAZIONE DATI (da fare a fine progetto)

---

### FASE 19 - Script import dati

Script PHP per migrare dati dal vecchio componente al nuovo:

- [ ] **17.1** Convertire `room_gallery` da path stringa a JSON subform (estrarre immagini da cartella e creare array)
- [ ] **17.2** Mappare dati esistenti nei nuovi campi alt (se disponibili altrove)
- [ ] **17.3** Decidere come gestire `room_pano` (migrare a video? scartare?)
- [ ] **17.4** Verificare/pulire dati rates, periods, typologies, categories
- [ ] **17.5** Creare script di backup pre-migrazione
- [ ] **17.6** Creare script di rollback in caso di errori

---

## POST-RELEASE

---

- [ ] **POST.1** Documentazione componente nel Help button: utilizzare il pulsante Help nella toolbar di configurazione Joomla per linkare/mostrare una documentazione completa del componente (parametri, CSS custom properties, JS custom events, struttura DB, ecc.)

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
