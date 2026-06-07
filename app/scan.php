<!DOCTYPE html>
<html lang="ja">
<head>
<meta charset="UTF-8">
<title>QR読み取り</title>
<style>
body {
    font-family: sans-serif;
    text-align: center;
    padding: 30px;
}
#reader {
    width: 360px;
    margin: 20px auto;
}
.result {
    font-size: 28px;
    font-weight: bold;
    margin-top: 20px;
}
</style>
</head>
<body>

<h1>入口チェックイン</h1>

<div id="reader"></div>

<div class="result" id="result">QRを読み取ってください</div>

<script src="https://unpkg.com/html5-qrcode"></script>

<script>
function getNumberFromUrl(text) {
    try {
        const url = new URL(text);
        return url.searchParams.get("number");
    } catch {
        return null;
    }
}

async function checkin(number) {
    const res = await fetch("api.php?action=checkin&number=" + number);
    const data = await res.json();

    document.getElementById("result").textContent =
        data.ok ? data.message : "エラー: " + data.error;
}

function onScanSuccess(decodedText) {
    const number = getNumberFromUrl(decodedText);

    if (!number) {
        document.getElementById("result").textContent = "整理券QRではありません";
        return;
    }

    checkin(number);
}

const qrScanner = new Html5QrcodeScanner(
    "reader",
    { fps: 10, qrbox: 250 },
    false
);

qrScanner.render(onScanSuccess);
</script>

</body>
</html>