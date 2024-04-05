export function bindTaskForm() {
    const cancelButton = document.getElementById("cancel");
    const form = document.getElementsByClassName("new-task")[0];
    const addTaskButton = document.getElementsByClassName("add-task-button")[0];
    const desc = document.getElementById("desc");

    cancelButton.addEventListener("click", () => {
        form.style.display = "none";
        desc.style.height = "14px";
        addTaskButton.style.display = "flex";
    });

    addTaskButton.addEventListener("click", () => {
        form.style.display = "flex";
        addTaskButton.style.display = "none"
    });

}