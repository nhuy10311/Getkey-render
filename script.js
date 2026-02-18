async function api(url){
  let res = await fetch(url);
  return await res.json();
}

function setMsg(text){
  document.getElementById("msg").innerText = text || "";
}

async function loadList(){
  let master = document.getElementById("master").value.trim();

  if(!master){
    document.getElementById("list").innerHTML = "Chưa có key";
    return;
  }

  let data = await api("api.php?action=list&master=" + encodeURIComponent(master));

  if(data.status !== "ok"){
    document.getElementById("list").innerHTML = "Sai master key hoặc không có quyền.";
    return;
  }

  let keys = data.keys || [];
  if(keys.length === 0){
    document.getElementById("list").innerHTML = "Chưa có key";
    return;
  }

  let html = "";
  for(let k of keys){
    html += `
      <div class="row">
        <div class="k">${k.key}</div>
        <div class="e">${k.expire}</div>
      </div>
    `;
  }

  document.getElementById("list").innerHTML = html;
}

async function createKey(){
  let master = document.getElementById("master").value.trim();
  let expire = document.getElementById("expire").value.trim();
  let custom = document.getElementById("custom").value.trim();

  if(!master) return setMsg("Nhập master key!");
  if(!expire) return setMsg("Chọn hạn!");
  if(!custom) return setMsg("Nhập key muốn tạo!");

  setMsg("Đang tạo key...");

  let data = await api(
    "api.php?action=create&master=" + encodeURIComponent(master) +
    "&expire=" + encodeURIComponent(expire) +
    "&custom=" + encodeURIComponent(custom)
  );

  if(data.status !== "ok"){
    if(data.status === "error") return setMsg(data.msg || "Lỗi!");
    if(data.status === "exists") return setMsg("Key đã tồn tại!");
    return setMsg("Sai master key!");
  }

  document.getElementById("custom").value = "";
  setMsg("Tạo key thành công: " + data.new.key);

  await loadList();
}

document.getElementById("master").addEventListener("input", function(){
  loadList();
});
