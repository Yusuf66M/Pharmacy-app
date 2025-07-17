<?php
session_start();

// Predefined drug inventory (in a real app, this would come from a database)
if (!isset($_SESSION['inventory'])) {
    $_SESSION['inventory'] = [
        ['id' => 1, 'name' => 'Paracetamol', 'price' => 1.50, 'quantity' => 100],
        ['id' => 2, 'name' => 'Ibuprofen', 'price' => 2.00, 'quantity' => 80],
        ['id' => 3, 'name' => 'Amoxicillin', 'price' => 5.00, 'quantity' => 50],
    ];
}

$message = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $drugId = (int)$_POST['drug_id'];
    $sellQty = (int)$_POST['quantity'];
    foreach ($_SESSION['inventory'] as &$drug) {
        if ($drug['id'] === $drugId) {
            if ($sellQty > 0 && $sellQty <= $drug['quantity']) {
                $drug['quantity'] -= $sellQty;
                $message = "Sold $sellQty of {$drug['name']}.";
            } else {
                $message = "Invalid quantity for {$drug['name']}.";
            }
            break;
        }
    }
    unset($drug); // break reference
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Pharmacy Sales Application</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
    <h1>Pharmacy Sales Application</h1>
    <?php if ($message): ?>
        <div class="message"><?= htmlspecialchars($message) ?></div>
    <?php endif; ?>
    <table>
        <thead>
            <tr>
                <th>Drug Name</th>
                <th>Price ($)</th>
                <th>Available Qty</th>
                <th>Sell</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($_SESSION['inventory'] as $drug): ?>
            <tr>
                <td><?= htmlspecialchars($drug['name']) ?></td>
                <td><?= number_format($drug['price'], 2) ?></td>
                <td><?= $drug['quantity'] ?></td>
                <td>
                    <?php if ($drug['quantity'] > 0): ?>
                    <form method="post" class="sell-form">
                        <input type="hidden" name="drug_id" value="<?= $drug['id'] ?>">
                        <input type="number" name="quantity" min="1" max="<?= $drug['quantity'] ?>" required>
                        <button type="submit">Sell</button>
                    </form>
                    <?php else: ?>
                        <span class="out-of-stock">Out of stock</span>
                    <?php endif; ?>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    </div>
</body>
</html>