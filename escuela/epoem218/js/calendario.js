const months = [
    { name: 'Enero', days: 31 },
    { name: 'Febrero', days: 28 }, 
    { name: 'Marzo', days: 31 },
    { name: 'Abril', days: 30 },
    { name: 'Mayo', days: 31 },
    { name: 'Junio', days: 30 },
    { name: 'Julio', days: 31 },
    { name: 'Agosto', days: 31 },
    { name: 'Septiembre', days: 30 },
    { name: 'Octubre', days: 31 },
    { name: 'Noviembre', days: 30 },
    { name: 'Diciembre', days: 31 }
];

const activities = {
    "Enero": {
        5: "Reunión de personal",
        12: "Evento deportivo",
        18: "Examen de matemáticas",
        22: "Concierto escolar"
    },
    "Febrero": {
        17: "Feliz Cumpleaños",
        14: "Día de San Valentín",
        20: "Taller de ciencia"
    },
    "Marzo": {
        8: "Día de la Mujer",
        17: "Examen de Historia"
    },
    "Abril":{
        8: "Junta de Consejo"
    }
};

let currentMonthIndex = new Date().getMonth();
let currentYear = new Date().getFullYear();
const currentDay = new Date().getDate();

function isLeapYear(year) {
    return (year % 4 === 0 && (year % 100 !== 0 || year % 400 === 0));
}

function showCalendar() {
    currentMonthIndex = new Date().getMonth();
    currentYear = new Date().getFullYear();
    updateCalendar();
    document.getElementById("calendar").style.display = "block";
}

function closeCalendar() {
    document.getElementById("calendar").style.display = "none";
}

function updateCalendar() {
    const month = months[currentMonthIndex];
    document.getElementById("monthName").textContent = `${month.name} ${currentYear}`;

    if (month.name === 'Febrero') {
        month.days = isLeapYear(currentYear) ? 29 : 28;
    }

    const firstDayOfMonth = new Date(currentYear, currentMonthIndex, 1).getDay();
    const totalDays = month.days;

    let calendarBody = '';
    let currentDayCount = 1;

    for (let i = 0; i < 6; i++) {
        let weekRow = '<tr>';
        for (let j = 0; j < 7; j++) {
            if (i === 0 && j < firstDayOfMonth) {
                weekRow += `<td></td>`;
            } else if (currentDayCount <= totalDays) {
                const dayClass = (currentDayCount === currentDay && currentMonthIndex === new Date().getMonth() && currentYear === new Date().getFullYear()) ? 'today' : '';
                const activityClass = activities[month.name] && activities[month.name][currentDayCount] ? 'activity-day' : '';
                weekRow += `<td class="${dayClass} ${activityClass}" onclick="showActivity(${currentDayCount})">${currentDayCount}</td>`;
                currentDayCount++;
            } else {
                weekRow += `<td></td>`;
            }
        }
        weekRow += '</tr>';
        calendarBody += weekRow;
        if (currentDayCount > totalDays) break;
    }

    document.querySelector("#calendarTable tbody").innerHTML = calendarBody;
}

function changeMonth(direction) {
    currentMonthIndex += direction;

    if (currentMonthIndex < 0) {
        currentMonthIndex = 11;
        currentYear--;
    } else if (currentMonthIndex > 11) {
        currentMonthIndex = 0;
        currentYear++;
    }

    updateCalendar();
}

function showActivity(day) {
    const monthName = months[currentMonthIndex].name;
    const activity = activities[monthName] && activities[monthName][day];
    if (activity) {
        document.getElementById("activityDetails").textContent = activity;
        document.getElementById("activityModal").style.display = "flex";
    }
}

function closeModal() {
    document.getElementById("activityModal").style.display = "none";
}
