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
        const body = new URLSearchParams();
        body.append('uid', uid);
        body.append('key', key);
        body.append('value', value);
        body.append('idToken', getCookie('idToken'));

        const response = await fetch('https://todo.madzhuga.com/api/postgres/rest/update_field.php', {
            method: 'PUT',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded'
            },
            body: body
        });

        if (!response.ok) {
            throw new Error('Request failed with status ' + response.status);
        }

        const data = await response.json();

        if (!data.success) {
            throw new Error(data.message);
        }

        // Update successful
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
