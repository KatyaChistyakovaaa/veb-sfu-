javascript
function isPrime(number) {
  if (number <= 1) {
    return false;
  }

  for (let i = 2; i <= Math.sqrt(number); i++) {
    if (number % i === 0) {
      return false;
    }
  }

  return true;
}

// Пример использования функции:
const number = 17; // Заданное число
if (isPrime(number)) {
  console.log(number + " является простым числом.");
} else {
  console.log(number + " не является простым числом.");
} 