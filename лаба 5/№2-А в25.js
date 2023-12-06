javascript
// Заданный массив
const numbers = [1, 2, 3, 4, 5];

// Функция для проверки условия синуса числа
function isPositiveSine(num) {
  return Math.sin(num) > 0;
}

// Функция для получения суммы элементов массива до первого числа, удовлетворяющего условию
function getSumUntilCondition(numbersArray, conditionFn) {
  let sum = 0;

  for (let i = 0; i < numbersArray.length; i++) {
    const currentNum = numbersArray[i];
    
    if (conditionFn(currentNum)) {
      break;
    }
    
    sum += currentNum;
  }
  
  return sum;
}

// Вычисляем сумму элементов массива до первого числа, удовлетворяющего условию
const sum = getSumUntilCondition(numbers, isPositiveSine);

console.log('Сумма элементов массива до первого числа с положительным синусом:', sum);
