export function isContrastEnough(backgroundColor, textColor) {


    if (!backgroundColor) {
        return true
    }

    if (!textColor) {
        return true
    }

    // Удаляем символ # из значений цветов
    backgroundColor = backgroundColor.toLowerCase().replace('#', '');
    textColor = textColor.toLowerCase().replace('#', '');


    // Преобразуем значения цветов в числа
    var bgRed = parseInt(backgroundColor.substr(0, 2), 16);
    var bgGreen = parseInt(backgroundColor.substr(2, 2), 16);
    var bgBlue = parseInt(backgroundColor.substr(4, 2), 16);

    var textRed = parseInt(textColor.substr(0, 2), 16);
    var textGreen = parseInt(textColor.substr(2, 2), 16);
    var textBlue = parseInt(textColor.substr(4, 2), 16);

    // Вычисляем яркость фона и текста
    var bgBrightness = (bgRed * 299 + bgGreen * 587 + bgBlue * 114) / 1000;
    var textBrightness = (textRed * 299 + textGreen * 587 + textBlue * 114) / 1000;

    if (bgBrightness > 250) {
        return true;
    }

    // Вычисляем контрастность
    var contrast = Math.abs(bgBrightness - textBrightness);

    // Возвращаем true, если контрастность выше порогового значения (4.5 для обычного текста)
    return contrast >= 125;
}

export function getTextColorFromBG(bgColor){
    if(isContrastEnough(bgColor, "#242424")){
        return "#242424";
    }

    return "#FFFFFF";
}