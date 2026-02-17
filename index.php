<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>BLACK CAT VIP</title>
  <link rel="stylesheet" href="style.css">
</head>

<body>
  <div class="wrap">
    <div class="card">

      <div class="logo">
        <div class="icon">😀</div>
        <h1>BLACK CAT VIP</h1>
        <p>AUTOWALK SYSTEM ACTIVE</p>
      </div>

      <input id="master" placeholder="Nhập master key..." />

      <select id="expire">
        <option value="1 ngày">1 ngày</option>
        <option value="3 ngày">3 ngày</option>
        <option value="7 ngày">7 ngày</option>
        <option value="30 ngày">30 ngày</option>
        <option value="999 ngày">999 ngày</option>
      </select>

      <!-- Ô nhập key tự tạo -->
      <input id="custom" placeholder="Nhập key muốn tạo..." />

      <button onclick="createKey()">TẠO KEY MỚI</button>

      <div class="list-title">BẢNG KEY</div>
      <div id="list" class="list">Chưa có key</div>

    </div>
  </div>

  <script src="script.js"></script>
</body>
</html>
