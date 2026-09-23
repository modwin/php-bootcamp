<?php

namespace WPROG2\S4_Session_Management\Ex4_3_Native_Sessions\public;

require_once __DIR__ . '/../../../../vendor/autoload.php';

session_start();

header("Content-Type: text/html; charset=utf-8");
readfile(__DIR__ . '/nainve_sessions.html');