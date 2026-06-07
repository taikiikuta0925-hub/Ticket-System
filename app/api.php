<?php
header("Content-Type: application/json; charset=utf-8");

$file = __DIR__ . "/data.json";

if (!file_exists($file)) {
    file_put_contents($file, json_encode([
        "last_number" => 0,
        "current_number" => 0,
        "tickets" => []
    ], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
}

$data = json_decode(file_get_contents($file), true);
$action = $_GET["action"] ?? "";

function saveData($file, $data) {
    file_put_contents($file, json_encode($data, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
}

if ($action === "create") {
    $data["last_number"]++;

    $ticket = [
        "number" => $data["last_number"],
        "status" => "waiting",
        "created_at" => date("Y-m-d H:i:s")
    ];

    $data["tickets"][] = $ticket;
    saveData($file, $data);

    echo json_encode(["ok" => true, "ticket" => $ticket], JSON_UNESCAPED_UNICODE);
    exit;
}

if ($action === "next") {
    $data["current_number"]++;

    foreach ($data["tickets"] as &$ticket) {
        if ($ticket["number"] == $data["current_number"]) {
            $ticket["status"] = "called";
        }
    }

    saveData($file, $data);

    echo json_encode(["ok" => true, "current_number" => $data["current_number"]], JSON_UNESCAPED_UNICODE);
    exit;
}
if ($action === "checkin") {
    $number = $_GET["number"] ?? null;

    if ($number === null) {
        echo json_encode(["ok" => false, "error" => "number required"], JSON_UNESCAPED_UNICODE);
        exit;
    }

    foreach ($data["tickets"] as &$ticket) {
        if ($ticket["number"] == $number) {
            $ticket["status"] = "checked_in";
            $ticket["checked_in_at"] = date("Y-m-d H:i:s");

            saveData($file, $data);

            echo json_encode([
                "ok" => true,
                "message" => $number . "番 チェックイン完了",
                "ticket" => $ticket
            ], JSON_UNESCAPED_UNICODE);
            exit;
        }
    }

    echo json_encode(["ok" => false, "error" => "ticket not found"], JSON_UNESCAPED_UNICODE);
    exit;
}
if ($action === "status") {
    $number = $_GET["number"] ?? null;
    $myTicket = null;

    if ($number !== null) {
        foreach ($data["tickets"] as $ticket) {
            if ($ticket["number"] == $number) {
                $myTicket = $ticket;
                break;
            }
        }
    }

    echo json_encode([
        "ok" => true,
        "last_number" => $data["last_number"],
        "current_number" => $data["current_number"],
        "waiting_count" => max(0, $data["last_number"] - $data["current_number"]),
        "ticket" => $myTicket
    ], JSON_UNESCAPED_UNICODE);
    exit;
}

echo json_encode(["ok" => false, "error" => "unknown action"], JSON_UNESCAPED_UNICODE);