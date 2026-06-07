<?php
$number = $_GET["number"] ?? "不明";
?>
<!DOCTYPE html>
<html lang="ja">
<head>
<meta charset="UTF-8">
<title>整理券</title>
<style>
body {
    font-family: sans-serif;
    text-align: center;
    padding: 40px;
}
.card {
    max-width: 420px;
    margin: auto;
    padding: 30px;
    border-radius: 24px;
    box-shadow: 0 0 20px #ddd;
}
.ticket {
    font-size: 90px;
    font-weight: bold;
}
.info {
    font-size: 28px;
    margin: 18px;
}
.note {
    margin-top: 20px;
    color: #666;
}
</style>
</head>
<body>

<div class="card">
    <h1>あなたの整理番号</h1>

    <h2 id="message">待機中です</h2>

    <div class="ticket"><?php echo htmlspecialchars($number); ?></div>

    <div class="info">
        現在案内中：<span id="current">-</span>
    </div>

    <div class="info">
        あと <span id="wait">-</span> 組
    </div>

    <div class="info">
        目安：約<span id="minutes">-</span>分
    </div>

    <canvas id="qr"></canvas>

    <div class="note">
        この画面を保存してお待ちください
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/qrcode/build/qrcode.min.js"></script>

<script>
const myNumber = Number("<?php echo htmlspecialchars($number); ?>");
const myUrl = location.href;

QRCode.toCanvas(document.getElementById("qr"), myUrl);

async function loadStatus() {
    const res = await fetch("api.php?action=status");
    const data = await res.json();

    const current = data.current_number;
    const wait = Math.max(0, myNumber - current);

    document.getElementById("current").textContent = current;
    document.getElementById("wait").textContent = wait;
    document.getElementById("minutes").textContent = wait * 5;
    const message = document.getElementById("message");
    const card = document.querySelector(".card");
    
if (data.ticket && data.ticket.status === "checked_in") {

    message.textContent = "入場済みです";
    card.classList.remove("called");

} else if (current >= myNumber) {

    message.textContent = "あなたの順番です。入口へお越しください";
    card.classList.add("called");

} else {

    message.textContent = "待機中です";
    card.classList.remove("called");
}
}

loadStatus();
setInterval(loadStatus, 5000);
</script>

</body>
</html>