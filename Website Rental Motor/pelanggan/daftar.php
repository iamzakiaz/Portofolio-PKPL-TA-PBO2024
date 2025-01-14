<?php

$update = ((isset($_GET['action']) AND $_GET['action'] == 'update') OR isset($_SESSION["pelanggan"])) ? true : false;
if ($update) {
    $sql = $connection->query("SELECT * FROM pelanggan WHERE id_pelanggan='$_SESSION[pelanggan][id]'");
    $row = $sql->fetch_assoc();
}

$errorMessages = [];
$formData = [
    'nama' => $_POST['nama'] ?? ($update ? $row['nama'] : ''),
    'no_ktp' => $_POST['no_ktp'] ?? ($update ? $row['no_ktp'] : ''),
    'no_telp' => $_POST['no_telp'] ?? ($update ? $row['no_telp'] : ''),
    'alamat' => $_POST['alamat'] ?? ($update ? $row['alamat'] : ''),
    'email' => $_POST['email'] ?? ($update ? $row['email'] : ''),
    'username' => $_POST['username'] ?? ($update ? $row['username'] : ''),
    'password' => $_POST['password'] ?? ''
];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Validasi Nama
    if (empty(trim($formData['nama']))) {
        $errorMessages[] = "Nama harus diisi.";
    } elseif (!preg_match("/^[a-zA-Z ]+$/", $formData['nama'])) {
        $errorMessages[] = "Nama harus berupa huruf.";
    } elseif (strlen($formData['nama']) > 50) { // Batasan panjang maksimal
        $errorMessages[] = "Nama tidak boleh lebih dari 50 karakter.";
    }

    // Validasi No KTP
    if (empty(trim($formData['no_ktp']))) {
        $errorMessages[] = "KTP harus diisi.";
    } elseif (!preg_match("/^\d{16}$/", $formData['no_ktp'])) {
        $errorMessages[] = "No KTP harus terdiri dari 16 angka.";
    }

    // Validasi No Telepon
    if (empty(trim($formData['no_telp']))) {
        $errorMessages[] = "No Telepon harus diisi.";
    } elseif (!preg_match("/^\d+$/", $formData['no_telp'])) {
        $errorMessages[] = "No Telepon harus berupa angka.";
    }

    // Validasi Alamat
    if (empty(trim($formData['alamat']))) {
        $errorMessages[] = "Alamat harus diisi.";
    } elseif (strlen($formData['alamat']) > 255) { // Batasan panjang maksimal
        $errorMessages[] = "Alamat tidak boleh lebih dari 255 karakter.";
    }

    // Validasi Email
    if (empty(trim($formData['email']))) {
        $errorMessages[] = "Email harus diisi.";
    } elseif (!filter_var($formData['email'], FILTER_VALIDATE_EMAIL)) {
        $errorMessages[] = "Format email tidak valid.";
    } elseif (!str_ends_with($formData['email'], '@gmail.com')) {
        $errorMessages[] = "Email harus berakhiran @gmail.com.";
    }

    // Validasi Username
    if (empty(trim($formData['username']))) {
        $errorMessages[] = "Username harus diisi.";
    } elseif (strlen($formData['username']) > 30) { // Batasan panjang maksimal
        $errorMessages[] = "Username tidak boleh lebih dari 30 karakter.";
    }

    // Validasi Password
    if (empty($formData['password'])) {
        $errorMessages[] = "Password harus diisi.";
    } elseif (strlen($formData['password']) < 8) {
        $errorMessages[] = "Password harus lebih dari 8 karakter.";
    } elseif (!preg_match("/[A-Z]/", $formData['password'])) {
        $errorMessages[] = "Password harus mengandung minimal 1 huruf kapital.";
    } elseif (!preg_match("/[\W]/", $formData['password'])) {
        $errorMessages[] = "Password harus mengandung minimal 1 simbol.";
    } elseif (!preg_match("/[a-zA-Z]/", $formData['password']) || !preg_match("/[0-9]/", $formData['password'])) {
        $errorMessages[] = "Password harus mengandung kombinasi huruf dan angka.";
    }

    // Validasi Konfirmasi Password
    if (empty($_POST['confirm_password'])) {
        $errorMessages[] = "Konfirmasi password harus diisi.";
    } elseif ($_POST['confirm_password'] !== $formData['password']) {
        $errorMessages[] = "Password dan konfirmasi password tidak cocok.";
    }

    // Cek duplikasi Username
    if (empty($errorMessages)) {
        $checkUsernameQuery = "SELECT * FROM pelanggan WHERE username='{$formData['username']}'";
        if ($update) {
            $checkUsernameQuery .= " AND id_pelanggan != '{$_SESSION['pelanggan']['id']}'";
        }
        $checkUsernameResult = $connection->query($checkUsernameQuery);
        if ($checkUsernameResult->num_rows > 0) {
            $errorMessages[] = "Username sudah terdaftar.";
        }
    }

    // Cek duplikasi No KTP
    if (empty($errorMessages)) {
        $checkKTPQuery = "SELECT * FROM pelanggan WHERE no_ktp='{$formData['no_ktp']}'";
        if ($update) {
            $checkKTPQuery .= " AND id_pelanggan != '{$_SESSION['pelanggan']['id']}'";
        }
        $checkKTPResult = $connection->query($checkKTPQuery);
        if ($checkKTPResult->num_rows > 0) {
            $errorMessages[] = "No KTP sudah terdaftar.";
        }
    }

    // Cek duplikasi Email
    if (empty($errorMessages)) {
        $checkEmailQuery = "SELECT * FROM pelanggan WHERE email='{$formData['email']}'";
        if ($update) {
            $checkEmailQuery .= " AND id_pelanggan != '{$_SESSION['pelanggan']['id']}'";
        }
        $checkEmailResult = $connection->query($checkEmailQuery);
        if ($checkEmailResult->num_rows > 0) {
            $errorMessages[] = "Email sudah terdaftar.";
        }
    }

    // Jika tidak ada error, lanjutkan dengan menyimpan data
    if (empty($errorMessages)) {
        if ($update) {
            $sql = "UPDATE pelanggan SET no_ktp='{$formData['no_ktp']}', nama='{$formData['nama']}', email='{$formData['email']}', no_telp='{$formData['no_telp']}', alamat='{$formData['alamat']}', username='{$formData['username']}'";
            if (!empty($formData['password'])) {
                $sql .= ", password='" . md5($formData['password']) . "'";
            }
            $sql .= " WHERE id_pelanggan='{$_SESSION['pelanggan']['id']}'";
        } else {
            $sql = "INSERT INTO pelanggan VALUES (NULL, '{$formData['no_ktp']}', '{$formData['nama']}', '{$formData['email']}', '{$formData['no_telp']}', '{$formData['alamat']}', '{$formData['username']}', '" . md5($formData['password']) . "')";
        }
        if ($connection->query($sql)) {
            echo alert("Berhasil! Silahkan login", "login.php");
        } else {
            echo alert("Gagal!", "?page=pelanggan");
        }
    }
}



