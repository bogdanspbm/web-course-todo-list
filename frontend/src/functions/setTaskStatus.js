export async function setTaskStatus(checkbox, uid) {
    const checked = checkbox.checked;

    // Блокируем checkbox на время выполнения запроса
    checkbox.disabled = true;

    try {
        const response = await fetch('https://todo.madzhuga.com/api/redis/rest/set_task_completion.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded'
            },
            body: `uid=${uid}&status=${checked}&idToken=${encodeURIComponent(getCookie('idToken'))}`
        });

        if (!response.ok) {
            // Если статус ответа не 200, возвращаем checkbox в исходное положение
            checkbox.checked = !checked;
            throw new Error('Request failed with status ' + response.status);
        }

        const data = await response.json();
        if (!data.success) {
            // Если запрос не удался, возвращаем checkbox в исходное положение
            checkbox.checked = !checked;
            throw new Error(data.message);
        }
        

        // Успешный запрос, ничего не делаем, т.к. checkbox остается в новом состоянии
    } catch (error) {
        console.error('Error:', error.message);
    } finally {
        // После выполнения запроса разблокируем checkbox
        checkbox.disabled = false;
    }
}

function getCookie(name) {
    const value = `; ${document.cookie}`;
    const parts = value.split(`; ${name}=`);
    if (parts.length === 2) return parts.pop().split(';').shift();
}