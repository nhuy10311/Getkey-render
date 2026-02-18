script_name("AutoWalk AutoY")
script_author("KN_BOSS")

require "lib.moonloader"
local imgui = require "mimgui"
local json = require "dkjson"
local http = require("socket.http")
local ffi = require("ffi")

-- ====== API CHECK KEY (PHP Render) ======
local API = "https://TENRENDER.onrender.com/api.php?check_key="

-- ====== CONFIG ======
local config_path = getWorkingDirectory() .. "\\config\\AutoWalk_KN.json"
local spamTime = 1500

-- ====== UI ======
local showLogin = imgui.new.bool(true)
local showMenu  = imgui.new.bool(false)

local keyInput = imgui.new.char[128]("")
local statusText = "Nhap key de vao menu..."

-- ====== AUTOWALK ======
local running = false
local points = {}
local idx = 1

-- ====== SAVE/LOAD ======
function saveConfig()
    if not doesDirectoryExist(getWorkingDirectory() .. "\\config") then
        createDirectory(getWorkingDirectory() .. "\\config")
    end
    local f = io.open(config_path, "w")
    if f then
        f:write(json.encode(points))
        f:close()
        sampAddChatMessage("{d946ef}[KN]: {ffffff}Da luu toa do!", -1)
    end
end

function loadConfig()
    local f = io.open(config_path, "r")
    if f then
        local c = f:read("*a")
        f:close()
        points = json.decode(c) or {}
        sampAddChatMessage("{d946ef}[KN]: {ffffff}Da tai toa do!", -1)
    end
end

-- ====== SEND Y ======
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

-- ====== CHECK KEY ======
local function checkKey(key)
    local url = API .. key
    local body, code = http.request(url)

    if not body then
        return false, "Loi ket noi API!"
    end

    if tostring(body):find("AUTH_OK") then
        return true, "Login thanh cong!"
    end

    return false, "Sai key!"
end

-- ====== LOGIN UI ======
imgui.OnFrame(function() return showLogin[0] end, function()
    imgui.Begin("LOGIN KEY - BOSS KN", showLogin)

    imgui.Text("Nhap key de vao menu AutoWalk")
    imgui.InputText("KEY", keyInput, 128)
    imgui.Separator()

    if imgui.Button("LOGIN") then
        local k = ffi.string(keyInput)

        if k == "" then
            statusText = "Chua nhap key!"
        else
            statusText = "Dang check key..."
            lua_thread.create(function()
                local ok, msg = checkKey(k)

                if ok then
                    statusText = msg
                    wait(200)

                    -- ====== ĐÂY NÈ: LOGIN OK -> HIỆN MENU ======
                    showLogin[0] = false
                    showMenu[0] = true

                    sampAddChatMessage("{00ff88}[KN]: {ffffff}Login thanh cong! Menu da bat.", -1)
                else
                    statusText = msg
                    sampAddChatMessage("{ff4444}[KN]: {ffffff}"..msg, -1)
                end
            end)
        end
    end

    imgui.Separator()
    imgui.Text(statusText)

    imgui.End()
end)

-- ====== MENU AUTOWALK UI ======
imgui.OnFrame(function() return showMenu[0] end, function()
    imgui.Begin("AutoWalk AutoY - BOSS KN", showMenu)

    imgui.Text("Points: "..#points)
    imgui.Text("Current: "..idx)
    imgui.Text(running and "STATUS: RUNNING" or "STATUS: STOPPED")

    if imgui.Button("Add Point") then
        table.insert(points,{getCharCoordinates(PLAYER_PED)})
    end

    if imgui.Button("START") then
        if #points>0 then
            running, idx = true, 1
        end
    end

    if imgui.Button("STOP") then
        running = false
        setGameKeyState(1,0)
    end

    if imgui.Button("CLEAR") then
        points = {}
    end

    imgui.Separator()

    if imgui.Button("SAVE CONFIG") then saveConfig() end
    imgui.SameLine()
    if imgui.Button("LOAD CONFIG") then loadConfig() end

    imgui.End()
end)

function main()
    repeat wait(0) until isSampAvailable()

    loadConfig()

    -- mở login trước
    showLogin[0] = true
    showMenu[0] = false

    -- lệnh bật/tắt UI
    sampRegisterChatCommand("awui", function()
        if showMenu[0] then
            showMenu[0] = not showMenu[0]
        else
            showLogin[0] = not showLogin[0]
        end
    end)

    while true do
        wait(0)
        if running and #points > 0 then
            if walk(points[idx]) then
                local t = os.clock()
                while os.clock()-t < spamTime/1000 do
                    sendY()
                    wait(120)
                end
                idx = (idx % #points) + 1
            end
        end
    end
end

lua_thread.create(main)
