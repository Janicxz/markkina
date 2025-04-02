<?php
session_start();
include("kantayhteys.php");

ini_set("display_errors", 1);
ini_set("display_startup_errors", 1);
error_reporting(E_ALL);

header("Content-Type: text/html; charset=utf-8");

// Asetetaan aikavyöhyke Suomen aikaan
date_default_timezone_set("Europe/Helsinki");

?>