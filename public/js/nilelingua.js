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

        // Labels come from the page so they follow the current language.
        const label = isDark
            ? (themeToggle.dataset.labelLight || 'Switch to light mode')
            : (themeToggle.dataset.labelDark || 'Switch to dark mode');

        themeToggle.setAttribute('aria-pressed', String(isDark));
        themeToggle.setAttribute('aria-label', label);
        themeToggle.setAttribute('title', label);
        themeToggle.querySelector('span').textContent = isDark ? '☀' : '◐';

        if (themeColor) {
            themeColor.content = isDark ? '#0A1518' : '#FFFFFF';
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

    const closeMenu = () => {
        menuToggle.setAttribute('aria-expanded', 'false');
        navigation.classList.remove('is-open');
    };

    navigation.addEventListener('click', (event) => {
        if (event.target instanceof HTMLAnchorElement && navigation.classList.contains('is-open')) {
            closeMenu();
        }
    });

    document.addEventListener('keydown', (event) => {
        if (event.key === 'Escape' && navigation.classList.contains('is-open')) {
            closeMenu();
            menuToggle.focus();
        }
    });
}

// Close open navigation dropdowns when clicking elsewhere or pressing Escape.
const navDropdowns = document.querySelectorAll('[data-navigation] details');

if (navDropdowns.length) {
    document.addEventListener('click', (event) => {
        navDropdowns.forEach((dropdown) => {
            if (dropdown.open && !dropdown.contains(event.target)) {
                dropdown.open = false;
            }
        });
    });

    document.addEventListener('keydown', (event) => {
        if (event.key !== 'Escape') {
            return;
        }

        navDropdowns.forEach((dropdown) => {
            if (dropdown.open) {
                dropdown.open = false;
                dropdown.querySelector('summary')?.focus();
            }
        });
    });
}

// Gentle entrance animations for elements marked with data-reveal.
const revealTargets = document.querySelectorAll('[data-reveal]');

if (revealTargets.length && 'IntersectionObserver' in window && !window.matchMedia('(prefers-reduced-motion: reduce)').matches) {
    document.documentElement.classList.add('nll-reveal-ready');

    const revealObserver = new IntersectionObserver((entries) => {
        entries.forEach((entry) => {
            if (entry.isIntersecting) {
                entry.target.classList.add('is-revealed');
                revealObserver.unobserve(entry.target);
            }
        });
    }, { rootMargin: '0px 0px -8% 0px', threshold: 0.08 });

    revealTargets.forEach((target) => revealObserver.observe(target));
}

// Highlight the table-of-contents entry for the section currently being read.
const tocLinks = document.querySelectorAll('.nll-toc a[href^="#"]');

if (tocLinks.length && 'IntersectionObserver' in window) {
    const linkFor = new Map([...tocLinks].map((link) => [link.getAttribute('href').slice(1), link]));

    const tocObserver = new IntersectionObserver((entries) => {
        entries.forEach((entry) => {
            if (entry.isIntersecting) {
                tocLinks.forEach((link) => link.classList.remove('is-active'));
                linkFor.get(entry.target.id)?.classList.add('is-active');
            }
        });
    }, { rootMargin: '-20% 0px -70% 0px' });

    linkFor.forEach((link, id) => {
        const section = document.getElementById(id);

        if (section) {
            tocObserver.observe(section);
        }
    });
}

// Languages map: highlight a language's map marker and list entry together.
document.querySelectorAll('[data-language-map]').forEach((map) => {
    const setActive = (id) => {
        map.querySelectorAll('[data-map-marker], [data-map-link]').forEach((element) => {
            const elementId = element.dataset.mapMarker ?? element.dataset.mapLink;
            element.classList.toggle('is-active', elementId === id);
        });
    };

    map.querySelectorAll('[data-map-link], [data-map-marker]').forEach((element) => {
        const id = element.dataset.mapLink ?? element.dataset.mapMarker;
        element.addEventListener('mouseenter', () => setActive(id));
        element.addEventListener('mouseleave', () => setActive(null));
    });
});

// Knowledge Center: filter articles by topic and search text.
document.querySelectorAll('[data-article-filter]').forEach((section) => {
    const cards = [...section.querySelectorAll('.nll-article-card')];
    const chips = [...section.querySelectorAll('.nll-chip-filter')];
    const search = section.querySelector('[data-article-search]');
    const empty = section.querySelector('[data-article-empty]');
    let topic = '';

    const apply = () => {
        const query = (search?.value || '').trim().toLowerCase();
        let shown = 0;

        cards.forEach((card) => {
            const visible = (!topic || card.dataset.topic === topic) && (!query || card.dataset.search.includes(query));
            card.hidden = !visible;
            card.classList.add('is-revealed');
            shown += visible ? 1 : 0;
        });

        chips.forEach((chip) => {
            const active = chip.dataset.topicFilter === topic;
            chip.classList.toggle('is-active', active);
            chip.setAttribute('aria-pressed', String(active));
        });

        if (empty) {
            empty.hidden = shown > 0;
        }
    };

    // Topic buttons anywhere on the page (chips, the topics table, "show all").
    document.querySelectorAll('[data-topic-filter]').forEach((button) => {
        button.addEventListener('click', () => {
            topic = button.dataset.topicFilter;
            if (search && !topic) {
                search.value = '';
            }
            apply();
            if (!section.contains(button) || button.closest('[data-article-empty]')) {
                section.scrollIntoView({ behavior: 'smooth', block: 'start' });
            }
        });
    });

    search?.addEventListener('input', apply);
});
