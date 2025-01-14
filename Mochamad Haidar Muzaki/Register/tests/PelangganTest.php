<?php

use PHPUnit\Framework\TestCase;
require_once __DIR__ . '/../src/daftar.php';

class PelangganTest extends TestCase
{
    public function testNamaValidation()
    {
        // Kasus ketika nama kosong
        $this->assertEquals("Nama harus diisi.", validateNama(''));

        // Kasus ketika nama mengandung angka
        $this->assertEquals("Nama harus berupa huruf.", validateNama('Muzaki123'));

        // Kasus ketika nama valid
        $this->assertNull(validateNama('Mochamad Haidar Muzaki'));
    }

    public function testNoKtpValidation()
    {
        // Kasus ketika No KTP kosong
        $this->assertEquals("KTP harus diisi.", validateNoKtp(''));

        // Kasus ketika No KTP tidak valid
        $this->assertEquals("No KTP harus terdiri dari 16 angka.", validateNoKtp('123456'));

        // Kasus ketika No KTP valid
        $this->assertNull(validateNoKtp('1234567890123456'));
    }

    public function testEmailValidation()
    {
        // Kasus ketika Email kosong
        $this->assertEquals("Email harus diisi.", validateEmail(''));

        // Kasus ketika format Email tidak valid
        $this->assertEquals("Format email tidak valid.", validateEmail('mochamad@domain'));

        // Kasus ketika Email tidak berakhiran @gmail.com
        $this->assertEquals("Email harus berakhiran @gmail.com.", validateEmail('mochamad@outlook.com'));

        // Kasus ketika Email valid
        $this->assertNull(validateEmail('mochamad@gmail.com'));
    }

    public function testPasswordValidation()
    {
        // Kasus ketika password kosong
        $this->assertEquals("Password harus diisi.", validatePassword(''));

        // Kasus ketika password kurang dari 8 karakter
        $this->assertEquals("Password harus lebih dari 8 karakter.", validatePassword('short'));

        // Kasus ketika password tidak mengandung huruf kapital
        $this->assertEquals("Password harus mengandung minimal 1 huruf kapital.", validatePassword('password1'));

        // Kasus ketika password valid
        $this->assertNull(validatePassword('Password1!'));
    }

    public function testInsertData()
    {
        // Menggunakan mock atau pengujian database untuk memastikan data disimpan dengan benar.
        $mockConnection = $this->createMock(mysqli::class);
        $mockConnection->expects($this->once())
                       ->method('query')
                       ->with($this->stringContains('INSERT INTO pelanggan'))
                       ->willReturn(true);

        $formData = [
            'no_ktp' => '1234567890123456',
            'nama' => 'Mochamad Haidar Muzaki',
            'email' => 'mochamad@gmail.com',
            'no_telp' => '123456789',
            'alamat' => 'Address example',
            'username' => 'muzaki',
            'password' => 'Password1!'
        ];

        $this->assertTrue(insertData($formData, $mockConnection));
    }
}
?>
