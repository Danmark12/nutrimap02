  document.addEventListener('DOMContentLoaded', () => {
    const burgerBtn = document.getElementById('burgerBtn');
    const mobileMenu = document.getElementById('mobileMenu');
    burgerBtn.addEventListener('click', () => mobileMenu.classList.toggle('hidden'));

    const aboutBtn = document.getElementById('aboutBtn');
    const aboutDropdown = document.getElementById('aboutDropdown');
    const aboutArrow = document.getElementById('aboutArrow');
    aboutBtn.addEventListener('click', (e) => {
      e.stopPropagation();
      aboutDropdown.classList.toggle('hidden');
      aboutArrow.classList.toggle('rotate-180');
    });

    document.addEventListener('click', () => {
      if (!aboutDropdown.classList.contains('hidden')) {
        aboutDropdown.classList.add('hidden');
        aboutArrow.classList.remove('rotate-180');
      }
    });

    const mobileAboutBtn = document.getElementById('mobileAboutBtn');
    const mobileAboutDropdown = document.getElementById('mobileAboutDropdown');
    const mobileAboutArrow = document.getElementById('mobileAboutArrow');
    mobileAboutBtn.addEventListener('click', (e) => {
      e.stopPropagation();
      mobileAboutDropdown.classList.toggle('hidden');
      mobileAboutArrow.classList.toggle('rotate-180');
    });
  });
 