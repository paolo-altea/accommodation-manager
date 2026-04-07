#!/usr/bin/env php
<?php
/**
 * Seed script for Accommodation Manager
 * Populates the database with realistic fake content for testing.
 *
 * Usage: php build/seed.php /path/to/joomla
 *
 * @version    1.0.0
 * @package    Com_Accommodation_manager
 * @author     Altea Software Srl <web@altea.it>
 */

// ─── CLI check ──────────────────────────────────────────────────
if (php_sapi_name() !== 'cli') {
	die('This script must be run from the command line.');
}

// ─── Joomla root ────────────────────────────────────────────────
$joomlaRoot = $argv[1] ?? null;

if (!$joomlaRoot || !is_file($joomlaRoot . '/configuration.php')) {
	echo "Usage: php build/seed.php /path/to/joomla\n";
	echo "Example: php build/seed.php /path/to/joomla\n";
	exit(1);
}

$joomlaRoot = rtrim($joomlaRoot, '/');

// ─── Load Joomla configuration ─────────────────────────────────
require_once $joomlaRoot . '/configuration.php';
$config = new JConfig();

$dsn = "mysql:host={$config->host};dbname={$config->db};charset=utf8mb4";

try {
	$pdo = new PDO($dsn, $config->user, $config->password, [
		PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
		PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_OBJ,
	]);
} catch (PDOException $e) {
	echo "DB connection failed: " . $e->getMessage() . "\n";
	exit(1);
}

$prefix = $config->dbprefix;

echo "Connected to {$config->db} (prefix: {$prefix})\n\n";

// ─── Download placeholder images ───────────────────────────────
$imageBaseDir = $joomlaRoot . '/images/accommodation_manager';
$imageDirs    = [
	'categories'       => $imageBaseDir . '/categories',
	'rooms_thumbnails' => $imageBaseDir . '/rooms/thumbnails',
	'rooms_floorplans' => $imageBaseDir . '/rooms/floorplans',
	'rooms_gallery'    => $imageBaseDir . '/rooms/gallery',
];

foreach ($imageDirs as $dir) {
	if (!is_dir($dir)) {
		mkdir($dir, 0755, true);
		echo "Created directory: $dir\n";
	}
}

/**
 * Download an image from picsum.photos if it doesn't already exist.
 */
function downloadImage(string $savePath, int $width, int $height, int $picsumId): bool
{
	if (is_file($savePath)) {
		return true;
	}

	$url  = "https://picsum.photos/id/{$picsumId}/{$width}/{$height}";
	$data = @file_get_contents($url);

	if ($data === false) {
		echo "  Warning: Could not download $url\n";
		return false;
	}

	file_put_contents($savePath, $data);
	return true;
}

/**
 * Build Joomla media field value with #joomlaImage fragment.
 */
function joomlaImagePath(string $relativePath, int $width, int $height): string
{
	// relativePath is like: images/accommodation_manager/rooms/thumbnails/alpenrose.jpg
	$localPath = str_replace('images/', '', $relativePath);
	return $relativePath . '#joomlaImage://local-images/' . $localPath . '?width=' . $width . '&height=' . $height;
}

echo "\nDownloading placeholder images...\n";

// Picsum IDs for realistic hotel/nature images
$picsumIds = [
	'categories' => [164, 110, 271, 139],
	'thumbnails' => [164, 271, 188, 119, 237, 134],
	'floorplans' => [368, 368, 368, 368, 368, 368],
	'gallery'    => [164, 110, 271, 139, 188, 119, 237, 134, 160, 167, 180, 190, 173, 178, 210, 100, 114, 136],
];

// Category images (4)
for ($i = 0; $i < 4; $i++) {
	$file = $imageDirs['categories'] . '/category_' . ($i + 1) . '.jpg';
	downloadImage($file, 800, 600, $picsumIds['categories'][$i]);
}

// Room thumbnails (6)
for ($i = 0; $i < 6; $i++) {
	$file = $imageDirs['rooms_thumbnails'] . '/room_' . ($i + 1) . '.jpg';
	downloadImage($file, 800, 600, $picsumIds['thumbnails'][$i]);
}

// Room floor plans (6) — use same simple image
for ($i = 0; $i < 6; $i++) {
	$file = $imageDirs['rooms_floorplans'] . '/floorplan_' . ($i + 1) . '.jpg';
	downloadImage($file, 600, 400, $picsumIds['floorplans'][$i]);
}

