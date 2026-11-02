<?php
function hitungNilaiAkhir($tugas, $uts, $uas)
{
    return ($tugas * 0.30) + ($uts * 0.30) + ($uas * 0.40);
}

function tentukanGrade($nilaiAkhir)
{
    if ($nilaiAkhir >= 85) {
        return "A";
    } elseif ($nilaiAkhir >= 75) {
        return "B";
    } elseif ($nilaiAkhir >= 65) {
        return "C";
    } elseif ($nilaiAkhir >= 50) {
        return "D";
    } else {
        return "E";
    }
}

function tentukanStatus($nilaiAkhir)
{
    if ($nilaiAkhir >= 65) {
        return "Lulus";
    }

    return "Tidak Lulus";
}

function tentukanPredikat($nilaiAkhir)
{
    if ($nilaiAkhir >= 85) {
        return "Sangat Baik";
    } elseif ($nilaiAkhir >= 75) {
        return "Baik";
    } elseif ($nilaiAkhir >= 65) {
        return "Cukup";
    }

    return "Perlu Ditingkatkan";
}

function e($teks)
{
    return htmlspecialchars((string) $teks, ENT_QUOTES, 'UTF-8');
}

$hasilMahasiswa = [];
$pesanError = [];

$namaList = (isset($_POST['nama']) && is_array($_POST['nama'])) ? $_POST['nama'] : [''];
$nimList = (isset($_POST['nim']) && is_array($_POST['nim'])) ? $_POST['nim'] : [''];
$tugasList = (isset($_POST['tugas']) && is_array($_POST['tugas'])) ? $_POST['tugas'] : [''];
$utsList = (isset($_POST['uts']) && is_array($_POST['uts'])) ? $_POST['uts'] : [''];
$uasList = (isset($_POST['uas']) && is_array($_POST['uas'])) ? $_POST['uas'] : [''];

