function showAlert(message, type = "success") {
    const alertBox = document.createElement("div");

    alertBox.innerText = message;
    alertBox.className = `
        fixed top-5 right-5 px-4 py-2 rounded text-white
        ${type === "success" ? "bg-green-500" : "bg-red-500"}
    `;

    document.body.appendChild(alertBox);

    setTimeout(() => {
        alertBox.remove();
    }, 3000);
}