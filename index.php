<?php ?>
<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>NHUY VIP - GETKEY</title>

  <link rel="stylesheet" href="style.css" />
</head>

<body>
  <div class="wrap">
    <div class="card">
      <div class="logo">
        <img src="logo.png" alt="Nhuy" />
      </div>

      <h1 class="title">NHUY VIP</h1>
      <div class="sub">AUTOWALK SYSTEM ACTIVE</div>

      <input id="master" class="input" placeholder="Nhập master key..." />
      
      <select id="expire" class="input">
        <option value="1 ngày">1 ngày</option>
        <option value="3 ngày">3 ngày</option>
        <option value="7 ngày">7 ngày</option>
        <option value="30 ngày">30 ngày</option>
      </select>

      <input id="custom" class="input" placeholder="Nhập key muốn tạo (VD: NHUYVIP123)" />

      <button class="btn" onclick="createKey()">TẠO KEY MỚI</button>

      <div class="note" id="msg"></div>

      <div class="list-title">BẢNG KEY</div>
      <div id="list" class="list">Chưa có key</div>
    </div>
  </div>

  <script src="script.js"></script>
</body>
</html>
