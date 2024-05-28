
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet"  href="p2formstyles.css">
    <title>Thanks for your order!</title>
    <style>
        @import url(http://fonts.googleapis.com/css?family=Carrois+Gothic);
        body {
            border: 3px solid #000;
            margin:10px auto;
	        width:700px;
	        font-family: 'Carrois Gothic';
	        border-radius: 10px
            
        }
        h1, h2 {
            color: #333;
            padding:2px;
        }
        h1
{
	font-size:22px;

}
        table {
           
            font-size:14px;
	        width:580px;
	         margin:0px auto 1em auto;
	         border-radius: 10px;
        }
        td
{
	border: 1px solid #000;
	padding: 2px;
	margin: 3px;
}
.table1{
    border: 3px solid #000;
}
.table2{
    border: 3px solid #000;
 
}
        table, th {
            border: 1px solid #000;
        }
        th, td {
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: white;
        }
         
        .error {
            color: red;
        }
        #easteregg {
            position: fixed;
            bottom: 20px;
            right: 20px;
            width: 100px;
            height: 100px;
            background-color: #ffcc00;
            border-radius: 50%;
            display: none;
            cursor: pointer;
            z-index: 9999;
        }
    </style>
</head>
<body>
    <?php
    function display_errors($errors) {
        echo "<h2 class='error'>Form could not be processed due to the following errors:</h2><ul class='error'>";
        foreach ($errors as $error) {
            echo "<li>$error</li>";
        }
        echo "</ul>";
        echo '<a href="p2form.html">Go back to the form</a>';
    }

    $errors = [];

    $email = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);
    if (!$email) {
        $errors[] = "Invalid email address.";
    }

    $postal = filter_input(INPUT_POST, 'postal', FILTER_VALIDATE_REGEXP, [
        "options" => ["regexp" => "/^[A-Za-z]\d[A-Za-z][ -]?\d[A-Za-z]\d$/"]
    ]);
    if (!$postal) {
        $errors[] = "Invalid postal code.";
    }

    $cardnumber = filter_input(INPUT_POST, 'cardnumber', FILTER_VALIDATE_INT);
    if (!$cardnumber || strlen((string)$cardnumber) != 10) {
        $errors[] = "Credit card number must be exactly 10 digits.";
    }

    $month = filter_input(INPUT_POST, 'month', FILTER_VALIDATE_INT, [
        "options" => ["min_range" => 1, "max_range" => 12]
    ]);
    if (!$month) {
        $errors[] = "Invalid credit card expiration month.";
    }

    $current_year = date("Y");
    $year = filter_input(INPUT_POST, 'year', FILTER_VALIDATE_INT, [
        "options" => ["min_range" => $current_year, "max_range" => $current_year + 5]
    ]);
    if (!$year) {
        $errors[] = "Invalid credit card expiration year.";
    }

    $cardtype = isset($_POST['cardtype']) ? $_POST['cardtype'] : null;
    if (!$cardtype) {
        $errors[] = "You must choose a card type.";
    }

    $required_fields = ['fullname', 'cardname', 'address', 'city', 'province'];
    foreach ($required_fields as $field) {
        if (empty($_POST[$field])) {
            $errors[] = ucfirst($field) . " is required.";
        }
    }

    $valid_provinces = ['AB', 'BC', 'MB', 'NB', 'NL', 'NS', 'ON', 'PE', 'QC', 'SK', 'NT', 'NU', 'YT'];
    $province = isset($_POST['province']) ? $_POST['province'] : null;
    if (!$province || !in_array($province, $valid_provinces)) {
        $errors[] = "Invalid province selected.";
    }

    $quantities = ['qty1', 'qty2', 'qty3', 'qty4', 'qty5'];
    foreach ($quantities as $qty) {
        if (!empty($_POST[$qty]) && !filter_var($_POST[$qty], FILTER_VALIDATE_INT)) {
            $errors[] = "Invalid quantity for item " . substr($qty, -1) . ".";
        }
    }

    if (!empty($errors)) {
        display_errors($errors);
        exit;
    }

    $items = [
        ['name' => 'iMac', 'price' => 3799.98, 'qty' => $_POST['qty1']],
        ['name' => 'WD HDD', 'price' => 359.98, 'qty' => $_POST['qty2']],
        ['name' => 'Drums', 'price' => 359.97, 'qty' => $_POST['qty3']],
    ];

    $total = 0;
    ?>

    <h1>Thanks for your order <?php echo htmlspecialchars($_POST['fullname']); ?>.</h1>
    <h3>Here's a summary of your order:</h3>

    <table class="table1">
        <tr>
            <th colspan="2">Address Information</th>
        </tr>
        <tr>
            <td>Address:</td>
            <td><?php echo htmlspecialchars($_POST['address']); ?></td>
        </tr>
        <tr>
            <td>City:</td>
            <td><?php echo htmlspecialchars($_POST['city']); ?></td>
        </tr>
        <tr>
            <td>Province:</td>
            <td><?php echo htmlspecialchars($_POST['province']); ?></td>
        </tr>
        <tr>
            <td>Postal Code:</td>
            <td><?php echo htmlspecialchars($_POST['postal']); ?></td>
        </tr>
        <tr>
            <td>Email:</td>
            <td><?php echo htmlspecialchars($_POST['email']); ?></td>
        </tr>
    </table>

    <table class="table2">
    <tr>
            <th colspan="3">order Information</th>
        </tr>
        <tr>
            <th>Quantity</th>
            <th>Description</th>
            <th>Cost</th>
        </tr>
        <?php
        foreach ($items as $item) {
            if (!empty($item['qty']) && $item['qty'] > 0) {
                $item_total = $item['price'] * $item['qty'];
                $total += $item_total;
                echo "<tr>";
                echo "<td>{$item['qty']}</td>";
                echo "<td>{$item['name']}</td>";
                echo "<td>\$" . number_format($item['price'], 2) . "</td>";
                echo "</tr>";
            }
        }
        ?>
        <tr>
            <td colspan="2"><strong>Totals</strong></td>
            <td><strong>$<?php echo number_format($total, 2); ?></strong></td>
        </tr>
    </table>

    <!-- Easter Egg hidden element -->
    <div id="easteregg">🎉 You found the Easter egg! 🎉</div>

    <!-- JavaScript to reveal the Easter egg -->
    <script>
        function revealEasterEgg() {
            var easteregg = document.getElementById('easteregg');
            easteregg.style.display = 'block';
        }

        document.addEventListener('keydown', function(event) {
            if (event.ctrlKey && event.altKey && event.key === 'e') {
                revealEasterEgg();
            }
        });
    ?>
    </script>
    
</body>
</html>
