document.addEventListener('DOMContentLoaded', function() {
    const toggleMenu = document.getElementById('toggle-menu');
    const fullscreenMenu = document.getElementById('fullscreen-menu');
    const menuLinks = document.querySelectorAll('.menu-link');
    const body = document.body;
    
    // Открытие/закрытие меню
    toggleMenu.addEventListener('click', function() {
        fullscreenMenu.classList.toggle('active');
    });
    
    // Закрытие меню при клике на ссылку
    menuLinks.forEach(link => {
        link.addEventListener('click', function() {
            fullscreenMenu.classList.remove('active');
        });
    });
    
    // Закрытие меню при клике вне меню
    fullscreenMenu.addEventListener('click', function(e) {
        if (e.target === this) {
            this.classList.remove('active');
        }
    });

  // Swiper
  const swiper = new Swiper('.swiper', {
      loop: false,
      speed: 1000,
      slidesPerView: 'auto',
      spaceBetween: 40,
      grabCursor: true,

      breakpoints: {
        768: {
          spaceBetween: 40
        },

        0: {
          spaceBetween: 20
        },
      },

      navigation: {
        nextEl: '#sliderNext',
        prevEl: '#sliderPrev',
      },
      
      scrollbar: {
        el: '.scrollbar',
      },
    });


});