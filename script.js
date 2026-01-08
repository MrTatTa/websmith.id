// script.js - JavaScript for Gaming Portfolio Website

document.addEventListener('DOMContentLoaded', function() {
    console.log('WebSmith.id Portfolio loaded successfully!');
    
    // Set current year in footer
    document.getElementById('current-year').textContent = new Date().getFullYear();
    
    // Navbar scroll effect
    const navbar = document.getElementById('navbar');
    const mobileMenuButton = document.getElementById('mobile-menu-button');
    const mobileMenu = document.getElementById('mobile-menu');
    
    // Scroll event listener for navbar
    window.addEventListener('scroll', function() {
        if (window.scrollY > 50) {
            navbar.classList.add('bg-gray-900/95', 'backdrop-blur-sm', 'shadow-xl', 'py-3');
            navbar.classList.remove('py-4');
        } else {
            navbar.classList.remove('bg-gray-900/95', 'backdrop-blur-sm', 'shadow-xl', 'py-3');
            navbar.classList.add('py-4');
        }
        
        // Back to top button visibility
        const backToTopBtn = document.getElementById('back-to-top');
        if (window.scrollY > 300) {
            backToTopBtn.classList.add('show');
        } else {
            backToTopBtn.classList.remove('show');
        }
    });
    
    // Mobile menu toggle
    mobileMenuButton.addEventListener('click', function() {
        mobileMenu.classList.toggle('hidden');
        const icon = mobileMenuButton.querySelector('i');
        if (mobileMenu.classList.contains('hidden')) {
            icon.classList.remove('fa-times');
            icon.classList.add('fa-bars');
        } else {
            icon.classList.remove('fa-bars');
            icon.classList.add('fa-times');
        }
    });
    
    // Close mobile menu when clicking a link
    document.querySelectorAll('.mobile-nav-link').forEach(link => {
        link.addEventListener('click', function() {
            mobileMenu.classList.add('hidden');
            mobileMenuButton.querySelector('i').classList.remove('fa-times');
            mobileMenuButton.querySelector('i').classList.add('fa-bars');
        });
    });
    
    // Smooth scrolling for anchor links
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function(e) {
            const targetId = this.getAttribute('href');
            
            if (targetId === '#') return;
            
            const targetElement = document.querySelector(targetId);
            if (targetElement) {
                e.preventDefault();
                window.scrollTo({
                    top: targetElement.offsetTop - 80,
                    behavior: 'smooth'
                });
            }
        });
    });
    
    // Back to top button
    document.getElementById('back-to-top').addEventListener('click', function() {
        window.scrollTo({
            top: 0,
            behavior: 'smooth'
        });
    });
    
    // Intersection Observer for scroll animations
    const observerOptions = {
        root: null,
        rootMargin: '0px',
        threshold: 0.1
    };
    
    const observer = new IntersectionObserver(function(entries) {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add('fade-up');
            }
        });
    }, observerOptions);
    
    // Observe elements for animation
    document.querySelectorAll('.fade-up').forEach(el => {
        observer.observe(el);
    });
    
    // Gallery Modal
    const galleryItems = document.querySelectorAll('.gallery-item');
    const galleryModal = document.getElementById('gallery-modal');
    const closeModalBtn = document.getElementById('close-modal');
    const modalImage = document.getElementById('modal-image');
    const modalTitle = document.getElementById('modal-title');
    const modalDescription = document.getElementById('modal-description');
    const prevBtn = document.getElementById('prev-btn');
    const nextBtn = document.getElementById('next-btn');
    
    // Gallery data
    const galleryData = [
        {
            title: "Web Development Project",
            description: "A modern website with responsive design and interactive elements built using HTML5, Tailwind CSS, and JavaScript.",
            icon: "fas fa-laptop-code",
            color: "text-cyan-400",
            bg: "bg-gradient-to-br from-cyan-900 to-gray-900"
        },
        {
            title: "Coding Task Solution",
            description: "Completed coding assignment with clean, efficient algorithms and detailed documentation.",
            icon: "fas fa-file-code",
            color: "text-purple-400",
            bg: "bg-gradient-to-br from-purple-900 to-gray-900"
        },
        {
            title: "Laptop Servicing",
            description: "Professional laptop repair and upgrade service including hardware replacement and software optimization.",
            icon: "fas fa-tools",
            color: "text-green-400",
            bg: "bg-gradient-to-br from-green-900 to-gray-900"
        },
        {
            title: "Backend Server Setup",
            description: "Configured server environment with database, API endpoints, and security protocols.",
            icon: "fas fa-server",
            color: "text-cyan-300",
            bg: "bg-gradient-to-br from-cyan-900 to-purple-900"
        },
        {
            title: "Mobile Responsive Design",
            description: "Website optimized for all devices with fluid layouts and touch-friendly interfaces.",
            icon: "fas fa-mobile-alt",
            color: "text-purple-300",
            bg: "bg-gradient-to-br from-purple-900 to-green-900"
        },
        {
            title: "UI/UX Design Project",
            description: "User interface design with focus on usability, accessibility, and modern aesthetics.",
            icon: "fas fa-desktop",
            color: "text-green-300",
            bg: "bg-gradient-to-br from-green-900 to-cyan-900"
        },
        {
            title: "Database Management",
            description: "Designed and implemented efficient database structure with optimized queries.",
            icon: "fas fa-database",
            color: "text-gray-300",
            bg: "bg-gradient-to-br from-gray-800 to-cyan-900"
        },
        {
            title: "Gaming Website",
            description: "Interactive gaming portal with user profiles, leaderboards, and game integration.",
            icon: "fas fa-gamepad",
            color: "text-gray-300",
            bg: "bg-gradient-to-br from-gray-800 to-purple-900"
        }
    ];
    
    let currentGalleryIndex = 0;
    
    // Open modal with gallery item
    galleryItems.forEach((item, index) => {
        item.addEventListener('click', function() {
            currentGalleryIndex = index;
            updateModalContent();
            galleryModal.classList.remove('hidden');
            setTimeout(() => {
                document.querySelector('.modal-content').classList.add('show');
            }, 10);
        });
    });
    
    // Close modal
    closeModalBtn.addEventListener('click', closeModal);
    
    // Close modal when clicking outside
    galleryModal.addEventListener('click', function(e) {
        if (e.target === galleryModal) {
            closeModal();
        }
    });
    
    // Gallery navigation
    prevBtn.addEventListener('click', function() {
        currentGalleryIndex = (currentGalleryIndex - 1 + galleryData.length) % galleryData.length;
        updateModalContent();
    });
    
    nextBtn.addEventListener('click', function() {
        currentGalleryIndex = (currentGalleryIndex + 1) % galleryData.length;
        updateModalContent();
    });
    
    // Keyboard navigation for gallery
    document.addEventListener('keydown', function(e) {
        if (!galleryModal.classList.contains('hidden')) {
            if (e.key === 'Escape') {
                closeModal();
            } else if (e.key === 'ArrowLeft') {
                currentGalleryIndex = (currentGalleryIndex - 1 + galleryData.length) % galleryData.length;
                updateModalContent();
            } else if (e.key === 'ArrowRight') {
                currentGalleryIndex = (currentGalleryIndex + 1) % galleryData.length;
                updateModalContent();
            }
        }
    });
    
    function updateModalContent() {
        const item = galleryData[currentGalleryIndex];
        
        // Clear and update modal content
        modalImage.className = `w-full h-64 md:h-96 flex items-center justify-center ${item.bg}`;
        modalImage.innerHTML = `<i class="${item.icon} ${item.color} text-8xl"></i>`;
        
        modalTitle.textContent = item.title;
        modalDescription.textContent = item.description;
    }
    
    function closeModal() {
        document.querySelector('.modal-content').classList.remove('show');
        setTimeout(() => {
            galleryModal.classList.add('hidden');
        }, 300);
    }
    
    // Contact form validation and submission
    const contactForm = document.getElementById('contact-form');
    const formSuccess = document.getElementById('form-success');
    
    contactForm.addEventListener('submit', function(e) {
        e.preventDefault();
        
        // Reset previous errors
        document.querySelectorAll('.form-error').forEach(el => {
            el.classList.add('hidden');
        });
        
        // Get form values
        const name = document.getElementById('name').value.trim();
        const email = document.getElementById('email').value.trim();
        const message = document.getElementById('message').value.trim();
        
        let isValid = true;
        
        // Validate name
        if (name.length < 3) {
            document.getElementById('name-error').classList.remove('hidden');
            isValid = false;
        }
        
        // Validate email
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if (!emailRegex.test(email)) {
            document.getElementById('email-error').classList.remove('hidden');
            isValid = false;
        }
        
        // Validate message
        if (message.length < 10) {
            document.getElementById('message-error').classList.remove('hidden');
            isValid = false;
        }
        
        // If valid, show success message
        if (isValid) {
            formSuccess.classList.remove('hidden');
            contactForm.reset();
            
            // Scroll to success message
            formSuccess.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
            
            // Hide success message after 5 seconds
            setTimeout(() => {
                formSuccess.classList.add('hidden');
            }, 5000);
        }
    });
    
    // Add focus effects to form inputs
    const formInputs = document.querySelectorAll('.form-input');
    formInputs.forEach(input => {
        input.addEventListener('focus', function() {
            this.parentElement.classList.add('input-focused');
        });
        
        input.addEventListener('blur', function() {
            this.parentElement.classList.remove('input-focused');
        });
    });
    
    // Add hover effect to service cards
    const serviceCards = document.querySelectorAll('.service-card');
    serviceCards.forEach(card => {
        card.addEventListener('mouseenter', function() {
            this.style.transform = 'translateY(-10px)';
        });
        
        card.addEventListener('mouseleave', function() {
            this.style.transform = 'translateY(0)';
        });
    });
    
    // Add loading animation to page
    window.addEventListener('load', function() {
        document.body.classList.add('loaded');
        
        // Trigger initial animations
        setTimeout(() => {
            document.querySelectorAll('.fade-up').forEach(el => {
                el.style.opacity = '1';
                el.style.transform = 'translateY(0)';
            });
        }, 100);
    });
    
    // Add active state to navbar links based on scroll position
    function setActiveNavLink() {
        const sections = document.querySelectorAll('section[id]');
        const scrollPos = window.scrollY + 100;
        
        sections.forEach(section => {
            const sectionTop = section.offsetTop;
            const sectionHeight = section.offsetHeight;
            const sectionId = section.getAttribute('id');
            
            if (scrollPos >= sectionTop && scrollPos < sectionTop + sectionHeight) {
                document.querySelectorAll('.nav-link, .mobile-nav-link').forEach(link => {
                    link.classList.remove('active');
                    if (link.getAttribute('href') === `#${sectionId}`) {
                        link.classList.add('active');
                        link.style.color = '#06b6d4';
                    } else {
                        link.style.color = '';
                    }
                });
            }
        });
    }
    
    window.addEventListener('scroll', setActiveNavLink);
    
    // Initialize
    setActiveNavLink();
});