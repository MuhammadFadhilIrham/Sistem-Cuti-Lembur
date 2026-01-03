<?php
session_start();
include("config/db.php");

if (!isset($_SESSION['user'])) {
  header("Location: login.php");
  exit;
}

// Ambil nama dari session
$nama = isset($_SESSION['user']['nama']) ? $_SESSION['user']['nama'] : 'Pengguna';

// Ambil data cuti dan lembur semua user, kecuali yang ditolak
$cuti = mysqli_query($conn, "SELECT nama, tanggal, alasan, 'Cuti' AS jenis FROM cuti WHERE status != 'Ditolak'");
$lembur = mysqli_query($conn, "SELECT nama, tanggal, keterangan AS alasan, 'Lembur' AS jenis FROM lembur WHERE status != 'Ditolak'");

$events = [];
while ($r = mysqli_fetch_assoc($cuti)) {
  $events[] = [
    'title' => 'Cuti - ' . $r['nama'],
    'start' => $r['tanggal'],
    'color' => '#dc3545', // merah
    'description' => htmlspecialchars($r['alasan'])
  ];
}
while ($r = mysqli_fetch_assoc($lembur)) {
  $events[] = [
    'title' => 'Lembur - ' . $r['nama'],
    'start' => $r['tanggal'],
    'color' => '#0d6efd', // biru
    'description' => htmlspecialchars($r['alasan'])
  ];
}
?>

<?php include("includes/header.php"); ?>
<?php include("includes/navbar.php"); ?>

<!-- FullCalendar -->
<link href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.11/index.global.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.11/index.global.min.js"></script>

<style>
  body {
    background: url('assets/bg-dashboard.jpg') no-repeat center center fixed;
    background-size: cover;
    font-family: 'Poppins', sans-serif;
    color: #fff;
  }

  /* Selamat datang */
  .welcome-container {
    position: relative;
    z-index: 2;
    text-align: center;
    color: #fff;
    margin-top: 120px;
    background: rgba(255, 255, 255, 0.1);
    backdrop-filter: blur(8px);
    border-radius: 20px;
    padding: 40px 30px;
    max-width: 650px;
    margin-left: auto;
    margin-right: auto;
    box-shadow: 0 8px 25px rgba(0,0,0,0.3);
    transition: all 0.3s ease-in-out;
  }

  .welcome-container:hover {
    transform: scale(1.02);
    box-shadow: 0 12px 30px rgba(0,0,0,0.4);
  }

  .welcome-container h2 {
    font-weight: 700;
    font-size: 2rem;
  }

  .welcome-container p {
    font-size: 1.1rem;
    margin-top: 15px;
  }

  /* Kalender */
  .calendar-container {
    max-width: 1000px;
    margin: 60px auto 80px auto;
    background: rgba(255, 255, 255, 0.15);
    backdrop-filter: blur(10px);
    border-radius: 20px;
    padding: 40px;
    box-shadow: 0 8px 25px rgba(0,0,0,0.3);
  }

  #calendar {
    background: rgba(255, 255, 255, 0.95);
    border-radius: 15px;
    padding: 15px;
    color: #333;
  }

  h3 {
    font-weight: 700;
    text-shadow: 1px 1px 3px rgba(0,0,0,0.5);
    margin-bottom: 20px;
  }

  .fc-daygrid-event {
    border-radius: 6px;
    font-size: 0.9rem;
    padding: 3px 6px;
  }

  .fc-toolbar-title {
    color: #222;
    font-weight: 600;
  }

  .tooltip-custom {
    position: absolute;
    background: rgba(0,0,0,0.85);
    color: #fff;
    padding: 8px 12px;
    border-radius: 8px;
    font-size: 0.9rem;
    display: none;
    z-index: 1000;
  }

  /* 🔒 Modal Rahasia */
  .secret-modal {
    display: none;
    position: fixed;
    z-index: 9999;
    left: 0; top: 0;
    width: 100%; height: 100%;
    background-color: rgba(0,0,0,0.7);
    justify-content: center;
    align-items: center;
  }

  .secret-content {
    background: rgba(255,255,255,0.9);
    backdrop-filter: blur(10px);
    border-radius: 15px;
    padding: 30px;
    text-align: center;
    max-width: 400px;
    color: #111;
    box-shadow: 0 5px 20px rgba(0,0,0,0.3);
    animation: fadeIn 0.4s ease;
  }

  #openSecretBtn {
    margin-top: 20px;
    padding: 10px 20px;
    background: #0db1fd;
    border: none;
    border-radius: 10px;
    color: white;
    cursor: pointer;
    transition: 0.2s;
  }

  #openSecretBtn:hover {
    background: #007acc;
  }

  @keyframes fadeIn {
    from {opacity: 0; transform: scale(0.9);}
    to {opacity: 1; transform: scale(1);}
  }
