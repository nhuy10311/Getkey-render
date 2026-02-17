<?php
header("Content-Type: text/plain; charset=UTF-8");

$DATA_FILE = __DIR__ . "/keys.json";

/* ====== LOAD KEYS ====== */
function load_keys($file) {
    if (!file_exists($file)) return [];
    $json = file_get_contents($file);
    $data = json_decode($json, true);
    if (!is_array($data)) return [];
    return $data;
}

/* ====== CHECK KEY ====== */
function check_key($key, $keys) {
    if (!isset($keys[$key])) return "INVALID";

    $info = $keys[$key];

    // nếu có expire
    if (isset($info["expire"]) && is_numeric($info["expire"])) {
        if (time() > intval($info["expire"])) return "EXPIRED";
    }

    return "OK";
}

/* ====== LUA MENU CODE (CỦA M) ====== */
$LUA_CODE = <<<'LUA'
-- [[ 🛡️ TRỌN BỘ SCRIPT AUTOWALK CHUẨN BOSS KN ]]
script_name("AutoWalk AutoY")
script_author("KN_BOSS")

require "lib.moonloader"
local imgui = require "mimgui"
local json = require "dkjson"

local config_path = getWorkingDirectory() .. "\\config\\AutoWalk_KN.json"
local spamTime = 1500 
local show = imgui.new.bool(true)
local running = false
local points = {}
local idx = 1

function saveConfig()
    if not doesDirectoryExist(getWorkingDirectory() .. "\\config") then createDirectory(getWorkingDirectory() .. "\\config") end
    local f = io.open(config_path, "w")
    if f then f:write(json.encode(points)) f:close() sampAddChatMessage("{d946ef}[KN]: {ffffff}Da luu toa do!", -1) end
end

function loadConfig()
    local f = io.open(config_path, "r")
    if f then local c = f:read("*a") f:close() points = json.decode(c) or {} sampAddChatMessage("{d946ef}[KN]: {ffffff}Da tai toa do!", -1) end
end

function sendY()
    local pId = select(2, sampGetPlayerIdByCharHandle(PLAYER_PED))
    local m = allocateMemory(68)
    sampStorePlayerOnfootData(pId, m)
    setStructElement(m, 36, 1, 64, false)
    sampSendOnfootData(m)
    freeMemory(m)
end

local function walk(p)
    local x,y,z = getCharCoordinates(PLAYER_PED)
    local dx, dy = p[1]-x, p[2]-y
    local dist = math.sqrt(dx*dx+dy*dy)
    if dist > 1.2 then
        setCharHeading(PLAYER_PED, math.deg(math.atan2(-dx, dy)))
        setGameKeyState(1, 255)
        return false
    else
        setGameKeyState(1, 0)
        return true
    end
end

imgui.OnFrame(function() return show[0] end, function()
    imgui.Begin("AutoWalk AutoY - BOSS KN", show)
    imgui.Text("Points: "..#points)
    imgui.Text("Current: "..idx)
    imgui.Text(running and "STATUS: RUNNING" or "STATUS: STOPPED")
    if imgui.Button("Add Point") then table.insert(points,{getCharCoordinates(PLAYER_PED)}) end
    if imgui.Button("START") then if #points>0 then running, idx = true, 1 end end
    if imgui.Button("STOP") then running = false setGameKeyState(1,0) end
    if imgui.Button("CLEAR") then points = {} end
    imgui.Separator()
    if imgui.Button("SAVE CONFIG") then saveConfig() end
    imgui.SameLine()
    if imgui.Button("LOAD CONFIG") then loadConfig() end
    imgui.End()
end)

function main()
    repeat wait(0) until isSampAvailable()
    loadConfig()
    show[0] = true -- Ép hiện menu ngay khi login xong
    sampRegisterChatCommand("awui", function() show[0]=not show[0] end)
    while true do
        wait(0)
        if running and #points > 0 then
            if walk(points[idx]) then
                local t = os.clock()
                while os.clock()-t < spamTime/1000 do sendY() wait(120) end
                idx = (idx % #points) + 1
            end
        end
    end
end

-- KÍCH HOẠT SCRIPT
lua_thread.create(main)
LUA;

/* ====== MAIN ROUTE ====== */
if (isset($_GET["check_key"])) {
    $key = trim($_GET["check_key"]);
    $keys = load_keys($DATA_FILE);
    $st = check_key($key, $keys);

    if ($st === "OK") {
        echo "AUTH_SUCCESS|" . $LUA_CODE;
    } else if ($st === "EXPIRED") {
        echo "AUTH_ERR|EXPIRED";
    } else {
        echo "AUTH_ERR|INVALID";
    }
    exit;
}

echo "OK";