// Gallery images (18 = 6 rooms × 3 images each)
for ($i = 0; $i < 18; $i++) {
	$file = $imageDirs['rooms_gallery'] . '/gallery_' . ($i + 1) . '.jpg';
	downloadImage($file, 1200, 800, $picsumIds['gallery'][$i]);
}

echo "Images downloaded.\n\n";

// ─── Truncate existing data ────────────────────────────────────
echo "Clearing existing data...\n";

$pdo->exec("SET FOREIGN_KEY_CHECKS = 0");
$pdo->exec("TRUNCATE TABLE {$prefix}accommodation_manager_rates");
$pdo->exec("TRUNCATE TABLE {$prefix}accommodation_manager_rate_periods");
$pdo->exec("TRUNCATE TABLE {$prefix}accommodation_manager_rate_typologies");
$pdo->exec("TRUNCATE TABLE {$prefix}accommodation_manager_rooms");
$pdo->exec("TRUNCATE TABLE {$prefix}accommodation_manager_room_categories");
$pdo->exec("SET FOREIGN_KEY_CHECKS = 1");

echo "Tables cleared.\n\n";

// ─── Insert Room Categories ────────────────────────────────────
echo "Inserting room categories...\n";

$categories = [
	[
		'title' => 'Double Room',
		'name_de' => 'Doppelzimmer', 'name_it' => 'Camera Doppia', 'name_en' => 'Double Room', 'name_fr' => 'Chambre Double', 'name_es' => 'Habitación Doble',
		'desc_de' => 'Unsere gemütlichen Doppelzimmer bieten Ihnen allen Komfort für einen erholsamen Aufenthalt in den Dolomiten.',
		'desc_it' => 'Le nostre accoglienti camere doppie offrono tutto il comfort per un soggiorno rilassante nelle Dolomiti.',
		'desc_en' => 'Our cosy double rooms offer you all the comfort for a relaxing stay in the Dolomites.',
		'desc_fr' => 'Nos confortables chambres doubles vous offrent tout le confort pour un séjour relaxant dans les Dolomites.',
		'desc_es' => 'Nuestras acogedoras habitaciones dobles le ofrecen todo el confort para una estancia relajante en los Dolomitas.',
		'parent' => 0, 'image_idx' => 1,
	],
	[
		'title' => 'Suite',
		'name_de' => 'Suite', 'name_it' => 'Suite', 'name_en' => 'Suite', 'name_fr' => 'Suite', 'name_es' => 'Suite',
		'desc_de' => 'Großzügige Suiten mit separatem Wohnbereich und atemberaubendem Bergblick.',
		'desc_it' => 'Suite spaziose con zona soggiorno separata e vista mozzafiato sulle montagne.',
		'desc_en' => 'Spacious suites with separate living area and stunning mountain views.',
		'desc_fr' => 'Suites spacieuses avec espace de vie séparé et vue imprenable sur les montagnes.',
		'desc_es' => 'Amplias suites con zona de estar separada e impresionantes vistas a la montaña.',
		'parent' => 0, 'image_idx' => 2,
	],
	[
		'title' => 'Single Room',
		'name_de' => 'Einzelzimmer', 'name_it' => 'Camera Singola', 'name_en' => 'Single Room', 'name_fr' => 'Chambre Simple', 'name_es' => 'Habitación Individual',
		'desc_de' => 'Komfortable Einzelzimmer, ideal für Alleinreisende und Geschäftsreisende.',
		'desc_it' => 'Comode camere singole, ideali per chi viaggia da solo o per lavoro.',
		'desc_en' => 'Comfortable single rooms, ideal for solo travellers and business guests.',
		'desc_fr' => 'Chambres simples confortables, idéales pour les voyageurs seuls et les clients d\'affaires.',
		'desc_es' => 'Cómodas habitaciones individuales, ideales para viajeros solos y huéspedes de negocios.',
		'parent' => 0, 'image_idx' => 3,
	],
	[
		'title' => 'Family Room',
		'name_de' => 'Familienzimmer', 'name_it' => 'Camera Famiglia', 'name_en' => 'Family Room', 'name_fr' => 'Chambre Familiale', 'name_es' => 'Habitación Familiar',
		'desc_de' => 'Geräumige Familienzimmer mit Platz für die ganze Familie und kindgerechter Ausstattung.',
		'desc_it' => 'Spaziose camere famiglia con spazio per tutti e attrezzature a misura di bambino.',
		'desc_en' => 'Spacious family rooms with space for everyone and child-friendly amenities.',
		'desc_fr' => 'Chambres familiales spacieuses avec de la place pour toute la famille et des équipements adaptés aux enfants.',
		'desc_es' => 'Amplias habitaciones familiares con espacio para todos y comodidades para niños.',
		'parent' => 0, 'image_idx' => 4,
	],
];

