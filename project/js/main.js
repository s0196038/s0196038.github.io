document.addEventListener('DOMContentLoaded', function(){
  const toggleMenu = document.querySelector('#toggle-menu');
  const headerMenu = document.querySelector('#header-menu');
  const bodyLock = document.body;

  // Mobile nav
  toggleMenu.addEventListener('click', function(){
    if (this.classList.contains('toggle-menu--active')) {
      this.classList.remove('toggle-menu--active');
      headerMenu.classList.remove('header-menu--active');
      bodyLock.classList.remove('lock');
    } else {
      this.classList.add('toggle-menu--active');
      headerMenu.classList.add('header-menu--active');
      bodyLock.classList.add('lock');
    }
  });

  headerMenu.addEventListener('click', function(){
    this.classList.remove('header-menu--active');
    toggleMenu.classList.remove('toggle-menu--active');
    bodyLock.classList.remove('lock');
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