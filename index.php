<?php
error_reporting(0);
ini_set('display_errors', 0);

$iller = [
    'adana' => 'Adana', 'adiyaman' => 'Adıyaman', 'afyonkarahisar' => 'Afyonkarahisar', 
    'agri' => 'Ağrı', 'aksaray' => 'Aksaray', 'amasya' => 'Amasya', 'ankara' => 'Ankara', 
    'antalya' => 'Antalya', 'ardahan' => 'Ardahan', 'artvin' => 'Artvin', 'aydin' => 'Aydın', 
    'balikesir' => 'Balıkesir', 'bartin' => 'Bartın', 'batman' => 'Batman', 'bayburt' => 'Bayburt', 
    'bilecik' => 'Bilecik', 'bingol' => 'Bingöl', 'bitlis' => 'Bitlis', 'bolu' => 'Bolu', 
    'burdur' => 'Burdur', 'bursa' => 'Bursa', 'canakkale' => 'Çanakkale', 'cankiri' => 'Çankırı', 
    'corum' => 'Çorum', 'denizli' => 'Denizli', 'diyarbakir' => 'Diyarbakır', 'duzce' => 'Düzce', 
    'edirne' => 'Edirne', 'elazig' => 'Elazığ', 'erzincan' => 'Erzincan', 'erzurum' => 'Erzurum', 
    'eskisehir' => 'Eskişehir', 'gaziantep' => 'Gaziantep', 'giresun' => 'Giresun', 
    'gumushane' => 'Gümüşhane', 'hakkari' => 'Hakkari', 'hatay' => 'Hatay', 'igdir' => 'Iğdır', 
    'isparta' => 'Isparta', 'istanbul' => 'İstanbul', 'izmir' => 'İzmir', 
    'kahramanmaras' => 'Kahramanmaraş', 'karabuk' => 'Karabük', 'karaman' => 'Karaman', 
    'kars' => 'Kars', 'kastamonu' => 'Kastamonu', 'kayseri' => 'Kayseri', 'kilis' => 'Kilis', 
    'kirikkale' => 'Kırıkkale', 'kirklareli' => 'Kırklareli', 'kirsehir' => 'Kırşehir', 
    'kocaeli' => 'Kocaeli', 'konya' => 'Konya', 'kutahya' => 'Kütahya', 'malatya' => 'Malatya', 
    'manisa' => 'Manisa', 'mardin' => 'Mardin', 'mersin' => 'Mersin', 'mugla' => 'Muğla', 
    'mus' => 'Muş', 'nevsehir' => 'Nevşehir', 'nigde' => 'Niğde', 'ordu' => 'Ordu', 
    'osmaniye' => 'Osmaniye', 'rize' => 'Rize', 'sakarya' => 'Sakarya', 'samsun' => 'Samsun', 
    'sanliurfa' => 'Şanlıurfa', 'siirt' => 'Siirt', 'sinop' => 'Sinop', 'sivas' => 'Sivas', 
    'sirnak' => 'Şırnak', 'tekirdag' => 'Tekirdağ', 'tokat' => 'Tokat', 'trabzon' => 'Trabzon', 
    'tunceli' => 'Tunceli', 'usak' => 'Uşak', 'van' => 'Van', 'yalova' => 'Yalova', 
    'yozgat' => 'Yozgat', 'zonguldak' => 'Zonguldak'
];

$apiEndpoints = [
    'https://api.egemenkeydal.app/havadurumu/hava1.php',
    'https://api.egemenkeydal.app/havadurumu/hava2.php',
    'https://api.egemenkeydal.app/havadurumu/hava3.php',
    'https://api.egemenkeydal.app/havadurumu/hava4.php',
    'https://api.egemenkeydal.app/havadurumu/hava5.php'
];

$selectedIl = isset($_GET['il']) ? strtolower(trim($_GET['il'])) : 'ankara';
if (!array_key_exists($selectedIl, $iller)) {
    $selectedIl = 'ankara';
}

