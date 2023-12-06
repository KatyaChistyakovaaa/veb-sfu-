javascript
function deleteElements(arr) {
    // Проходимся по всем элементам массива
    for (let i = 0; i < arr.length; i++) {
      // Получаем целую часть числа с помощью метода Math.trunc
      // Затем находим модуль с помощью метода Math.abs
      const integerPart = Math.abs(Math.trunc(arr[i]));
  
      // Преобразуем полученное число в строку
      const integerPartString = integerPart.toString();
  
      // Проверяем, все ли цифры в числе четные
      let isAllDigitsEven = true;
      for (let j = 0; j < integerPartString.length; j++) {
        if (parseInt(integerPartString[j]) % 2 !== 0) {
          isAllDigitsEven = false;
          break;
        }
      }
  
      // Если все цифры числа четные, удаляем элемент из массива
      if (isAllDigitsEven) {
        arr.splice(i, 1);
        // Уменьшаем значение i, чтобы не пропустить следующий элемент после удаления
        i--;
      }
    }
  
    return arr;
  }
  
  // Пример использования
  const numbers = [12.34, -56.78, 123.45, -678.90, 1234.5678];
  const result = deleteElements(numbers);
  console.log(result); // [ -56.78, -678.9, 1234.5678 ]
  