$catStmt = $pdo->prepare("
	INSERT INTO {$prefix}accommodation_manager_room_categories
	(room_category_title, room_category_name_de, room_category_name_it, room_category_name_en, room_category_name_fr, room_category_name_es,
	 room_category_description_de, room_category_description_it, room_category_description_en, room_category_description_fr, room_category_description_es,
	 room_category_parent, room_category_image, room_category_image_alt_de, room_category_image_alt_it, room_category_image_alt_en, room_category_image_alt_fr, room_category_image_alt_es,
	 state, ordering)
	VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 1, ?)
");

foreach ($categories as $i => $cat) {
	$imgRel = 'images/accommodation_manager/categories/category_' . $cat['image_idx'] . '.jpg';
	$imgVal = joomlaImagePath($imgRel, 800, 600);
	$catStmt->execute([
		$cat['title'],
		$cat['name_de'], $cat['name_it'], $cat['name_en'], $cat['name_fr'], $cat['name_es'],
		$cat['desc_de'], $cat['desc_it'], $cat['desc_en'], $cat['desc_fr'], $cat['desc_es'],
		$cat['parent'],
		$imgVal,
		$cat['name_de'], $cat['name_it'], $cat['name_en'], $cat['name_fr'], $cat['name_es'],
		$i + 1,
	]);
}

echo "  " . count($categories) . " categories inserted.\n";

// ─── Insert Rooms ──────────────────────────────────────────────
echo "Inserting rooms...\n";

