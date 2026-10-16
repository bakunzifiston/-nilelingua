const menuToggle = document.querySelector('[data-menu-toggle]');
const navigation = document.querySelector('[data-navigation]');
const themeToggle = document.querySelector('[data-theme-toggle]');

if (themeToggle) {
    const savedTheme = localStorage.getItem('nilelingua-theme');

    if (savedTheme === 'light' || savedTheme === 'dark') {
        document.documentElement.dataset.theme = savedTheme;
    }

    const updateThemeButton = () => {
        const isDark = document.documentElement.dataset.theme === 'dark'
            || (!document.documentElement.dataset.theme && window.matchMedia('(prefers-color-scheme: dark)').matches);
        const themeColor = document.querySelector('meta[name="theme-color"]');

        themeToggle.setAttribute('aria-pressed', String(isDark));
        themeToggle.setAttribute('aria-label', `Switch to ${isDark ? 'light' : 'dark'} mode`);
        themeToggle.setAttribute('title', `Switch to ${isDark ? 'light' : 'dark'} mode`);
        themeToggle.querySelector('span').textContent = isDark ? '☀' : '◐';

        if (themeColor) {
            themeColor.content = isDark ? '#0E1816' : '#FAF7F2';
        }
    };

    updateThemeButton();

    themeToggle.addEventListener('click', () => {
        const isDark = document.documentElement.dataset.theme === 'dark'
            || (!document.documentElement.dataset.theme && window.matchMedia('(prefers-color-scheme: dark)').matches);
        const nextTheme = isDark ? 'light' : 'dark';

        document.documentElement.dataset.theme = nextTheme;
        localStorage.setItem('nilelingua-theme', nextTheme);
        updateThemeButton();
    });
}

if (menuToggle && navigation) {
    menuToggle.addEventListener('click', () => {
        const isExpanded = menuToggle.getAttribute('aria-expanded') === 'true';

        menuToggle.setAttribute('aria-expanded', String(!isExpanded));
        navigation.classList.toggle('is-open', !isExpanded);
    });

    navigation.addEventListener('click', (event) => {
        if (event.target instanceof HTMLAnchorElement && window.matchMedia('(max-width: 850px)').matches) {
            menuToggle.setAttribute('aria-expanded', 'false');
            navigation.classList.remove('is-open');
        }
    });
}
