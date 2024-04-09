import {bindTaskForm} from "../functions/bindTaskForm.js";
import {getTextColorFromBG} from "../functions/ColorUtils.js";
import {setTaskStatus} from "../functions/setTaskStatus.js";

document.setTaskStatus = setTaskStatus;
document.getTextColorFromBG = getTextColorFromBG;

bindTaskForm();