import {bindTaskForm} from "../functions/bindTaskForm.js";
import {getTextColorFromBG} from "../functions/ColorUtils.js";
import {setTaskStatus} from "../functions/setTaskStatus.js";
import {setInputValue} from "../functions/setInputValue.js";
import {uploadFile} from "../functions/uploadFile.js";

document.setTaskStatus = setTaskStatus;
document.setInputValue = setInputValue;
document.uploadFile = uploadFile;
document.getTextColorFromBG = getTextColorFromBG;

bindTaskForm();