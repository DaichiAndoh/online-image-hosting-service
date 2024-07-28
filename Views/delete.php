<div style="width: 800px; margin: 0 auto;">
    <p><?= $message ?></p>
    <button style="margin-top: 25px;" onclick="returnTop()">Return Top Page</button>
</div>

<script>
    function returnTop() {
        window.location.href = "/";
    }
</script>