$jumlahBaris = max(1, count($namaList), count($nimList), count($tugasList), count($utsList), count($uasList));

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $adaData = false;

    for ($i = 0; $i < $jumlahBaris; $i++) {
        $nama = trim(is_scalar($namaList[$i] ?? '') ? (string) ($namaList[$i] ?? '') : '');
        $nim = trim(is_scalar($nimList[$i] ?? '') ? (string) ($nimList[$i] ?? '') : '');
        $tugasInput = trim(is_scalar($tugasList[$i] ?? '') ? (string) ($tugasList[$i] ?? '') : '');
        $utsInput = trim(is_scalar($utsList[$i] ?? '') ? (string) ($utsList[$i] ?? '') : '');
        $uasInput = trim(is_scalar($uasList[$i] ?? '') ? (string) ($uasList[$i] ?? '') : '');

        if ($nama === '' && $nim === '' && $tugasInput === '' && $utsInput === '' && $uasInput === '') {
            continue;
        }

        $adaData = true;
        $masalah = [];

        if ($nama === '') {
            $masalah[] = 'nama belum diisi';
        }

        if ($nim === '') {
            $masalah[] = 'NIM belum diisi';
        }

        $daftarNilai = [
            'Tugas' => $tugasInput,
            'UTS' => $utsInput,
            'UAS' => $uasInput
        ];

        foreach ($daftarNilai as $jenisNilai => $nilaiInput) {
            if ($nilaiInput === '') {
                $masalah[] = 'nilai ' . $jenisNilai . ' belum diisi';
            } elseif (!is_numeric($nilaiInput)) {
                $masalah[] = 'nilai ' . $jenisNilai . ' harus berupa angka';
            } elseif ((float) $nilaiInput < 0 || (float) $nilaiInput > 100) {
                $masalah[] = 'nilai ' . $jenisNilai . ' harus berada pada rentang 0 sampai 100';
            }
        }

        if (!empty($masalah)) {
            $pesanError[] = 'Data mahasiswa baris ' . ($i + 1) . ': ' . implode(', ', $masalah) . '.';
            continue;
        }

        $tugas = (float) $tugasInput;
        $uts = (float) $utsInput;
        $uas = (float) $uasInput;

        $nilaiAkhir = hitungNilaiAkhir($tugas, $uts, $uas);

        $hasilMahasiswa[] = [
            'nama' => $nama,
            'nim' => $nim,
            'tugas' => $tugas,
            'uts' => $uts,
            'uas' => $uas,
            'nilaiAkhir' => $nilaiAkhir,
            'grade' => tentukanGrade($nilaiAkhir),
            'status' => tentukanStatus($nilaiAkhir),
            'predikat' => tentukanPredikat($nilaiAkhir)
        ];
    }

    if (!$adaData) {
        $pesanError[] = 'Silakan isi minimal satu data mahasiswa terlebih dahulu.';
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Penghitung Nilai Mahasiswa</title>
    <style>
        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            padding: 32px 16px;
            font-family: Arial, Helvetica, sans-serif;
            color: #202432;
            background: #f3f5f9;
        }

        .container {
            width: 100%;
            max-width: 1050px;
            margin: 0 auto;
        }

        .header {
            margin-bottom: 24px;
            text-align: center;
        }

        .header h1 {
            margin: 0 0 8px;
            font-size: clamp(25px, 4vw, 34px);
        }

        .header p {
            margin: 0;
            color: #687083;
            line-height: 1.6;
        }

        .panel {
            margin-bottom: 22px;
            padding: 24px;
            background: #ffffff;
            border: 1px solid #e3e7ef;
            border-radius: 14px;
            box-shadow: 0 8px 25px rgba(29, 42, 68, 0.05);
        }

        h2 {
            margin: 0 0 16px;
            font-size: 20px;
        }

        .petunjuk {
            margin: -5px 0 18px;
            color: #687083;
            font-size: 14px;
            line-height: 1.5;
        }

        .input-row {
            display: grid;
            grid-template-columns: 1.4fr 1fr repeat(3, 0.8fr) auto;
            gap: 12px;
            align-items: end;
            padding: 16px 0;
            border-top: 1px solid #edf0f5;
        }

        .field label {
            display: block;
            margin-bottom: 7px;
            font-size: 13px;
            font-weight: 700;
        }

        .field input {
            width: 100%;
            min-width: 0;
            padding: 11px 12px;
            border: 1px solid #d8deea;
            border-radius: 8px;
            outline: none;
            font: inherit;
        }

        .field input:focus {
            border-color: #5368d5;
            box-shadow: 0 0 0 3px rgba(83, 104, 213, 0.12);
        }

        button {
            padding: 11px 15px;
            border: 0;
            border-radius: 8px;
            font: inherit;
            font-weight: 700;
            cursor: pointer;
        }

        .btn-tambah {
            color: #3446ad;
            background: #edf0ff;
        }

        .btn-hitung {
            margin-top: 18px;
            color: #ffffff;
            background: #3446ad;
        }

        .btn-hapus {
            color: #a12d3b;
            background: #fff0f1;
            font-size: 13px;
        }

        .pesan-error {
            margin-bottom: 18px;
            padding: 14px 16px;
            color: #8f1d2c;
            background: #fff0f1;
            border: 1px solid #ffd4d8;
            border-radius: 9px;
            line-height: 1.6;
        }

        .pesan-error p {
            margin: 4px 0;
        }

        .tabel-wrapper {
            overflow-x: auto;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            white-space: nowrap;
        }

        th, td {
            padding: 12px 10px;
            border-bottom: 1px solid #e9edf3;
            text-align: left;
            font-size: 14px;
        }

        th {
            color: #4b5670;
            background: #f6f7fb;
        }

        .status-lulus {
            color: #167345;
            font-weight: 700;
        }

        .status-tidak {
            color: #b42332;
            font-weight: 700;
        }

        .catatan {
            margin: 14px 0 0;
            color: #687083;
            font-size: 13px;
            line-height: 1.6;
        }

        @media (max-width: 800px) {
            .input-row {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }

            .btn-hapus {
                grid-column: 1 / -1;
            }
        }

        @media (max-width: 480px) {
            body {
                padding: 22px 10px;
            }

            .panel {
                padding: 17px;
            }

            .input-row {
                grid-template-columns: 1fr;
            }

            .btn-hapus {
                grid-column: auto;
            }
        }
    </style>
