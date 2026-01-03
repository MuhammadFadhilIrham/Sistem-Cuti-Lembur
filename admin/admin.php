<?php
session_start();
include("../config/db.php");

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] != 'admin') {
    header("Location: ../login.php");
    exit;
}

// =============================
// PROSES SETUJU / TOLAK
// =============================
if (isset($_GET['tabel'], $_GET['id'], $_GET['aksi'])) {

    $tabel = $_GET['tabel']; // cuti / lembur
    $id    = intval($_GET['id']);
    $aksi  = ($_GET['aksi'] == 'setuju') ? 'Disetujui' : 'Ditolak';

    // Ambil user_id pemilik pengajuan
    $ambilUser = mysqli_query($conn, "SELECT user_id FROM $tabel WHERE id = $id");

    if ($ambilUser && mysqli_num_rows($ambilUser) > 0) {

        $row = mysqli_fetch_assoc($ambilUser);
        $user_id = $row['user_id'];

        // Update status pengajuan
        mysqli_query($conn, "UPDATE $tabel SET status='$aksi' WHERE id=$id");

        // Buat pesan notifikasi
        $pesan = "Pengajuan " . ucfirst($tabel) . " Anda telah " . strtolower($aksi) . ".";

        // Insert ke tabel notifications
        mysqli_query($conn, "
            INSERT INTO notifications (user_id, pesan, status, created_at)
            VALUES ('$user_id', '$pesan', 'unread', NOW())
        ");
    }

    header("Location: admin.php");
    exit;
}

// =============================
// AMBIL DATA
// =============================
$cuti   = mysqli_query($conn, "SELECT * FROM cuti ORDER BY id DESC");
$lembur = mysqli_query($conn, "SELECT * FROM lembur ORDER BY id DESC");

// Statistik dashboard
$total_cuti = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS jml FROM cuti"))['jml'];
$total_lembur = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS jml FROM lembur"))['jml'];

$cuti_pending = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS jml FROM cuti WHERE status='Pending'"))['jml'];
$lembur_pending = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS jml FROM lembur WHERE status='Pending'"))['jml'];

// Ambil rekap absensi
$absensi = mysqli_query($conn, "
    SELECT 
        users.nama,
        COUNT(absensi.id) AS total_hadir,
        SUM(CASE WHEN absensi.status = 'Terlambat' THEN 1 ELSE 0 END) AS total_terlambat,
        SUM(CASE WHEN absensi.status = 'Izin' THEN 1 ELSE 0 END) AS total_izin
    FROM absensi
    JOIN users ON absensi.user_id = users.id
    GROUP BY users.id
    ORDER BY users.nama ASC
");

// Ambil laporan harian
$laporan = mysqli_query($conn, "
    SELECT 
        laporan_harian.*, 
        users.nama 
    FROM laporan_harian
    JOIN users ON laporan_harian.user_id = users.id
    ORDER BY laporan_harian.id DESC
");

// Ambil semua pengguna (kecuali admin)
$pengguna = mysqli_query($conn, "
    SELECT id, nama, username, role, foto, dibuat
    FROM users
    ORDER BY id DESC
");

include("../includes/header.php");
include("../includes/navbar.php");
?>

<style>
  body {
    background: url('../assets/bg-dashboard.jpg') no-repeat center center fixed;
    background-size: cover;
    font-family: 'Poppins', sans-serif;
  }
  .admin-container {
    position: relative;
    z-index: 2;
    margin: 90px auto;
    max-width: 1200px;
    background: rgba(255, 255, 255, 0.12);
    backdrop-filter: blur(10px);
    border-radius: 20px;
    padding: 40px;
    color: #fff;
    box-shadow: 0 8px 25px rgba(0,0,0,0.3);
  }
  .stat-box {
    background: rgba(255,255,255,0.15);
    padding: 25px;
    border-radius: 15px;
    text-align: center;
    color: white;
    backdrop-filter: blur(5px);
  }
  .stat-box ion-icon {
    font-size: 45px;
    margin-bottom: 10px;
  }
  table {
    background: rgba(255,255,255,0.95);
    color: #333;
    border-radius: 10px;
    overflow: hidden;
  }
  th {
    background-color: #0d6efd !important;
    color: white;
  }
</style>

<div class="admin-container">

  <h3 class="text-center fw-bold mb-4">Dashboard Admin</h3>

  <!-- ================================================== -->
  <!-- STATISTIK -->
  <!-- ================================================== -->
  <div class="row text-center mb-4">
    <div class="col-md-3">
      <div class="stat-box">
        <ion-icon name="document-text-outline"></ion-icon>
        <h4><?= $total_cuti ?></h4>
        <p>Total Pengajuan Cuti</p>
      </div>
    </div>

    <div class="col-md-3">
      <div class="stat-box">
        <ion-icon name="timer-outline"></ion-icon>
        <h4><?= $total_lembur ?></h4>
        <p>Total Pengajuan Lembur</p>
      </div>
    </div>

    <div class="col-md-3">
      <div class="stat-box">
        <ion-icon name="alert-circle-outline"></ion-icon>
        <h4><?= $cuti_pending ?></h4>
        <p>Cuti Pending</p>
      </div>
    </div>

    <div class="col-md-3">
      <div class="stat-box">
        <ion-icon name="time-outline"></ion-icon>
        <h4><?= $lembur_pending ?></h4>
        <p>Lembur Pending</p>
      </div>
    </div>

    <!-- ========================= -->
    <!-- REKAP ABSENSI -->
    <!-- ========================= -->
    <h5 class="mt-5">Rekap Absensi</h5>
    <div class="table-responsive">
      <table class="table table-bordered text-center align-middle">
        <thead>
          <tr>
            <th>Nama</th>
            <th>Total Hadir</th>
            <th>Total Terlambat</th>
            <th>Total Izin</th>
          </tr>
        </thead>
        <tbody>
          <?php while ($a = mysqli_fetch_assoc($absensi)): ?>
          <tr>
            <td><?= htmlspecialchars($a['nama']); ?></td>
            <td><?= $a['total_hadir']; ?></td>
            <td><?= $a['total_terlambat']; ?></td>
            <td><?= $a['total_izin']; ?></td>
          </tr>
          <?php endwhile; ?>
        </tbody>
      </table>
    </div>
  </div>

  <!-- ================================================== -->
  <!-- GRAFIK -->
  <!-- ================================================== -->
  <canvas id="grafikCutiLembur" height="120" style="margin-bottom: 40px; background: white; border-radius: 10px;"></canvas>

  <!-- ================================================== -->
  <!-- TABEL CUTI -->
  <!-- ================================================== -->
  <h5>Pengajuan Cuti</h5>
  <table class="table table-bordered text-center align-middle mb-4">
    <thead>
      <tr>
        <th>Nama</th><th>Tanggal</th><th>Alasan</th><th>Bukti</th><th>Status</th><th>Aksi</th>
      </tr>
    </thead>
    <tbody>
      <?php while($r = mysqli_fetch_assoc($cuti)): ?>
      <tr>
        <td><?= $r['nama']; ?></td>
        <td><?= $r['tanggal']; ?></td>
        <td><?= $r['alasan']; ?></td>
        <td>
          <?php if ($r['bukti']): ?>
            <a class="btn btn-info btn-sm" href="../assets/uploads/<?= $r['bukti']; ?>" target="_blank">Lihat</a>
          <?php else: ?>
            <span class="text-muted">Tidak ada</span>
          <?php endif; ?>
        </td>
        <td><?= $r['status']; ?></td>
        <td>
          <a href="?tabel=cuti&id=<?= $r['id']; ?>&aksi=setuju" class="btn btn-success btn-sm">Setuju</a>
          <a href="?tabel=cuti&id=<?= $r['id']; ?>&aksi=tolak" class="btn btn-danger btn-sm">Tolak</a>
        </td>
      </tr>
      <?php endwhile; ?>
    </tbody>
  </table>

  <!-- ================================================== -->
  <!-- TABEL LEMBUR -->
  <!-- ================================================== -->
  <h5 class="mt-5">Pengajuan Lembur</h5>
  <table class="table table-bordered text-center align-middle">
    <thead>
      <tr>
        <th>Nama</th><th>Tanggal</th><th>Jam</th><th>Keterangan</th><th>Bukti</th><th>Status</th><th>Aksi</th>
      </tr>
    </thead>
    <tbody>
      <?php while($r = mysqli_fetch_assoc($lembur)): ?>
      <tr>
        <td><?= $r['nama']; ?></td>
        <td><?= $r['tanggal']; ?></td>
        <td><?= $r['jam']; ?></td>
        <td><?= $r['keterangan']; ?></td>
        <td>
          <?php if ($r['bukti']): ?>
            <a class="btn btn-info btn-sm" href="../assets/uploads/<?= $r['bukti']; ?>" target="_blank">Lihat</a>
          <?php else: ?>
            <span class="text-muted">Tidak ada</span>
          <?php endif; ?>
        </td>
        <td><?= $r['status']; ?></td>
        <td>
          <a href="?tabel=lembur&id=<?= $r['id']; ?>&aksi=setuju" class="btn btn-success btn-sm">Setuju</a>
          <a href="?tabel=lembur&id=<?= $r['id']; ?>&aksi=tolak" class="btn btn-danger btn-sm">Tolak</a>
        </td>
      </tr>
      <?php endwhile; ?>
    </tbody>
  </table>

  <!-- ================================================== -->
  <!-- TABEL LAPORAN HARIAN -->
  <!-- ================================================== -->
  <h5 class="mt-5">Laporan Harian Pengguna</h5>

  <div class="table-responsive">
      <table class="table table-bordered text-center align-middle mb-4">
          <thead>
              <tr>
                  <th>Nama</th>
                  <th>Tanggal</th>
                  <th>Kegiatan</th>
                  <th>Kendala</th>
                  <th>Bukti</th>
                  <th>Status</th>
                  <th>Dibuat</th>
              </tr>
          </thead>
          <tbody>
              <?php while($r = mysqli_fetch_assoc($laporan)): ?>
              <tr>
                  <td><?= htmlspecialchars($r['nama']); ?></td>
                  <td><?= $r['tanggal']; ?></td>
                  <td><?= nl2br($r['kegiatan']); ?></td>
                  <td><?= nl2br($r['kendala']); ?></td>

                  <td>
                      <?php if (!empty($r['bukti'])): ?>
                          <a href="../assets/uploads/<?= $r['bukti']; ?>" 
                            class="btn btn-info btn-sm"
                            target="_blank">
                            Lihat
                          </a>
                      <?php else: ?>
                        <span class="text-muted">Tidak ada</span>
                      <?php endif; ?>
                  </td>

                  <td><?= $r['status']; ?></td>
                  <td><?= $r['created_at']; ?></td>
              </tr>
              <?php endwhile; ?>
          </tbody>
      </table>

  <!-- ================================================== -->
  <!-- MANAJEMEN PENGGUNA -->
  <!-- ================================================== -->

  <h5 class="mt-5">Manajemen Pengguna</h5>

  <div class="table-responsive">
    <table class="table table-bordered text-center align-middle mb-4">
      <thead>
        <tr>
          <th>Foto</th>
          <th>Nama</th>
          <th>Username</th>
          <th>Role</th>
          <th>Dibuat</th>
          <th>Aksi</th>
        </tr>
      </thead>
      <tbody>
        <?php while($u = mysqli_fetch_assoc($pengguna)): ?>
        <tr>
          <td>
            <img src="<?= !empty($u['foto']) ? '../uploads/'.$u['foto'] : '../assets/default.png' ?>" 
                style="width:40px; height:40px; border-radius:50%; object-fit:cover;">
          </td>

          <td><?= htmlspecialchars($u['nama']); ?></td>
          <td><?= htmlspecialchars($u['username']); ?></td>
          <td><?= ucfirst($u['role']); ?></td>
          <td><?= $u['dibuat']; ?></td>

          <td>
            <a href="user_edit.php?id=<?= $u['id']; ?>" class="btn btn-warning btn-sm">Edit</a>
            <a href="user_delete.php?id=<?= $u['id']; ?>" 
              class="btn btn-danger btn-sm"
              onclick="return confirm('Hapus pengguna ini?');">
              Hapus
            </a>
          </td>
        </tr>
        <?php endwhile; ?>
      </tbody>
    </table>
  </div>

</div>

<!-- ================================================== -->
<!-- SCRIPT CHART.JS -->
<!-- ================================================== -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
  let ctx = document.getElementById("grafikCutiLembur");

  new Chart(ctx, {
    type: "bar",
    data: {
      labels: ["Cuti", "Lembur"],
      datasets: [{
        label: "Jumlah Pengajuan",
        data: [<?= $total_cuti ?>, <?= $total_lembur ?>],
        backgroundColor: ["#0d6efd", "#198754"]
      }]
    }
  });
</script>

<!-- Ionicons -->
<script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
<script nomodule src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.js"></script>

<?php include("../includes/footer.php"); ?>
