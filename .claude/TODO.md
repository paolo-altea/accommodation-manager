# TODO - Accommodation Manager Component (Joomla 5/6)

## Da fare

### FASE 1 - Sicurezza (CRITICA)

- [x] **1.1** Rimuovere `ajax/update.php` - contiene RCE via unserialize(), path traversal, CSRF bypass, query SQL dirette senza sanitizzazione, user ID hardcoded (413). File da eliminare completamente. (2026-02-05)
- [x] **1.2** Riscrivere `tmpl/managerrates/default.php` - attualmente posta direttamente a ajax/update.php senza CSRF, contiene query SQL nel template (viola MVC), usa JFactory/JHtml/JText (fatal error su Joomla 5). La logica di aggiornamento massivo tariffe va spostata in un Controller dedicato con token CSRF. (2026-02-05)
- [ ] **1.3** Fixare `filter="raw"` nei form XML (`roommanagercategory.xml`, `managerrateperiod.xml`, `managerratetypology.xml`) - cambiare in `filter="string"` o `filter="safehtml"` per sanitizzare input utente.
- [ ] **1.4** Fixare `filter="JComponentHelper::filterText"` nei form XML (`roommanager.xml`, `roommanagercategory.xml`) - cambiare in `filter="safehtml"` (class name Joomla 3 deprecato).

### FASE 2 - Database schema

- [ ] **2.1** Fixare tipi colonna sbagliati:
  - `rates.rate`: VARCHAR(255) -> DECIMAL(10,2)
  - `rooms.room_surface`: VARCHAR(255) -> DECIMAL(8,2)
  - `rooms.room_people`: VARCHAR(255) -> SMALLINT
  - `rate_periods.period_start/period_end`: DATETIME -> DATE
- [ ] **2.2** Aggiungere colonne Joomla standard mancanti a tutte le tabelle: `created` (DATETIME), `modified` (DATETIME), `modified_by` (INT)
- [ ] **2.3** Aggiungere `asset_id` alle 4 tabelle che non ce l'hanno (room_categories, rate_periods, rates, rate_typologies) oppure rimuovere il codice ACL inutile dalle relative Table classes
- [ ] **2.4** Aggiungere colonna `version_note` a tutte le tabelle (richiesta da VersionableTableInterface nei form e nelle Table classes) oppure rimuovere l'interfaccia
- [ ] **2.5** Aggiungere indici secondari:
  - `rooms`: INDEX su `room_category`, `state`, `created_by`, `checked_out`
  - `room_categories`: INDEX su `room_category_parent`, `state`
  - `rates`: INDEX su `room_id`, `period_id`, `typology_id`, `state` + INDEX composto `(room_id, period_id, typology_id)`
  - `rate_periods`: INDEX su `state`, `(period_start, period_end)`
  - `rate_typologies`: INDEX su `state`
- [ ] **2.6** Sincronizzare install SQL col DB reale: `room_floor_plan` e `room_thumbnail` (VARCHAR vs TEXT) - decidere quale tipo tenere e allineare
- [ ] **2.7** Aggiornare collation da `utf8mb3_general_ci` a `utf8mb4_unicode_ci`
- [ ] **2.8** Creare sistema update SQL funzionale con version markers (attuale non ha marker, referenzia path Joomla 3)
- [ ] **2.9** Aggiungere cleanup in uninstall SQL: rimuovere entries da `#__content_types`, `#__assets`, `#__ucm_content`

### FASE 3 - API deprecate Joomla 3 -> Joomla 5

- [ ] **3.1** Sostituire `Factory::getUser()` con `$this->getCurrentUser()` o `Factory::getApplication()->getIdentity()` in tutti i Model, View, Table
- [ ] **3.2** Sostituire `Factory::getDbo()` con dependency injection (`$this->getDatabase()`) in tutti i Model e Table
- [ ] **3.3** Rimuovere `jimport('joomla.filter.output')` da tutti i 5 AdminModel (no-op in J5)
- [ ] **3.4** Rimuovere uso di `Sidebar` HTML Helper da tutte le list View (deprecato in J4+)
- [ ] **3.5** Sostituire `$table->getError()` con try/catch exceptions nei metodi `duplicate()` di tutti i controller
- [ ] **3.6** Sostituire `JText::_()` con `Text::_()` in `tmpl/managerrates/default.php` (4 occorrenze - fatal error su J5)
- [ ] **3.7** Aggiornare `checked_out_time` default da `"0000-00-00 00:00:00"` a `NULL` in tutti i form XML

### FASE 4 - Funzionalita' rotte

