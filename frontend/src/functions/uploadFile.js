export async function uploadFile(input) {
    const uid = document.getElementById('task-uid').value;
    const process = document.getElementById('progress-loader');
    const successProcess = document.getElementById('progress-loader-success');
    const failedProcess = document.getElementById('progress-loader-failed');

    process.style.display = "block";
    successProcess.style.display = "none";
    failedProcess.style.display = "none";

    if (input.files.length > 0) {
        const file = input.files[0];
        const formData = new FormData();
        formData.append('file', file);
        formData.append('uid', uid);
        formData.append('_method', 'PUT');

        try {
            const response = await fetch('https://todo.madzhuga.com/api/postgres/rest/update_file.php', {
                method: 'POST', // Изменено на POST
                body: formData
            });

            if (!response.ok) {
                throw new Error('Request failed with status ' + response.status);
            }

            const data = await response.json();

            if (!data.success) {
                throw new Error(data.message);
            }

            document.getElementById('upload-file-button').innerText = `${file.name}`;
            process.style.display = "none";
            successProcess.style.display = "block";
            failedProcess.style.display = "none";
        } catch (error) {
            console.error(error);
            process.style.display = "none";
            successProcess.style.display = "none";
            failedProcess.style.display = "block";
        }
    }
}
