// Toggle Single Dropdowns (Individual & Batch)
function toggleCatDropdown(e, id) {
  e.stopPropagation();
  document.querySelectorAll(".category-dropdown").forEach((dropdown) => {
    if (dropdown.id !== "cat-dropdown-" + id) dropdown.style.display = "none";
  });
  const dropdown = document.getElementById("cat-dropdown-" + id);
  dropdown.style.display =
    dropdown.style.display === "block" ? "none" : "block";
}

// Search Filter
function filterCategories(id, query) {
  const listContainer = document.getElementById("cat-list-" + id);
  const categoryForms = listContainer.querySelectorAll(".cat-form-item");
  const noFoundMsg = listContainer.querySelector(".no-cat-found");
  let hasMatch = false;
  query = query.toLowerCase();

  categoryForms.forEach((form) => {
    const categoryName = form.querySelector("button").innerText.toLowerCase();
    if (categoryName.includes(query)) {
      form.style.display = "block";
      hasMatch = true;
    } else {
      form.style.display = "none";
    }
  });
  noFoundMsg.style.display = !hasMatch ? "block" : "none";
}

// Close Dropdowns if clicking outside
document.addEventListener("click", function (e) {
  if (!e.target.closest(".cat-dropdown-container")) {
    document.querySelectorAll(".category-dropdown").forEach((dropdown) => {
      dropdown.style.display = "none";
    });
  }
});

// --- BATCH UI LOGIC ---

// Select All Checkbox
function toggleAllCheckboxes(source) {
  const checkboxes = document.querySelectorAll(".row-checkbox");
  checkboxes.forEach((cb) => (cb.checked = source.checked));
  updateBatchUI();
}

// Show/Hide floating bar based on checkbox count
function updateBatchUI() {
  const checkboxes = document.querySelectorAll(".row-checkbox");
  const checkedBoxes = Array.from(checkboxes).filter((cb) => cb.checked);
  const actionBar = document.getElementById("batchActionBar");
  const countDisplay = document.getElementById("selectedCountDisplay");

  // Update Select All Checkbox state
  const selectAllCheckbox = document.getElementById("selectAll");
  if (selectAllCheckbox) {
    selectAllCheckbox.checked =
      checkboxes.length > 0 && checkedBoxes.length === checkboxes.length;
  }

  if (checkedBoxes.length > 0) {
    countDisplay.innerText = checkedBoxes.length;
    actionBar.style.display = "flex";
  } else {
    actionBar.style.display = "none";
  }
}

// For Single Move
function submitSingleMove(productId, newCatId, catName) {
  if (!confirm("Move product to " + catName + "?")) return;

  const form = document.getElementById("batchForm");

  // Temporarily uncheck all row checkboxes so they don't submit with single actions
  document
    .querySelectorAll(".row-checkbox")
    .forEach((cb) => (cb.checked = false));

  // Create a temporary hidden input for product_id
  const input = document.createElement("input");
  input.type = "hidden";
  input.name = "product_id";
  input.value = productId;
  form.appendChild(input);

  document.getElementById("batchActionType").value = "move";
  document.getElementById("batchNewCategoryId").value = newCatId;
  form.submit();
}

// For Batch Move
function submitBatchMove(newCatId, catName) {
  if (!confirm("Move selected products to " + catName + "?")) return;

  const form = document.getElementById("batchForm");
  document.getElementById("batchActionType").value = "batch_move";
  document.getElementById("batchNewCategoryId").value = newCatId;
  form.submit();
}

// For Batch Remove
function submitBatchRemove() {
  if (!confirm("Are you sure you want to remove the selected products?"))
    return;

  const form = document.getElementById("batchForm");
  document.getElementById("batchActionType").value = "batch_remove";
  form.submit();
}
