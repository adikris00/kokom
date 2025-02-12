<?php
session_start();

error_reporting(E_ALL);
ini_set('display_errors', 1);

$default_scan_dir = __DIR__;
$correct_password_hash = '$2y$10$4d0wceK1avmUAR.5M8v9sOcUrzX4fjUWKBeD3Au.zFfgHJWOoq/nq'; // admin123

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['password'])) {
    if (password_verify($_POST['password'], $correct_password_hash)) {
        $_SESSION['authenticated'] = true;
        header("Location: " . $_SERVER['PHP_SELF']);
        exit;
    } else {
        $error = "Password salah!";
    }
}

if (!isset($_SESSION['authenticated'])) {
    echo '<form method="post" style="text-align:center; margin-top: 20%; font-family:monospace;">
            <input type="password" name="password" required>
            <p style="color:red;">' . ($error ?? '') . '</p>
          </form>';
    exit;
}

$signatures = array(
    'eval(base64_decode(', 'assert(base64_decode(', 'preg_replace("/.*/e",',
    'create_function(', 'array_map("assert",', 'system($_GET[', 'shell_exec("',
    'exec($_REQUEST[', 'passthru($_POST[', 'proc_open(', 'popen(', 'pcntl_exec(',
    'assert($_REQUEST[', 'file_put_contents($_POST[', 'file_get_contents("php://input"',
    'move_uploaded_file($_FILES[', 'chmod($_GET[', 'header("HTTP/1.1 200 OK")',
    'stream_socket_client(', 'fsockopen(', 'mail($_POST[', 'mail($_GET[', 'mb_send_mail(',
    'str_rot13(base64_decode(', 'gzuncompress(base64_decode('
);

function scanFile($file, $signatures) {
    $content = file_get_contents($file);
    foreach ($signatures as $signature) {
        if (stripos($content, $signature) !== false) {
            return $signature;
        }
    }
    return false;
}

function scanDirectory($dir, $signatures) {
    if (!is_dir($dir)) {
        return array('error' => "Direktori tidak ditemukan atau tidak valid!");
    }

    $files = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($dir));
    $suspicious_files = array();
    $totalFiles = 0;

    foreach ($files as $file) {
        if ($file->isFile() && pathinfo($file, PATHINFO_EXTENSION) === 'php') {
            $totalFiles++;
            $found = scanFile($file->getRealPath(), $signatures);
            if ($found) {
                $suspicious_files[] = array('file' => $file->getRealPath(), 'signature' => $found);
            }
        }
    }

    return array('total' => $totalFiles, 'suspicious' => $suspicious_files);
}

if (isset($_GET['view'])) {
    $file = realpath($_GET['file']);
    if ($file && file_exists($file)) {
        echo htmlspecialchars(file_get_contents($file));
    } else {
        echo "File tidak ditemukan!";
    }
    exit;
}

if (isset($_GET['delete'])) {
    $file = realpath($_GET['file']);
    if ($file && file_exists($file)) {
        unlink($file);
        echo "File berhasil dihapus!";
    } else {
        echo "File tidak ditemukan!";
    }
    exit;
}

if (isset($_GET['scan'])) {
    $scan_dir = $_GET['dir'] ?? $default_scan_dir;
    $scan_dir = realpath($scan_dir);

    echo json_encode(scanDirectory($scan_dir, $signatures));
    exit;
}
?>

<!DOCTYPE html>
<html lang="id" data-bs-theme="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Polisi Pamung Praja Pengamanan Pasar</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <style>
        body {
            background-color: #000;
            color: #0f0;
            font-family: monospace;
        }
        .table {
            color: #0f0;
            background: #111;
        }
        .table tbody tr:hover {
            background: #222;
        }
        .progress {
            height: 30px;
            background: #111;
        }
        .progress-bar {
            background: #0f0;
        }
        .card {
            background: #111;
            border: 1px solid #0f0;
        }
        input {
            background: #000;
            color: #0f0;
            border: 1px solid #0f0;
        }
        .btn-success {
            background: #0a0;
            border: 1px solid #0f0;
        }
        .modal-content {
            background: #111;
            color: #0f0;
        }
        .btn-close {
            filter: invert(1);
        }
    </style>
</head>
<body>
    <div class="container mt-5">
        <div class="text-center mb-4">
            <h2>Polisi Pamung Praja Bagian Pengamanan</h2>
            <p>Deteksi file PHP berbahaya dengan mudah</p>
        </div>
        <div class="card shadow p-4">
            <label for="scanDir" class="form-label">Direktori Scan:</label>
            <input type="text" id="scanDir" class="form-control" value="<?= htmlspecialchars($default_scan_dir) ?>">
            <div class="text-center mt-3">
                <button id="startScan" class="btn btn-success">&#x1F50D; Mulai Scan</button>
            </div>
        </div>

        <div class="progress mt-4 d-none">
            <div id="progressBar" class="progress-bar progress-bar-striped progress-bar-animated" style="width: 0%;">0%</div>
        </div>

        <div class="table-responsive mt-4 d-none" id="resultTableContainer">
            <table class="table table-hover table-bordered text-center">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>File</th>
                        <th>Signature</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody id="scanResult"></tbody>
            </table>
        </div>
    </div>

    <div class="modal fade" id="fileModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Isi File</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <pre id="fileContent" class="p-3 rounded" style="background: #000; color: #0f0;"></pre>
                </div>
            </div>
        </div>
    </div>

    <script>
        $("#startScan").click(function () {
            $(".progress").removeClass("d-none");
            $("#progressBar").css("width", "0%").text("Scanning...");
            $("#resultTableContainer").addClass("d-none");
            $("#scanResult").html("");
            let dir = $("#scanDir").val().trim();
            
            $.get("scanner.php?scan=1&dir=" + encodeURIComponent(dir), function (data) {
                let result = JSON.parse(data);
                if (result.error) {
                    alert(result.error);
                    $("#progressBar").css("width", "0%").text("Error!");
                    return;
                }
                if (result.suspicious.length > 0) {
                    $("#resultTableContainer").removeClass("d-none");
                    let rows = "";
                    result.suspicious.forEach((file, index) => {
                        rows += `<tr>
                            <td>${index + 1}</td>
                            <td>${file.file}</td>
                            <td>${file.signature}</td>
                            <td>
                                <button class='btn btn-info btn-sm viewFile' data-file='${file.file}'>Lihat</button>
                                <button class='btn btn-danger btn-sm deleteFile' data-file='${file.file}'>Hapus</button>
                            </td>
                        </tr>`;
                    });
                    $("#scanResult").html(rows);
                } else {
                    alert("Tidak ada file mencurigakan ditemukan!");
                }
                $("#progressBar").css("width", "100%").text("Selesai");
            });
        });

        $(document).on("click", ".viewFile", function () {
            let file = $(this).data("file");
            $.get("scanner.php?view=1&file=" + encodeURIComponent(file), function (data) {
                $("#fileContent").text(data);
                $("#fileModal").modal("show");
            });
        });

        $(document).on("click", ".deleteFile", function () {
            let file = $(this).data("file");
            if (confirm("Apakah Anda yakin ingin menghapus file ini?")) {
                $.get("scanner.php?delete=1&file=" + encodeURIComponent(file), function (data) {
                    alert(data);
                    location.reload();
                });
            }
        });
    </script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
