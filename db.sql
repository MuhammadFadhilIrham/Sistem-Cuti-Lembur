CREATE TABLE users (
  id INT AUTO_INCREMENT PRIMARY KEY,
  nama VARCHAR(100),
  username VARCHAR(50),
  password VARCHAR(50),
  role ENUM('user','admin') DEFAULT 'user'
);

INSERT INTO users (nama, username, password, role)
VALUES ('Admin', 'admin', 'admin', 'admin');

CREATE TABLE cuti (
  id INT AUTO_INCREMENT PRIMARY KEY,
  nama VARCHAR(100),
  tanggal DATE,
  alasan TEXT,
  status VARCHAR(50)
);

CREATE TABLE lembur (
  id INT AUTO_INCREMENT PRIMARY KEY,
  nama VARCHAR(100),
  tanggal DATE,
  jam INT,
  keterangan TEXT,
  status VARCHAR(50)
);

CREATE TABLE jadwal (
  id INT AUTO_INCREMENT PRIMARY KEY,
  user_id INT NOT NULL,
  tanggal DATE NOT NULL,
  kegiatan VARCHAR(255) NOT NULL,
  waktu_mulai TIME,
  waktu_selesai TIME,
  keterangan TEXT,
  FOREIGN KEY (user_id) REFERENCES users(id)
);
