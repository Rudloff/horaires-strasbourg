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
<html>
    <head>
<title>Horaires de la Communauté urbaine de Strasbourg</title>
<meta charset="UTF-8" />
<link rel="stylesheet"
href="jquery.mobile-1.3.2/jquery.mobile.structure-1.3.2.min.css" />
<link href='https://fonts.googleapis.com/css?family=The+Girl+Next+Door'
    rel='stylesheet' type='text/css'>
<link
    href='https://fonts.googleapis.com/css?family=Source+Sans+Pro:200,300,400,600,700,900'
    rel='stylesheet' type='text/css'>
<link href='dist/main.css' rel='stylesheet' type='text/css'>
<script src="jquery-1.9.1.min.js"></script>
<script async
src="jquery.mobile-1.3.2/jquery.mobile-1.3.2.min.js"></script>
<meta name="viewport" content="width=device-width, initial-scale=1" />
<meta name="description"
    content="Horaires des services de la Communauté urbaine de Strasbourg" />
<link rel="icon" href="favicon_32.png" />
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
$csvFile = 'data/export_des_horaires.csv';
$lastUpdate = json_decode(file_get_contents($timestampFile));
if (date('Y-m-d', strtotime($lastUpdate->date)) != date('Y-m-d')) {
    file_put_contents(
        $csvFile, file_get_contents(
            'http://media.strasbourg.eu/alfresco/d/d/workspace/SpacesStore/'.
            'eb8550eb-a479-4037-9533-e06977765f9a/export_des_horaires.csv'
        )
    );
    file_put_contents($timestampFile, json_encode(new DateTime()));
}
setlocale(LC_TIME, 'fr_FR.UTF-8', 'fr_FR', 'fr');
$handle = fopen($csvFile, 'r');
while ($line = fgetcsv($handle, null, '|')) {
    $error = false;
    if (!isset($headers)) {
        $headers = $line;
    } else {
        echo '<li><a href="#', urlencode($line[1]),
        '" data-rel="dialog">', $line[1], '</a></li>';
        $hours[$line[1]] = '
        <table data-role="table" class="ui-responsive">
            <thead>
    <tr>
      <th>Jour</th>
      <th>Horaires</th>
    </tr>
  </thead>
  <tbody>';
        $date = new DateTime();
        for ($i=2; $i<=8; $i++) {
            if (empty($line[$i])) {
                $hours[$line[1]] = '
                <p>Nous sommes désolés mais la Communauté urbaine de Strasbourg
                ne nous fournit pas encore les horaires de ce service.</p>';
                $error=true;
                break;
            }
            $hours[$line[1]] .= '<tr><th>'.strftime(
                '%A %e %B', $date->getTimestamp()
            ).'</th><td>'.str_replace(
                '*', '', str_replace(';', '<br/>', $line[$i])
            ).'</td></tr>';
            $date->add(new DateInterval('P1D'));
        }
        if (!$error) {
            $hours[$line[1]] .= '</tbody></table>';
        }
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
