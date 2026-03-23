/**
 * PintaPeg - Animaciones GSAP
 * Requiere GSAP + ScrollTrigger
 */

document.addEventListener('DOMContentLoaded', () => {
  // Verificar que GSAP este cargado
  if (typeof gsap === 'undefined') return;

  // Registrar ScrollTrigger
  if (typeof ScrollTrigger !== 'undefined') {
    gsap.registerPlugin(ScrollTrigger);
  }

  // =============================================
  // Hero animations
  // =============================================
  const heroText = document.querySelector('.hero-text');
  const heroImage = document.querySelector('.hero-image');

  if (heroText) {
    const tl = gsap.timeline({ defaults: { ease: 'power3.out' } });

    tl.from('.hero-text h2', {
      y: 50,
      opacity: 0,
      duration: 1,
      delay: 0.3,
    })
    .from('.hero-text p', {
      y: 30,
      opacity: 0,
      duration: 0.8,
    }, '-=0.5')
    .from('.hero-text .btn-hero', {
      y: 20,
      opacity: 0,
      duration: 0.6,
    }, '-=0.4');
  }

  if (heroImage) {
    gsap.from('.hero-image', {
      x: 60,
      opacity: 0,
      duration: 1.2,
      delay: 0.5,
      ease: 'power3.out',
    });
  }

  // =============================================
  // Scroll animations - sections
  // =============================================
  if (typeof ScrollTrigger !== 'undefined') {
    // Section titles
    gsap.utils.toArray('.section-title').forEach(title => {
      gsap.from(title, {
        y: 30,
        opacity: 0,
        duration: 0.8,
        scrollTrigger: {
          trigger: title,
          start: 'top 85%',
          toggleActions: 'play none none none',
        },
      });
    });

    // Value cards / team cards
    gsap.utils.toArray('.value-card, .team-card').forEach((card, i) => {
      gsap.from(card, {
        y: 40,
        opacity: 0,
        duration: 0.6,
        delay: i * 0.15,
        scrollTrigger: {
          trigger: card,
          start: 'top 85%',
          toggleActions: 'play none none none',
        },
      });
    });

    // Product cards - stagger
    gsap.utils.toArray('.products-grid').forEach(grid => {
      const cards = grid.querySelectorAll('.product-card');
      if (cards.length === 0) return;

      gsap.from(cards, {
        y: 30,
        opacity: 0,
        duration: 0.5,
        stagger: 0.1,
        scrollTrigger: {
          trigger: grid,
          start: 'top 85%',
          toggleActions: 'play none none none',
        },
      });
    });

    // Contact grid
    gsap.utils.toArray('.contact-grid > *').forEach((col, i) => {
      gsap.from(col, {
        x: i === 0 ? -40 : 40,
        opacity: 0,
        duration: 0.8,
        scrollTrigger: {
          trigger: col,
          start: 'top 85%',
          toggleActions: 'play none none none',
        },
      });
    });

    // Footer
    gsap.from('.footer-grid', {
      y: 30,
      opacity: 0,
      duration: 0.8,
      scrollTrigger: {
        trigger: '.footer',
        start: 'top 90%',
        toggleActions: 'play none none none',
      },
    });
  }

  // =============================================
  // Product detail animation
  // =============================================
  const detailImg = document.querySelector('.product-detail-img');
  const detailInfo = document.querySelector('.product-detail-info');

  if (detailImg && detailInfo) {
    gsap.from(detailImg, {
      x: -40,
      opacity: 0,
      duration: 0.8,
      delay: 0.2,
      ease: 'power3.out',
    });

    gsap.from(detailInfo, {
      x: 40,
      opacity: 0,
      duration: 0.8,
      delay: 0.4,
      ease: 'power3.out',
    });
  }

  // =============================================
  // Navbar animation
  // =============================================
  gsap.from('.navbar', {
    y: -70,
    duration: 0.6,
    ease: 'power3.out',
    delay: 0.1,
  });
});

// =============================================
// Re-animate products after filter/search
// =============================================
function animateNewProducts() {
  if (typeof gsap === 'undefined') return;

  const cards = document.querySelectorAll('.product-card');
  gsap.from(cards, {
    y: 20,
    opacity: 0,
    duration: 0.4,
    stagger: 0.06,
    ease: 'power2.out',
  });
}
