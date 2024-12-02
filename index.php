<?php

// Show Errors
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Database connection parameters
define('DB_HOST', 'localhost');
define('DB_NAME', 'inventory');
define('DB_USER', 'screenprinting');
define('DB_PASS', 'arch');

try {
    // Connect to the database
    $pdo = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PASS);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Error: Could not connect to the database. " . $e->getMessage());
}

// Handle search and filter
$search = isset($_GET['search']) ? $_GET['search'] : '';
$filterColor = isset($_GET['filterColor']) ? $_GET['filterColor'] : '';
$filterBin = isset($_GET['filterBin']) ? $_GET['filterBin'] : '';



// Handle form submission for editing a product
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['editProduct'])) {
    $infoID = $_POST['infoID'];
    $Pname = $_POST['Pname'];
    $Pidentifier = $_POST['Pidentifier'];
    $Color = $_POST['Color'];
    $Notes = $_POST['Notes'];
    $AutoNum = $_POST['AutoNum'];
    $Bin = $_POST['Bin'];
    $sizes = [
        'YXS' => $_POST['YXS'],
        'YS' => $_POST['YS'],
        'YM' => $_POST['YM'],
        'YL' => $_POST['YL'],
        'YXL' => $_POST['YXL'],
        'XS' => $_POST['XS'],
        'S' => $_POST['S'],
        'M' => $_POST['M'],
        'L' => $_POST['L'],
        'XL' => $_POST['XL'],
        '2XL' => $_POST['2XL'],
        '3XL' => $_POST['3XL'],
    ];

    try {
        // Update Info table
        $stmtInfo = $pdo->prepare("UPDATE Info SET Pname = ?, Pidentifier = ?, Notes = ? WHERE infoID = ?");
        $stmtInfo->execute([$Pname, $Pidentifier, $Notes, $infoID]);

        // Update Stock table
        $stmtStock = $pdo->prepare(
            "UPDATE Stock SET Color = ?, Bin = ?, YXS = ?, YS = ?, YM = ?, YL = ?, YXL = ?, XS = ?, S = ?, M = ?, L = ?, XL = ?, 2XL = ?, 3XL = ? 
             WHERE AutoNum = ?"
        );
        $stmtStock->execute(array_merge([$Color], [$Bin], array_values($sizes), [$AutoNum]));

//        echo "<p>$infoID $Pname<br><p>";
        echo "<p>Product updated successfully!</p>";
    } catch (PDOException $e) {
        echo "<p>Error updating product: " . $e->getMessage() . "</p>";
    }
}



// Handle form submission for adding a new product
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['addProduct'])) {
    $Pname = $_POST['Pname'];
    $Pidentifier = $_POST['Pidentifier'];
    $Color = $_POST['Color'];
    $Notes = $_POST['Notes'];
    $Bin = $_POST['Bin'];
    $sizes = [
        'YXS' => $_POST['YXS'],
        'YS' => $_POST['YS'],
        'YM' => $_POST['YM'],
        'YL' => $_POST['YL'],
        'YXL' => $_POST['YXL'],
        'XS' => $_POST['XS'],
        'S' => $_POST['S'],
        'M' => $_POST['M'],
        'L' => $_POST['L'],
        'XL' => $_POST['XL'],
        '2XL' => $_POST['2XL'],
        '3XL' => $_POST['3XL'],
    ];

    try {
        // Add product to Info table
        $stmtInfo = $pdo->prepare("INSERT INTO Info (Pname, Pidentifier, Notes) VALUES (?, ?, ?)");
        $stmtInfo->execute([$Pname, $Pidentifier, $Notes]);

        // Get the last inserted ID for the Info table
        $infoID = $pdo->lastInsertId();

        // Add the first stock entry (with a bin) to the Stock table
        $stmtStock = $pdo->prepare(
            "INSERT INTO Stock (InfoID, Color, Bin, YXS, YS, YM, YL, YXL, XS, S, M, L, XL, 2XL, 3XL) 
             VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)"
        );
        $stmtStock->execute(array_merge([$infoID, $Color, $Bin], array_values($sizes)));

        echo "<p>New product added successfully!</p>";
    } catch (PDOException $e) {
        echo "<p>Error adding product: " . $e->getMessage() . "</p>";
    }
}