$ilAdi = $iller[$selectedIl];
$currentDate = date('d.m.Y');
$currentYear = date('Y');

function fetchWeatherData($il, $endpoints) {
    $cacheFile = sys_get_temp_dir() . '/weather_' . $il . '.json';
    $cacheTime = 1800;
    
    if (file_exists($cacheFile) && (time() - filemtime($cacheFile)) < $cacheTime) {
        $cached = json_decode(file_get_contents($cacheFile), true);
        if ($cached && isset($cached['data'])) {
            return $cached;
        }
    }
    
    foreach ($endpoints as $index => $endpoint) {
        $url = $endpoint . '?il=' . urlencode($il);
        
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 5);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0');
        
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        
        if ($response && $httpCode == 200) {
            $data = json_decode($response, true);
            if ($data && is_array($data) && count($data) > 0) {
                $result = ['success' => true, 'data' => $data, 'api' => $index + 1];
                file_put_contents($cacheFile, json_encode($result));
                return $result;
            }
        }
    }
    
    return ['success' => false, 'data' => null, 'api' => null];
}

$weatherResult = fetchWeatherData($selectedIl, $apiEndpoints);

$metaDescription = $weatherResult['success'] 
    ? "$ilAdi hava durumu tahmini. Bugün " . round($weatherResult['data'][0]['degree']) . "°C, " . $weatherResult['data'][0]['description'] . ". 7 günlük detaylı hava durumu tahminini görüntüleyin."
    : "$ilAdi için güncel hava durumu tahmini. 7 günlük detaylı hava durumu bilgileri.";

$canonicalUrl = 'https://' . $_SERVER['HTTP_HOST'] . $_SERVER['PHP_SELF'] . '?il=' . $selectedIl;

