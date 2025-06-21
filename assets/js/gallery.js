// ===== GALLERY FUNCTIONALITY =====

document.addEventListener('DOMContentLoaded', function() {
    initializeGalleryFilter();
    initializeGalleryModal();
    initializeLoadMore();
});

// Gallery Filter
function initializeGalleryFilter() {
    const filterButtons = document.querySelectorAll('.gallery-filter button');
    const galleryItems = document.querySelectorAll('.gallery-item');
    
    filterButtons.forEach(button => {
        button.addEventListener('click', function() {
            const filter = this.getAttribute('data-filter');
            
            // Update active button
            filterButtons.forEach(btn => btn.classList.remove('active'));
            this.classList.add('active');
            
            // Filter gallery items
            galleryItems.forEach(item => {
                const category = item.getAttribute('data-category');
                
                if (filter === 'all' || category === filter) {
                    item.style.display = 'block';
                    item.style.animation = 'fadeInUp 0.5s ease-in-out';
                } else {
                    item.style.display = 'none';
                }
            });
            
            // Re-trigger AOS animation for visible items
            setTimeout(() => {
                AOS.refresh();
            }, 100);
        });
    });
}

// Gallery Modal
function initializeGalleryModal() {
    const galleryModal = document.getElementById('galleryModal');
    const modalImage = document.getElementById('modalImage');
    const modalTitle = document.getElementById('modalTitle');
    const modalDescription = document.getElementById('modalDescription');
    const downloadBtn = document.getElementById('downloadBtn');
    const galleryButtons = document.querySelectorAll('[data-bs-target="#galleryModal"]');
    
    if (galleryModal && modalImage) {
        galleryButtons.forEach(button => {
            button.addEventListener('click', function() {
                const imageSrc = this.getAttribute('data-image');
                const imageTitle = this.getAttribute('data-title');
                const imageDescription = this.getAttribute('data-description');
                
                modalImage.src = imageSrc;
                modalTitle.textContent = imageTitle;
                modalDescription.textContent = imageDescription;
                
                // Update download button
                downloadBtn.onclick = function() {
                    downloadImage(imageSrc, imageTitle);
                };
            });
        });
        
        // Keyboard navigation
        galleryModal.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                const modal = bootstrap.Modal.getInstance(galleryModal);
                modal.hide();
            }
        });
    }
}

// Load More Functionality
function initializeLoadMore() {
    const loadMoreBtn = document.getElementById('loadMoreBtn');
    let currentPage = 1;
    const itemsPerPage = 9;
    
    if (loadMoreBtn) {
        loadMoreBtn.addEventListener('click', function() {
            // Simulate loading more images
            this.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Memuat...';
            this.disabled = true;
            
            setTimeout(() => {
                // Add more gallery items (simulated)
                addMoreGalleryItems();
                
                // Reset button
                this.innerHTML = '<i class="fas fa-plus me-2"></i>Muat Lebih Banyak';
                this.disabled = false;
                
                currentPage++;
                
                // Hide button after certain pages
                if (currentPage >= 3) {
                    this.style.display = 'none';
                    
                    // Show end message
                    const endMessage = document.createElement('p');
                    endMessage.className = 'text-muted text-center';
                    endMessage.textContent = 'Semua foto telah dimuat';
                    this.parentNode.appendChild(endMessage);
                }
            }, 1500);
        });
    }
}

// Add more gallery items (simulated)
function addMoreGalleryItems() {
    const galleryGrid = document.getElementById('galleryGrid');
    const newItems = [
        {
            image: 'assets/images/galeri/IMG-20250618-WA0008.jpg',
            category: 'pembelajaran',
            title: 'Pembelajaran Interaktif',
            description: 'Metode pembelajaran yang lebih interaktif dan menyenangkan'
        },
        {
            image: 'assets/images/galeri/IMG-20250618-WA0009.jpg',
            category: 'ekstrakurikuler',
            title: 'Kegiatan Pramuka',
            description: 'Pembentukan karakter melalui kegiatan kepramukaan'
        },
        {
            image: 'assets/images/galeri/IMG-20250618-WA0010.jpg',
            category: 'kegiatan',
            title: 'Upacara Bendera',
            description: 'Upacara bendera setiap hari Senin untuk menumbuhkan nasionalisme'
        }
    ];
    
    newItems.forEach((item, index) => {
        const galleryItem = document.createElement('div');
        galleryItem.className = 'col-lg-4 col-md-6 mb-4 gallery-item';
        galleryItem.setAttribute('data-category', item.category);
        galleryItem.setAttribute('data-aos', 'fade-up');
        galleryItem.setAttribute('data-aos-delay', (index + 1) * 100);
        
        galleryItem.innerHTML = `
            <div class="gallery-card">
                <img src="${item.image}" alt="${item.title}" class="img-fluid">
                <div class="gallery-overlay">
                    <div class="gallery-content">
                        <h5>${item.title}</h5>
                        <p>${item.description}</p>
                        <button class="btn btn-light btn-sm" data-bs-toggle="modal" data-bs-target="#galleryModal" 
                                data-image="${item.image}" data-title="${item.title}" data-description="${item.description}">
                            <i class="fas fa-search-plus"></i> Lihat
                        </button>
                    </div>
                </div>
            </div>
        `;
        
        galleryGrid.appendChild(galleryItem);
    });
    
    // Re-initialize modal buttons for new items
    initializeGalleryModal();
    
    // Refresh AOS
    AOS.refresh();
}

