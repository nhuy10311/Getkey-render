<?php
header("Content-Type: text/plain; charset=UTF-8");

$MASTER_KEY = "123456";
$dbFile = "keys.json";
$scriptFile = "menu.lua";

if (!file_exists($dbFile)) {
    file_put_contents($dbFile, json_encode([]));
}

$keys = json_decode(file_get_contents($dbFile), true);
if (!is_array($keys)) $keys = [];

function saveKeys($dbFile, $keys) {
    file_put_contents($dbFile, json_encode($keys, JSON_PRETTY_PRINT));
}

function genKey($len = 20) {
    $chars = "ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
    $out = "";
    for ($i=0; $i<$len; $i++) {
        $out .= $chars[random_int(0, strlen($chars)-1)];
    }
    return $out;
}

if (isset($_POST["action"]) && $_POST["action"] === "create") {
    $master = $_POST["master"] ?? "";

    if ($master !== $MASTER_KEY) {
        echo "AUTH_ERR|MASTER_INVALID";
        exit;
    }

    $newKey = genKey();

    $keys[$newKey] = [
        "created" => time(),
        "used" => false
    ];

    saveKeys($dbFile, $keys);

    echo "AUTH_SUCCESS|NEW_KEY=" . $newKey;
    exit;
}

if (isset($_GET["check_key"])) {

    $key = strtoupper(trim($_GET["check_key"] ?? ""));

    if ($key === "") {
        echo "AUTH_ERR|EMPTY_KEY";
        exit;
    }

    if (!isset($keys[$key])) {
        echo "AUTH_ERR|KEY_INVALID";
        exit;
    }

    if (!file_exists($scriptFile)) {
        echo "AUTH_ERR|SCRIPT_NOT_FOUND";
        exit;
    }

    $luaCode = file_get_contents($scriptFile);

    echo "AUTH_SUCCESS|" . $luaCode;
    exit;
}

echo "AUTH_ERR|NO_ACTION";
