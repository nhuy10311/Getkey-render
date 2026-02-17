<?php
header("Content-Type: application/json; charset=utf-8");

$db_file = __DIR__ . "/db.json";
$db = json_decode(file_get_contents($db_file), true);

function save_db($db_file, $db){
  file_put_contents($db_file, json_encode($db, JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE));
}

$action = $_GET["action"] ?? "";

/* CHECK KEY (tool dùng) */
if($action === "check"){
  $key = $_GET["key"] ?? "";
  $ip = $_SERVER["REMOTE_ADDR"];

  foreach($db["keys"] as $k){
    if($k["key"] === $key){
      echo json_encode(["status"=>"ok", "expire"=>$k["expire"]]);
      exit;
    }
  }

  echo json_encode(["status"=>"invalid"]);
  exit;
}

/* LIST KEY */
if($action === "list"){
  $master = $_GET["master"] ?? "";

  if($master !== $db["master_key"]){
    echo json_encode(["status"=>"no"]);
    exit;
  }

  echo json_encode(["status"=>"ok", "keys"=>$db["keys"]]);
  exit;
}

/* CREATE KEY (tự nhập custom) */
if($action === "create"){

  $master = $_GET["master"] ?? "";
  $expire = $_GET["expire"] ?? "";
  $custom = strtoupper(trim($_GET["custom"] ?? ""));

  if($master !== $db["master_key"]){
    echo json_encode(["status"=>"no"]);
    exit;
  }

  if(!$expire){
    echo json_encode(["status"=>"error","msg"=>"Thiếu expire"]);
    exit;
  }

  if($custom === ""){
    echo json_encode(["status"=>"error","msg"=>"Chưa nhập key"]);
    exit;
  }

  foreach($db["keys"] as $k){
    if($k["key"] === $custom){
      echo json_encode(["status"=>"exists"]);
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

echo json_encode(["status"=>"error","msg"=>"Sai action"]);