$rooms = [
	[
		'name' => 'Alpenrose', 'code' => 'ALR', 'category' => 1,
		'surface' => '28', 'people' => '2', 'price_from' => '85',
		'title_de' => 'Doppelzimmer Alpenrose', 'title_it' => 'Camera Doppia Alpenrose', 'title_en' => 'Double Room Alpenrose', 'title_fr' => 'Chambre Double Alpenrose', 'title_es' => 'Habitación Doble Alpenrose',
		'intro_de' => 'Gemütliches Doppelzimmer mit Südbalkon und Blick auf die Dolomiten.',
		'intro_it' => 'Accogliente camera doppia con balcone a sud e vista sulle Dolomiti.',
		'intro_en' => 'Cosy double room with south-facing balcony and Dolomite views.',
		'intro_fr' => 'Chambre double confortable avec balcon plein sud et vue sur les Dolomites.',
		'intro_es' => 'Acogedora habitación doble con balcón orientado al sur y vistas a los Dolomitas.',
		'desc_de' => 'Das Zimmer Alpenrose besticht durch sein warmes Ambiente mit viel Holz und alpinem Charme. Der geräumige Südbalkon lädt zum Verweilen ein und bietet einen wunderbaren Blick auf die umliegende Bergwelt. Ausstattung: Dusche/WC, Föhn, Safe, Sat-TV, Telefon, WLAN.',
		'desc_it' => 'La camera Alpenrose colpisce per la sua atmosfera calda con molto legno e fascino alpino. L\'ampio balcone a sud invita a soffermarsi e offre una splendida vista sulle montagne circostanti. Dotazioni: doccia/WC, asciugacapelli, cassaforte, TV sat, telefono, WiFi.',
		'desc_en' => 'The Alpenrose room impresses with its warm ambience featuring plenty of wood and Alpine charm. The spacious south-facing balcony invites you to linger and offers wonderful views of the surrounding mountains. Amenities: shower/WC, hairdryer, safe, satellite TV, telephone, WiFi.',
		'desc_fr' => 'La chambre Alpenrose séduit par son ambiance chaleureuse avec beaucoup de bois et un charme alpin. Le spacieux balcon plein sud invite à la détente et offre une vue magnifique sur les montagnes environnantes. Équipements: douche/WC, sèche-cheveux, coffre-fort, TV satellite, téléphone, WiFi.',
		'desc_es' => 'La habitación Alpenrose impresiona con su ambiente cálido con mucha madera y encanto alpino. El amplio balcón orientado al sur invita a relajarse y ofrece vistas maravillosas de las montañas circundantes. Equipamiento: ducha/WC, secador, caja fuerte, TV satélite, teléfono, WiFi.',
		'video' => '',
	],
	[
		'name' => 'Edelweiss', 'code' => 'EDW', 'category' => 1,
		'surface' => '32', 'people' => '2', 'price_from' => '95',
		'title_de' => 'Doppelzimmer Edelweiss', 'title_it' => 'Camera Doppia Edelweiss', 'title_en' => 'Double Room Edelweiss', 'title_fr' => 'Chambre Double Edelweiss', 'title_es' => 'Habitación Doble Edelweiss',
		'intro_de' => 'Großzügiges Doppelzimmer mit Panoramafenster und modernem Design.',
		'intro_it' => 'Spaziosa camera doppia con finestra panoramica e design moderno.',
		'intro_en' => 'Generous double room with panoramic window and modern design.',
		'intro_fr' => 'Grande chambre double avec fenêtre panoramique et design moderne.',
		'intro_es' => 'Amplia habitación doble con ventana panorámica y diseño moderno.',
		'desc_de' => 'Das Zimmer Edelweiss überzeugt mit modernem Design und großzügigem Raumangebot. Das Panoramafenster bietet einen herrlichen Ausblick auf die Berglandschaft. Ausstattung: Dusche/WC, Föhn, Safe, Sat-TV, Minibar, WLAN.',
		'desc_it' => 'La camera Edelweiss convince con design moderno e ampio spazio. La finestra panoramica offre una splendida vista sul paesaggio montano. Dotazioni: doccia/WC, asciugacapelli, cassaforte, TV sat, minibar, WiFi.',
		'desc_en' => 'The Edelweiss room impresses with modern design and generous space. The panoramic window offers a magnificent view of the mountain landscape. Amenities: shower/WC, hairdryer, safe, satellite TV, minibar, WiFi.',
		'desc_fr' => 'La chambre Edelweiss convainc par son design moderne et son espace généreux. La fenêtre panoramique offre une vue magnifique sur le paysage montagneux. Équipements: douche/WC, sèche-cheveux, coffre-fort, TV satellite, minibar, WiFi.',
		'desc_es' => 'La habitación Edelweiss impresiona con diseño moderno y amplio espacio. La ventana panorámica ofrece una magnífica vista del paisaje montañoso. Equipamiento: ducha/WC, secador, caja fuerte, TV satélite, minibar, WiFi.',
		'video' => '',
	],
	[
		'name' => 'Enzian', 'code' => 'ENZ', 'category' => 2,
		'surface' => '45', 'people' => '2-4', 'price_from' => '150',
		'title_de' => 'Suite Enzian', 'title_it' => 'Suite Enzian', 'title_en' => 'Suite Enzian', 'title_fr' => 'Suite Enzian', 'title_es' => 'Suite Enzian',
		'intro_de' => 'Elegante Suite mit separatem Wohnbereich und Whirlpool-Badewanne.',
		'intro_it' => 'Elegante suite con zona soggiorno separata e vasca idromassaggio.',
		'intro_en' => 'Elegant suite with separate living area and whirlpool bath.',
		'intro_fr' => 'Suite élégante avec espace de vie séparé et baignoire à remous.',
		'intro_es' => 'Elegante suite con zona de estar separada y bañera de hidromasaje.',
		'desc_de' => 'Die Suite Enzian vereint alpinen Stil mit modernem Luxus. Der separate Wohnbereich mit Sofa und Leseecke bietet viel Platz zum Entspannen. Das Bad mit Whirlpool-Badewanne rundet das Wohlfühlerlebnis ab. Ausstattung: Bad mit Whirlpool/WC, Föhn, Safe, Sat-TV, Minibar, Nespresso, WLAN.',
		'desc_it' => 'La Suite Enzian unisce lo stile alpino con il lusso moderno. La zona soggiorno separata con divano e angolo lettura offre ampio spazio per rilassarsi. Il bagno con vasca idromassaggio completa l\'esperienza di benessere. Dotazioni: bagno con idromassaggio/WC, asciugacapelli, cassaforte, TV sat, minibar, Nespresso, WiFi.',
		'desc_en' => 'The Enzian Suite combines Alpine style with modern luxury. The separate living area with sofa and reading corner offers plenty of space to relax. The bathroom with whirlpool bath rounds off the wellness experience. Amenities: bathroom with whirlpool/WC, hairdryer, safe, satellite TV, minibar, Nespresso, WiFi.',
		'desc_fr' => 'La Suite Enzian associe le style alpin au luxe moderne. L\'espace de vie séparé avec canapé et coin lecture offre beaucoup de place pour se détendre. La salle de bain avec baignoire à remous complète l\'expérience de bien-être. Équipements: salle de bain avec jacuzzi/WC, sèche-cheveux, coffre-fort, TV satellite, minibar, Nespresso, WiFi.',
		'desc_es' => 'La Suite Enzian combina el estilo alpino con el lujo moderno. La zona de estar separada con sofá y rincón de lectura ofrece mucho espacio para relajarse. El baño con bañera de hidromasaje completa la experiencia de bienestar. Equipamiento: baño con hidromasaje/WC, secador, caja fuerte, TV satélite, minibar, Nespresso, WiFi.',
		'video' => '',
	],
	[
		'name' => 'Bergkristall', 'code' => 'BKR', 'category' => 2,
		'surface' => '55', 'people' => '2-4', 'price_from' => '180',
		'title_de' => 'Suite Bergkristall', 'title_it' => 'Suite Bergkristall', 'title_en' => 'Suite Bergkristall', 'title_fr' => 'Suite Bergkristall', 'title_es' => 'Suite Bergkristall',
		'intro_de' => 'Unsere exklusive Suite mit privater Sauna und Panoramaterrasse.',
		'intro_it' => 'La nostra suite esclusiva con sauna privata e terrazza panoramica.',
		'intro_en' => 'Our exclusive suite with private sauna and panoramic terrace.',
		'intro_fr' => 'Notre suite exclusive avec sauna privé et terrasse panoramique.',
		'intro_es' => 'Nuestra suite exclusiva con sauna privada y terraza panorámica.',
		'desc_de' => 'Die Suite Bergkristall ist unser exklusivstes Zimmer. Mit privater Finnischer Sauna, großer Panoramaterrasse und hochwertigster Ausstattung bietet sie ein einzigartiges Erlebnis. Ausstattung: private Sauna, Panoramaterrasse, Dusche + Badewanne/WC, Föhn, Safe, Smart-TV, Minibar, Nespresso, WLAN.',
		'desc_it' => 'La Suite Bergkristall è la nostra camera più esclusiva. Con sauna finlandese privata, ampia terrazza panoramica e arredi di altissima qualità, offre un\'esperienza unica. Dotazioni: sauna privata, terrazza panoramica, doccia + vasca/WC, asciugacapelli, cassaforte, Smart TV, minibar, Nespresso, WiFi.',
		'desc_en' => 'The Bergkristall Suite is our most exclusive room. With a private Finnish sauna, large panoramic terrace and premium furnishings, it offers a unique experience. Amenities: private sauna, panoramic terrace, shower + bathtub/WC, hairdryer, safe, smart TV, minibar, Nespresso, WiFi.',
		'desc_fr' => 'La Suite Bergkristall est notre chambre la plus exclusive. Avec un sauna finlandais privé, une grande terrasse panoramique et un mobilier haut de gamme, elle offre une expérience unique. Équipements: sauna privé, terrasse panoramique, douche + baignoire/WC, sèche-cheveux, coffre-fort, Smart TV, minibar, Nespresso, WiFi.',
		'desc_es' => 'La Suite Bergkristall es nuestra habitación más exclusiva. Con sauna finlandesa privada, amplia terraza panorámica y mobiliario de primera calidad, ofrece una experiencia única. Equipamiento: sauna privada, terraza panorámica, ducha + bañera/WC, secador, caja fuerte, Smart TV, minibar, Nespresso, WiFi.',
		'video' => '',
	],
	[
		'name' => 'Lavendel', 'code' => 'LAV', 'category' => 3,
		'surface' => '18', 'people' => '1', 'price_from' => '60',
		'title_de' => 'Einzelzimmer Lavendel', 'title_it' => 'Camera Singola Lavendel', 'title_en' => 'Single Room Lavendel', 'title_fr' => 'Chambre Simple Lavendel', 'title_es' => 'Habitación Individual Lavendel',
		'intro_de' => 'Gemütliches Einzelzimmer mit allem Komfort für Alleinreisende.',
		'intro_it' => 'Accogliente camera singola con tutti i comfort per chi viaggia da solo.',
		'intro_en' => 'Cosy single room with all comforts for solo travellers.',
		'intro_fr' => 'Chambre simple confortable avec tout le confort pour les voyageurs seuls.',
		'intro_es' => 'Acogedora habitación individual con todas las comodidades para viajeros solos.',
		'desc_de' => 'Das Einzelzimmer Lavendel bietet trotz kompakter Größe allen Komfort für einen angenehmen Aufenthalt. Die warmen Farben und natürlichen Materialien schaffen eine behagliche Atmosphäre. Ausstattung: Dusche/WC, Föhn, Safe, Sat-TV, WLAN.',
		'desc_it' => 'La camera singola Lavendel offre, nonostante le dimensioni compatte, tutti i comfort per un piacevole soggiorno. I colori caldi e i materiali naturali creano un\'atmosfera accogliente. Dotazioni: doccia/WC, asciugacapelli, cassaforte, TV sat, WiFi.',
		'desc_en' => 'The Lavendel single room offers all comforts for a pleasant stay despite its compact size. Warm colours and natural materials create a cosy atmosphere. Amenities: shower/WC, hairdryer, safe, satellite TV, WiFi.',
		'desc_fr' => 'La chambre simple Lavendel offre tout le confort pour un séjour agréable malgré sa taille compacte. Les couleurs chaudes et les matériaux naturels créent une atmosphère accueillante. Équipements: douche/WC, sèche-cheveux, coffre-fort, TV satellite, WiFi.',
		'desc_es' => 'La habitación individual Lavendel ofrece todas las comodidades para una estancia agradable a pesar de su tamaño compacto. Los colores cálidos y los materiales naturales crean un ambiente acogedor. Equipamiento: ducha/WC, secador, caja fuerte, TV satélite, WiFi.',
		'video' => '',
	],
	[
		'name' => 'Sonnblick', 'code' => 'SON', 'category' => 4,
		'surface' => '50', 'people' => '2-5', 'price_from' => '130',
		'title_de' => 'Familienzimmer Sonnblick', 'title_it' => 'Camera Famiglia Sonnblick', 'title_en' => 'Family Room Sonnblick', 'title_fr' => 'Chambre Familiale Sonnblick', 'title_es' => 'Habitación Familiar Sonnblick',
		'intro_de' => 'Großzügiges Familienzimmer mit Kinderschlafbereich und Spielecke.',
		'intro_it' => 'Spaziosa camera famiglia con zona notte bambini e angolo giochi.',
		'intro_en' => 'Spacious family room with children\'s sleeping area and play corner.',
		'intro_fr' => 'Grande chambre familiale avec espace de couchage pour enfants et coin jeux.',
		'intro_es' => 'Amplia habitación familiar con zona de dormir para niños y rincón de juegos.',
		'desc_de' => 'Das Familienzimmer Sonnblick ist perfekt für Familien mit Kindern. Der separate Kinderschlafbereich mit Stockbett und die Spielecke lassen Kinderherzen höher schlagen. Ausstattung: Dusche/WC, Föhn, Safe, Sat-TV, Minibar, WLAN, Babybett auf Anfrage.',
		'desc_it' => 'La camera famiglia Sonnblick è perfetta per famiglie con bambini. La zona notte separata per bambini con letto a castello e l\'angolo giochi faranno felici i più piccoli. Dotazioni: doccia/WC, asciugacapelli, cassaforte, TV sat, minibar, WiFi, lettino su richiesta.',
		'desc_en' => 'The Sonnblick family room is perfect for families with children. The separate children\'s sleeping area with bunk bed and play corner will delight young hearts. Amenities: shower/WC, hairdryer, safe, satellite TV, minibar, WiFi, cot on request.',
		'desc_fr' => 'La chambre familiale Sonnblick est parfaite pour les familles avec enfants. L\'espace de couchage séparé pour les enfants avec lits superposés et le coin jeux raviront les plus jeunes. Équipements: douche/WC, sèche-cheveux, coffre-fort, TV satellite, minibar, WiFi, lit bébé sur demande.',
		'desc_es' => 'La habitación familiar Sonnblick es perfecta para familias con niños. La zona de dormir separada para niños con litera y el rincón de juegos harán las delicias de los más pequeños. Equipamiento: ducha/WC, secador, caja fuerte, TV satélite, minibar, WiFi, cuna bajo petición.',
		'video' => '',
	],
];

