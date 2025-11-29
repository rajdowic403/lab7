<?php

$komunikat = "";

$storage_account_name = "lab7-server"; 
$uploads_container = "uploads"; 

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_FILES["plik"])) {
    $plik = $_FILES["plik"];

    if ($plik["error"] !== UPLOAD_ERR_OK) {
        $komunikat = "<div class='error-box'>Błąd: Nie udało się przesłać pliku.</div>";
    } else {
        $nazwa_pliku = basename($plik["name"]);
        $data_przeslania = date("Y-m-d H:i:s");
        
        
        $blob_url = "https://{$storage_account_name}.blob.core.windows.net/{$uploads_container}/{$nazwa_pliku}"; 
        
       
        $metadata_document = [
            "id" => uniqid(),
            "filename" => $nazwa_pliku, 
            "content_type" => $plik["type"],
            "size_kb" => round($plik["size"] / 1024, 2),
            "blob_location" => $blob_url,
            "uploaded_at" => $data_przeslania
        ];
        
        
        $komunikat = "
            <div class='success-box'>
                <p>Plik '{$nazwa_pliku}' odebrano.</p>
                <p><strong>Krok 1 (BLOB):</strong> Plik musi być ręcznie wgrany do Azure Storage: <code>{$blob_url}</code>.</p>
                <p><strong>Krok 2 (NoSQL):</strong> Poniższy dokument JSON musi być ręcznie zapisany w Cosmos DB:</p>
                <pre>" . json_encode($metadata_document, JSON_PRETTY_PRINT) . "</pre>
            </div>
        ";
    }
}
?>
<!DOCTYPE html>
<html lang="pl">
<head>
    <title>Zadanie 7</title>
    <style> body {
            font-family: Arial, sans-serif;
            margin: 40px;
            background-color: #f4f4f9;
            color: #333;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
            background: #fff;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
        h1 {
            color: #0078d4; /* Kolor Azure Blue */
            text-align: center;
        }
        form {
            margin-top: 20px;
            padding: 20px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }
        label {
            display: block;
            margin-bottom: 8px;
            font-weight: bold;
        }
        input[type="text"] {
            width: 90%;
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid #ccc;
            border-radius: 4px;
        }
        input[type="submit"] {
            background-color: #0078d4;
            color: white;
            padding: 10px 15px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
        }
        input[type="submit"]:hover {
            background-color: #005a9e;
        }
        .success-box {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 4px;
        } </style>
</head>
<body>
    <div class="container">
        <h1>Zadanie 7</h1>
        <?php echo $komunikat; ?>

        <form method="post" enctype="multipart/form-data" action="index.php">
            <label for="plik">Wybierz plik:</label><br>
            <input type="file" id="plik" name="plik" required><br><br>
            <input type="submit" value="Prześlij i Generuj Metadane">
        </form>
    </div>
</body>
</html>