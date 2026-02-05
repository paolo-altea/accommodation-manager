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
- [ ] **1.5** Rate Typologies - Verificare layout, campi, messaggi

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
- [ ] **2.2** Form edit Rate Period - Verificare campi, validazione, UX
- [x] **2.3** Form edit Room Category - Bootstrap 5, tab per lingua, sidebar stato (2026-02-05)
- [ ] **2.4** Form edit Rate Typology - Verificare campi, validazione, UX

### FASE 3 - Funzionalità rotte

- [ ] **3.1** Fixare ricerca testo rotta in 4 ListModel: `$search` viene preparato ma `$query->where()` mai chiamato. File: `ManagerroomcategoriesModel`, `ManagerratesModel`, `ManagerrateperiodsModel`, `ManagerratetypologiesModel`
- [ ] **3.2** Fixare ForeignKey fields in `managerrate.xml`: `value_field="id"` mostra ID numerici grezzi. Cambiare: room_id -> `room_name`, period_id -> `period_title_en`, typology_id -> `rate_typology_en`
- [ ] **3.3** Fixare bug `strrpos()` in Table bind(): `!= false` -> `!== false` (fallisce se comma è a posizione 0). File: RoommanagerTable, RoommanagercategoryTable, ManagerrateTable
- [ ] **3.4** Aggiungere language key mancante `COM_ACCOMMODATION_MANAGER_NO_ELEMENT_SELECTED` in tutti i file .ini (usata in 5 controller)

### FASE 4 - API deprecate Joomla 3 → Joomla 5

- [ ] **4.1** Sostituire `Factory::getUser()` con `$this->getCurrentUser()` o `Factory::getApplication()->getIdentity()` in tutti i Model, View, Table
- [ ] **4.2** Sostituire `Factory::getDbo()` con dependency injection (`$this->getDatabase()`) in tutti i Model e Table
- [ ] **4.3** Rimuovere `jimport('joomla.filter.output')` da tutti i 5 AdminModel (no-op in J5)
- [ ] **4.4** Rimuovere uso di `Sidebar` HTML Helper da tutte le list View (deprecato in J4+)
- [ ] **4.5** Sostituire `$table->getError()` con try/catch exceptions nei metodi `duplicate()` di tutti i controller
- [ ] **4.6** Aggiornare `checked_out_time` default da `"0000-00-00 00:00:00"` a `NULL` in tutti i form XML

### FASE 5 - Database schema

- [ ] **5.1** Fixare tipi colonna sbagliati:
  - `rates.rate`: VARCHAR(255) → DECIMAL(10,2)
  - `rooms.room_surface`: VARCHAR(255) → DECIMAL(8,2)
  - `rooms.room_people`: VARCHAR(255) → SMALLINT
  - `rate_periods.period_start/period_end`: DATETIME → DATE
- [ ] **5.2** Aggiungere colonne Joomla standard mancanti a tutte le tabelle: `created` (DATETIME), `modified` (DATETIME), `modified_by` (INT)
- [ ] **5.3** Aggiungere `asset_id` alle 4 tabelle che non ce l'hanno (room_categories, rate_periods, rates, rate_typologies) oppure rimuovere il codice ACL inutile dalle relative Table classes
- [ ] **5.4** Aggiungere colonna `version_note` a tutte le tabelle (richiesta da VersionableTableInterface nei form e nelle Table classes) oppure rimuovere l'interfaccia
- [ ] **5.5** Aggiungere indici secondari:
  - `rooms`: INDEX su `room_category`, `state`, `created_by`, `checked_out`
  - `room_categories`: INDEX su `room_category_parent`, `state`
  - `rates`: INDEX su `room_id`, `period_id`, `typology_id`, `state` + INDEX composto `(room_id, period_id, typology_id)`
  - `rate_periods`: INDEX su `state`, `(period_start, period_end)`
  - `rate_typologies`: INDEX su `state`
- [ ] **5.6** Sincronizzare install SQL col DB reale: `room_floor_plan` e `room_thumbnail` (VARCHAR vs TEXT) - decidere quale tipo tenere e allineare
- [ ] **5.7** Aggiornare collation da `utf8mb3_general_ci` a `utf8mb4_unicode_ci`
- [ ] **5.8** Creare sistema update SQL funzionale con version markers (attuale non ha marker, referenzia path Joomla 3)
- [ ] **5.9** Aggiungere cleanup in uninstall SQL: rimuovere entries da `#__content_types`, `#__assets`, `#__ucm_content`

