<?php
$conn = new mysqli(
  getenv("MYSQL_HOST"),
  getenv("MYSQL_USER"),
  getenv("MYSQL_PASSWORD"),
  getenv("MYSQL_DATABASE"),
  getenv("MYSQL_PORT")
);

if ($conn->connect_error) {
  die("DB connection failed");
}

/* CREATE USERS TABLE */
$sql = "
CREATE TABLE IF NOT EXISTS users (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(100) NOT NULL,
  email VARCHAR(150) NOT NULL UNIQUE,
  password VARCHAR(255) NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)";
$conn->query($sql);
?>
