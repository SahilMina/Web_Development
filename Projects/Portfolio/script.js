// Wait for DOM to be fully loaded
document.addEventListener('DOMContentLoaded', function() {
    // Add cursor effect
    const cursor = document.createElement('div');
    cursor.className = 'cursor-fx';
    document.body.appendChild(cursor);

    document.addEventListener('mousemove', function(e) {
        cursor.style.left = e.clientX + 'px';
        cursor.style.top = e.clientY + 'px';
    });

    document.addEventListener('mousedown', function() {
        cursor.classList.add('cursor-fx-click');
        setTimeout(() => {
            cursor.classList.remove('cursor-fx-click');
        }, 500);
    });
    
    // Mobile navigation toggle
    const menuToggle = document.querySelector('.menu-toggle');
    const nav = document.querySelector('nav ul');
    
    if (menuToggle) {
        menuToggle.addEventListener('click', function() {
            nav.classList.toggle('show');
            menuToggle.classList.toggle('active');
        });
    }
    
    // Type writer effect for about text
    const aboutText = document.querySelector('#about p');
    if (aboutText) {
        const text = aboutText.textContent;
        aboutText.textContent = '';
        let i = 0;
        
        function typeWriter() {
            if (i < text.length) {
                aboutText.textContent += text.charAt(i);
                i++;
                setTimeout(typeWriter, 30);
            }
        }
        
        // Start the typing animation when about section is in view
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    typeWriter();
                    observer.unobserve(entry.target);
                }
            });
        }, { threshold: 0.5 });
        
        observer.observe(document.querySelector('#about .section-content'));
    }
    
    // Gallery image modal
    const galleryItems = document.querySelectorAll('.gallery-item');
    const body = document.body;
    
    galleryItems.forEach(item => {
        item.addEventListener('click', function() {
            const imgSrc = this.querySelector('img').src;
            const title = this.querySelector('h3').textContent;
            const description = this.querySelector('p').textContent;
            
            // Create modal
            const modal = document.createElement('div');
            modal.className = 'modal';
            
            // Create modal content with sci-fi theme
            modal.innerHTML = `
                <div class="modal-content">
                    <div class="modal-scanner"></div>
                    <span class="close-modal">&times;</span>
                    <img src="${imgSrc}" alt="${title}">
                    <div class="modal-info">
                        <h3>${title}</h3>
                        <div class="tech-specs">
                            <div class="spec-item">
                                <span class="spec-label">Project Type</span>
                                <span class="spec-value">Digital Media</span>
                            </div>
                            <div class="spec-item">
                                <span class="spec-label">Status</span>
                                <span class="spec-value completed">Completed</span>
                            </div>
                        </div>
                        <p>${description}</p>
                    </div>
                </div>
            `;
            
            // Add modal to body
            body.appendChild(modal);
            
            // Prevent scroll on body
            body.classList.add('modal-open');
            
            // Show modal with animation
            setTimeout(() => {
                modal.classList.add('show');
            }, 10);
            
            // Close modal when clicking on close button
            const closeBtn = modal.querySelector('.close-modal');
            closeBtn.addEventListener('click', closeModal);
            
            // Close modal when clicking outside of content
            modal.addEventListener('click', function(e) {
                if (e.target === modal) {
                    closeModal();
                }
            });
            
            // Close modal with Escape key
            document.addEventListener('keydown', function(e) {
                if (e.key === 'Escape') {
                    closeModal();
                }
            });
            
            function closeModal() {
                modal.classList.remove('show');
                
                // Remove modal after animation completes
                setTimeout(() => {
                    body.removeChild(modal);
                    body.classList.remove('modal-open');
                }, 300);
            }
        });
    });
    
    // Smooth scrolling for navigation links
    const navLinks = document.querySelectorAll('nav a[href^="#"]');
    
    navLinks.forEach(link => {
        link.addEventListener('click', function(e) {
            e.preventDefault();
            
            // Close mobile menu if open
            if (nav && nav.classList.contains('show')) {
                nav.classList.remove('show');
                if (menuToggle) {
                    menuToggle.classList.remove('active');
                }
            }
            
            const targetId = this.getAttribute('href');
            const targetElement = document.querySelector(targetId);
            
            if (targetElement) {
                const headerHeight = document.querySelector('header').offsetHeight;
                const offsetTop = targetElement.offsetTop - headerHeight;
                
                window.scrollTo({
                    top: offsetTop,
                    behavior: 'smooth'
                });
            }
        });
    });
    
    // Animate elements when they come into view
    function animateOnScroll(elements, className) {
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.add(className);
                    observer.unobserve(entry.target);
                }
            });
        }, { threshold: 0.1 });
        
        elements.forEach(el => {
            observer.observe(el);
        });
    }
    
    // Animate section titles
    animateOnScroll(document.querySelectorAll('h2'), 'animate-title');
    
    // Animate gallery items
    animateOnScroll(document.querySelectorAll('.gallery-item'), 'animate-fade-in');
    
    // Animate contact items
    animateOnScroll(document.querySelectorAll('.contact-item'), 'animate-slide-up');
    
    // Progress bar animation
    const skills = document.querySelectorAll('.skill');
    
    skills.forEach((skill, index) => {
        const progressBar = skill.querySelector('.progress');
        const width = progressBar.style.width;
        progressBar.style.width = '0';
        
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    // Add delay based on index for staggered animation
                    setTimeout(() => {
                        progressBar.style.width = width;
                    }, 100 * index);
                    observer.unobserve(entry.target);
                }
            });
        }, { threshold: 0.5 });
        
        observer.observe(skill);
    });
    
    // Create scanning line effect for skills section
    const skillsSection = document.querySelector('#skills .section-content');
    if (skillsSection) {
        const scanLine = document.createElement('div');
        scanLine.className = 'scan-line';
        skillsSection.appendChild(scanLine);
        
        function animateScanLine() {
            scanLine.style.top = '0';
            scanLine.style.opacity = '1';
            
            setTimeout(() => {
                scanLine.style.top = '100%';
                
                setTimeout(() => {
                    scanLine.style.opacity = '0';
                    setTimeout(() => {
                        scanLine.style.top = '0';
                        setTimeout(animateScanLine, 1000);
                    }, 500);
                }, 1000);
            }, 100);
        }
        
        // Start scan line animation when skills section is in view
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    setTimeout(animateScanLine, 500);
                    observer.unobserve(entry.target);
                }
            });
        }, { threshold: 0.2 });
        
        observer.observe(skillsSection);
    }
}); 