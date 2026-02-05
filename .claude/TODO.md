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
- [ ] **5.2** Aggiungere colonne Joomla standard mancanti a tutte le tabelle: `created` (DATETIME), `modified` (DATETIME), `modified_by` (INT)
- [ ] **5.3** Aggiungere `asset_id` alle 4 tabelle che non ce l'hanno (room_categories, rate_periods, rates, rate_typologies) oppure rimuovere il codice ACL inutile dalle relative Table classes
- [ ] **5.4** Aggiungere colonna `version_note` a tutte le tabelle (richiesta da VersionableTableInterface nei form e nelle Table classes) oppure rimuovere l'interfaccia
- [x] **5.5** Aggiungere indici secondari (2026-02-05):
  - `rooms`: idx_room_category, idx_state_ordering ✓
  - `room_categories`: idx_room_category_parent, idx_state_ordering ✓
  - `rates`: idx_room_id, idx_period_id, idx_typology_id, idx_state ✓
  - `rate_periods`: idx_state_ordering ✓
  - `rate_typologies`: idx_state_ordering ✓
- [ ] **5.6** Sincronizzare install SQL col DB reale: `room_floor_plan` e `room_thumbnail` (VARCHAR vs TEXT) - decidere quale tipo tenere e allineare
- [ ] **5.7** Aggiornare collation da `utf8mb3_general_ci` a `utf8mb4_unicode_ci`
- [x] **5.8** Creare sistema update SQL funzionale: creato `sql/updates/mysql/3.1.0.sql` (2026-02-05)
- [ ] **5.9** Aggiungere cleanup in uninstall SQL: rimuovere entries da `#__content_types`, `#__assets`, `#__ucm_content`

### FASE 6 - Performance

- [x] **6.1** ~~Eliminare N+1 queries in `ManagerratesModel::getItems()`~~ - Risolto: griglia usa query dedicate (getPeriods/getRooms/getTypologies/getRatesGrid), rimosso getListQuery/getItems (2026-02-05)
- [x] **6.2** Eliminare N+1 queries in `RoomsmanagerModel::getItems()` - Usato valore già recuperato dal JOIN invece di N query (2026-02-05)
- [x] **6.3** Eliminare N+1 queries in `ManagerroomcategoriesModel::getItems()` - Usato valore già recuperato dal JOIN invece di N query (2026-02-05)
- [x] **6.4** Rimuovere jQuery dependency da `joomla.asset.json` - admin.js era già vanilla JS, rimossa dipendenza + aggiornata versione a 3.0.0 (2026-02-05)

### FASE 7 - Gestione lingue

**Decisione**: Manteniamo l'approccio a colonne separate (`*_de`, `*_it`, `*_en`, `*_fr`, `*_es`) ma rendiamo la lista lingue configurabile.

- [ ] **7.1** Creare configurazione centralizzata per lista lingue supportate (es. in config.xml o costante)
- [ ] **7.2** Aggiornare form XML per usare la lista lingue configurata
- [ ] **7.3** Aggiornare template per usare la lista lingue configurata (rimuovere hardcoded `$lang = substr(..., 0, 2)`)
- [ ] **7.4** Documentare procedura per aggiungere nuova lingua (SQL + config)

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

- [x] Rimosso codice morto edit singola rate: managerrate.xml, ManagerrateController, ManagerrateModel, ManagerrateTable, View/Managerrate, tmpl/managerrate (2026-02-05)
- [x] Rimosso getListQuery/getItems da ManagerratesModel - sostituiti da metodi griglia (2026-02-05)
- [x] Sicurezza 1.1: Rimuovere `ajax/update.php` - RCE, path traversal, CSRF bypass (2026-02-05)
- [x] Sicurezza 1.2: Riscrivere `tmpl/managerrates/default.php` - MVC fix + CSRF (2026-02-05)
- [x] Sicurezza 1.3: Fixare `filter="raw"` nei form XML (2026-02-05)
- [x] Sicurezza 1.4: Fixare `filter="JComponentHelper::filterText"` nei form XML (2026-02-05)
- [x] Fixare `$this->transitions` undefined in tutte le 5 list View (2026-02-05)
- [x] Rimuovere riferimento al plugin Finder dal manifest (2026-02-05)
- [x] Rimuovere plugin Finder legacy (2026-02-04)
