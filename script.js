const slider = document.getElementById('date-slider');
const title = document.getElementById('event-title');
const description = document.getElementById('event-description');
const addButton = document.getElementById('add-event');

const events = {
    862: { title: "Основание Полоцка", description: "Первое упоминание в летописях." },
    1991: { title: "Объявление независимости", description: "Республика Беларусь стала независимой." },
};

slider.addEventListener('input', () => {
    const year = slider.value;
    if (events[year]) {
        title.textContent = events[year].title;
        description.textContent = events[year].description;
    } else {
        title.textContent = "Событие отсутствует";
        description.textContent = "Добавьте новое событие для этой даты.";
    }
});

addButton.addEventListener('click', () => {
    const year = slider.value;
    const newTitle = prompt("Введите название события:");
    const newDescription = prompt("Введите описание события:");
    if (newTitle && newDescription) {
        events[year] = { title: newTitle, description: newDescription };
        alert("Событие добавлено!");
    }
});
