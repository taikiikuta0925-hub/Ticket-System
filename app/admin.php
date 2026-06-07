<!DOCTYPE html>
<html lang="ja">
<head>
<meta charset="UTF-8">
<title>ホスト管理</title>
<style>
body {
    font-family: sans-serif;
    text-align: center;
    padding: 50px;
}
.card {
    max-width: 500px;
    margin: auto;
    padding: 30px;
    border-radius: 24px;
    box-shadow: 0 0 20px #ddd;
}
.number {
    font-size: 80px;
    font-weight: bold;
}
button {
    font-size: 28px;
    padding: 18px 40px;
    margin: 10px;
    border-radius: 18px;
}
.url {
    margin-top: 20px;
    word-break: break-all;
}
</style>
</head>
<body>

<div class="card">
    <h1>整理券管理</h1>

    <p>現在案内中</p>
    <div class="number" id="current">0</div>

    <button onclick="createTicket()">整理券発行</button>
    <button onclick="callNext()">次を呼ぶ</button>

    <h2 id="ticketNumber"></h2>
    <div class="url" id="ticketUrl"></div>
</div>

<script>
async function loadStatus() {
    const res = await fetch("api.php?action=status");
    const data = await res.json();
    document.getElementById("current").textContent = data.current_number;
}

async function createTicket() {
    const res = await fetch("api.php?action=create");
    const data = await res.json();

    const number = data.ticket.number;
    const url = location.origin + "/ticket.php?number=" + number;

    document.getElementById("ticketNumber").textContent = "整理番号 " + number;
    document.getElementById("ticketUrl").textContent = url;

    QRCode.toCanvas(document.getElementById("qr"), url);
    
    loadStatus();
}

async function callNext() {
    await fetch("api.php?action=next");
    loadStatus();
}

loadStatus();
</script>

<canvas id="qr"></canvas>

<script src="https://cdn.jsdelivr.net/npm/qrcode/build/qrcode.min.js"></script>

</body>
</html>