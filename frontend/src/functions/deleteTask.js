export async function deleteTask(uid) {
    const container = document.getElementById(`container-${uid}`);
    const taskControl = document.getElementById(`task-control-${uid}`);
    const loader = document.getElementById(`progress-loader-${uid}`);

    taskControl.style.display = 'none';
    loader.style.display = 'block';

    try {
        const response = await fetch('https://todo.madzhuga.com/api/postgres/rest/delete_task.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded'
            },
            body: `uid=${uid}&idToken=${encodeURIComponent(getCookie('idToken'))}`
        });

        if (!response.ok) {
            taskControl.style.display = 'block';
            loader.style.display = 'none';
            throw new Error('Request failed with status ' + response.status);
        }

        const data = await response.json();
        if (!data.success) {
            taskControl.style.display = 'block';
            loader.style.display = 'none';
            throw new Error(data.message);
        }

        // Задача успешно удалена
        container.style.maxHeight = "0px";

        const dateClass = container.classList[0];
        const sameElements = document.getElementsByClassName(dateClass);

        if(dateClass.includes("date") && sameElements.length === 1){
            const dateHeader = document.getElementById(dateClass);
            if(dateHeader){
                dateHeader.remove();
            }
        }

        await new Promise(r => setTimeout(r, 1000));

        container.remove();

    } catch (error) {
        taskControl.style.display = 'block';
        loader.style.display = 'none';
        console.error('Error:', error.message);
        // Обработка ошибки при удалении задачи
    }
}

function getCookie(name) {
    const value = `; ${document.cookie}`;
    const parts = value.split(`; ${name}=`);
    if (parts.length === 2) return parts.pop().split(';').shift();
}
