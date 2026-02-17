async function api(url){
  let r = await fetch(url);
  return await r.json();
}

async function loadList(){
  let master = document.getElementById("master").value.trim();
  if(!master) return;

  let data = await api("api.php?action=list&master="+encodeURIComponent(master));
  if(data.status !== "ok"){
    document.getElementById("list").innerHTML = "<div style='opacity:.5;text-align:center'>Sai master key</div>";
    return;
  }

  let html = "";
  data.keys.forEach(k=>{
    html += `
      <div class="keyitem">
        <div class="k">${k.key}</div>
        <div class="meta">Hết hạn: ${k.expire}</div>
        <div class="meta">IP: ${k.ip}</div>
        <div class="del" onclick="delKey('${k.key}')">XÓA</div>
      </div>
    `;
  });

  document.getElementById("list").innerHTML = html || "<div style='opacity:.5;text-align:center'>Chưa có key</div>";
}

async function createKey(){
  let master = document.getElementById("master").value.trim();
  let expire = document.getElementById("expire").value;

  if(!master) return alert("Nhập master key!");
  if(!expire) return alert("Chọn hạn!");

  let data = await api("api.php?action=create&master="+encodeURIComponent(master)+"&expire="+encodeURIComponent(expire));
  if(data.status !== "ok") return alert("Sai master key!");

  await loadList();
  alert("Tạo key thành công: "+data.new.key);
}

async function delKey(key){
  let master = document.getElementById("master").value.trim();
  if(!master) return alert("Nhập master key!");

  let ok = confirm("Xóa key: "+key+" ?");
  if(!ok) return;

  await api("api.php?action=delete&master="+encodeURIComponent(master)+"&key="+encodeURIComponent(key));
  await loadList();
}

document.getElementById("master").addEventListener("input", ()=>{
  clearTimeout(window.t);
  window.t = setTimeout(loadList, 400);
});
