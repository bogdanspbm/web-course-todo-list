export function downloadFile(uid, fileName) {
    const token = getCookie('idToken'); // Функция для получения значения cookie
    const url = `https://todo.madzhuga.com/api/postgres/rest/download_file.php?uid=${encodeURIComponent(uid)}&token=${encodeURIComponent(token)}`;

    fetch(url, {
        method: 'GET'
    })
        .then(response => {
            if (!response.ok) {
                throw new Error('Network response was not ok.');
            }
            return response.blob();
        })
        .then(blob => {
            const url = window.URL.createObjectURL(blob);
            const a = document.createElement('a');
            a.style.display = 'none';
            a.href = url;
            // Используем предоставленное имя файла для атрибута download
            a.download = fileName || 'default_filename'; // Используйте предоставленное имя файла, иначе дефолтное
            document.body.appendChild(a);
            a.click();
            window.URL.revokeObjectURL(url);
            alert('Your file has started downloading...');
        })
        .catch(error => {
            console.error('There has been a problem with your fetch operation:', error);
        });
}

function getCookie(name) {
    const value = `; ${document.cookie}`;
    const parts = value.split(`; ${name}=`);
    if (parts.length === 2) return parts.pop().split(';').shift();
    return null;
}