// Handle form submission for adding a new stock entry in a different bin
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['addStock'])) {
    $infoID = $_POST['infoID']; // The infoID of the product
    $Bin = $_POST['Bin'];
    $Color = $_POST['Color'];
    $sizes = [
        'YXS' => $_POST['YXS'],
        'YS' => $_POST['YS'],
        'YM' => $_POST['YM'],
        'YL' => $_POST['YL'],
        'YXL' => $_POST['YXL'],
        'XS' => $_POST['XS'],
        'S' => $_POST['S'],
        'M' => $_POST['M'],
        'L' => $_POST['L'],
        'XL' => $_POST['XL'],
        '2XL' => $_POST['2XL'],
        '3XL' => $_POST['3XL'],
    ];

    try {
        // Add the new stock entry to the Stock table
        $stmtStock = $pdo->prepare(
            "INSERT INTO Stock (InfoID, Color, Bin, YXS, YS, YM, YL, YXL, XS, S, M, L, XL, 2XL, 3XL) 
             VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)"
        );
        $stmtStock->execute(array_merge([$infoID, $Color, $Bin], array_values($sizes)));

        echo "<p>Stock entry added successfully for the product in Bin: $Bin!</p>";
    } catch (PDOException $e) {
        echo "<p>Error adding stock: " . $e->getMessage() . "</p>";
    }
}
// Handle stock deletion
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['deleteStock'])) {
    $AutoNum = $_POST['AutoNum']; // Unique identifier for the stock entry

    try {
        // Delete the stock entry from the Stock table
        $stmtDelete = $pdo->prepare("DELETE FROM Stock WHERE AutoNum = ?");
        $stmtDelete->execute([$AutoNum]);

        echo "<p>Stock entry deleted successfully!</p>";
    } catch (PDOException $e) {
        echo "<p>Error deleting stock: " . $e->getMessage() . "</p>";
    }
}

// Fetch catalog data
$query = "SELECT Info.infoID, Pname, Pidentifier, Notes, AutoNum, Color, Bin, 
                 YXS, YS, YM, YL, YXL, XS, S, M, L, XL, 2XL, 3XL 
          FROM Info 
          LEFT JOIN Stock ON Info.infoID = Stock.infoID 
          WHERE CONCAT(Pname, Pidentifier, Color, Notes) LIKE :search";

if ($filterColor) {
    $query .= " AND Color = :filterColor";
}
if ($filterBin) {
    $query .= " AND Bin = :filterBin";
}
$query .= " ORDER BY Bin";

$stmt = $pdo->prepare($query);
$stmt->bindValue(':search', "%$search%");
if ($filterColor) {
    $stmt->bindValue(':filterColor', $filterColor);
}
if ($filterBin) {
    $stmt->bindValue(':filterBin', $filterBin);
}
$stmt->execute();
$catalog = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch unique colors and bins for filtering
$colors = $pdo->query("SELECT DISTINCT Color FROM Stock")->fetchAll(PDO::FETCH_COLUMN);
$bins = $pdo->query("SELECT DISTINCT Bin FROM Stock")->fetchAll(PDO::FETCH_COLUMN);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inventory Catalog</title>
    <style>
        table {
            border-collapse: collapse;
            width: 100%;
        }
        table, th, td {
            border: 1px solid black;
        }
        th, td {
            padding: 8px;
            text-align: left;
        }
        .form-container {
            margin: 20px 0;
        }
    </style>
