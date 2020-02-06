<?php
/*
* Some script
*/

if(!isset($_GET['date']) || preg_match("/\d\d\/\d\d\/\d\d\d\d/", $_GET['date']) == 0) {
    http_response_code(403);
    echo 'DATE param not found';
    die();
}

$db = [
    'user' => 'root',
    'pass' => 'root'
];

try {
    $pdo = new PDO('mysql:host=localhost;dbname=usd', $db['user'], $db['pass']);
} catch (PDOException $e) {
    echo 'DB Error: ' . $e->getMessage();
    die();
}

$date = $_GET['date'];

//check db

$sql = 'SELECT `value` FROM `values` WHERE `date` = ?';
$stmt = $pdo->prepare($sql);
$stmt->execute([
    DateTime::createFromFormat('d/m/Y', $date)->format('Y-m-d')
    ]);
$val = $stmt->fetch();

if(isset($val['value'])) {
    echo $val['value'];
} else {
    $xml = file_get_contents('http://www.cbr.ru/scripts/XML_daily.asp?date_req='.$date);
    $xml = simplexml_load_string($xml) or die("Error: api format invalid");
    //CharCode = USD
    foreach($xml->Valute as $item) {
        if($item->CharCode == 'USD') {
            echo $item->Value;
            $sql = 'INSERT INTO `values` (`date`,`value`) VALUES (?, ?)';
            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                DateTime::createFromFormat('d/m/Y', $date)->format('Y-m-d'),
                str_replace(',','.',$item->Value),
            ]);
            die();
        }            
    }
}
