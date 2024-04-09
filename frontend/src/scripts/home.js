import {bindTaskForm} from "../functions/bindTaskForm.js";
import {getTextColorFromBG} from "../functions/ColorUtils.js";
import {setTaskStatus} from "../functions/setTaskStatus.js";
import {setInputValue} from "../functions/setInputValue.js";
import {deleteTask} from "../functions/deleteTask.js";

document.setTaskStatus = setTaskStatus;
document.setInputValue = setInputValue;
document.deleteTask = deleteTask;
document.getTextColorFromBG = getTextColorFromBG;

bindTaskForm();