<?php
header("Content-Type: text/plain; charset=UTF-8");

$MASTER_KEY = "nhuy103"; // đổi master key
$dbFile = "keys.json";
$scriptFile = "menu.lua"; // file chứa menu AutoWalk của m

// tạo db nếu chưa có
if (!file_exists($dbFile)) {
    file_put_contents($dbFile, json_encode([]));
}

$keys = json_decode(file_get_contents($dbFile), true);
if (!is_array($keys)) $keys = [];

// random key
function genKey($len = 20) {
    $chars = "ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
    $out = "";
    for ($i=0; $i<$len; $i++) {
        $out .= $chars[random_int(0, strlen($chars)-1)];
    }
    return $out;
}

// lưu db
function saveKeys($dbFile, $keys) {
    file_put_contents($dbFile, json_encode($keys, JSON_PRETTY_PRINT));
}

/* =========================
   CREATE KEY (ADMIN)
   ========================= */
if (isset($_GET["create_key"])) {
    $master = $_GET["master"] ?? "";

    if ($master !== $MASTER_KEY) {
        echo "AUTH_ERR|MASTER_INVALID";
        exit;
    }

    $newKey = genKey();

    $keys[$newKey] = [
        "created" => time()
    ];

    saveKeys($dbFile, $keys);

    echo "AUTH_SUCCESS|NEW_KEY=" . $newKey;
    exit;
}

/* =========================
   CHECK KEY (LOADER)
   ========================= */
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

    // đọc file menu.lua
    if (!file_exists($scriptFile)) {
        echo "AUTH_ERR|SCRIPT_NOT_FOUND";
        exit;
    }

    $luaCode = file_get_contents($scriptFile);

    // trả về đúng format loader của m cần
    echo "AUTH_SUCCESS|" . $luaCode;
    exit;
}

/* =========================
   DEFAULT
   ========================= */
echo "AUTH_ERR|NO_ACTION";