</head>
<body>
    <h1>Inventory Catalog</h1>


    <!-- Search and filter -->
    <form method="GET" action="">
        <input type="text" name="search" placeholder="Search..." value="<?= htmlspecialchars($search) ?>">
        <select name="filterColor">
            <option value="">All Colors</option>
            <?php foreach ($colors as $color): ?>
                <option value="<?= htmlspecialchars($color) ?>" <?= $filterColor == $color ? 'selected' : '' ?>>
                    <?= htmlspecialchars($color) ?>
                </option>
            <?php endforeach; ?>
        </select>
        <select name="filterBin">
            <option value="">All Bins</option>
            <?php foreach ($bins as $bin): ?>
                <option value="<?= htmlspecialchars($bin) ?>" <?= $filterBin == $bin ? 'selected' : '' ?>>
                    <?= htmlspecialchars($bin) ?>
                </option>
            <?php endforeach; ?>
        </select>
        <button type="submit">Search</button><br><br>
    </form>

    <!-- Catalog Table -->
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Product Name</th>
                <th>SKU</th>
                <th>Notes/Alt SKU</th>
                <th>Color</th>
                <th>Bin</th>
                <th>YXS</th>
                <th>YS</th>
                <th>YM</th>
                <th>YL</th>
                <th>YXL</th>
                <th>XS</th>
                <th>S</th>
                <th>M</th>
                <th>L</th>
                <th>XL</th>
                <th>2XL</th>
                <th>3XL</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($catalog): ?>
                <?php foreach ($catalog as $item): ?>
                    <tr>
                        <td><?= htmlspecialchars($item['infoID']) ?></td>
                        <td><?= htmlspecialchars($item['Pname']) ?></td>
                        <td><?= htmlspecialchars($item['Pidentifier']) ?></td>
                        <td><?= htmlspecialchars($item['Notes']) ?></td>
                        <td><?= htmlspecialchars($item['Color']) ?></td>
                        <td><?= htmlspecialchars($item['Bin']?? 'No Stock') ?></td>
                        <td style="background-color: #d5d5d5;"><?= htmlspecialchars($item['YXS'] ?? '') ?></td>
                        <td><?= htmlspecialchars($item['YS'] ?? '') ?></td>
                        <td style="background-color: #d5d5d5;"><?= htmlspecialchars($item['YM'] ?? '') ?></td>
                        <td><?= htmlspecialchars($item['YL'] ?? '') ?></td>
                        <td style="background-color: #d5d5d5;"><?= htmlspecialchars($item['YXL'] ?? '') ?></td>
                        <td><?= htmlspecialchars($item['XS'] ?? '') ?></td>
                        <td style="background-color: #d5d5d5;"><?= htmlspecialchars($item['S'] ?? '') ?></td>
                        <td><?= htmlspecialchars($item['M'] ?? '') ?></td>
                        <td style="background-color: #d5d5d5;"><?= htmlspecialchars($item['L'] ?? '') ?></td>
                        <td><?= htmlspecialchars($item['XL'] ?? '') ?></td>
                        <td style="background-color: #d5d5d5;"><?= htmlspecialchars($item['2XL'] ?? '') ?></td>
                        <td><?= htmlspecialchars($item['3XL'] ?? '') ?></td>
<!--                        <td><?= htmlspecialchars($item['AutoNum'] ?? '') ?></td> -->
                        <td>
                            <form method="POST" action="">
                                <input type="hidden" name="infoID" value="<?= htmlspecialchars($item['infoID']) ?>">
                                <input type="hidden" name="AutoNum" value="<?= htmlspecialchars($item['AutoNum']) ?>">
                                <button type="submit" name="edit">Edit</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr><td colspan="18">No products found.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>

    <!-- Below is the editable general information of what's in what bin -->
    <p>Bin 1: Juniors || Bin 2: We're Feelin' Blue || Bin 3: White || Bin 4: Stokes</p>
    <!-- Link to help doc -->
    <p> <a href="http://192.168.1.48/help.php">Help!</a> </p>



    <!-- Add Product Form -->
    <div class="form-container">
        <h2>Add New Product</h2>
        <form method="POST" style="line-height: 1.5" action="">
            <label>Product Name: <input type="text" name="Pname" style="width:300px" required></label><br>
            <label>SKU: <input type="text" name="Pidentifier" required></label><br>
            <label>Notes: <input type="text" name="Notes"></label><br>
            <label>Color: <input type="text" name="Color" required></label><br>
            <label>Bin: <input type="text" name="Bin" style="width:50px" required></label><br>
