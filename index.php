<?php
// find your transaction json here: https://account.battle.net/api/transactions?regionId=2
$j = json_decode(file_get_contents("transactions.json"));
$currency = "€";

// purchases done in other stores, Google Play, Amazon Coins etc require parsing because the total sum is not displayed on Battle.NET

$AIData = [ // regex 
    "40(.*)packs" => 49.99,
    "15(.*)packs" => 19.99,
    "2(.*)packs" => 2.99,
    '(.*)Tavern Pass(.*)' => 9.99,
    '(.*)New Hero(.*)' => 9.99,          
    
    "Onyxia's Lair Mini-Set" => 14.99,
    "Onyxia's Lair Golden Mini-Set" => 69.99,
    'Voyage to the Sunken City Bundle \(Rank 1\)' => 19.99,
    'Voyage to the Sunken City Bundle \(Rank 2\)' => 29.99,
    'United in Stormwind Mega Bundle \(Pre-Purchase\)' => 79.99,
    'Wailing Caverns Mini-Set' => 14.99,
    'Golden Stormwind Bundle' => 39.99,
    'United in Stormwind Bundle' => 19.99,
    'Eternal Flame Bundle' => 19.99,
    'United in Stormwind \(Pre-Purchase\)' => 49.99,
    'Tavern Special' => 29.99,
    'Bob(.*)Bargain' => 29.99,
    'Welcome Back Bundle \(Rank 1\)' => 19.99,
    'Welcome Back Bundle \(Rank 2\)' => 29.99,
    'Blackrock Mountain' => 24.99,
    'League of Explorers' => 19.99,
    'Alleria Windrunner Hero Set' => 9.99,
];
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BNET Transactions</title>
    <style>
        /*! normalize.css v8.0.1 | MIT License | github.com/necolas/normalize.css */html{line-height:1.15;-webkit-text-size-adjust:100%}body{margin:0}main{display:block}h1{font-size:2em;margin:.67em 0}hr{box-sizing:content-box;height:0;overflow:visible}pre{font-family:monospace,monospace;font-size:1em}a{background-color:transparent}abbr[title]{border-bottom:none;text-decoration:underline;text-decoration:underline dotted}b,strong{font-weight:bolder}code,kbd,samp{font-family:monospace,monospace;font-size:1em}small{font-size:80%}sub,sup{font-size:75%;line-height:0;position:relative;vertical-align:baseline}sub{bottom:-.25em}sup{top:-.5em}img{border-style:none}button,input,optgroup,select,textarea{font-family:inherit;font-size:100%;line-height:1.15;margin:0}button,input{overflow:visible}button,select{text-transform:none}[type=button],[type=reset],[type=submit],button{-webkit-appearance:button}[type=button]::-moz-focus-inner,[type=reset]::-moz-focus-inner,[type=submit]::-moz-focus-inner,button::-moz-focus-inner{border-style:none;padding:0}[type=button]:-moz-focusring,[type=reset]:-moz-focusring,[type=submit]:-moz-focusring,button:-moz-focusring{outline:1px dotted ButtonText}fieldset{padding:.35em .75em .625em}legend{box-sizing:border-box;color:inherit;display:table;max-width:100%;padding:0;white-space:normal}progress{vertical-align:baseline}textarea{overflow:auto}[type=checkbox],[type=radio]{box-sizing:border-box;padding:0}[type=number]::-webkit-inner-spin-button,[type=number]::-webkit-outer-spin-button{height:auto}[type=search]{-webkit-appearance:textfield;outline-offset:-2px}[type=search]::-webkit-search-decoration{-webkit-appearance:none}::-webkit-file-upload-button{-webkit-appearance:button;font:inherit}details{display:block}summary{display:list-item}template{display:none}[hidden]{display:none}
    </style>
</head>
<body>
    <div id="wrp">

        <?php

        $total = 0;
        $Buffer = "";
        foreach ($j->purchases as $p) {

            $Buffer .= sprintf("<div class='bx' style='%s'>", "border:1px solid lightgrey;border-radius:10px;padding:10px;margin-bottom:10px;");
            $Buffer .= sprintf("<h3>%s</h3><p>%s <small style='float:right'>%s</small></p>", $p->productTitle, ($p->formattedTotal), $p->globalOrderId);

            // Calc from other stores, e.g Google Play, Amazon Coins
            $AI = $p->status;
            if ($p->total == 0) {
                $AI = "!! MISSING INFO !!";
                $_t = $p->productTitle;
                foreach ($AIData as $_aid => $v) {
                    if (preg_match_all("/" . $_aid . "/i", $_t)) {
                        $clean = str_replace("(.*)", " ", $_aid);
                        $clean = str_replace("\\", "", $clean);
                        $AI = $clean . ": " . $currency . $v;
                        $total += $v;
                    }
                }
            } else $AI = "";

            $Buffer .= sprintf("<p style='color:" . ($AI == '!! MISSING INFO !!' ? 'red' : 'darkgreen') . ";'><sup></sup> %s</p>", $AI);

            $Buffer .= "</div>";
            $total += $p->total;
        }
        ?>

        <h2>Found <?= $currency . $total; ?> worth of purchases</h2>

        <?= $Buffer; ?>
    </div>
</body>
</html>