$roomStmt = $pdo->prepare("
	INSERT INTO {$prefix}accommodation_manager_rooms
	(room_name, room_code, room_category, room_surface, room_people, room_price_from,
	 room_title_de, room_title_it, room_title_en, room_title_fr, room_title_es,
	 room_intro_de, room_intro_it, room_intro_en, room_intro_fr, room_intro_es,
	 room_description_de, room_description_it, room_description_en, room_description_fr, room_description_es,
	 room_thumbnail, room_thumbnail_alt_de, room_thumbnail_alt_it, room_thumbnail_alt_en, room_thumbnail_alt_fr, room_thumbnail_alt_es,
	 room_floor_plan, room_floor_plan_alt_de, room_floor_plan_alt_it, room_floor_plan_alt_en, room_floor_plan_alt_fr, room_floor_plan_alt_es,
	 room_gallery, room_video,
	 state, ordering)
	VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 1, ?)
");

foreach ($rooms as $i => $room) {
	$idx = $i + 1;

	// Thumbnail
	$thumbRel = 'images/accommodation_manager/rooms/thumbnails/room_' . $idx . '.jpg';
	$thumbVal = joomlaImagePath($thumbRel, 800, 600);

	// Floor plan
	$fpRel = 'images/accommodation_manager/rooms/floorplans/floorplan_' . $idx . '.jpg';
	$fpVal = joomlaImagePath($fpRel, 600, 400);

	// Gallery (3 images per room)
	$galleryOffset = ($i * 3) + 1;
	$galleryItems  = [];
	for ($g = 0; $g < 3; $g++) {
		$gIdx  = $galleryOffset + $g;
		$gPath = 'images/accommodation_manager/rooms/gallery/gallery_' . $gIdx . '.jpg';
		$galleryItems[] = [
			'image'        => $gPath,
			'image_mobile' => '',
			'alt_de'       => $room['title_de'] . ' - Bild ' . ($g + 1),
			'alt_it'       => $room['title_it'] . ' - Foto ' . ($g + 1),
			'alt_en'       => $room['title_en'] . ' - Photo ' . ($g + 1),
			'alt_fr'       => $room['title_fr'] . ' - Photo ' . ($g + 1),
			'alt_es'       => $room['title_es'] . ' - Foto ' . ($g + 1),
		];
	}
	$galleryJson = json_encode($galleryItems, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

	$roomStmt->execute([
		$room['name'], $room['code'], $room['category'], $room['surface'], $room['people'], $room['price_from'],
		$room['title_de'], $room['title_it'], $room['title_en'], $room['title_fr'], $room['title_es'],
		$room['intro_de'], $room['intro_it'], $room['intro_en'], $room['intro_fr'], $room['intro_es'],
		$room['desc_de'], $room['desc_it'], $room['desc_en'], $room['desc_fr'], $room['desc_es'],
		$thumbVal,
		$room['title_de'], $room['title_it'], $room['title_en'], $room['title_fr'], $room['title_es'],
		$fpVal,
		'Grundriss ' . $room['name'], 'Planimetria ' . $room['name'], 'Floor plan ' . $room['name'], 'Plan ' . $room['name'], 'Plano ' . $room['name'],
		$galleryJson, $room['video'],
		$i + 1,
	]);
}

echo "  " . count($rooms) . " rooms inserted.\n";

// ─── Insert Rate Typologies ────────────────────────────────────
echo "Inserting rate typologies...\n";

$typologies = [
	[
		'title' => 'B&B',
		'de' => 'Übernachtung mit Frühstück', 'it' => 'Pernottamento e colazione', 'en' => 'Bed & Breakfast',
		'fr' => 'Nuit et petit-déjeuner', 'es' => 'Alojamiento y desayuno',
	],
	[
		'title' => 'Half Board',
		'de' => 'Halbpension', 'it' => 'Mezza pensione', 'en' => 'Half Board',
		'fr' => 'Demi-pension', 'es' => 'Media pensión',
	],
];

$typStmt = $pdo->prepare("
	INSERT INTO {$prefix}accommodation_manager_rate_typologies
	(rate_typology_title, rate_typology_de, rate_typology_it, rate_typology_en, rate_typology_fr, rate_typology_es,
	 state, ordering)
	VALUES (?, ?, ?, ?, ?, ?, 1, ?)
");

foreach ($typologies as $i => $typ) {
	$typStmt->execute([
		$typ['title'],
		$typ['de'], $typ['it'], $typ['en'], $typ['fr'], $typ['es'],
		$i + 1,
	]);
}

echo "  " . count($typologies) . " typologies inserted.\n";

// ─── Insert Rate Periods ───────────────────────────────────────
echo "Inserting rate periods...\n";

$periods = [
	['start' => '2026-01-01', 'end' => '2026-01-31',
		'de' => 'Winterpause', 'it' => 'Pausa invernale', 'en' => 'Winter break',
		'fr' => 'Pause hivernale', 'es' => 'Pausa invernal'],
	['start' => '2026-02-01', 'end' => '2026-02-28',
		'de' => 'Fasching', 'it' => 'Carnevale', 'en' => 'Carnival',
		'fr' => 'Carnaval', 'es' => 'Carnaval'],
	['start' => '2026-03-01', 'end' => '2026-03-31',
		'de' => 'Frühling', 'it' => 'Primavera', 'en' => 'Spring',
		'fr' => 'Printemps', 'es' => 'Primavera'],
	['start' => '2026-05-01', 'end' => '2026-06-30',
		'de' => 'Frühsommer', 'it' => 'Inizio estate', 'en' => 'Early summer',
		'fr' => 'Début d\'été', 'es' => 'Principio de verano'],
	['start' => '2026-07-01', 'end' => '2026-08-31',
		'de' => 'Hochsommer', 'it' => 'Alta stagione', 'en' => 'High season',
		'fr' => 'Haute saison', 'es' => 'Temporada alta'],
	['start' => '2026-09-01', 'end' => '2026-10-31',
		'de' => 'Herbst', 'it' => 'Autunno', 'en' => 'Autumn',
		'fr' => 'Automne', 'es' => 'Otoño'],
	['start' => '2026-11-01', 'end' => '2026-12-22',
		'de' => 'Vorweihnachtszeit', 'it' => 'Pre-Natale', 'en' => 'Pre-Christmas',
		'fr' => 'Pré-Noël', 'es' => 'Pre-Navidad'],
	['start' => '2026-12-23', 'end' => '2027-01-06',
		'de' => 'Weihnachten & Neujahr', 'it' => 'Natale e Capodanno', 'en' => 'Christmas & New Year',
		'fr' => 'Noël et Nouvel An', 'es' => 'Navidad y Año Nuevo'],
];

$perStmt = $pdo->prepare("
	INSERT INTO {$prefix}accommodation_manager_rate_periods
	(period_start, period_end, period_title_de, period_title_it, period_title_en, period_title_fr, period_title_es,
	 state, ordering)
	VALUES (?, ?, ?, ?, ?, ?, ?, 1, ?)
");

foreach ($periods as $i => $per) {
	$perStmt->execute([
		$per['start'], $per['end'],
		$per['de'], $per['it'], $per['en'], $per['fr'], $per['es'],
		$i + 1,
	]);
}

echo "  " . count($periods) . " periods inserted.\n";

// ─── Insert Rates ──────────────────────────────────────────────
echo "Inserting rates...\n";

// Base prices per room (B&B), HP adds ~20€
$basePrices = [
	1 => 85,   // Alpenrose
	2 => 95,   // Edelweiss
	3 => 150,  // Enzian
	4 => 180,  // Bergkristall
	5 => 60,   // Lavendel
	6 => 130,  // Sonnblick
];

// Season multiplier per period
$seasonMultipliers = [
	1 => 0.85,  // Winter break (low)
	2 => 1.00,  // Carnival
	3 => 0.90,  // Spring
	4 => 1.00,  // Early summer
	5 => 1.25,  // High season
	6 => 1.00,  // Autumn
	7 => 0.80,  // Pre-Christmas (low)
	8 => 1.40,  // Christmas & New Year (peak)
];

$hpSupplement = 20; // Half board supplement

$rateStmt = $pdo->prepare("
	INSERT INTO {$prefix}accommodation_manager_rates
	(period_id, room_id, typology_id, rate, state, ordering)
	VALUES (?, ?, ?, ?, 1, 0)
");

$rateCount = 0;

foreach ($periods as $pi => $period) {
	$periodId   = $pi + 1;
	$multiplier = $seasonMultipliers[$periodId];

	foreach ($basePrices as $roomId => $basePrice) {
		foreach ($typologies as $ti => $typology) {
			$typologyId = $ti + 1;
			$price      = round($basePrice * $multiplier, 2);

			if ($typologyId === 2) {
				$price += $hpSupplement;
			}

			// Some rates are not available — e.g. single room at Christmas
			// Use NULL if DB supports it, otherwise skip
			$rateValue = $price;
			$skip      = false;
			if ($roomId === 5 && $periodId === 8) {
				$rateValue = null;
				$skip      = true;
			}

			if (!$skip) {
				$rateStmt->execute([$periodId, $roomId, $typologyId, $rateValue]);
				$rateCount++;
			}
		}
	}
}

echo "  $rateCount rates inserted.\n";

// ─── Done ──────────────────────────────────────────────────────
echo "\n";
echo "================================================\n";
echo "Seed complete!\n";
echo "================================================\n";
echo "\n";
echo "  Categories:  " . count($categories) . "\n";
echo "  Rooms:       " . count($rooms) . "\n";
echo "  Typologies:  " . count($typologies) . "\n";
echo "  Periods:     " . count($periods) . "\n";
echo "  Rates:       $rateCount\n";
echo "  Images:      " . (4 + 6 + 6 + 18) . " downloaded\n";
echo "\n";