<table style="width: 800px;border: none;border-collapse: collapse;">
    <tr>
        <td style= "text-align: right;border: none;">
            <label>YXS: <input type="number" name="YXS" value="0" style="width:50px" required></label></td>
        <td style= "text-align: right;border: none;">
            <label>YS: <input type="number" name="YS" value="0" style="width:50px" required></label></td>
        <td style= "text-align: right;border: none;">            
            <label>YM: <input type="number" name="YM" value="0" style="width:50px" required></label></td>
        <td style= "text-align: right;border: none;">
            <label>YL: <input type="number" name="YL" value="0" style="width:50px" required></label></td>
        <td style= "text-align: right;border: none;">
            <label>YXL: <input type="number" name="YXL" value="0" style="width:50px" required></label><br></td>
    </tr>
    <tr>
        <td style= "text-align: right;border: none;">
            <label>XS: <input type="number" name="XS" value="0" style="width:50px" required></label></td>
        <td style= "text-align: right;border: none;">
            <label>S: <input type="number" name="S" value="0" style="width:50px" required></label></td>
        <td style= "text-align: right;border: none;">
            <label>M: <input type="number" name="M" value="0" style="width:50px" required></label></td>
        <td style= "text-align: right;border: none;">
            <label>L: <input type="number" name="L" value="0" style="width:50px" required></label></td>
        <td style= "text-align: right;border: none;">
            <label>XL: <input type="number" name="XL" value="0" style="width:50px" required></label></td>
        <td style= "text-align: right;border: none;">
            <label>2XL: <input type="number" name="2XL" value="0" style="width:50px" required></label></td>
        <td style= "text-align: right;border: none;">
            <label>3XL: <input type="number" name="3XL" value="0" style="width:50px" required></label><br></td>

</table>
            <button type="submit" name="addProduct">Add Product</button>
        </form>
    </div>

    <!-- Add Stock Form -->
    <div class="form-container">
        <h2>Add Stock for Product</h2>
        <form method="POST" style="line-height: 1.5" action="">
            <label>ID: <input type="text" name="infoID" required></label><br>
            <label>Color: <input type="text" name="Color" required></label><br>
            <label>Bin: <input type="text" name="Bin" style="width:50px" required></label><br>

<table style="width: 800px;border: none;border-collapse: collapse;">
    <tr>
        <td style= "text-align: right;border: none;">
            <label>YXS: <input type="number" name="YXS" value="0" style="width:50px" required></label>
        </td>
        <td style= "text-align: right;border: none;">
            <label>YS: <input type="number" name="YS" value="0" style="width:50px" required></label>
            </td>
            <td style= "text-align: right;border: none;">
            <label>YM: <input type="number" name="YM" value="0" style="width:50px" required></label>
            </td>
            <td style= "text-align: right;border: none;">
            <label>YL: <input type="number" name="YL" value="0" style="width:50px" required></label>
            </td>
            <td style= "text-align: right;border: none;">
            <label>YXL: <input type="number" name="YXL" value="0" style="width:50px" required></label><br>
            </td>
    </tr>
    <tr>
            <td style= "text-align: right;border: none;">
            <label>XS: <input type="number" name="XS" value="0" style="width:50px" required></label>
            </td>
            <td style= "text-align: right;border: none;">
            <label>S: <input type="number" name="S" value="0" style="width:50px" required></label>
            </td>
            <td style= "text-align: right;border: none;">
            <label>M: <input type="number" name="M" value="0" style="width:50px" required></label>
            </td>
            <td style= "text-align: right;border: none;">
            <label>L: <input type="number" name="L" value="0" style="width:50px" required></label>
            </td>
            <td style= "text-align: right;border: none;">
            <label>XL: <input type="number" name="XL" value="0" style="width:50px" required></label>
            </td>
            <td style= "text-align: right;border: none;">
            <label>2XL: <input type="number" name="2XL" value="0" style="width:50px" required></label>
            </td>
            <td style= "text-align: right;border: none;">
            <label>3XL: <input type="number" name="3XL" value="0" style="width:50px" required></label><br>
            </td>
        </tr>
