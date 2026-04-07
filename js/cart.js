/**
 * Validation for removing items
 * Prevents default link action unless user confirms
 */
function confirmRemove(productName) {
    const response = confirm("Do you want to remove " + productName + " from your cart?");
    return response; 
}