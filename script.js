if (isset($_GET["create_key"])) {
    $master = $_GET["master"] ?? "";

    if ($master !== $MASTER_KEY) {
        echo "AUTH_ERR|MASTER_INVALID";
        exit;
    }

    $newKey = genKey();

    $keys[$newKey] = [
        "created" => time(),
        "used" => false
    ];

    saveKeys($dbFile, $keys);

    echo "AUTH_SUCCESS|NEW_KEY=" . $newKey;
    exit;
}
