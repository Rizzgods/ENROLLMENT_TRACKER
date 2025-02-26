document.addEventListener('DOMContentLoaded', function() {
    // Make sure modal element exists
    const modal = document.getElementById('imageModal');
    const modalImage = document.getElementById('modalImage');
    
    if (!modal || !modalImage) {
        console.error('Modal or modal image element not found');
        return;
    }
    
    // Add global functions to window object
    window.showModal = function(imgSrc) {
        console.log('showModal called with imgSrc:', imgSrc); // Debugging log
        if (imgSrc) {
            modalImage.src = imgSrc;
            modalImage.alt = "Document Preview";
        } else {
            modalImage.src = "";
            modalImage.alt = "No image found";
            modalImage.style.display = "none";
            const noImageText = document.createElement("p");
            noImageText.id = "noImageText";
            noImageText.textContent = "No image found";
            noImageText.classList.add("text-gray-500", "text-center");
            modalImage.parentNode.appendChild(noImageText);
        }
        modal.classList.remove('hidden');
        document.body.classList.add('overflow-hidden'); // Prevent scrolling when modal is open
        console.log('Modal should be visible now'); // Debugging log
    };
    
    window.closeModal = function() {
        console.log('closeModal called'); // Debugging log
        modal.classList.add('hidden');
        document.body.classList.remove('overflow-hidden');
        modalImage.style.display = "";
        const noImageText = document.getElementById("noImageText");
        if (noImageText) {
            noImageText.remove();
        }
    };
    
    // Close on background click
    modal.addEventListener('click', function(e) {
        if (e.target === modal || e.target.classList.contains('modal-backdrop')) {
            window.closeModal();
        }
    });
    
    // Close on escape key
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape' && !modal.classList.contains('hidden')) {
            window.closeModal();
        }
    });
    
    console.log('Modal script initialized');
});