### FASE 6 - Performance

- [ ] **6.1** Eliminare N+1 queries in `ManagerratesModel::getItems()` - 3 query per ogni item (room_id, period_id, typology_id). Usare JOIN nella query principale.
- [ ] **6.2** Eliminare N+1 queries in `RoomsmanagerModel::getItems()` - query separata per ogni room per risolvere room_category. Usare JOIN.
- [ ] **6.3** Eliminare N+1 queries in `ManagerroomcategoriesModel::getItems()` - query separata per ogni categoria per risolvere parent. Usare JOIN.
- [ ] **6.4** Rimuovere jQuery dependency da `joomla.asset.json` - usare vanilla JS

### FASE 7 - Gestione lingue

**Decisione**: Manteniamo l'approccio a colonne separate (`*_de`, `*_it`, `*_en`, `*_fr`, `*_es`) ma rendiamo la lista lingue configurabile.

- [ ] **7.1** Creare configurazione centralizzata per lista lingue supportate (es. in config.xml o costante)
- [ ] **7.2** Aggiornare form XML per usare la lista lingue configurata
- [ ] **7.3** Aggiornare template per usare la lista lingue configurata (rimuovere hardcoded `$lang = substr(..., 0, 2)`)
- [ ] **7.4** Documentare procedura per aggiungere nuova lingua (SQL + config)

### FASE 8 - Pulizia codice morto

- [ ] **8.1** Rimuovere 5 Field classes orfane (mai usate nei form XML): `ModifiedbyField.php`, `TimecreatedField.php`, `TimeupdatedField.php`, `NestedparentField.php`, `FilemultipleField.php` - sia da admin che da site
- [ ] **8.2** Rimuovere `listhelper.php` (nessun namespace, naming J3, non autoloadable in J5)
- [ ] **8.3** Rimuovere `getSortFields()` da tutte le 5 list View (mai chiamato)
- [ ] **8.4** Rimuovere variabile `$date` inutilizzata da tutti i 5 Table bind()
- [ ] **8.5** Rimuovere doppia assegnazione `$task` da tutti i 5 Table bind()
- [ ] **8.6** Rimuovere placeholder `//XXX_CUSTOM_TABLE_FUNCTION` da tutte le 5 Table classes
- [ ] **8.7** Rimuovere tutti gli import `use` inutilizzati (SiteApplication, Multilanguage, Route, Uri, TagsHelper, ParameterType, ArrayHelper, Text in ListModel, OutputFilter, File, ContentHelper)
- [ ] **8.8** Rimuovere override `store()` inutile da tutte le Table (chiama solo parent con default)
- [ ] **8.9** Rimuovere override `delete()` inutile da tutte le Table (load + parent::delete senza logica aggiuntiva)
- [ ] **8.10** Rimuovere binding `params`/`metadata` da Table bind() (colonne inesistenti nel DB)
- [ ] **8.11** Rimuovere `$app` inutilizzato da `getForm()` in tutti i 5 AdminModel
- [ ] **8.12** Sostituire `@$table->ordering` (error suppression) con check esplicito in tutti i AdminModel prepareTable()
- [ ] **8.13** Rimuovere tutti i file `index.html` (convenzione Joomla 3, non necessaria)

### FASE 9 - Refactoring strutturale

- [ ] **9.1** Estrarre base class comune per i 5 AdminController (duplicate/getModel/saveOrderAjax identici)
- [ ] **9.2** Estrarre base class comune per i 5 AdminModel (getForm/loadFormData/getItem/duplicate/prepareTable identici)
- [ ] **9.3** Estrarre base class comune per le 5 Table classes (bind/check/store/delete identici)
- [ ] **9.4** Estrarre base class comune per le 5 list HtmlView + 5 edit HtmlView
- [ ] **9.5** Spostare query SQL raw in `prepareTable()` a Query Builder
- [ ] **9.6** Aggiungere check `core.edit.own` nelle edit View (attualmente solo core.edit e core.create)
- [ ] **9.7** Valutare se le costanti globali MODIFIED/NOT_MODIFIED in script.php debbano diventare class constants

---

## CONFIGURAZIONE & BUILD

---

### FASE 10 - Manifest e configurazione