if (isset($_GET['action']) AND $_GET['action'] == 'delete') {
    $connection->query("DELETE FROM pelanggan WHERE id_pelanggan='{$_SESSION['pelanggan']['id']}'");
    echo alert("Berhasil!", "?page=pelanggan");
}
?>

<div class="container">
    <div class="col-md-2"></div>
    <div class="col-md-8">
        <div class="page-header">
            <?php if ($update): ?>
                <h2>Update <small>data pelanggan!</small></h2>
            <?php else: ?>
                <h2>Daftar <small>sebagai pelanggan!</small></h2>
            <?php endif; ?>
        </div>
        <?php if (!empty($errorMessages)): ?>
            <div class="alert alert-danger">
                <ul>
                    <?php foreach ($errorMessages as $message): ?>
                        <li><?= $message ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>
        <form action="<?=$_SERVER['REQUEST_URI']?>" method="POST">
            <div class="form-group">
                <label for="nama">Nama</label>
                <input type="text" name="nama" class="form-control" autofocus="on" value="<?= htmlspecialchars($formData['nama']) ?>">
            </div>
            <div class="form-group">
                <label for="no_ktp">No KTP</label>
                <input type="text" name="no_ktp" class="form-control" value="<?= htmlspecialchars($formData['no_ktp']) ?>">
            </div>
            <div class="form-group">
                <label for="no_telp">No Telp</label>
                <input type="text" name="no_telp" class="form-control" value="<?= htmlspecialchars($formData['no_telp']) ?>">
            </div>
            <div class="form-group">
                <label for="alamat">Alamat</label>
                <textarea rows="2" name="alamat" class="form-control"><?= htmlspecialchars($formData['alamat']) ?></textarea>
            </div>
            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" name="email" class="form-control" value="<?= htmlspecialchars($formData['email']) ?>">
            </div>
            <div class="form-group">
                <label for="username">Username</label>
                <input type="text" name="username" class="form-control" value="<?= htmlspecialchars($formData['username']) ?>">
            </div>
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" name="password" class="form-control">
            </div>
            <div class="form-group">
                <label for="confirm_password">Konfirmasi Password</label>
                <input type="password" name="confirm_password" class="form-control">
            </div>
            <?php if ($update): ?>
                <div class="row">
                    <div class="col-md-10">
                        <button type="submit" class="btn btn-warning btn-block">Update</button>
                    </div>
                    <div class="col-md-2">
                        <a href="?page=kriteria" class="btn btn-default btn-block">Batal</a>
                    </div>
                </div>
            <?php else: ?>
                <button type="submit" class="btn btn-primary btn-block">Register</button>
            <?php endif; ?>
        </form>
    </div>
    <div class="col-md-2"></div>
</div>
