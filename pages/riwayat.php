<?php
session_start();
include("../config/db.php");
if (!isset($_SESSION['user'])) {
  header("Location: ../login.php");
  exit;
}

$user = $_SESSION['user'];
$nama = $user['nama'];
$cuti = mysqli_query($conn, "SELECT * FROM cuti WHERE nama='$nama' ORDER BY tanggal DESC");
$lembur = mysqli_query($conn, "SELECT * FROM lembur WHERE nama='$nama' ORDER BY tanggal DESC");

include("../includes/header.php");
include("../includes/navbar.php");
?>

<style>
body {
  background: url('../assets/bg-dashboard.jpg') no-repeat center center fixed;
  background-size: cover;
  font-family: 'Poppins', sans-serif;
  color: #fff;
}

.container-riwayat {
  position: relative;
  z-index: 2;
  margin-top: 80px;
  background: rgba(0, 0, 0, 0.45);
  backdrop-filter: blur(10px);
  border-radius: 20px;
  padding: 40px;
  box-shadow: 0 8px 25px rgba(0,0,0,0.3);
}

h3 {
  color: #fff;
  text-shadow: 1px 1px 3px rgba(0,0,0,0.5);
}

.table {
  background: rgba(255, 255, 255, 0.15);
  border-radius: 10px;
  overflow: hidden;
}

.table thead {
  background: rgba(255, 255, 255, 0.25);
  color: #fff;
  font-weight: bold;
}

.table tbody tr:hover {
  background: rgba(255, 255, 255, 0.2);
  transition: background 0.3s ease;
}

.table th, .table td {
  color: #000000ff;
  border-color: rgba(255, 255, 255, 0.3);
  text-shadow: 0 1px 2px rgba(0,0,0,0.3);
}

.card-header {
  background: rgba(0, 0, 0, 0.5);
  border-bottom: 1px solid rgba(255, 255, 255, 0.2);
}

.btn-secondary {
  border-radius: 10px;
  background: rgba(255,255,255,0.15);
  border: 1px solid rgba(255,255,255,0.3);
  color: #fff;
  transition: all 0.3s ease;
}
.btn-secondary:hover {
  background: rgba(255,255,255,0.3);
  color: #000;
}

.link-bukti {
  color: #007bff;
  font-weight: 600;
  text-decoration: none;
}
.link-bukti:hover {
  text-decoration: underline;
}
</style>

<div class="container container-riwayat">
  <h3 class="mb-4 text-center fw-bold">Riwayat Pengajuan</h3>

  <!-- Riwayat Cuti -->
  <div class="card mb-4 shadow-sm bg-transparent border-light">
    <div class="card-header fw-bold text-white">Riwayat Cuti</div>
    <div class="card-body">
      <table class="table table-bordered table-hover align-middle text-center">
        <thead>
          <tr>
            <th>No</th>
            <th>Tanggal</th>
            <th>Alasan</th>
            <th>Bukti Dokumen</th>
            <th>Status</th>
          </tr>
        </thead>
        <tbody>
          <?php 
          $no = 1;
          if (mysqli_num_rows($cuti) > 0) {
            while ($row = mysqli_fetch_assoc($cuti)) {
              echo "<tr>
                      <td>$no</td>
                      <td>{$row['tanggal']}</td>
                      <td>{$row['alasan']}</td>";
              
              if (!empty($row['bukti'])) {
                echo "<td><a href='../assets/uploads/{$row['bukti']}' target='_blank' class='link-bukti'>Lihat</a></td>";
              } else {
                echo "<td><span class='text-muted'>Tidak ada</span></td>";
              }

              echo "<td>{$row['status']}</td>
                    </tr>";
              $no++;
            }
          } else {
            echo "<tr><td colspan='5' class='text-center text-light'>Belum ada pengajuan cuti.</td></tr>";
          }
          ?>
        </tbody>
      </table>
    </div>
  </div>

  <!-- Riwayat Lembur -->
  <div class="card mb-4 shadow-sm bg-transparent border-light">
    <div class="card-header fw-bold text-white">Riwayat Lembur</div>
    <div class="card-body">
      <table class="table table-bordered table-hover align-middle text-center">
        <thead>
          <tr>
            <th>No</th>
            <th>Tanggal</th>
            <th>Jam</th>
            <th>Keterangan</th>
            <th>Bukti Dokumen</th>
            <th>Status</th>
          </tr>
        </thead>
        <tbody>
          <?php 
          $no = 1;
          if (mysqli_num_rows($lembur) > 0) {
            while ($row = mysqli_fetch_assoc($lembur)) {
              echo "<tr>
                      <td>$no</td>
                      <td>{$row['tanggal']}</td>
                      <td>{$row['jam']}</td>
                      <td>{$row['keterangan']}</td>";

              if (!empty($row['bukti'])) {
                echo "<td><a href='../assets/uploads/{$row['bukti']}' target='_blank' class='link-bukti'>Lihat</a></td>";
              } else {
                echo "<td><span class='text-muted'>Tidak ada</span></td>";
              }

              echo "<td>{$row['status']}</td>
                    </tr>";
              $no++;
            }
          } else {
            echo "<tr><td colspan='6' class='text-center text-light'>Belum ada pengajuan lembur.</td></tr>";
          }
          ?>
        </tbody>
      </table>
    </div>
  </div>

  <div class="text-center mt-4">
    <a href="../dashboard.php" class="btn btn-secondary">
      <i class="bi bi-arrow-left"></i> Kembali ke Dashboard
    </a>
  </div>
</div>

<?php include("../includes/footer.php"); ?>
