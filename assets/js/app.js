document.addEventListener("DOMContentLoaded", function () {
    console.log("Clinic Management System Initialized.");

    const deleteButtons = document.querySelectorAll(".btn-delete");
    deleteButtons.forEach(button => {
        button.addEventListener("click", function (e) {
            if (!confirm("Are you sure you want to delete this record?")) {
                e.preventDefault();
            }
        });
    });
});