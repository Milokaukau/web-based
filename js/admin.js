function openModal(id) {
  document.getElementById(id).classList.add("open");
}

function closeModal(id) {
  document.getElementById(id).classList.remove("open");
}

document.querySelectorAll(".modal-overlay").forEach((o) => {
  o.addEventListener("click", (e) => {
    if (e.target === o) o.classList.remove("open");
  });
});

function updateClock() {
  const n = new Date();
  document.getElementById("clock").textContent =
    n.toLocaleDateString("en-MY", {
      day: "2-digit",
      month: "short",
      year: "numeric",
    }) +
    "  " +
    n.toLocaleTimeString("en-MY", { hour: "2-digit", minute: "2-digit" });
}

updateClock();
setInterval(updateClock, 1000);

// Highlight active sidebar link dynamically
const currentPath = window.location.pathname;
const currentPageParam = new URLSearchParams(location.search).get("page");

document.querySelectorAll(".nav-link").forEach((link) => {
  // First, clear the active classes that PHP or old loads might have set
  link.classList.remove("active");
  const linkUrl = new URL(link.href, window.location.origin);

  // FIXED: Changed from .includes("admin.php") to .endsWith("/admin.php")
  if (currentPath.endsWith("/admin.php")) {
    // If on the unified admin.php router, match the page query parameter
    const targetParam = currentPageParam || "members";
    if (
      linkUrl.pathname.endsWith("/admin.php") &&
      linkUrl.searchParams.get("page") === targetParam
    ) {
      link.classList.add("active");
    }
  } else {
    // If on standalone admin pages, match strictly by pathname
    if (linkUrl.pathname === currentPath) {
      link.classList.add("active");
    }
    // Force highlight the parent lists if inside sub-menus:
    else if (
      (currentPath.includes("admin_add.php") ||
        currentPath.includes("admin_edit.php")) &&
      linkUrl.pathname.includes("admin_list.php")
    ) {
      link.classList.add("active");
    } else if (
      (currentPath.includes("category_add.php") ||
        currentPath.includes("category_items.php")) &&
      linkUrl.pathname.includes("category_list.php")
    ) {
      link.classList.add("active");
    }
    // Make sure order details highlights the orders tab
    else if (
      currentPath.includes("order_details_admin.php") &&
      linkUrl.pathname.includes("order_listing.php")
    ) {
      link.classList.add("active");
    }
  }
});
