// Hiển thị ngày tháng
window.onload = function() {
    const dateElement = document.getElementById('current-date');
    const today = new Date();
    const day = today.getDate();
    const month = today.getMonth() + 1; // Tháng bắt đầu từ 0
    const year = today.getFullYear();

    const currentDate = `API Key Ngày : ${day} / ${month} / ${year}`;
    dateElement.textContent = currentDate;
};

// Hàm lấy key từ API
function getKey() {
    const resultContainer = document.getElementById('key-result');
    const loading = document.getElementById('loading');
    const copyButton = document.getElementById('copy-key-btn');
    
    // Hiển thị loading
    loading.style.display = 'block';
    resultContainer.innerText = '';
    copyButton.style.display = 'none'; // Ẩn nút copy trong khi chờ key

    // Gọi API lấy key (giả sử PHP xử lý việc này)
    fetch('getkey.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
    })
    .then(response => response.json())
    .then(data => {
        // Ẩn loading
        loading.style.display = 'none';

        if (data.success) {
            resultContainer.innerText = `DQuanDev - Key của bạn là: ${data.key}`;
            copyButton.style.display = 'inline-block'; // Hiển thị nút copy khi có key
        } else {
            resultContainer.innerText = `DQuanDev - Lỗi: Không thể lấy key.`;
        }
    })
    .catch(error => {
        loading.style.display = 'none';
        resultContainer.innerText = `DQuanDev - Lỗi: ${error.message}`;
    });
}

// Hàm copy key vào clipboard
function copyKey() {
    const resultContainer = document.getElementById('key-result');
    const key = resultContainer.innerText.replace('DQuanDev - Key của bạn là: ', '').trim();

    if (key) {
        // Tạo một input tạm thời để copy key
        const tempInput = document.createElement('input');
        document.body.appendChild(tempInput);
        tempInput.value = key;
        tempInput.select();
        document.execCommand('copy');
        document.body.removeChild(tempInput);

        // Thông báo người dùng đã copy thành công
        alert('DQuanDev - Key đã được copy vào clipboard!');
    }
}