// Download image function
function downloadImage(imageSrc, imageTitle) {
    const link = document.createElement('a');
    link.href = imageSrc;
    link.download = `${imageTitle.replace(/\s+/g, '_')}.jpg`;
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
    
    // Show success message
    showNotification('Gambar berhasil didownload!', 'success');
}

// Gallery search functionality
function initializeGallerySearch() {
    const searchInput = document.getElementById('gallerySearch');
    
    if (searchInput) {
        searchInput.addEventListener('input', function() {
            const searchTerm = this.value.toLowerCase();
            const galleryItems = document.querySelectorAll('.gallery-item');
            
            galleryItems.forEach(item => {
                const title = item.querySelector('h5').textContent.toLowerCase();
                const description = item.querySelector('p').textContent.toLowerCase();
                
                if (title.includes(searchTerm) || description.includes(searchTerm)) {
                    item.style.display = 'block';
                } else {
                    item.style.display = 'none';
                }
            });
        });
    }
}

// Lightbox navigation
function initializeLightboxNavigation() {
    const galleryModal = document.getElementById('galleryModal');
    let currentImageIndex = 0;
    let allImages = [];
    
    // Collect all images
    function updateImagesList() {
        const visibleItems = document.querySelectorAll('.gallery-item:not([style*="display: none"])');
        allImages = Array.from(visibleItems).map(item => {
            const button = item.querySelector('[data-bs-target="#galleryModal"]');
            return {
                src: button.getAttribute('data-image'),
                title: button.getAttribute('data-title'),
                description: button.getAttribute('data-description')
            };
        });
    }
    
    // Add navigation buttons to modal
    if (galleryModal) {
        const modalBody = galleryModal.querySelector('.modal-body');
        
        // Previous button
        const prevBtn = document.createElement('button');
        prevBtn.className = 'btn btn-outline-light position-absolute start-0 top-50 translate-middle-y';
        prevBtn.style.zIndex = '1060';
        prevBtn.innerHTML = '<i class="fas fa-chevron-left"></i>';
        prevBtn.onclick = () => navigateImage(-1);
        
        // Next button
        const nextBtn = document.createElement('button');
        nextBtn.className = 'btn btn-outline-light position-absolute end-0 top-50 translate-middle-y';
        nextBtn.style.zIndex = '1060';
        nextBtn.innerHTML = '<i class="fas fa-chevron-right"></i>';
        nextBtn.onclick = () => navigateImage(1);
        
        modalBody.style.position = 'relative';
        modalBody.appendChild(prevBtn);
        modalBody.appendChild(nextBtn);
        
        // Update images list when modal is shown
        galleryModal.addEventListener('shown.bs.modal', function() {
            updateImagesList();
            
            // Find current image index
            const currentSrc = document.getElementById('modalImage').src;
            currentImageIndex = allImages.findIndex(img => img.src.includes(currentSrc.split('/').pop()));
        });
    }
    
    function navigateImage(direction) {
        currentImageIndex += direction;
        
        if (currentImageIndex < 0) {
            currentImageIndex = allImages.length - 1;
        } else if (currentImageIndex >= allImages.length) {
            currentImageIndex = 0;
        }
        
        const currentImage = allImages[currentImageIndex];
        document.getElementById('modalImage').src = currentImage.src;
        document.getElementById('modalTitle').textContent = currentImage.title;
        document.getElementById('modalDescription').textContent = currentImage.description;
        
        // Update download button
        document.getElementById('downloadBtn').onclick = function() {
            downloadImage(currentImage.src, currentImage.title);
        };
    }
    
    // Keyboard navigation
    document.addEventListener('keydown', function(e) {
        if (galleryModal.classList.contains('show')) {
            if (e.key === 'ArrowLeft') {
                navigateImage(-1);
            } else if (e.key === 'ArrowRight') {
                navigateImage(1);
            }
        }
    });
}

// Initialize lightbox navigation
document.addEventListener('DOMContentLoaded', function() {
    initializeLightboxNavigation();
});

console.log('Gallery functionality loaded successfully!');