</head>
<body>
    <main class="container">
        <header class="header">
            <h1>Penghitung Nilai Mahasiswa</h1>
            <p>Masukkan data mahasiswa untuk menghitung nilai akhir, grade, predikat, dan status kelulusan.</p>
        </header>

        <section class="panel">
            <h2>Input Data Mahasiswa</h2>
            <p class="petunjuk">Nilai tugas, UTS, dan UAS harus berada pada rentang 0 sampai 100. Gunakan tombol “Tambah Mahasiswa” untuk menambah baris.</p>

            <?php if (!empty($pesanError)): ?>
                <div class="pesan-error">
                    <strong>Periksa kembali data berikut:</strong>
                    <?php foreach ($pesanError as $pesan): ?>
                        <p>• <?= e($pesan) ?></p>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

            <form method="POST" action="">
                <div id="daftar-input">
                    <?php for ($i = 0; $i < $jumlahBaris; $i++): ?>
                        <div class="input-row">
                            <div class="field">
                                <label>Nama Mahasiswa</label>
                                <input type="text" name="nama[]" placeholder="Contoh: Andi" value="<?= e($namaList[$i] ?? '') ?>">
                            </div>
                            <div class="field">
                                <label>NIM</label>
                                <input type="text" name="nim[]" placeholder="Nomor induk" value="<?= e($nimList[$i] ?? '') ?>">
                            </div>
                            <div class="field">
                                <label>Nilai Tugas</label>
                                <input type="number" name="tugas[]" min="0" max="100" step="0.01" placeholder="0-100" value="<?= e($tugasList[$i] ?? '') ?>">
                            </div>
                            <div class="field">
                                <label>Nilai UTS</label>
                                <input type="number" name="uts[]" min="0" max="100" step="0.01" placeholder="0-100" value="<?= e($utsList[$i] ?? '') ?>">
                            </div>
                            <div class="field">
                                <label>Nilai UAS</label>
                                <input type="number" name="uas[]" min="0" max="100" step="0.01" placeholder="0-100" value="<?= e($uasList[$i] ?? '') ?>">
                            </div>
                            <button type="button" class="btn-hapus hapus-baris">Hapus</button>
                        </div>
                    <?php endfor; ?>
                </div>

                <button type="button" class="btn-tambah" id="tambah-baris">+ Tambah Mahasiswa</button>
                <br>
                <button type="submit" class="btn-hitung">Hitung Nilai</button>
            </form>
        </section>

        <?php if (!empty($hasilMahasiswa)): ?>
            <section class="panel">
                <h2>Hasil Perhitungan</h2>
                <div class="tabel-wrapper">
                    <table>
                        <thead>
                            <tr>
                                <th>No.</th>
                                <th>Nama</th>
                                <th>NIM</th>
                                <th>Tugas</th>
                                <th>UTS</th>
                                <th>UAS</th>
                                <th>Nilai Akhir</th>
                                <th>Grade</th>
                                <th>Predikat</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($hasilMahasiswa as $nomor => $mhs): ?>
                                <tr>
                                    <td><?= $nomor + 1 ?></td>
                                    <td><?= e($mhs['nama']) ?></td>
                                    <td><?= e($mhs['nim']) ?></td>
                                    <td><?= number_format($mhs['tugas'], 2, ',', '.') ?></td>
                                    <td><?= number_format($mhs['uts'], 2, ',', '.') ?></td>
                                    <td><?= number_format($mhs['uas'], 2, ',', '.') ?></td>
                                    <td><strong><?= number_format($mhs['nilaiAkhir'], 2, ',', '.') ?></strong></td>
                                    <td><strong><?= e($mhs['grade']) ?></strong></td>
                                    <td><?= e($mhs['predikat']) ?></td>
                                    <td class="<?= $mhs['status'] === 'Lulus' ? 'status-lulus' : 'status-tidak' ?>"><?= e($mhs['status']) ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <p class="catatan">Rumus: (Tugas × 30%) + (UTS × 30%) + (UAS × 40%). Mahasiswa dinyatakan lulus jika nilai akhir minimal 65.</p>
            </section>
        <?php endif; ?>
    </main>

    <script>
        const daftarInput = document.getElementById('daftar-input');
        const tombolTambah = document.getElementById('tambah-baris');

        tombolTambah.addEventListener('click', function () {
            const barisBaru = document.createElement('div');
            barisBaru.className = 'input-row';
            barisBaru.innerHTML = `
                <div class="field">
                    <label>Nama Mahasiswa</label>
                    <input type="text" name="nama[]" placeholder="Contoh: Andi">
                </div>
                <div class="field">
                    <label>NIM</label>
                    <input type="text" name="nim[]" placeholder="Nomor induk">
                </div>
                <div class="field">
                    <label>Nilai Tugas</label>
                    <input type="number" name="tugas[]" min="0" max="100" step="0.01" placeholder="0-100">
                </div>
                <div class="field">
                    <label>Nilai UTS</label>
                    <input type="number" name="uts[]" min="0" max="100" step="0.01" placeholder="0-100">
                </div>
                <div class="field">
                    <label>Nilai UAS</label>
                    <input type="number" name="uas[]" min="0" max="100" step="0.01" placeholder="0-100">
                </div>
                <button type="button" class="btn-hapus hapus-baris">Hapus</button>
            `;
            daftarInput.appendChild(barisBaru);
        });

    
        daftarInput.addEventListener('click', function (event) {
            if (event.target.classList.contains('hapus-baris')) {
                const semuaBaris = daftarInput.querySelectorAll('.input-row');

                if (semuaBaris.length > 1) {
                    event.target.closest('.input-row').remove();
                } else {
                    semuaBaris[0].querySelectorAll('input').forEach(function (input) {
                        input.value = '';
                    });
                }
            }
        });
    </script>
</body>
</html>
