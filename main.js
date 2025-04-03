document.querySelector('#dark-mode-toggle').addEventListener('click', function () {
    document.body.classList.toggle('dark-mode');
    let isDarkMode = document.body.classList.contains('dark-mode');
    
    localStorage.setItem('darkMode', isDarkMode ? 'enabled' : 'disabled');
});

let storedDarkMode = localStorage.getItem('darkMode');

if (storedDarkMode === 'enabled') {
    document.body.classList.add('dark-mode');
} else {
    console.log("Dark mode is not enabled in localStorage");
}

document.querySelectorAll('.complete-task').forEach(button => {
    button.addEventListener('click', function () {
        let taskId = this.getAttribute('data-id');
        let buttonElement = this;
        let row = buttonElement.closest('tr');
        let statusCell = row.querySelector('td#status');

        fetch('mark_task_complete.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: 'task_id=' + taskId
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                buttonElement.style.display = 'none';

                // Update the status column to "Completed"
                statusCell.textContent = 'Completed';
            } else {
                alert(data.message || "Failed to update task.");
            }
        });
    });
});

document.getElementById("editTaskForm").addEventListener("submit", function(event) {
    let valid = true;

    var title = document.getElementById("title").value.trim();
    var description = document.getElementById("description").value.trim();
    var dueDate = document.getElementById("due_date").value;
    var csrfToken = document.getElementById("csrf_token").value;

    document.getElementById("titleError").innerText = "";
    document.getElementById("descriptionError").innerText = "";
    document.getElementById("dateError").innerText = "";

    if (title === "") {
        document.getElementById("titleError").innerText = "Title is required.";
        valid = false;
    }

    if (description.length >= 0 && description.length < 5) {
        document.getElementById("descriptionError").innerText = "Description must be at least 5 characters.";
        valid = false;
    }

    if (dueDate === "") {
        document.getElementById("dateError").innerText = "Due date is required.";
        valid = false;
    }

    if (!csrfToken) {
        alert("Invalid request! CSRF token missing.");
        valid = false;
    }

    if (!valid) {
        event.preventDefault();
    }
});

