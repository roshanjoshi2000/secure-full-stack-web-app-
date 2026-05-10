<?php
$mysqli = new mysqli("localhost", "2402361", "University7623", "db2402361");

if ($mysqli->connect_errno) {
    die("Failed to connect to MySQL: " . $mysqli->connect_error);
}
?>