- [ ] **10.1** Rimuovere manifest duplicato da `src/administrator/components/com_accommodation_manager/accommodation_manager.xml` (quello vero è alla root)
- [ ] **10.2** Rimuovere `script.php` duplicato dalla cartella admin (quello vero è alla root)
- [ ] **10.3** Fixare `creationDate` nel manifest: contiene "2.1.1" (versione) anziché una data reale
- [ ] **10.4** Allineare versioni: manifest 2.1.1, joomla.asset.json "CVS: 2.0.1" - unificare
- [ ] **10.5** Aggiornare `version="4.0"` a `version="5.0"` nel tag `<extension>` del manifest
- [ ] **10.6** Rimuovere o riscrivere `presets/content.xml` (copia letterale del preset com_content, irrilevante)
- [ ] **10.7** Pulire `config.xml`: rimuovere fieldset vuoto e blocco commentato boilerplate
- [ ] **10.8** Aggiungere in `config.xml` campi link multilingua:
  - Link richiesta (request_link_de, request_link_it, request_link_en, request_link_fr, request_link_es)
  - Link booking (booking_link_de, booking_link_it, booking_link_en, booking_link_fr, booking_link_es)
- [ ] **10.9** Fixare `joomla.asset.json`: rimuovere prefisso "CVS:", aggiornare versione
- [ ] **10.10** Aggiungere sezioni ACL mancanti in `access.xml` per le altre 4 entità (o decidere di non supportare ACL granulare)
- [ ] **10.11** Fixare language key errata nei filter form: `COM_USERS_FILTER_SEARCH_DESC` → `COM_ACCOMMODATION_MANAGER_FILTER_SEARCH_DESC`
- [ ] **10.12** Rimuovere language key duplicata `COM_ACCOMMODATION_MANAGER_XML_DESCRIPTION` in com_accommodation_manager.ini
- [x] **10.13** Aggiungere filtro per categoria in Rooms list (2026-02-05)
- [x] **10.15** Aggiungere filtro per parent category in Room Categories list (2026-02-05)
- [x] **10.16** Mostrare "No Parent" invece di "0" nella colonna Parent della lista Room Categories (2026-02-05)
- [ ] **10.14** Aggiungere filtro per room in Rates list

### FASE 11 - Build e packaging

- [ ] **11.1** Creare `pkg_accommodation_manager.xml` (package manifest) seguendo modello enquirytools
- [ ] **11.2** Creare `pkg_accommodation_manager_script.php` (package install script)
- [x] **11.3** Creare `build/build.sh` per generare ZIP del pacchetto (2026-02-05)
- [x] **11.4** Creare `.gitignore` appropriato (dist/, *.zip, .DS_Store, .idea/) (2026-02-05)
- [ ] **11.5** Popolare `language/` root con file sys.ini a livello pacchetto
- [ ] **11.6** Inizializzare git repository

---

## FRONTEND (da fare dopo il backend)

---

### FASE 12 - Frontend pubblico

- [ ] **12.1** Progettare nuova architettura frontend (da definire)
- [ ] **12.2** Implementare nuove View frontend
- [ ] **12.3** Implementare Router SEF
- [ ] **12.4** Usare come riferimento/spunto: `/Users/paolodaponte/projects/base/static/room_rate` (da approfondire)

---

## MIGRAZIONE DATI (da fare a fine progetto)

---

### FASE 13 - Script import dati

Script PHP per migrare dati dal vecchio componente al nuovo:

- [ ] **13.1** Convertire `room_gallery` da path stringa a JSON subform (estrarre immagini da cartella e creare array)
- [ ] **13.2** Mappare dati esistenti nei nuovi campi alt (se disponibili altrove)
- [ ] **13.3** Decidere come gestire `room_pano` (migrare a video? scartare?)
- [ ] **13.4** Verificare/pulire dati rates, periods, typologies, categories
- [ ] **13.5** Creare script di backup pre-migrazione
- [ ] **13.6** Creare script di rollback in caso di errori

---

## Completati (storico)

- [x] Sicurezza 1.1: Rimuovere `ajax/update.php` - RCE, path traversal, CSRF bypass (2026-02-05)
- [x] Sicurezza 1.2: Riscrivere `tmpl/managerrates/default.php` - MVC fix + CSRF (2026-02-05)
- [x] Sicurezza 1.3: Fixare `filter="raw"` nei form XML (2026-02-05)
- [x] Sicurezza 1.4: Fixare `filter="JComponentHelper::filterText"` nei form XML (2026-02-05)
- [x] Fixare `$this->transitions` undefined in tutte le 5 list View (2026-02-05)
- [x] Rimuovere riferimento al plugin Finder dal manifest (2026-02-05)
- [x] Rimuovere plugin Finder legacy (2026-02-04)
