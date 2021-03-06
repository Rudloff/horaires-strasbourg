<?php
/**
 * Display hours of various services in Strasbourg
 *
 * PHP version 5.4.4
 *
 * @category Open_Data
 * @package  Horaires
 * @author   Pierre Rudloff <contact@rudloff.pro>
 * @license  GPL https://www.gnu.org/copyleft/gpl.html
 * @link     http://rudloff.pro/
 * */
?>
<!Doctype HTML>
<html lang="fr">
    <head>
<title>Horaires de la Communauté urbaine de Strasbourg</title>
<meta charset="UTF-8" />
<link rel="stylesheet"
href="bower_components/jquery-mobile-bower/css/jquery.mobile.structure-1.3.2.min.css" />
<link href='https://fonts.googleapis.com/css?family=The+Girl+Next+Door'
    rel='stylesheet' type='text/css'>
<link
    href='https://fonts.googleapis.com/css?family=Source+Sans+Pro:200,300,400,600,700,900'
    rel='stylesheet' type='text/css'>
<link href='dist/main.css' rel='stylesheet' type='text/css'>
<script src="bower_components/jquery/jquery.min.js"></script>
<script async
src="bower_components/jquery-mobile-bower/js/jquery.mobile-1.3.2.min.js"></script>
<meta name="viewport" content="width=device-width, initial-scale=1" />
<meta name="description"
    content="Horaires des services de la Communauté urbaine de Strasbourg" />
<link rel="icon" href="favicon_32.png" />
<link rel="canonical" href="https://horaires-strasbourg.netlib.re/" />
<meta property="og:url" content="https://horaires-strasbourg.netlib.re/" />
<meta property="og:title"
    content="Horaires de la Communauté urbaine de Strasbourg" />
<meta property="og:description"
    content="Horaires des services de la Communauté urbaine de Strasbourg" />
<meta property="og:image" content="https://horaires-strasbourg.netlib.re/favicon.png" />
</head>
<body>
<div data-role="page">
    <header data-role="header">
        <h1>Horaires de la
            <abbr title="Communauté urbaine de Strasbourg">CUS</abbr>
        </h1>
    </header>
<div data-role="content">
    <ul data-role="listview" data-filter="true"
    data-filter-reveal="true"
    data-filter-placeholder="Entrez le nom d'un service municipal">
<?php
$url = 'http://media.strasbourg.eu/alfresco/d/d/workspace/SpacesStore/'.
    'eb8550eb-a479-4037-9533-e06977765f9a/export_des_horaires.csv';
$timestampFile = 'data/timestamp.json';
$jsonFile = 'data/export.json';
$lastUpdate = json_decode(file_get_contents($timestampFile));
if (date('Y-m-d', strtotime($lastUpdate->date)) != date('Y-m-d')) {
    file_put_contents(
        $jsonFile,
        file_get_contents(
            'https://www.strasbourg.eu/Cus-all-hook/api/jsonws/?cusplaceasset/getJson'
        )
    );
    file_put_contents($timestampFile, json_encode(new DateTime()));
}
setlocale(LC_TIME, 'fr_FR.UTF-8', 'fr_FR', 'fr');
$data = json_decode(file_get_contents($jsonFile));
foreach ($data->list as $line) {
    $error = false;
    echo '<li><a href="#', urlencode($line->nomLieu),
    '" data-rel="dialog">', $line->nomLieu, '</a></li>';
    $hours[$line->nomLieu] = '
    <table data-role="table" class="ui-responsive">
        <thead>
        <tr>
          <th scope="col">Jour</th>
          <th scope="col">Horaires</th>
        </tr>
        </thead>
        <tbody>';
    if (!array_filter((array)$line->horaires->map)) {
        $hours[$line->nomLieu] = '
        <p>Nous sommes désolés mais la Communauté urbaine de Strasbourg
        ne nous fournit pas encore les horaires de ce service.</p>';
        $error=true;
    } else {
        foreach ($line->horaires->map as $date=>$hour) {
            $date = new DateTime($date);

            $hours[$line->nomLieu] .= '<tr><th scope="row">'.strftime(
                '%A %e %B', $date->getTimestamp()
            ).'</th><td>'.str_replace(
                '*', '', str_replace(';', '<br/>', $hour)
            ).'</td></tr>';
        }
        $hours[$line->nomLieu] .= '</tbody></table>';
    }
}
?>
</ul>
</div>
<footer data-role="footer">
    <a data-icon="info" data-shadow="false" data-corners="false"
    target="_blank"
    href="http://www.strasbourg.eu/ma-situation/professionnel/open-data/donnees/culture-patrimoine-open-data/horaires-ouverture-lieux-ouverts-publics-cus">
    Données fournies par la
    <abbr title="Communauté urbaine de Strasbourg">CUS</abbr></a>
    <a target="_blank" href="https://github.com/Rudloff/horaires-strasbourg">
        Code disponible sur GitHub</a>
</footer>
</div>
<?php
foreach ($hours as $name=>$content) {
    echo '<div data-role="dialog" itemscope
        itemtype="http://schema.org/LocalBusiness" id="', urlencode($name), '">
    <div data-role="header" data-theme="d">
        <h1 itemprop="name">', $name, '</h1>
    </div>
    <div data-role="content" itemprop="openingHours">', $content,
    '</div>
    </div>';
}
?>
</body>
</html>