- [ ] **4.1** Fixare ricerca testo rotta in 4 ListModel: `$search` viene preparato ma `$query->where()` mai chiamato. File: `ManagerroomcategoriesModel`, `ManagerratesModel`, `ManagerrateperiodsModel`, `ManagerratetypologiesModel`
- [x] **4.2** Fixare `$this->transitions` undefined in tutte le 5 list View - definire la variabile o rimuovere il check (2026-02-05)
- [ ] **4.3** Fixare ForeignKey fields in `managerrate.xml`: `value_field="id"` mostra ID numerici grezzi. Cambiare: room_id -> `room_name`, period_id -> `period_title_en`, typology_id -> `rate_typology_en`
- [ ] **4.4** Fixare bug `strrpos()` in Table bind(): `!= false` -> `!== false` (fallisce se comma e' a posizione 0). File: RoommanagerTable, RoommanagercategoryTable, ManagerrateTable
- [ ] **4.5** Aggiungere language key mancante `COM_ACCOMMODATION_MANAGER_NO_ELEMENT_SELECTED` in tutti i file .ini (usata in 5 controller)

### FASE 5 - Performance

- [ ] **5.1** Eliminare N+1 queries in `ManagerratesModel::getItems()` - 3 query per ogni item (room_id, period_id, typology_id). Usare JOIN nella query principale. Le query attuali sono anche inutili (cercano id per restituire id).
- [ ] **5.2** Eliminare N+1 queries in `RoomsmanagerModel::getItems()` - query separata per ogni room per risolvere room_category. Usare JOIN.
- [ ] **5.3** Eliminare N+1 queries in `ManagerroomcategoriesModel::getItems()` - query separata per ogni categoria per risolvere parent. Usare JOIN.
- [ ] **5.4** Rimuovere jQuery dependency da `joomla.asset.json` - usare vanilla JS

### FASE 6 - Pulizia codice morto e orfano

- [ ] **6.1** Rimuovere 5 Field classes orfane (mai usate nei form XML): `ModifiedbyField.php`, `TimecreatedField.php`, `TimeupdatedField.php`, `NestedparentField.php`, `FilemultipleField.php` - sia da admin che da site
- [ ] **6.2** Rimuovere `listhelper.php` (nessun namespace, naming J3, non autoloadable in J5)
- [ ] **6.3** Rimuovere `getSortFields()` da tutte le 5 list View (mai chiamato)
- [ ] **6.4** Rimuovere variabile `$date` inutilizzata da tutti i 5 Table bind()
- [ ] **6.5** Rimuovere doppia assegnazione `$task` da tutti i 5 Table bind()
- [ ] **6.6** Rimuovere placeholder `//XXX_CUSTOM_TABLE_FUNCTION` da tutte le 5 Table classes
- [ ] **6.7** Rimuovere tutti gli import `use` inutilizzati (SiteApplication, Multilanguage, Route, Uri, TagsHelper, ParameterType, ArrayHelper, Text in ListModel, OutputFilter, File, ContentHelper)
- [ ] **6.8** Rimuovere override `store()` inutile da tutte le Table (chiama solo parent con default)
- [ ] **6.9** Rimuovere override `delete()` inutile da tutte le Table (load + parent::delete senza logica aggiuntiva)
- [ ] **6.10** Rimuovere binding `params`/`metadata` da Table bind() (colonne inesistenti nel DB)
- [ ] **6.11** Rimuovere `$app` inutilizzato da `getForm()` in tutti i 5 AdminModel
- [ ] **6.12** Sostituire `@$table->ordering` (error suppression) con check esplicito in tutti i AdminModel prepareTable()
- [ ] **6.13** Rimuovere tutti i file `index.html` (convenzione Joomla 3, non necessaria)

### FASE 7 - Manifest e configurazione

- [ ] **7.1** Rimuovere manifest duplicato da `src/administrator/components/com_accommodation_manager/accommodation_manager.xml` (quello vero e' alla root)
- [ ] **7.2** Rimuovere `script.php` duplicato dalla cartella admin (quello vero e' alla root)
- [ ] **7.3** Fixare `creationDate` nel manifest: contiene "2.1.1" (versione) anziche' una data reale
- [ ] **7.4** Allineare versioni: manifest 2.1.1, joomla.asset.json "CVS: 2.0.1" - unificare
- [ ] **7.5** Aggiornare `version="4.0"` a `version="5.0"` nel tag `<extension>` del manifest
- [ ] **7.6** Rimuovere o riscrivere `presets/content.xml` (copia letterale del preset com_content, irrilevante)
- [ ] **7.7** Pulire `config.xml`: rimuovere fieldset vuoto e blocco commentato boilerplate
- [ ] **7.8** Fixare `joomla.asset.json`: rimuovere prefisso "CVS:", aggiornare versione
- [ ] **7.9** Aggiungere sezioni ACL mancanti in `access.xml` per le altre 4 entita' (o decidere di non supportare ACL granulare)
- [ ] **7.10** Fixare language key errata nei filter form: `COM_USERS_FILTER_SEARCH_DESC` -> `COM_ACCOMMODATION_MANAGER_FILTER_SEARCH_DESC`
- [ ] **7.11** Rimuovere language key duplicata `COM_ACCOMMODATION_MANAGER_XML_DESCRIPTION` in com_accommodation_manager.ini
- [ ] **7.12** Aggiungere filtri per entita' correlate nei filter form XML (es. filtra rooms per categoria, rates per room)
- [x] **7.13** Rimuovere riferimento al plugin Finder dal manifest del componente (sezione `<plugins>` se presente) (2026-02-05)

### FASE 8 - Refactoring strutturale

- [ ] **8.1** Estrarre base class comune per i 5 AdminController (duplicate/getModel/saveOrderAjax identici)
- [ ] **8.2** Estrarre base class comune per i 5 AdminModel (getForm/loadFormData/getItem/duplicate/prepareTable identici)
- [ ] **8.3** Estrarre base class comune per le 5 Table classes (bind/check/store/delete identici)
- [ ] **8.4** Estrarre base class comune per le 5 list HtmlView + 5 edit HtmlView
- [ ] **8.5** Spostare query SQL raw in `prepareTable()` a Query Builder
- [ ] **8.6** Aggiungere check `core.edit.own` nelle edit View (attualmente solo core.edit e core.create)
- [ ] **8.7** Valutare se le costanti globali MODIFIED/NOT_MODIFIED in script.php debbano diventare class constants

### FASE 9 - Build e packaging

- [ ] **9.1** Creare `pkg_accommodation_manager.xml` (package manifest) seguendo modello enquirytools
- [ ] **9.2** Creare `pkg_accommodation_manager_script.php` (package install script)
- [x] **9.3** Creare `build/build.sh` per generare ZIP del pacchetto (2026-02-05)
- [x] **9.4** Creare `.gitignore` appropriato (dist/, *.zip, .DS_Store, .idea/) (2026-02-05)
- [ ] **9.5** Popolare `language/` root con file sys.ini a livello pacchetto
- [ ] **9.6** Inizializzare git repository

### FASE 10 - Frontend (da rifare da zero)

- [ ] **10.1** Progettare nuova architettura frontend (da definire)
- [ ] **10.2** Implementare nuove View frontend
- [ ] **10.3** Implementare Router SEF
- [ ] **10.4** Usare come riferimento/spunto: `/Users/paolodaponte/projects/base/static/room_rate` (da approfondire)

### FASE 11 - Revisione interfacce admin

Revisione una ad una delle interfacce backend per verificare usabilità e consistenza:

- [x] **11.1** Rates (griglia tariffe) - Rivista struttura tabella, paginazione, formato date (2026-02-05)
- [x] **11.2** Rate Periods - Formato date corretto (2026-02-05)
- [ ] **11.3** Rooms Manager - Verificare layout, campi, messaggi
- [ ] **11.4** Room Categories - Verificare layout, campi, messaggi
- [ ] **11.5** Rate Typologies - Verificare layout, campi, messaggi
- [ ] **11.6** Form edit Room - Verificare campi, validazione, UX
- [ ] **11.7** Form edit Rate Period - Verificare campi, validazione, UX
- [ ] **11.8** Form edit Room Category - Verificare campi, validazione, UX
- [ ] **11.9** Form edit Rate Typology - Verificare campi, validazione, UX

### FASE 12 - Gestione lingue

- [ ] **12.1** Rivedere architettura multilingua: attualmente le traduzioni sono salvate come colonne separate nel DB (`*_de`, `*_it`, `*_en`, `*_fr`, `*_es`). Valutare alternative:
  - Mantenere approccio attuale ma renderlo configurabile (lista lingue dinamica)
  - Usare tabelle di traduzione separate (es. `#__accommodation_manager_rooms_translations`)
  - Integrare con sistema Associations di Joomla
- [ ] **12.2** Rendere la lista delle lingue supportate configurabile invece di hardcoded
- [ ] **12.3** Aggiornare form XML per generare dinamicamente i campi lingua
- [ ] **12.4** Aggiornare template per gestire lingue dinamiche (attualmente hardcoded `$lang = substr(..., 0, 2)`)

## In corso

## Completati

- [x] Rimuovere plugin Finder (`src/plugins/finder/accommodation_manager_roomsmanager/`) - plugin legacy non compatibile J5, rimosso completamente. Se servira' la ricerca Smart Search si ricreera' da zero con architettura moderna. (2026-02-04)
