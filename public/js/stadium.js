document.addEventListener("DOMContentLoaded", function () {
    const sections = document.querySelectorAll(".stadium-section");
    const stadiumContainer = document.querySelector(".stadium-svg-container");
    const zoomInBtn = document.getElementById("zoom-in");
    const zoomOutBtn = document.getElementById("zoom-out");
    const quantityInput = document.getElementById("quantity");
    const totalPriceSpan = document.getElementById("totalPrice");
    const sectionInfoDiv = document.getElementById("section-info");
    const sectionInfoDetailsDiv = document.getElementById("section-info-details");
    const selectedSectionIdInput = document.getElementById("selected_section_id");
    const view360Btn = document.getElementById("view-360-btn");
    const debugBtn = document.getElementById("debug-btn");

    // Get section data from the window variable (populated by Laravel)
    const sectionData = window.sectionData || window.matchSectionData || {};

    // Map path IDs to section IDs (based on the structure of your SVG)
    // You may need to update this based on your actual SVG structure
    const pathToSectionMap = {
        "path138476": "1", // Example: if path138476 corresponds to section 1
        "path138478": "2",
        "path138480": "3",
        "path138482": "4",
        "path138484": "5",
        "path138486": "6",
        "path138488": "7",
        "path138490": "8",
        "path138492": "9",
        "path138494": "10",
        "path138496": "11",
        "path138498": "13",
        "path138500": "17",
        "path138502": "19",
        "path138504": "20",
        "path138506": "27",
        // Add more mappings based on your SVG
    };

    let selectedSections = [];
    let ticketPrice = parseFloat(document.getElementById("ticketPrice")?.dataset.price || 0);
    let scale = 1;

    // Log available section IDs for debugging
    console.log("Available section IDs:", Object.keys(sectionData));

    // Add debug functionality only for admins
    if (debugBtn) {
        debugBtn.addEventListener("click", () => {
            console.log("--- DEBUG INFORMATION ---");
            console.log("All sections:", sections.length);

            // Create a debug overlay
            const debugInfo = document.createElement("div");
            debugInfo.style.position = "fixed";
            debugInfo.style.top = "10px";
            debugInfo.style.left = "10px";
            debugInfo.style.width = "400px";
            debugInfo.style.height = "300px";
            debugInfo.style.backgroundColor = "rgba(255,255,255,0.9)";
            debugInfo.style.border = "1px solid #ccc";
            debugInfo.style.padding = "10px";
            debugInfo.style.overflow = "auto";
            debugInfo.style.zIndex = "9999";
            debugInfo.style.fontSize = "12px";
            debugInfo.innerHTML = "<h3>SVG Debug Info</h3><p>Hover over sections to see IDs</p><button id='close-debug'>Close</button><hr/><div id='debug-content'></div>";
            document.body.appendChild(debugInfo);

            // Add event listener to close button
            document.getElementById("close-debug").addEventListener("click", () => {
                debugInfo.remove();
                // Remove all debug listeners and styles
                sections.forEach(section => {
                    section.style.outline = "";
                    section.removeEventListener("mouseover", debugHandler);
                });
            });

            // Add debug handlers to all sections
            const debugContent = document.getElementById("debug-content");
            function debugHandler(e) {
                const section = e.target;
                const id = section.getAttribute("id");
                const parent = section.parentElement;
                const parentId = parent.getAttribute("id");
                const texts = parent.querySelectorAll("text");
                const textContent = Array.from(texts).map(t => t.textContent.trim()).join(", ");

                section.style.outline = "2px solid red";

                debugContent.innerHTML = `
                    <p><strong>Path ID:</strong> ${id}</p>
                    <p><strong>Parent ID:</strong> ${parentId}</p>
                    <p><strong>Text Content:</strong> ${textContent}</p>
                `;
            }

            sections.forEach(section => {
                section.addEventListener("mouseover", debugHandler);
            });
        });
    }

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

    // Find the actual section ID based on element ID or path ID
    function findSectionId(element) {
        // Check if the element is a text element (like "F", "1", "A", etc.)
        if (element.nodeName === "text" || element.tagName === "text") {
            const textContent = element.textContent.trim();
            if (sectionData[textContent]) {
                return textContent;
            }
        }

        // First try to get the ID directly
        let sectionId = element.getAttribute("id");

        // If the ID is in the mapping, use the mapped value
        if (pathToSectionMap[sectionId]) {
            return pathToSectionMap[sectionId];
        }

        // If the ID is not in sectionData, try to find a related text element
        if (!sectionData[sectionId]) {
            // Check if we're in a group that contains text elements
            const parentGroup = element.closest("g");
            if (parentGroup) {
                const textElements = parentGroup.querySelectorAll("text");
                if (textElements.length > 0) {
                    for (const text of textElements) {
                        const textContent = text.textContent.trim();
                        if (sectionData[textContent]) {
                            console.log(`Found section ID "${textContent}" from text element in group`);
                            return textContent;
                        }
                    }
                }

                // Try using the parent ID
                const parentId = parentGroup.getAttribute("id");
                if (parentId && pathToSectionMap[parentId]) {
                    return pathToSectionMap[parentId];
                }
            }
        }

        return sectionId;
    }

    // Add class to highlight active sections and show unavailable sections
    sections.forEach((section) => {
        const sectionId = findSectionId(section);

        // If we have data for this section
        if (sectionData[sectionId]) {
            const data = sectionData[sectionId];

            // Section has available seats and is active
            if (data.available_seats > 0 && data.is_active) {
                section.classList.add("section-available");

                // Add tooltip with section info
                const tooltip = document.createElement("div");
                tooltip.classList.add("section-tooltip");
                tooltip.innerHTML = `
                    <div class="section-name">${data.name}</div>
                    <div class="section-type">${data.section_type}</div>
                    <div class="section-seats">Available: ${data.available_seats}/${data.capacity}</div>
                    <div class="section-price">£${parseFloat(data.price).toFixed(2)}</div>
                `;
                section.appendChild(tooltip);
            } else {
                // Section is sold out or inactive
                section.classList.add("section-unavailable");
            }
        } else {
            // No data for this section, mark as unavailable
            section.classList.add("section-unknown");
        }
    });

    // ✅ Sélection des sections avec amélioration de l'expérience utilisateur
    sections.forEach((section) => {
        section.addEventListener("click", function (e) {
            // Only process click on available sections
            const sectionId = findSectionId(this);

            console.log("Clicked section ID:", sectionId);

            // Check if section is available
            if (!sectionData[sectionId]) {
                showSectionMessage(`Section ${sectionId} is not available for booking`);
                hideView360Button();
                disableReservationButton();
                return;
            }

            if (sectionData[sectionId].available_seats <= 0 || !sectionData[sectionId].is_active) {
                showSectionMessage(`Section ${sectionId} is sold out or inactive`);
                hideView360Button();
                disableReservationButton();
                return;
            }

            // Reset all sections
            selectedSections = [];
            sections.forEach(s => s.classList.remove("section-selected"));

            // Select only this section
            selectedSections.push(sectionId);
            this.classList.add("section-selected");

            // Update hidden input with selected section ID for form submission
            if (selectedSectionIdInput) {
                selectedSectionIdInput.value = sectionId;
            }

            // Update hidden form input for section_id
            const formSectionIdInput = document.getElementById('form_section_id');
            if (formSectionIdInput) {
                formSectionIdInput.value = sectionId;
            }

            // Update view-360 button visibility and link
            if (sectionData[sectionId] && sectionData[sectionId].view_360_url) {
                showView360Button(sectionData[sectionId].view_360_url, sectionData[sectionId].name);
            } else {
                hideView360Button();
            }

            // Enable reservation button and update text
            enableReservationButton(sectionId);

            // Update quantity limits based on available seats
            updateQuantityLimits(sectionId);

            // Show section detail popup
            showSectionPopup(sectionId);

            updateSelectionInfo();
        });
    });

    // Set up 360 view button click handler (initial setup)
    if (view360Btn) {
        view360Btn.addEventListener("click", function() {
            const url = this.getAttribute("data-url");
            const sectionName = this.getAttribute("data-section-name");

            if (url) {
                if (typeof open360Preview === 'function') {
                    open360Preview(this, url, sectionName);
                } else {
                    window.open(url, '_blank');
                }
            }
        });
    }

    // Helper function to show 360 view button
    function showView360Button(url, sectionName) {
        if (view360Btn) {
            view360Btn.classList.remove("hidden");
            view360Btn.setAttribute("data-url", url);
            view360Btn.setAttribute("data-section-name", sectionName);

            // Update click handler for the 360 view button
            view360Btn.onclick = function() {
                if (typeof open360Preview === 'function') {
                    open360Preview(this, url, sectionName);
                } else {
                    window.open(url, '_blank');
                }
            };
        }
    }

    // Helper function to hide 360 view button
    function hideView360Button() {
        if (view360Btn) {
            view360Btn.classList.add("hidden");
            view360Btn.removeAttribute("data-url");
            view360Btn.removeAttribute("data-section-name");
        }
    }

    // Helper function to enable the reservation button
    function enableReservationButton(sectionId) {
        const reserveButton = document.getElementById('reserve-button');
        if (reserveButton) {
            reserveButton.disabled = false;
            reserveButton.classList.remove('bg-gray-400', 'cursor-not-allowed');
            reserveButton.classList.add('bg-green-600', 'hover:bg-green-700');
            reserveButton.textContent = `Réserver Section ${sectionData[sectionId].name}`;
        }
    }

    // Helper function to disable the reservation button
    function disableReservationButton() {
        const reserveButton = document.getElementById('reserve-button');
        if (reserveButton) {
            reserveButton.disabled = true;
            reserveButton.classList.remove('bg-green-600', 'hover:bg-green-700');
            reserveButton.classList.add('bg-gray-400', 'cursor-not-allowed');
            reserveButton.textContent = 'Sélectionnez une section d\'abord';
        }

        // Clear form section ID
        const formSectionIdInput = document.getElementById('form_section_id');
        if (formSectionIdInput) {
            formSectionIdInput.value = '';
        }
    }

    // Helper function to update quantity limits based on available seats
    function updateQuantityLimits(sectionId) {
        const quantityInput = document.getElementById('quantity');
        if (quantityInput && sectionData[sectionId]) {
            const availableSeats = sectionData[sectionId].available_seats;
            quantityInput.max = Math.min(10, availableSeats); // Limit to 10 tickets or available seats

            // If current quantity is more than available, reduce it
            if (parseInt(quantityInput.value) > availableSeats) {
                quantityInput.value = availableSeats;
            }

            // Update total price if updateTotalPrice function exists
            if (typeof updateTotalPrice === 'function') {
                updateTotalPrice();
            }
        }
    }

    function updateSelectionInfo() {
        if (selectedSections.length === 0) {
            sectionInfoDiv.innerHTML = `<p class="text-gray-700">Aucune section sélectionnée</p>`;
            if (sectionInfoDetailsDiv) {
                sectionInfoDetailsDiv.innerHTML = '';
            }
            if (quantityInput) quantityInput.value = 0;
            if (totalPriceSpan) totalPriceSpan.textContent = '$0.00';
            return;
        }

        let detailsHTML = `<h3 class="text-lg font-semibold text-gray-900 mb-2">Billets sélectionnés</h3><div class="grid grid-cols-1 md:grid-cols-2 gap-4">`;

        // Afficher des détails pour chaque section sélectionnée
        for (const sectionId of selectedSections) {
            const data = sectionData[sectionId];
            if (!data) continue;

            detailsHTML += `
                <div class="bg-white p-4 rounded-md shadow border border-gray-200">
                    <h4 class="font-medium text-gray-900">${data.name}</h4>
                    <div class="mt-2 space-y-1">
                        <p class="text-sm"><span class="text-gray-600">Type:</span> ${data.section_type}</p>
                        <p class="text-sm"><span class="text-gray-600">Prix:</span> £${parseFloat(data.price).toFixed(2)}</p>
                        <p class="text-sm"><span class="text-gray-600">Places disponibles:</span> ${data.available_seats} / ${data.capacity}</p>
                    </div>
                    ${data.view_360_url ? `
                        <button type="button"
                            class="mt-3 px-3 py-1 bg-blue-600 text-white text-xs rounded hover:bg-blue-700 inline-flex items-center"
                            onclick="window.open('${data.view_360_url}', '_blank')">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                            </svg>
                            Vue 360°
                        </button>
                    ` : ''}
                </div>
            `;
        }

        detailsHTML += `</div>`;
        sectionInfoDiv.innerHTML = detailsHTML;

        // If we have a separate details div, populate it too
        if (sectionInfoDetailsDiv) {
            sectionInfoDetailsDiv.innerHTML = detailsHTML;
        }

        // Update ticket quantity and price
        if (quantityInput && totalPriceSpan && selectedSections.length > 0) {
            const firstSectionData = sectionData[selectedSections[0]];
            if (firstSectionData) {
                quantityInput.value = 1;
                ticketPrice = parseFloat(firstSectionData.price);
                totalPriceSpan.textContent = '£' + ticketPrice.toFixed(2);
            }
        }
    }

    function showSectionMessage(message) {
        sectionInfoDiv.innerHTML = `
            <h3 class="text-lg font-semibold text-gray-900 mb-2">Information</h3>
            <div class="bg-yellow-50 border border-yellow-400 text-yellow-700 p-3 rounded">
                ${message}
            </div>
        `;
    }
});
