<?php
// Database connection
$dbuser = "u535931328_mjr";
$dbpass = "#dP9$433Rta";
$host = "localhost"; 
$dbname = "u535931328_rposystem";

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $dbuser, $dbpass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo json_encode(['status' => 'error', 'message' => 'Database connection failed: ' . $e->getMessage()]);
    exit;
}

// Handling POST request
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $prodName = $_POST['prod_name'] ?? '';
    $prodCode = $_POST['prod_code'] ?? '';
    $prodDesc = $_POST['prod_desc'] ?? '';
    $prodPrice = $_POST['prod_price'] ?? '';
    $prodStock = $_POST['prod_stock'] ?? 0;
    $prodExpiryDate = $_POST['prod_expiry_date'] ?? null;
    $prodImgBase64 = $_POST['prod_img_base64'] ?? '';
    $prodBarcode = $_POST['prod_barcode'] ?? null; // optional
    $updatedBy = $_POST['updated_by'] ?? null; // optional

    // Handle image saving
    $imgPath = '';
    if ($prodImgBase64) {
        $imgData = base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', $prodImgBase64));
        $imgFileName = uniqid('prod_', true) . '.png';
        $imgPath = '../admin/assets/img/products/' . $imgFileName;
        file_put_contents($imgPath, $imgData);
    }

    // Insert product into the "rpos_products" table
    $query = "INSERT INTO rpos_products 
                (prod_id, prod_code, prod_name, prod_img, prod_desc, prod_price, prod_barcode, prod_stock, prod_expiry_date, updated_by) 
              VALUES 
                (:prod_id, :prod_code, :prod_name, :prod_img, :prod_desc, :prod_price, :prod_barcode, :prod_stock, :prod_expiry_date, :updated_by)";

    $stmt = $pdo->prepare($query);

    // Create unique prod_id (could be based on prod_code or random)
    $prodId = uniqid('PID_');

    $stmt->bindParam(':prod_id', $prodId);
    $stmt->bindParam(':prod_code', $prodCode);
    $stmt->bindParam(':prod_name', $prodName);
    $stmt->bindParam(':prod_img', $imgFileName); // only filename is saved
    $stmt->bindParam(':prod_desc', $prodDesc);
    $stmt->bindParam(':prod_price', $prodPrice);
    $stmt->bindParam(':prod_barcode', $prodBarcode);
    $stmt->bindParam(':prod_stock', $prodStock);
    $stmt->bindParam(':prod_expiry_date', $prodExpiryDate);
    $stmt->bindParam(':updated_by', $updatedBy);

    // Execute and return response
    try {
        $stmt->execute();
        echo json_encode(['status' => 'success', 'message' => 'Product saved successfully.']);
    } catch (PDOException $e) {
        echo json_encode(['status' => 'error', 'message' => 'Database insert failed: ' . $e->getMessage()]);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request']);
}
?>
