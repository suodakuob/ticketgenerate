document.addEventListener("DOMContentLoaded", function () {
    const sections = document.querySelectorAll(".stadium-section");
    const stadiumContainer = document.querySelector(".stadium-svg-container");
    const zoomInBtn = document.getElementById("zoom-in");
    const zoomOutBtn = document.getElementById("zoom-out");
    const quantityInput = document.getElementById("quantity");
    const totalPriceSpan = document.getElementById("totalPrice");
    const sectionInfoDiv = document.getElementById("section-info");

    let selectedSections = [];
    let ticketPrice = parseFloat(document.getElementById("ticketPrice").dataset.price);
    let scale = 1;
    
    // ✅ Gestion des boutons de zoom
    zoomInBtn.addEventListener("click", () => {
        scale = Math.min(scale + 0.2, 2);
        stadiumContainer.style.transform = `scale(${scale})`;
    });

    zoomOutBtn.addEventListener("click", () => {
        scale = Math.max(scale - 0.2, 0.5);
        stadiumContainer.style.transform = `scale(${scale})`;
    });

    // ✅ Désactiver le déplacement si on clique sur une section
    sections.forEach((section) => {
        section.addEventListener("mousedown", (e) => e.stopPropagation());
    });

    // ✅ Sélection des sections sans déplacer le stade
    sections.forEach((section) => {
        section.addEventListener("click", function (e) {
            // ✅ Ajustement de la zone de clic (doit être légèrement à droite)
            if (e.offsetX < this.getBBox().width * 0.7) return; 

            const sectionId = this.getAttribute("id");

            if (!selectedSections.includes(sectionId)) {
                selectedSections.push(sectionId);
                this.classList.add("section-selected");
            } else {
                selectedSections = selectedSections.filter(sec => sec !== sectionId);
                this.classList.remove("section-selected");
            }

            updateSelectionInfo();
        });
    });

    function updateSelectionInfo() {
        quantityInput.value = selectedSections.length;
        const totalPrice = (selectedSections.length * ticketPrice).toFixed(2);
        totalPriceSpan.textContent = `$${totalPrice}`;

        sectionInfoDiv.innerHTML = `
            <h3 class="text-lg font-semibold text-gray-900 mb-2">Billets sélectionnés</h3>
            <p class="text-gray-700 mb-1">Nombre de billets: <span class="font-medium">${selectedSections.length}</span></p>
            <p class="text-gray-700">Prix total: <span class="font-medium">$${totalPrice}</span></p>
        `;
    }
});
