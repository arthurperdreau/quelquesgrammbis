const menuButton = document.querySelector('[aria-controls="mobile-menu"]');
const mobileMenu = document.getElementById('mobile-menu');
menuButton.addEventListener('click', () => {
    const isMenuVisible = mobileMenu.classList.contains('block');

    if (isMenuVisible) {
        mobileMenu.classList.remove('block');
        mobileMenu.classList.add('hidden');
    } else {
        mobileMenu.classList.remove('hidden');
        mobileMenu.classList.add('block');
    }
});


