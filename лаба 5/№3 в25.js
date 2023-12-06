javascript
function countSyllables(word) {
  // Удаляем лишние пробелы в начале и конце строки
  word = word.trim();

  // Игнорируем пустые строки
  if (word === "") {
    return 0;
  }

  // Регулярное выражение для поиска гласных букв
  const vowelsRegex = /[aeiouy]/gi;

  // Используем метод match() для получения массива гласных букв в слове
  const vowels = word.match(vowelsRegex);

  // Если массив гласных пуст, значит слово не содержит слогов
  if (vowels === null) {
    return 0;
  }

  // Возвращаем длину массива гласных букв, которая соответствует количеству слогов
  return vowels.length;
}

// Пример использования функции:
const word = "programming"; // Заданное слово
const syllableCount = countSyllables(word);
console.log("Количество слогов в слове", word, ":", syllableCount);