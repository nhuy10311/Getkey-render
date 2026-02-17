async function api(url){
  let res = await fetch(url);
  return await res.json();
}

async function loadList(){
  let master = document.getElementById("master").value.trim();
  if(!master){
    document.getElementById("list").innerHTML = "Chưa có key";
    return;
  }

  let data = await api("api.php?action=list&master=" + encodeURIComponent(master));

  if(data.status !== "ok"){
    document.getElementById("list").innerHTML = "Sai master key!";
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
  let expire = document.getElementById("expire").value;
  let custom = document.getElementById("custom").value.trim();

  if(!master) return alert("Nhập master key!");
  if(!expire) return alert("Chọn hạn!");
  if(!custom) return alert("Nhập key muốn tạo!");

  let data = await api(
    "api.php?action=create&master=" + encodeURIComponent(master) +
    "&expire=" + encodeURIComponent(expire) +
    "&custom=" + encodeURIComponent(custom)
  );

  if(data.status !== "ok"){
    if(data.status === "error") return alert(data.msg || "Lỗi!");
    if(data.status === "exists") return alert("Key đã tồn tại!");
    return alert("Sai master key!");
  }

  document.getElementById("custom").value = "";
  await loadList();
  alert("Tạo key thành công: " + data.new.key);
}

document.getElementById("master").addEventListener("input", function(){
  loadList();
});