</table>
            <button type="submit" name="addStock">Add Stock</button>
        </form>
    </div>


    <!-- Edit Stock Form (populated when 'Edit' is clicked) -->
    <?php if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['edit'])): ?>
        <?php
        $infoID = $_POST['infoID'];
        $AutoNum = $_POST['AutoNum'];
        $stmtEdit = $pdo->prepare("SELECT * FROM Info LEFT JOIN Stock ON Info.infoID = Stock.infoID WHERE Info.infoID = ? AND Stock.AutoNum = ?");
        $stmtEdit->execute([$infoID, $AutoNum]);
        $product = $stmtEdit->fetch(PDO::FETCH_ASSOC);
        ?>
        <div class="form-container">
            <h2>Edit Product</h2>
            <form method="POST" style="line-height: 1.5" action="">
                <input type="hidden" name="infoID" value="<?= htmlspecialchars($product['infoID']) ?>">
                <input type="hidden" name="AutoNum" value="<?= $AutoNum ?>"> <!-- Ensure the AutoNum is passed here -->
                <label>Product Name: <input type="text" name="Pname" value="<?= htmlspecialchars($product['Pname']) ?>" style="width:300px"required></label><br>
                <label>SKU: <input type="text" name="Pidentifier" value="<?= htmlspecialchars($product['Pidentifier']) ?>" required></label><br>
                <label>Notes: <input type="text" name="Notes" value="<?= htmlspecialchars($product['Notes']) ?>" ></label><br>
                <label>Color: <input type="text" name="Color" value="<?= htmlspecialchars($product['Color']) ?>" required></label><br>
                <label>Bin: <input type="text" name="Bin" value="<?= htmlspecialchars($product['Bin']) ?>" required></label><br>

<table style="width: 800px;border: none;border-collapse: collapse;">

    <tr>
    <td style= "text-align: right;border: none;">
    <label>YXS: <input type="number" name="YXS" value="<?= $product['YXS'] ?>" style="width:50px" required></label>
        </td>
        <td style= "text-align: right;border: none;">
                <label>YS: <input type="number" name="YS" value="<?= $product['YS'] ?>" style="width:50px" required></label>
        </td>
        <td style= "text-align: right;border: none;">
                <label>YM: <input type="number" name="YM" value="<?= $product['YM'] ?>" style="width:50px" required></label>
        </td>
        <td style= "text-align: right;border: none;">
                <label>YL: <input type="number" name="YL" value="<?= $product['YL'] ?>" style="width:50px" required></label>
        </td>
        <td style= "text-align: right;border: none;">
                <label>YXL: <input type="number" name="YXL" value="<?= $product['YXL'] ?>" style="width:50px" required></label>
        </td>
    </tr>
    <tr>
        <td style= "text-align: right;border: none;">
            <label>XS: <input type="number" name="XS" value="<?= $product['XS'] ?>" style="width:50px" required></label>
        </td>
        <td style= "text-align: right;border: none;">
                <label>S: <input type="number" name="S" value="<?= $product['S'] ?>" style="width:50px" required></label>
        </td>
        <td style= "text-align: right;border: none;">
                <label>M: <input type="number" name="M" value="<?= $product['M'] ?>" style="width:50px" required></label>
        </td>
        <td style= "text-align: right;border: none;">
                <label>L: <input type="number" name="L" value="<?= $product['L'] ?>" style="width:50px" required></label>
        </td>
        <td style= "text-align: right;border: none;">
                <label>XL: <input type="number" name="XL" value="<?= $product['XL'] ?>" style="width:50px" required></label>
        </td>
        <td style= "text-align: right;border: none;">
                <label>2XL: <input type="number" name="2XL" value="<?= $product['2XL'] ?>" style="width:50px" required></label>
        </td>
        <td style= "text-align: right;border: none;">
                <label>3XL: <input type="number" name="3XL" value="<?= $product['3XL'] ?>" style="width:50px" required></label>
        </td>
    </tr>
</table>
                <button type="submit" name="editProduct">Save Changes</button>
                <button type="submit" name="deleteStock" style="background-color: red; color: white;">Delete Stock</button>
            </form>
        </div>
    <?php endif; ?>

</body>
</html>
