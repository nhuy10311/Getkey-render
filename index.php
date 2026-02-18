<?php
$MASTER_KEY = "nhuy103"; // đổi master key của m

$dbFile = "keys.json";
$scriptFile = "menu.lua"; // file chứa code menu lua

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

function saveKeys($dbFile, $keys) {
    file_put_contents($dbFile, json_encode($keys, JSON_PRETTY_PRINT));
}

/* =========================
   1) CREATE KEY (ADMIN)
   ========================= */
if (isset($_GET["create_key"])) {
    header("Content-Type: application/json; charset=UTF-8");

    $master = $_GET["master"] ?? "";
    if ($master !== $MASTER_KEY) {
        echo json_encode(["status"=>"error","msg"=>"MASTER sai"]);
        exit;
    }

    $newKey = genKey();

    $keys[$newKey] = [
        "created" => time(),
        "used" => false
    ];

    saveKeys($dbFile, $keys);

    echo json_encode(["status"=>"success","key"=>$newKey]);
    exit;
}

/* =========================
   2) GET SCRIPT BY KEY
   ========================= */
if (isset($_GET["get_script"])) {
    header("Content-Type: text/plain; charset=UTF-8");

    $key = strtoupper(trim($_GET["key"] ?? ""));

    if ($key === "" || !isset($keys[$key])) {
        echo "KEY_INVALID";
        exit;
    }

    // nếu muốn key dùng 1 lần thì bật cái này:
    // $keys[$key]["used"] = true;
    // saveKeys($dbFile, $keys);

    // nếu chưa có file menu.lua thì báo lỗi
    if (!file_exists($scriptFile)) {
        echo "-- SCRIPT_NOT_FOUND";
        exit;
    }

    // trả code lua
    echo file_get_contents($scriptFile);
    exit;
}

/* =========================
   DEFAULT
   ========================= */
header("Content-Type: application/json; charset=UTF-8");
echo json_encode([
    "status" => "ok",
    "msg" => "API online",
    "how_to" => [
        "create_key" => "/index.php?create_key=1&master=123456",
        "get_script" => "/index.php?get_script=1&key=ABC123"
    ]
]);
