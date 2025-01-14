<?php
// src/daftar.php

function validateNama($nama) {
    if (empty(trim($nama))) {
        return "Nama harus diisi.";
    } elseif (!preg_match("/^[a-zA-Z ]+$/", $nama)) {
        return "Nama harus berupa huruf.";
    } elseif (strlen($nama) > 50) {
        return "Nama tidak boleh lebih dari 50 karakter.";
    }
    return null;
}

function validateNoKtp($noKtp) {
    if (empty(trim($noKtp))) {
        return "KTP harus diisi.";
    } elseif (!preg_match("/^\d{16}$/", $noKtp)) {
        return "No KTP harus terdiri dari 16 angka.";
    }
    return null;
}

function validateEmail($email) {
    if (empty(trim($email))) {
        return "Email harus diisi.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        return "Format email tidak valid.";
    } elseif (!str_ends_with($email, '@gmail.com')) {
        return "Email harus berakhiran @gmail.com.";
    }
    return null;
}

function validatePassword($password) {
    if (empty($password)) {
        return "Password harus diisi.";
    } elseif (strlen($password) < 8) {
        return "Password harus lebih dari 8 karakter.";
    } elseif (!preg_match("/[A-Z]/", $password)) {
        return "Password harus mengandung minimal 1 huruf kapital.";
    } elseif (!preg_match("/[\W]/", $password)) {
        return "Password harus mengandung minimal 1 simbol.";
    } elseif (!preg_match("/[a-zA-Z]/", $password) || !preg_match("/[0-9]/", $password)) {
        return "Password harus mengandung kombinasi huruf dan angka.";
    }
    return null;
}

function insertData($formData, $connection) {
    // Menyiapkan query INSERT
    $sql = "INSERT INTO pelanggan VALUES (NULL, '{$formData['no_ktp']}', '{$formData['nama']}', '{$formData['email']}', '{$formData['no_telp']}', '{$formData['alamat']}', '{$formData['username']}', '" . md5($formData['password']) . "')";
    return $connection->query($sql);
}
?>
