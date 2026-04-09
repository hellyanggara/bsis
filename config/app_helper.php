<?php
/**
 * Helper umum aplikasi
 * Gunakan dengan: require_once 'helpers/app_helper.php';
 */

/* =========================
   RESPONSE HELPERS
   ========================= */

function abort404()
{
    header("HTTP/1.1 404 Not Found");
    header("Location: " . $GLOBALS['httpHost'] . "404.php");
    exit;
}

function abort403()
{
    header("HTTP/1.1 403 Forbidden");
    header("Location: " . $GLOBALS['httpHost'] . "403.php");
    exit;
}


/* =========================
   REQUEST HELPERS
   ========================= */

function getValidIdFromUrl(string $key = 'id'): int
{
    $id = $_GET[$key] ?? null;

    if (!$id || !ctype_digit($id)) {
        abort404();
    }

    return (int) $id;
}


/* =========================
   DATA HELPERS
   ========================= */

function abortIfEmpty($data)
{
    if (empty($data)) {
        abort404();
    }
}

function getOr404(callable $callback)
{
    $data = $callback();

    if (!$data) {
        abort404();
    }

    return $data;
}


/* =========================
   AUTH / OWNERSHIP HELPERS
   ========================= */

function abortIfNotOwner($dataUserId, $sessionUserId)
{
    if ($dataUserId != $sessionUserId) {
        abort403();
    }
}
