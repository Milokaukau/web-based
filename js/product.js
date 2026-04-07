function addToCart(btn) {
    const qty = parseInt(document.getElementById('qty').value) || 1;
    const activeSwatch = document.querySelector('.swatch.active');
    
    if (!activeSwatch) {
        alert("Please choose a color before adding to your bag.");
        return;
    }

    const colorId = activeSwatch.dataset.colorId;
    const colorName = activeSwatch.dataset.colorName;

    const id = btn.dataset.id;
    let finalName = btn.dataset.name;
    if (colorName && colorName !== 'Default') {
        finalName += ' - ' + colorName;
    }
    const name = encodeURIComponent(finalName);
    const price = btn.dataset.price;
    const photo = encodeURIComponent(btn.dataset.photo);
    const maxStock = parseInt(btn.dataset.stock);

    if (qty > maxStock) {
        alert("Sorry, only " + maxStock + " units are available in stock.");
        return;
    }

    window.location.href = `cart.php?action=add&id=${id}&name=${name}&price=${price}&qty=${qty}&photo=${photo}&color=${colorId}`;
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

function addToWishlist(btn) {
    const id = btn.dataset.id;
    if (!id) return;
    
    const activeSwatch = document.querySelector('.swatch.active');
    if (!activeSwatch) {
        alert("Please choose a color before adding to your wishlist.");
        return;
    }

    const colorId = activeSwatch.dataset.colorId;
    window.location.href = `wishlist.php?action=add&id=${id}&color=${colorId}`;
}

// Component Scripts (Color, Size, Accordion)
function selectColor(el, colorName) {
    document.querySelectorAll('.swatch').forEach(s => s.classList.remove('active'));
    el.classList.add('active');
    document.getElementById('selectedColorLabel').innerText = colorName;
}

function selectSize(el, sizeName) {
    document.querySelectorAll('.pill').forEach(p => p.classList.remove('active'));
    el.classList.add('active');
    document.getElementById('selectedSizeLabel').innerText = sizeName;
}

function toggleAccordion(btn) {
    const item = btn.closest('.accordion-item');
    const content = item.querySelector('.accordion-content');
    const icon = item.querySelector('.accordion-icon');
    
    // Is it currently open?
    const isOpen = item.classList.contains('active');
    
    // Close all
    document.querySelectorAll('.accordion-item').forEach(i => {
        i.classList.remove('active');
        i.querySelector('.accordion-content').style.display = 'none';
        i.querySelector('.accordion-icon').innerText = '+';
    });

    // Open if it wasn't open
    if (!isOpen) {
        item.classList.add('active');
        content.style.display = 'block';
        icon.innerText = '−'; // minus symbol
    }
}

function changeImage(thumbElement) {
    document.querySelectorAll('.thumb').forEach(t => t.classList.remove('active'));
    thumbElement.classList.add('active');
    // Not actually replacing src since it's the same image in db, but simulates gallery structure
}