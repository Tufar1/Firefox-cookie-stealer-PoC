<?php
// index.php — jednoduchý upload: stejné jméno, když existuje přidá _1, _2, ...

//Prompt: Ahoj chate, vygeneruj prosím jednoduchý php index, který umožní upload souborů. Stejně jakobych uploadoval fotky, jen bude brát všechny možné přípony souborů. Klasika post request a kdyby se to poslaný jmenovalo stejně jako to co tam je, tak tam dej třeba číslo. Děkuju

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    // mini test stránka
    header('Content-Type: text/html; charset=utf-8');
    echo '<form method="post" enctype="multipart/form-data">
            <input type="file" name="file">
            <button type="submit">Upload</button>
          </form>';
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo "POST only\n";
    exit;
}

if (!isset($_FILES['file']) || $_FILES['file']['error'] !== UPLOAD_ERR_OK) {
    http_response_code(400);
    echo "Upload error\n";
    exit;
}

$uploadDir = __DIR__ . '/uploads';
if (!is_dir($uploadDir)) {
    mkdir($uploadDir, 0777, true);
}

$originalName = basename($_FILES['file']['name']);
$info = pathinfo($originalName);
$name = $info['filename'] ?? 'file';
$ext  = isset($info['extension']) && $info['extension'] !== '' ? '.' . $info['extension'] : '';

$finalName = $name . $ext;
$counter = 1;
while (file_exists($uploadDir . '/' . $finalName)) {
    $finalName = $name . '_' . $counter . $ext;
    $counter++;
}

$targetPath = $uploadDir . '/' . $finalName;

if (!move_uploaded_file($_FILES['file']['tmp_name'], $targetPath)) {
    http_response_code(500);
    echo "Save failed\n";
    exit;
}

echo "OK saved as $finalName\n";