</style>

<div class="overlay"></div>

<!-- Bagian Selamat Datang -->
<div class="welcome-container">
  <h2>Selamat Datang, <?= htmlspecialchars($nama); ?>!</h2>
  <img src="assets/welcome.gif" alt="Welcome" width="70" class="mt-3">
  <p class="mt-3">Pilih menu di atas untuk mengajukan cuti atau lembur.</p>
</div>

<!-- Kalender -->
<div class="calendar-container">
  <h3 class="text-center">📅 Kalender Cuti & Lembur Tim</h3>
  <p class="text-center text-light mb-4">
    Lihat jadwal cuti dan lembur seluruh tim Anda dalam satu tampilan.
  </p>

  <div id="calendar"></div>
  <div id="tooltip" class="tooltip-custom"></div>
</div>

<!-- 🔒 Modal -->
<div id="secretModal" class="secret-modal">
  <div class="secret-content">
    <h3>Easter Egg</h3>
    <p>Anda telah menemukan akses tersembunyi! Klik tombol di bawah untuk melanjutkan.</p>
    <button id="openSecretBtn">Buka Halaman</button>
  </div>
</div>

<script>
  document.addEventListener('DOMContentLoaded', function() {
    const calendarEl = document.getElementById('calendar');
    const tooltip = document.getElementById('tooltip');

    const calendar = new FullCalendar.Calendar(calendarEl, {
      initialView: 'dayGridMonth',
      height: 'auto',
      headerToolbar: {
        left: 'prev,next today',
        center: 'title',
        right: 'dayGridMonth,timeGridWeek,listWeek'
      },
      events: <?= json_encode($events) ?>,
      eventMouseEnter: function(info) {
        tooltip.style.display = 'block';
        tooltip.innerHTML = `<strong>${info.event.title}</strong><br>${info.event.extendedProps.description}`;
        tooltip.style.left = (info.jsEvent.pageX + 10) + 'px';
        tooltip.style.top = (info.jsEvent.pageY - 40) + 'px';
      },
      eventMouseLeave: function() {
        tooltip.style.display = 'none';
      },
      eventClick: function(info) {
        alert(`${info.event.title}\nTanggal: ${info.event.start.toISOString().split('T')[0]}\nKeterangan: ${info.event.extendedProps.description}`);
      }
    });

    calendar.render();

    // ==== Fitur Rahasia ====
    const welcomeContainer = document.querySelector('.welcome-container');
    const secretModal = document.getElementById('secretModal');
    const openSecretBtn = document.getElementById('openSecretBtn');
    let tapCount = 0;
    let timer;

    if (welcomeContainer) {
      welcomeContainer.addEventListener('click', function() {
        tapCount++;
        clearTimeout(timer);
        timer = setTimeout(() => { tapCount = 0; }, 1500);

        if (tapCount === 7) {
          secretModal.style.display = "flex";
          tapCount = 0;
        }
      });
    }

    openSecretBtn.addEventListener('click', function() {
      // 🔗 Ganti URL di bawah ini dengan halaman rahasiamu
      window.location.href = "https://www.youtube.com/watch?v=1o69aqubNcM&list=RD1o69aqubNcM&start_radio=1";
    });

    secretModal.addEventListener('click', function(e) {
      if (e.target === secretModal) {
        secretModal.style.display = "none";
      }
    });
  });
</script>

<?php include("includes/footer.php"); ?>
