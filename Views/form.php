<div style="width: 800px; margin: 0 auto;">
    <div id="form">
        <h5>new image file</h5>
        <label for="file">choose file</label>
        <input type="file" id="file" accept=".jpg, .jpeg, .png, .gif" required>
        <br>
        <button onclick="uploadFile()" style="margin-top: 25px;">Upload</button>
    </div>

    <div id="image-info" style="display: none;">
        <h5>image file uploaded!</h5>
        <div style="margin-top: 20px;">
            <h6>URL for share</h6>
            <a id="share-url" href="#" target=“_blank” rel=”noreferrer noopener”></a>
        </div>
        <div style="margin-top: 20px;">
            <h6>URL for deletion</h6>
            <a id="delete-url" href="#" target=“_blank” rel=”noreferrer noopener”></a>
        </div>

        <button style="margin-top: 25px;" onclick="returnTop()">Return Top Page</button>
    </div>
</div>

<script>
    async function uploadFile() {
        const fileInput = document.getElementById('file');
        const file = fileInput.files[0];

        if (!file) {
            alert('File is not selected.');
            return;
        }

        const formData = new FormData();
        formData.append('file', file);

        try {
            const response = await fetch('/create', {
                method: 'POST',
                body: formData
            });

            const resData = await response.json();

            if (resData.error) {
                alert(resData.error);
            } else {
                changeView(resData.shareUrl, resData.deleteUrl);
            }
        } catch (error) {
            console.error('Error:', error);
        }
    }

    function returnTop() {
        if (confirm('❗️This information will never appear again.')) {
            window.location.reload();
        }
    }

    function changeView(shareUrl, deleteUrl) {
        const formBlock = document.getElementById('form');
        formBlock.style.display = 'none';

        const shareUrlEl = document.getElementById('share-url');
        shareUrlEl.href = shareUrl;
        shareUrlEl.innerText = shareUrl;
        const deleteUrlEl = document.getElementById('delete-url');
        deleteUrlEl.href = deleteUrl;
        deleteUrlEl.innerText = deleteUrl;
        const imageInfoBlock = document.getElementById('image-info');
        imageInfoBlock.style.display = 'block';
    }
</script>
