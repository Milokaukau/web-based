document.addEventListener("DOMContentLoaded", () => {
  // 1. Order Status Live Update (Used in both List & Details mode)
  document.querySelectorAll(".status-dropdown").forEach(function (dropdown) {
    dropdown.addEventListener("change", function () {
      const orderId = this.dataset.orderId;
      const type = this.dataset.updateType || "order_status"; // Distinguish which status to modify
      const newStatus = this.value;
      const originalShadow = this.style.boxShadow;

      this.style.boxShadow = "0 0 0 3px rgba(243, 158, 158, 1)";

      const formData = new FormData();
      formData.append("order_id", orderId);
      formData.append("update_type", type);
      formData.append("status", newStatus);
      fetch("/pages/admin/order_listing.php", {
        method: "POST",
        body: formData,
      })
        .then((response) => {
          setTimeout(() => {
            this.style.boxShadow = originalShadow;
          }, 500);
        })
        .catch((err) => {
          alert("Failed to update order status.");
          this.style.boxShadow = originalShadow;
        });
    });
  });

  // 2. Custom Member Filter Dropdown Logic
  const customSelect = document.getElementById("custom-member-select");
  if (customSelect) {
    const trigger = customSelect.querySelector(".custom-trigger");
    const optionsPanel = customSelect.querySelector(".custom-options");
    const searchInput = document.getElementById("member-search");
    const hiddenInput = document.getElementById("hidden-member-val");
    const selectedText = document.getElementById("selected-member");
    const optItems = customSelect.querySelectorAll(".opt-item");

    // Automatically set textual label if pre-selected
    if (hiddenInput.value) {
      const activeOpt = Array.from(optItems).find(
        (i) => i.dataset.val === hiddenInput.value,
      );
      if (activeOpt) {
        // Gets the name without the ID tag
        selectedText.textContent = activeOpt.textContent
          .replace(/#\d+\s+/, "")
          .trim();
      }
    }

    // Toggle open/close
    trigger.addEventListener("click", (e) => {
      e.stopPropagation();
      const isClosed = !optionsPanel.classList.contains("open");

      if (isClosed) {
        optionsPanel.classList.add("open");
        customSelect.classList.add("open"); // Turns border input-active color
        searchInput.value = "";
        searchInput.dispatchEvent(new Event("input"));
        setTimeout(() => searchInput.focus(), 10);
      } else {
        optionsPanel.classList.remove("open");
        customSelect.classList.remove("open");
      }
    });

    // Search engine
    searchInput.addEventListener("input", function () {
      const query = this.value.toLowerCase();
      optItems.forEach((item) => {
        const match =
          item.textContent.trim().toLowerCase().includes(query) ||
          item.dataset.val === "";
        item.style.display = match ? "block" : "none";
      });
    });

    // Upon Selection
    optItems.forEach((item) => {
      item.addEventListener("click", function (e) {
        e.stopPropagation();
        hiddenInput.value = this.dataset.val;
        optionsPanel.classList.remove("open");
        customSelect.classList.remove("open");
        customSelect.closest("form").submit(); // Submits the filter form cleanly
      });
    });

    // Clicking outside closes the box
    document.addEventListener("click", (e) => {
      if (!customSelect.contains(e.target)) {
        optionsPanel.classList.remove("open");
        customSelect.classList.remove("open");
      }
    });
  }
});
