function toggleDropdown(id) {
  // Close all other dropdowns
  document.querySelectorAll(".admin-dropdown").forEach((dropdown) => {
    if (dropdown.id !== "dropdown-" + id) dropdown.style.display = "none";
  });

  // Toggle the clicked one
  const el = document.getElementById("dropdown-" + id);
  el.style.display = el.style.display === "block" ? "none" : "block";
}

// Close dropdowns if clicked outside
window.onclick = function (event) {
  if (!event.target.matches(".dropdown-trigger")) {
    document.querySelectorAll(".admin-dropdown").forEach((dropdown) => {
      dropdown.style.display = "none";
    });
  }
};
