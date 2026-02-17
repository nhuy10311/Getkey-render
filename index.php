<?php
// API check key kiểu m muốn: /index.php?check_key=XXXX
if (isset($_GET["check_key"])) {
    $key = $_GET["check_key"];
    $url = "api.php?action=check&key=" . urlencode($key);
    header("Content-Type: text/plain; charset=UTF-8");
    $res = file_get_contents($url);

    $json = json_decode($res, true);
    if (!$json) { echo "ERROR"; exit; }

    if ($json["status"] === "ok") echo "OK";
    else if ($json["status"] === "expired") echo "EXPIRED";
    else echo "INVALID";
    exit;
}
?>
<!doctype html>
<html>
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width,initial-scale=1" />
  <title>BLACK CAT VIP</title>
  <link rel="stylesheet" href="style.css">
</head>
<body>
  <div class="wrap">
    <div class="card">
      <div class="avatar">
        <img src="https://i.imgur.com/2yaf2wb.jpeg">
      </div>
      <div class="title">BLACK CAT VIP</div>
      <div class="sub">AUTOWALK SYSTEM ACTIVE</div>

      <input id="master" placeholder="Nhập master key..." />
      <select id="expire">
        <option value="">Chọn hạn</option>
        <option value="<?=date('Y-m-d', strtotime('+1 day'))?>">1 ngày</option>
        <option value="<?=date('Y-m-d', strtotime('+7 day'))?>">7 ngày</option>
        <option value="<?=date('Y-m-d', strtotime('+30 day'))?>">30 ngày</option>
        <option value="<?=date('Y-m-d', strtotime('+365 day'))?>">1 năm</option>
      </select>

      <button onclick="createKey()">TẠO KEY MỚI</button>

      <div class="list" id="list"></div>

      <div class="footer">BẢNG KEY</div>
    </div>
  </div>

<script src="script.js"></script>
</body>
</html>
