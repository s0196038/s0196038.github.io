const openBtn = document.getElementById("openFormBtn");
const closeBtn = document.getElementById("closeBtn");
const overlay = document.getElementById("overlay");
const form = document.getElementById("feedbackForm");
const statusText = document.getElementById("status");

// ---------- Открытие формы ----------
function openForm(push = true) {
    overlay.classList.add("active");
    if (push) {
        history.pushState({ popup: true }, "", "#feedback");
    }
    restoreData();
}

// ---------- Закрытие формы ----------
function closeForm() {
    overlay.classList.remove("active");
}

// ---------- Кнопки ----------
openBtn.addEventListener("click", () => openForm());
closeBtn.addEventListener("click", () => history.back());

// ---------- Назад в браузере ----------
window.addEventListener("popstate", () => {
    closeForm();
});

// ---------- LocalStorage ----------
function saveData() {
    const data = Object.fromEntries(new FormData(form));
    localStorage.setItem("feedbackData", JSON.stringify(data));
}

function restoreData() {
    const data = JSON.parse(localStorage.getItem("feedbackData"));
    if (!data) return;

    for (let key in data) {
        if (form.elements[key]) {
            form.elements[key].value = data[key];
        }
    }
}

form.addEventListener("input", saveData);

// ---------- Отправка формы ----------
form.addEventListener("submit", async (e) => {
    e.preventDefault();

    statusText.textContent = "Отправка...";

    const formData = new FormData(form);

    try {
        const response = await fetch("https://formcarry.com/s/aPzCEYszvvI", {
            method: "POST",
            body: formData,
            headers: { "Accept": "application/json" }
        });

        if (response.ok) {
            statusText.textContent = "Сообщение успешно отправлено!";
            form.reset();
            localStorage.removeItem("feedbackData");
        } else {
            statusText.textContent = "Ошибка при отправке";
        }
    } catch {
        statusText.textContent = "Ошибка сети";
    }
});
