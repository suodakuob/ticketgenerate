document.addEventListener("DOMContentLoaded", function () {
    const quantityInput = document.getElementById("quantity");
    const decrementButton = document.getElementById("decrement");
    const incrementButton = document.getElementById("increment");
    const availableTickets = parseInt(quantityInput.max);

    decrementButton.addEventListener("click", () => {
        let currentValue = parseInt(quantityInput.value);
        if (currentValue > 1) {
            quantityInput.value = currentValue - 1;
            updateTotalPrice();
        }
    });

    incrementButton.addEventListener("click", () => {
        let currentValue = parseInt(quantityInput.value);
        if (currentValue < availableTickets && currentValue < 10) {
            quantityInput.value = currentValue + 1;
            updateTotalPrice();
        }
    });

    function updateTotalPrice() {
        const totalPriceSpan = document.getElementById("totalPrice");
        const ticketPrice = parseFloat(document.getElementById("ticketPrice").dataset.price);
        const totalPrice = (parseInt(quantityInput.value) * ticketPrice).toFixed(2);
        totalPriceSpan.textContent = `$${totalPrice}`;
    }
});