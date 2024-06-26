import {generateUID} from "./StringUtils.js";

export function bindTaskForm() {
    const cancelButton = document.getElementById("cancel");
    const form = document.getElementById("task-create-form");
    const uidPlate = document.getElementById("task-uid");
    const addTaskButton = document.getElementsByClassName("add-task-button")[0];
    const desc = document.getElementById("desc");

    if (cancelButton) {
        cancelButton.addEventListener("click", () => {
            form.style.display = "none";
            desc.style.height = "14px";
            addTaskButton.style.display = "flex";
        });
    }

    if (addTaskButton) {
        addTaskButton.addEventListener("click", () => {
            form.style.display = "flex";
            addTaskButton.style.display = "none"
            uidPlate.value = generateUID();
        });
    }

}