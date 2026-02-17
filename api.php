<?php
header("Content-Type: application/json; charset=UTF-8");

$db_file = __DIR__ . "/db.json";
$db = json_decode(file_get_contents($db_file), true);

function save_db($db_file, $db) {
    file_put_contents($db_file, json_encode($db, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
}

function random_key($len = 12) {
    $chars = "ABCDEFGHJKLMNPQRSTUVWXYZ23456789";
    $out = "";
    for ($i=0; $i<$len; $i++) $out .= $chars[random_int(0, strlen($chars)-1)];
    return $out;
}

$action = $_GET["action"] ?? "";

if ($action === "check") {
    $key = $_GET["key"] ?? "";
    $ip  = $_SERVER["REMOTE_ADDR"];

    foreach ($db["keys"] as $k) {
        if ($k["key"] === $key) {
            if (strtotime($k["expire"]) < time()) {
                echo json_encode(["status"=>"expired"]);
                exit;
            }
            echo json_encode(["status"=>"ok", "expire"=>$k["expire"], "ip"=>$ip]);
            exit;
        }
    }
    echo json_encode(["status"=>"invalid"]);
    exit;
}

if ($action === "list") {
    $master = $_GET["master"] ?? "";
    if ($master !== $db["master_key"]) {
        echo json_encode(["status"=>"no"]);
        exit;
    }
    echo json_encode(["status"=>"ok", "keys"=>$db["keys"]]);
    exit;
}

if ($action === "create") {

    $master = $_GET["master"] ?? "";
    $expire = $_GET["expire"] ?? "";
    $custom = strtoupper(trim($_GET["custom"] ?? ""));

    if ($master !== $db["master_key"]) {
        echo json_encode(["status"=>"no"]);
        exit;
    }

    if (!$expire) {
        echo json_encode(["status"=>"error","msg"=>"Thiếu expire"]);
        exit;
    }

    if ($custom == "") {
        echo json_encode(["status"=>"error","msg"=>"Chưa nhập key"]);
        exit;
    }

    foreach ($db["keys"] as $k) {
        if ($k["key"] === $custom) {
            echo json_encode(["status"=>"error","msg"=>"Key đã tồn tại"]);
            exit;
        }
    }

    $new = [
        "key" => $custom,
        "expire" => $expire,
        "ip" => "Trống",
        "created" => date("Y-m-d")
    ];

    array_unshift($db["keys"], $new);
    save_db($db_file, $db);

    echo json_encode(["status"=>"ok", "new"=>$new]);
    exit;
}
}

if ($action === "delete") {
    $master = $_GET["master"] ?? "";
    $key = $_GET["key"] ?? "";

    if ($master !== $db["master_key"]) {
        echo json_encode(["status"=>"no"]);
        exit;
    }

    $db["keys"] = array_values(array_filter($db["keys"], function($k) use ($key) {
        return $k["key"] !== $key;
    }));

    save_db($db_file, $db);

    echo json_encode(["status"=>"ok"]);
    exit;
}

echo json_encode(["status"=>"error","msg"=>"unknown action"]);
