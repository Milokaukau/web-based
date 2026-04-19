// js/category_list.js

function toggleAllCheckboxes(source) {
  const checkboxes = document.querySelectorAll(".row-checkbox");
  checkboxes.forEach((cb) => (cb.checked = source.checked));
  updateBatchUI();
}

function updateBatchUI() {
  const checkboxes = document.querySelectorAll(".row-checkbox");
  const checkedBoxes = Array.from(checkboxes).filter((cb) => cb.checked);
  const actionBar = document.getElementById("batchActionBar");
  const countDisplay = document.getElementById("selectedCountDisplay");
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

// Ensure the form reference is only grabbed when a submit action fires
function submitSingleDeactivate(catId, productCount) {
  let msg = "Are you sure you want to deactivate this category?";
  if (productCount > 0) {
    msg =
      'Deactivating this category will move all its products to "Uncategorized". Are you sure? Want to move them to another category first?';
  }

  if (!confirm(msg)) return;

  const form = document.getElementById("batchForm");
  document
    .querySelectorAll(".row-checkbox")
    .forEach((cb) => (cb.checked = false));

  const input = document.createElement("input");
  input.type = "hidden";
  input.name = "category_id";
  input.value = catId;
  form.appendChild(input);

  // NEW: Attach the product count so the backend can read it
  const countInput = document.createElement("input");
  countInput.type = "hidden";
  countInput.name = "product_count";
  countInput.value = productCount;
  form.appendChild(countInput);

  document.getElementById("batchActionType").value = "deactivate";
  form.submit();
}

function submitSingleActivate(catId) {
  if (!confirm("Are you sure you want to activate this category?")) return;

  const form = document.getElementById("batchForm");
  document
    .querySelectorAll(".row-checkbox")
    .forEach((cb) => (cb.checked = false));

  const input = document.createElement("input");
  input.type = "hidden";
  input.name = "category_id";
  input.value = catId;
  form.appendChild(input);

  document.getElementById("batchActionType").value = "activate";
  form.submit();
}

function submitBatchActivate() {
  const checkboxes = document.querySelectorAll(".row-checkbox:checked");
  if (checkboxes.length === 0) return;

  let inactiveCount = 0;

  // Disable already-active checkboxes so the form ignores them
  checkboxes.forEach((cb) => {
    const statusText = cb
      .closest("tr")
      .querySelector(".badge")
      .textContent.trim()
      .toLowerCase();
    if (statusText === "active") {
      cb.disabled = true;
    } else {
      inactiveCount++;
    }
  });

  if (inactiveCount === 0) {
    alert("All selected categories are already active.");
    checkboxes.forEach((cb) => (cb.disabled = false)); // Re-enable for future clicks
    return;
  }

  if (
    !confirm(
      `Are you sure you want to activate the ${inactiveCount} inactive categories?`,
    )
  ) {
    checkboxes.forEach((cb) => (cb.disabled = false));
    return;
  }

  const form = document.getElementById("batchForm");
  document.getElementById("batchActionType").value = "batch_activate";
  form.submit();
}

function submitBatchDeactivate() {
  const checkboxes = document.querySelectorAll(".row-checkbox:checked");
  let hasProducts = false;
  let totalProducts = 0;
  let activeCount = 0;

  checkboxes.forEach((cb) => {
    const statusText = cb
      .closest("tr")
      .querySelector(".badge")
      .textContent.trim()
      .toLowerCase();

    // Disable already-inactive checkboxes so the form ignores them
    if (statusText === "inactive") {
      cb.disabled = true;
    } else {
      activeCount++;
      let count = parseInt(cb.dataset.productCount) || 0;
      if (count > 0) {
        hasProducts = true;
        totalProducts += count;
      }
    }
  });

  if (activeCount === 0) {
    alert("All selected categories are already inactive.");
    checkboxes.forEach((cb) => (cb.disabled = false));
    return;
  }

  let msg = `Are you sure you want to deactivate the ${activeCount} active categories?`;
  if (hasProducts) {
    msg = `Deactivating these ${activeCount} categories will move all their products to "Uncategorized". Are you sure? Want to move them to another category first?`;
  }

  if (!confirm(msg)) {
    checkboxes.forEach((cb) => (cb.disabled = false));
    return;
  }

  const form = document.getElementById("batchForm");

  // Attach the total products moved
  const countInput = document.createElement("input");
  countInput.type = "hidden";
  countInput.name = "total_products_moved";
  countInput.value = totalProducts;
  form.appendChild(countInput);

  document.getElementById("batchActionType").value = "batch_deactivate";
  form.submit();
}
