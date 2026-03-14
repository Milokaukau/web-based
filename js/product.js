function addToCart(btn) {
    const qty = parseInt(document.getElementById('qty').value) || 1;

    const id = btn.dataset.id;
    const name = encodeURIComponent(btn.dataset.name);
    const price = btn.dataset.price;
    const photo = encodeURIComponent(btn.dataset.photo);
    const maxStock = parseInt(btn.dataset.stock);

    if (qty > maxStock) {
        alert("Sorry, only " + maxStock + " units are available in stock.");
        return;
    }

    window.location.href = `cart.php?action=add&id=${id}&name=${name}&price=${price}&qty=${qty}&photo=${photo}`;
}

function changeQty(amt) {
    const input = document.getElementById('qty');
    if (!input) return;

    const maxStock = parseInt(document.querySelector('[data-stock]').dataset.stock);
    const newVal = (parseInt(input.value) || 1) + amt;

    if (newVal >= 1 && newVal <= maxStock) {
        input.value = newVal;
    }
}