<?php
?>
<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>KN GETKEY + VIEW MENU</title>
  <style>
    body{
      font-family: Arial, sans-serif;
      background:#0b0f14;
      color:#fff;
      display:flex;
      justify-content:center;
      align-items:center;
      min-height:100vh;
      margin:0;
      padding:18px;
    }
    .box{
      width:520px;
      background:#121826;
      border-radius:18px;
      padding:22px;
      box-shadow:0 0 20px rgba(0,0,0,0.4);
    }
    h1{font-size:20px;margin:0 0 8px}
    p{opacity:.8;margin:0 0 16px;font-size:14px;line-height:1.4}
    input{
      width:100%;
      padding:12px;
      border-radius:12px;
      border:none;
      outline:none;
      margin:8px 0;
      background:#0b0f14;
      color:#fff;
      font-size:14px;
    }
    button{
      width:100%;
      padding:12px;
      border-radius:12px;
      border:none;
      cursor:pointer;
      background:#00ffd5;
      font-weight:bold;
      margin-top:10px;
      font-size:15px;
    }
    button:hover{opacity:.9}
    .out{
      background:#0b0f14;
      padding:12px;
      border-radius:12px;
      margin-top:12px;
      white-space:pre-wrap;
      word-break:break-word;
      font-family: monospace;
      font-size:13px;
      max-height:320px;
      overflow:auto;
    }
    .row{
      display:flex;
      gap:10px;
    }
    .row button{
      width:50%;
    }
  </style>
</head>
<body>
  <div class="box">
    <h1>🛡️ KN SYSTEM</h1>
    <p>
      Nhập KEY để xem code menu.lua trực tiếp trên web.<br>
      (Dùng cho test / copy)
    </p>

    <input id="key" placeholder="Nhập KEY..." type="text">
    <div class="row">
      <button onclick="loginKey()">LOGIN</button>
      <button onclick="copyCode()">COPY CODE</button>
    </div>

    <div id="out" class="out" style="display:none;"></div>
  </div>

<script>
let lastCode = "";

async function loginKey(){
  const key = document.getElementById("key").value.trim();
  if(!key){
    alert("Nhập key trước đã!");
    return;
  }

  const res = await fetch("/api.php?check_key=" + encodeURIComponent(key));
  const text = await res.text();

  const out = document.getElementById("out");
  out.style.display = "block";

  if(text.startsWith("AUTH_SUCCESS|")){
    lastCode = text.replace("AUTH_SUCCESS|", "");
    out.innerText = lastCode;
  } else {
    lastCode = "";
    out.innerText = text;
  }
}

function copyCode(){
  if(!lastCode){
    alert("Chưa có code để copy!");
    return;
  }
  navigator.clipboard.writeText(lastCode);
  alert("Đã copy code menu.lua!");
}
</script>
</body>
</html>
