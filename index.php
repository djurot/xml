<?php

print '

<!DOCTYPE html>
<html lang="hr">

<head>
    <title>Pretraživanje dionica</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.min.js"></script>
    <style>
        p {margin: 0.3em}
        .green-background {background-color: #4CAF50; color: white; padding: 3px 6px; border-radius: 4px;}
        .red-background {background-color: #f44336; color: white; padding: 3px 6px; border-radius: 4px;}
        .back-button {
            margin-top: 20px;
            display: block;
        }
        .container {
            max-width: 600px;
            padding: 15px;
        }
    </style>
</head>

<body>

    <div class="container">';
    
    if (!isset($_POST['action']) || $_POST['action'] == '') { $_POST['action'] = FALSE; }
    
    if ($_POST['action'] == FALSE) {
        print '
          <h1 style="text-align:center;">Pretraživanje dionica</h1>
          <form class="form-horizontal" action="" name="stocksearch" method="POST">
            <div class="form-group">
              <label class="control-label col-sm-2" for="symbol">Simbol dionice:</label>
              <div class="col-sm-10">
                <input type="text" class="form-control" id="symbol" placeholder="Unesite simbol dionice" name="symbol" required>
              </div>
            </div>
            <input type="hidden" name="action" value="TRUE">
            <div class="form-group">        
              <div class="col-sm-offset-2 col-sm-10">
                <button type="submit" class="btn btn-default">Pretraži</button>
              </div>
            </div>
          </form>';
    } 
    
    else if ($_POST['action'] == TRUE) {
        print '
        <h1>Rezultati pretraživanja</h1>';
        
        $key = 'WN5K5K8SLG6KNER5';
        $symbol = urlencode($_POST['symbol']);
        $url = 'https://www.alphavantage.co/query?function=GLOBAL_QUOTE&symbol='.$symbol.'&apikey='.$key;
        $json = file_get_contents($url);
        $_data = json_decode($json,true);
        
        if (isset($_data['Global Quote']) && !empty($_data['Global Quote'])) {
            $quote = $_data['Global Quote'];
            $open = floatval($quote['02. open'] ?? 0);
            $previousClose = floatval($quote['08. previous close'] ?? 0);
            $backgroundClass = $open > $previousClose ? 'green-background' : ($open < $previousClose ? 'red-background' : '');

            $latestTradingDay = isset($quote['07. latest trading day']) ? new DateTime($quote['07. latest trading day']) : null;
            $formattedDate = $latestTradingDay ? $latestTradingDay->format('d.m.Y') : 'N/A';

            print '
            <div style="margin-top:20px;">
                <p><strong>Simbol:</strong> ' . ($quote['01. symbol'] ?? 'N/A') . '</p>
                <p><strong>Otvorena:</strong> <span class="' . $backgroundClass . '">' . number_format($open, 2, ',', '.') . ' USD</span></p>
                <p><strong>Najviša cijena:</strong> ' . number_format(floatval($quote['03. high'] ?? 0), 2, ',', '.') . ' USD</p>
                <p><strong>Najniža cijena:</strong> ' . number_format(floatval($quote['04. low'] ?? 0), 2, ',', '.') . ' USD</p>
                <p><strong>Zadnja cijena:</strong> ' . number_format(floatval($quote['05. price'] ?? 0), 2, ',', '.') . ' USD</p>
                <p><strong>Volumen:</strong> ' . number_format(floatval($quote['06. volume'] ?? 0), 0, ',', '.') . '</p>
                <p><strong>Zadnje vrijeme trgovanja:</strong> ' . $formattedDate . '</p>
                <p><strong>Prethodno zatvaranje:</strong> ' . number_format(floatval($quote['08. previous close'] ?? 0), 2, ',', '.') . ' USD</p>
                <p><strong>Promjena:</strong> ' . number_format(floatval($quote['09. change'] ?? 0), 2, ',', '.') . ' USD</p>
                <p><strong>Promjena u postotcima:</strong> ' . str_replace('.', ',', ($quote['10. change percent'] ?? 'N/A')) . '</p>
            </div>';
        }
        else {
            echo '<p>Nešto je pošlo po zlu ili nema podataka za uneseni simbol.</p>';
        }
        echo '<button class="btn btn-primary back-button" onclick="window.location.href=\'index.php\'">Nazad</button>';
    }

    print '

    </div>

</body>

</html>';

?>