$schemaData = null;
if ($weatherResult['success']) {
    $todayWeather = $weatherResult['data'][0];
    $schemaData = [
        "@context" => "https://schema.org",
        "@type" => "WeatherForecast",
        "name" => "$ilAdi Hava Durumu",
        "description" => $metaDescription,
        "location" => [
            "@type" => "Place",
            "name" => $ilAdi,
            "address" => ["@type" => "PostalAddress", "addressCountry" => "TR"]
        ],
        "datePublished" => date('c'),
        "temperature" => [
            "@type" => "QuantitativeValue",
            "value" => round($todayWeather['degree']),
            "unitCode" => "CEL"
        ]
    ];
}
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    
    <title><?php echo $ilAdi; ?> Hava Durumu - 7 Günlük Tahmin | <?php echo $currentDate; ?></title>
    <meta name="description" content="<?php echo htmlspecialchars($metaDescription); ?>">
    <meta name="keywords" content="<?php echo $ilAdi; ?> hava durumu, <?php echo $selectedIl; ?> hava tahmini, türkiye hava durumu, meteoroloji">
    <meta name="author" content="Egemen KEYDAL">
    <meta name="robots" content="index, follow">
    
    <meta property="og:title" content="<?php echo $ilAdi; ?> Hava Durumu - 7 Günlük Tahmin">
    <meta property="og:description" content="<?php echo htmlspecialchars($metaDescription); ?>">
    <meta property="og:type" content="website">
    <meta property="og:url" content="<?php echo $canonicalUrl; ?>">
    <script src="https://api.egemenkeydal.app/index/root.js"></script>
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="<?php echo $ilAdi; ?> Hava Durumu">
    
    <link rel="canonical" href="<?php echo $canonicalUrl; ?>">
    <link rel="icon" type="image/png" href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 100 100'><text y='.9em' font-size='90'>🌤️</text></svg>">
    
    <?php if ($schemaData): ?>
    <script type="application/ld+json">
    <?php echo json_encode($schemaData, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE); ?>
    </script>
    <?php endif; ?>
    
    <style>
        :root {
            --primary: #667eea;
            --secondary: #764ba2;
            --accent: #f093fb;
            --text: #2d3436;
            --bg-card: #ffffff;
            --shadow: rgba(0, 0, 0, 0.15);
            --gradient-1: #667eea;
            --gradient-2: #764ba2;
        }
        
        [data-theme="ocean"] {
            --primary: #0ea5e9;
            --secondary: #1e40af;
            --accent: #38bdf8;
            --gradient-1: #0c4a6e;
            --gradient-2: #0ea5e9;
        }
        
        [data-theme="sunset"] {
            --primary: #f97316;
            --secondary: #dc2626;
            --accent: #fbbf24;
            --gradient-1: #7c2d12;
            --gradient-2: #f97316;
        }
        
        [data-theme="forest"] {
            --primary: #10b981;
            --secondary: #059669;
            --accent: #34d399;
            --gradient-1: #064e3b;
            --gradient-2: #10b981;
        }
        
        [data-theme="dark"] {
            --primary: #3b82f6;
            --secondary: #8b5cf6;
            --accent: #a78bfa;
            --text: #e5e7eb;
            --bg-card: #1f2937;
            --shadow: rgba(0, 0, 0, 0.5);
            --gradient-1: #111827;
            --gradient-2: #374151;
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background: linear-gradient(135deg, var(--gradient-1) 0%, var(--gradient-2) 100%);
            min-height: 100vh;
            padding: 20px;
            transition: all 0.3s ease;
        }
        
        .container {
            max-width: 1200px;
            margin: 0 auto;
        }
        
        header {
            text-align: center;
            color: white;
            margin-bottom: 30px;
        }
        
        header h1 {
            font-size: 2.5em;
            margin-bottom: 10px;
            text-shadow: 2px 2px 4px rgba(0,0,0,0.3);
        }
        
        .theme-switcher {
            background: rgba(255,255,255,0.15);
            backdrop-filter: blur(10px);
            padding: 15px;
            border-radius: 20px;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            flex-wrap: wrap;
        }
        
        .theme-btn {
            background: rgba(255,255,255,0.2);
            color: white;
            border: 2px solid transparent;
            padding: 10px 20px;
            border-radius: 25px;
            cursor: pointer;
            transition: all 0.3s;
            font-weight: 600;
            font-size: 14px;
        }
        
        .theme-btn:hover {
            background: rgba(255,255,255,0.3);
            transform: translateY(-2px);
        }
        
        .theme-btn.active {
            background: white;
            color: var(--primary);
        }
        
        .breadcrumb {
            background: rgba(255,255,255,0.15);
            backdrop-filter: blur(10px);
            padding: 12px 20px;
            border-radius: 15px;
            margin-bottom: 20px;
            color: white;
        }
        
        .breadcrumb a {
            color: white;
            text-decoration: none;
            opacity: 0.8;
        }
        
        .breadcrumb a:hover {
            opacity: 1;
        }
        
        .city-selector {
            background: var(--bg-card);
            padding: 20px;
            border-radius: 20px;
            box-shadow: 0 10px 40px var(--shadow);
            margin-bottom: 30px;
        }
        
        .city-selector h2 {
            color: var(--text);
            margin-bottom: 15px;
        }
        
        .city-selector select {
            width: 100%;
            padding: 15px;
            font-size: 16px;
            border: 2px solid var(--primary);
            border-radius: 12px;
            background: var(--bg-card);
            color: var(--text);
            cursor: pointer;
            transition: all 0.3s;
        }
        
        .city-selector select:focus {
            outline: none;
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.3);
        }
        
        .current-weather {
            background: var(--bg-card);
            border-radius: 30px;
            overflow: hidden;
            margin-bottom: 30px;
            box-shadow: 0 20px 60px var(--shadow);
            position: relative;
        }
        
        .current-weather::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 250px;
            background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);
            z-index: 0;
        }
        
        .current-weather-content {
            position: relative;
            z-index: 1;
            padding: 40px;
        }
        
        .current-weather h2 {
            color: white;
            font-size: 1.8em;
            margin-bottom: 30px;
        }
        
        .current-info {
            display: flex;
            align-items: center;
            gap: 40px;
            background: var(--bg-card);
            padding: 40px;
            border-radius: 25px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.15);
        }
        
        @media (max-width: 768px) {
            .current-info {
                flex-direction: column;
                text-align: center;
            }
        }
        
        .current-icon img {
            width: 160px;
            height: 160px;
            filter: drop-shadow(0 8px 20px rgba(0,0,0,0.2));
            animation: float 3s ease-in-out infinite;
        }
        
        @keyframes float {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-15px); }
        }
        
        .current-details {
            flex: 1;
        }
        
        .current-details h3 {
            font-size: 5em;
            color: var(--primary);
            font-weight: 800;
            margin-bottom: 10px;
            line-height: 1;
        }
        
        .current-details p {
            font-size: 2em;
            color: var(--text);
            text-transform: capitalize;
            margin-bottom: 30px;
            font-weight: 600;
        }
        
        .current-stats {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 15px;
        }
        
        @media (max-width: 768px) {
            .current-stats {
                grid-template-columns: repeat(2, 1fr);
            }
        }
        
        .stat-item {
            background: linear-gradient(135deg, var(--primary), var(--secondary));
            padding: 20px;
            border-radius: 15px;
            text-align: center;
            color: white;
            transition: transform 0.3s;
        }
        
        .stat-item:hover {
            transform: translateY(-5px);
        }
        
        .stat-label {
            font-size: 0.9em;
            opacity: 0.9;
            margin-bottom: 8px;
        }
        
        .stat-value {
            font-size: 1.8em;
            font-weight: 700;
        }
        
        .forecast-section h2 {
            color: white;
            font-size: 2em;
            margin-bottom: 25px;
            text-align: center;
            text-shadow: 2px 2px 4px rgba(0,0,0,0.2);
        }
        
        .weather-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }
        
        .weather-card {
            background: var(--bg-card);
            border-radius: 20px;
            padding: 30px;
            box-shadow: 0 10px 30px var(--shadow);
            transition: all 0.3s;
        }
        
        .weather-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 15px 50px var(--shadow);
        }
        
        .weather-date {
            font-size: 0.9em;
            color: var(--primary);
            font-weight: 700;
            margin-bottom: 5px;
        }
        
        .weather-day {
            font-size: 1.4em;
            font-weight: 700;
            color: var(--text);
            margin-bottom: 20px;
        }
        
        .weather-icon {
            text-align: center;
            margin: 20px 0;
        }
        
        .weather-icon img {
            width: 90px;
            height: 90px;
            transition: transform 0.3s;
        }
        
        .weather-card:hover .weather-icon img {
            transform: scale(1.15) rotate(5deg);
        }
        
        .weather-desc {
            text-align: center;
            font-size: 1.1em;
            color: var(--text);
            margin-bottom: 15px;
            text-transform: capitalize;
            opacity: 0.8;
            font-weight: 500;
        }
        
        .weather-temp {
            text-align: center;
            font-size: 2.8em;
            font-weight: 800;
            background: linear-gradient(135deg, var(--primary), var(--secondary));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            margin-bottom: 20px;
        }
        
        .weather-details {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 12px;
        }
        
        .detail-item {
            background: linear-gradient(135deg, rgba(102, 126, 234, 0.1), rgba(118, 75, 162, 0.1));
            padding: 12px;
            border-radius: 12px;
            text-align: center;
            border: 2px solid transparent;
            transition: all 0.3s;
        }
        
        .detail-item:hover {
            border-color: var(--primary);
        }
        
        .detail-label {
            font-size: 0.85em;
            color: var(--text);
            opacity: 0.7;
            margin-bottom: 5px;
        }
        
        .detail-value {
            font-size: 1.2em;
            font-weight: 700;
            color: var(--text);
        }
        
        .info-section {
            background: var(--bg-card);
            padding: 30px;
            border-radius: 20px;
            margin-bottom: 30px;
            box-shadow: 0 10px 30px var(--shadow);
        }
        
        .info-section h2 {
            color: var(--text);
            margin-bottom: 15px;
            font-size: 1.6em;
        }
        
        .info-section p {
            color: var(--text);
            line-height: 1.8;
            opacity: 0.9;
            margin-bottom: 12px;
        }
        
        .cities-list {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(160px, 1fr));
            gap: 12px;
            margin-top: 20px;
        }
        
        .cities-list a {
            background: linear-gradient(135deg, var(--primary), var(--secondary));
            padding: 15px;
            border-radius: 12px;
            text-decoration: none;
            color: white;
            text-align: center;
            font-weight: 600;
            transition: all 0.3s;
        }
        
        .cities-list a:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 20px var(--shadow);
        }
        
        .error-box {
            background: linear-gradient(135deg, #ff4757, #ff6348);
            color: white;
            padding: 40px;
            border-radius: 20px;
            text-align: center;
            box-shadow: 0 10px 40px rgba(255, 71, 87, 0.4);
        }
        
        footer {
            background: rgba(255,255,255,0.15);
            backdrop-filter: blur(10px);
            color: white;
            padding: 30px;
            border-radius: 20px;
            margin-top: 30px;
            text-align: center;
        }
        
        .footer-links {
            display: flex;
            justify-content: center;
            flex-wrap: wrap;
            gap: 20px;
            margin: 20px 0;
        }
        
        .footer-links a {
            color: white;
            text-decoration: none;
            opacity: 0.8;
            transition: opacity 0.3s;
        }
        
        .footer-links a:hover {
            opacity: 1;
        }
    </style>
</head>
<body data-theme="default">
    <div class="container">
        <header>
            <h1>🌤️ <?php echo $ilAdi; ?> Hava Durumu</h1>
            <p>Güncel ve 7 Günlük Hava Durumu Tahmini</p>
        </header>
        
        <div class="theme-switcher">
            <button class="theme-btn active" onclick="setTheme('default')">🌈 Varsayılan</button>
            <button class="theme-btn" onclick="setTheme('ocean')">🌊 Okyanus</button>
            <button class="theme-btn" onclick="setTheme('sunset')">🌅 Gün Batımı</button>
            <button class="theme-btn" onclick="setTheme('forest')">🌲 Orman</button>
            <button class="theme-btn" onclick="setTheme('dark')">🌙 Karanlık</button>
        </div>
        
        <nav class="breadcrumb">
            <a href="?il=ankara">Ana Sayfa</a>
            <span> › </span>
            <span><?php echo $ilAdi; ?></span>
        </nav>
        
        <section class="city-selector">
            <h2>Şehir Seçin</h2>
            <select id="citySelect" onchange="changeCity()">
                <?php foreach ($iller as $slug => $name): ?>
                    <option value="<?php echo $slug; ?>" <?php echo ($slug === $selectedIl) ? 'selected' : ''; ?>>
                        <?php echo $name; ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </section>
        
        <?php if ($weatherResult['success']): ?>
            <?php $todayWeather = $weatherResult['data'][0]; ?>
            
            <article class="current-weather">
                <div class="current-weather-content">
                    <h2>Bugünün Hava Durumu - <?php echo $todayWeather['date']; ?></h2>
                    <div class="current-info">
                        <div class="current-icon">
                            <img src="<?php echo $todayWeather['icon']; ?>" alt="<?php echo $todayWeather['description']; ?>" loading="lazy">
                        </div>
                        <div class="current-details">
                            <h3><?php echo round($todayWeather['degree']); ?>°</h3>
                            <p><?php echo ucfirst($todayWeather['description']); ?></p>
                            <div class="current-stats">
                                <div class="stat-item">
                                    <div class="stat-label">Min</div>
                                    <div class="stat-value"><?php echo round($todayWeather['min']); ?>°</div>
                                </div>
                                <div class="stat-item">
                                    <div class="stat-label">Max</div>
                                    <div class="stat-value"><?php echo round($todayWeather['max']); ?>°</div>
                                </div>
                                <div class="stat-item">
                                    <div class="stat-label">Gece</div>
                                    <div class="stat-value"><?php echo round($todayWeather['night']); ?>°</div>
                                </div>
                                <div class="stat-item">
                                    <div class="stat-label">Nem</div>
                                    <div class="stat-value"><?php echo round($todayWeather['humidity']); ?>%</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </article>
            
            <section class="forecast-section">
                <h2>7 Günlük Hava Tahmini</h2>
                <div class="weather-grid">
                    <?php foreach (array_slice($weatherResult['data'], 1) as $day): ?>
                        <article class="weather-card">
                            <time class="weather-date"><?php echo $day['date']; ?></time>
                            <h3 class="weather-day"><?php echo $day['day']; ?></h3>
                            <div class="weather-icon">
                                <img src="<?php echo $day['icon']; ?>" alt="<?php echo $day['description']; ?>" loading="lazy">
                            </div>
                            <p class="weather-desc"><?php echo ucfirst($day['description']); ?></p>
                            <div class="weather-temp"><?php echo round($day['degree']); ?>°</div>
                            <div class="weather-details">
                                <div class="detail-item">
                                    <div class="detail-label">Min</div>
                                    <div class="detail-value"><?php echo round($day['min']); ?>°</div>
                                </div>
                                <div class="detail-item">
                                    <div class="detail-label">Max</div>
                                    <div class="detail-value"><?php echo round($day['max']); ?>°</div>
                                </div>
                                <div class="detail-item">
                                    <div class="detail-label">Gece</div>
                                    <div class="detail-value"><?php echo round($day['night']); ?>°</div>
                                </div>
                                <div class="detail-item">
                                    <div class="detail-label">Nem</div>
                                    <div class="detail-value"><?php echo round($day['humidity']); ?>%</div>
                                </div>
                            </div>
                        </article>
                    <?php endforeach; ?>
                </div>
            </section>
            
            <section class="info-section">
                <h2><?php echo $ilAdi; ?> Hava Durumu Hakkında</h2>
                <p>
                    <?php echo $ilAdi; ?> için güncel hava durumu ve 7 günlük detaylı hava tahmini. 
                    Bugün <?php echo $ilAdi; ?>'da hava <?php echo round($todayWeather['degree']); ?>°C derece ve <?php echo $todayWeather['description']; ?>. 
                    En yüksek sıcaklık <?php echo round($todayWeather['max']); ?>°C, en düşük sıcaklık <?php echo round($todayWeather['min']); ?>°C olarak ölçüldü. 
                    Nem oranı %<?php echo round($todayWeather['humidity']); ?> seviyesinde.
                </p>
                <p>
                    <?php echo $ilAdi; ?> için saatlik hava durumu, haftalık hava tahmini ve meteoroloji raporlarını 
                    bu sayfadan takip edebilirsiniz. Hava durumu verileri her 30 dakikada bir güncellenmektedir.
                </p>
            </section>
            
            <section class="info-section">
                <h2>Popüler Şehirler</h2>
                <div class="cities-list">
                    <?php 
                    $popularCities = ['istanbul', 'ankara', 'izmir', 'antalya', 'bursa', 'adana', 'gaziantep', 'konya'];
                    foreach ($popularCities as $city): 
                        if ($city !== $selectedIl):
                    ?>
                        <a href="?il=<?php echo $city; ?>" title="<?php echo $iller[$city]; ?> hava durumu">
                            <?php echo $iller[$city]; ?>
                        </a>
                    <?php 
                        endif;
                    endforeach; 
                    ?>
                </div>
            </section>
            
        <?php else: ?>
            <div class="error-box" role="alert">
                <h2>⚠️ Hata</h2>
                <p>Şu anda <?php echo $ilAdi; ?> için hava durumu verilerine ulaşılamıyor.</p>
                <p>Lütfen daha sonra tekrar deneyiniz.</p>
                <p style="margin-top: 15px; font-size: 0.9em;">Tüm API endpoint'leri denendi ancak yanıt alınamadı.</p>
            </div>
        <?php endif; ?>
        
        <footer>
            <p><strong>🌤️ Hava Durumu TR</strong></p>
            <p>Türkiye'nin En Güncel Hava Durumu Platformu</p>
            <?php if ($weatherResult['success']): ?>
                <p style="font-size: 0.9em; opacity: 0.8; margin-top: 10px;">
                    Veri Kaynağı: API <?php echo $weatherResult['api']; ?> | Son Güncelleme: <?php echo date('d.m.Y H:i'); ?> | Geliştirici: <a href="https://www.egemenkeydal.com/" style="color: white;">Egemen KEYDAL</a>
                </p>
            <?php endif; ?>
            <div class="footer-links">
                <a href="?il=istanbul">İstanbul</a>
                <a href="?il=ankara">Ankara</a>
                <a href="?il=izmir">İzmir</a>
                <a href="?il=antalya">Antalya</a>
                <a href="?il=bursa">Bursa</a>
            </div>
            <p style="margin-top: 20px; font-size: 0.85em; opacity: 0.7;">
                © <?php echo $currentYear; ?> Hava Durumu TR. Tüm hakları saklıdır.
            </p>
        </footer>
    </div>
    
    <script>
        function changeCity() {
            const city = document.getElementById('citySelect').value;
            if (city) {
                window.location.href = '?il=' + encodeURIComponent(city);
            }
        }
        
        function setTheme(theme) {
            document.body.setAttribute('data-theme', theme);
            localStorage.setItem('weatherTheme', theme);
            
            document.querySelectorAll('.theme-btn').forEach(btn => {
                btn.classList.remove('active');
            });
            event.target.classList.add('active');
        }
        
        document.addEventListener('DOMContentLoaded', function() {
            const savedTheme = localStorage.getItem('weatherTheme') || 'default';
            document.body.setAttribute('data-theme', savedTheme);
            
            document.querySelectorAll('.theme-btn').forEach(btn => {
                const btnTheme = btn.getAttribute('onclick').match(/'([^']+)'/)[1];
                if (btnTheme === savedTheme) {
                    btn.classList.add('active');
                } else {
                    btn.classList.remove('active');
                }
            });
        });
        
        if ('IntersectionObserver' in window) {
            const images = document.querySelectorAll('img[loading="lazy"]');
            const imageObserver = new IntersectionObserver((entries, observer) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        const img = entry.target;
                        img.src = img.src;
                        observer.unobserve(img);
                    }
                });
            });
            
            images.forEach(img => imageObserver.observe(img));
        }
        
        document.addEventListener('keydown', function(e) {
            if (e.altKey) {
                const themes = ['default', 'ocean', 'sunset', 'forest', 'dark'];
                const num = parseInt(e.key);
                if (num >= 1 && num <= 5) {
                    const theme = themes[num - 1];
                    document.body.setAttribute('data-theme', theme);
                    localStorage.setItem('weatherTheme', theme);
                    
                    document.querySelectorAll('.theme-btn').forEach(btn => {
                        const btnTheme = btn.getAttribute('onclick').match(/'([^']+)'/)[1];
                        btn.classList.toggle('active', btnTheme === theme);
                    });
                }
            }
        });
        
        const fontLink = document.createElement('link');
        fontLink.rel = 'preload';
        fontLink.as = 'font';
        fontLink.crossOrigin = 'anonymous';
        
        const observerOptions = {
            threshold: 0.1,
            rootMargin: '0px 0px -50px 0px'
        };
        
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.style.opacity = '1';
                    entry.target.style.transform = 'translateY(0)';
                }
            });
        }, observerOptions);
        
        document.querySelectorAll('.weather-card, .info-section').forEach(card => {
            card.style.opacity = '0';
            card.style.transform = 'translateY(20px)';
            card.style.transition = 'all 0.6s ease-out';
            observer.observe(card);
        });
    </script>
</body>
</html>