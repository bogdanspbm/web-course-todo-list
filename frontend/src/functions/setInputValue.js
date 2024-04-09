export async function setInputValue(input, key) {
    const value = getInputValue(input);
    const uid = document.getElementById('task-uid').value;

    const process = document.getElementById('progress-loader');
    const successProcess = document.getElementById('progress-loader-success');
    const failedProcess = document.getElementById('progress-loader-failed');

    process.style.display = "block";
    successProcess.style.display = "none";
    failedProcess.style.display = "none";

    try {
        const response = await fetch('https://todo.madzhuga.com/api/postgres/rest/update_field.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded'
            },
            body: `uid=${uid}&key=${key}&value=${encodeURIComponent(value)}&idToken=${encodeURIComponent(getCookie('idToken'))}`
        });

        if (!response.ok) {
            process.style.display = "none";
            successProcess.style.display = "none";
            failedProcess.style.display = undefined;
            throw new Error('Request failed with status ' + response.status);
        }

        const data = await response.json();
        if (!data.success) {
            process.style.display = "none";
            successProcess.style.display = "none";
            failedProcess.style.display = "block";
            throw new Error(data.message);
        }

        // Обновление поля выполнено успешно
        process.style.display = "none";
        successProcess.style.display = "block";
        failedProcess.style.display = "none";
    } catch (error) {
        process.style.display = "none";
        successProcess.style.display = "none";
        failedProcess.style.display = "block";
    }
}

function getInputValue(input) {
    if (input.tagName === 'SELECT') {
        return input.options[input.selectedIndex].value;
    } else {
        return input.value;
    }
}


function getCookie(name) {
    const value = `; ${document.cookie}`;
    const parts = value.split(`; ${name}=`);
    if (parts.length === 2) return parts.pop().split(';').shift();
}
