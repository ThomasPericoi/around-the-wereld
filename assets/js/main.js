document.addEventListener('DOMContentLoaded', () => {
    const root = document.documentElement;
    const body = document.body;
    const header = document.getElementById('header');
    const main = document.querySelector('main');
    const footer = document.querySelector('footer');
    const nav = document.querySelector('.nav-wrapper');
    const menuToggle = document.getElementById('menu-toggle');
    const desktopQuery = window.matchMedia('(min-width: 1200px)');
    const reduceMotionQuery = window.matchMedia('(prefers-reduced-motion: reduce)');

    const setElementState = (element, enabled) => {
        if (!element) {
            return;
        }

        element.toggleAttribute('inert', !enabled);
        element.setAttribute('aria-hidden', enabled ? 'false' : 'true');
    };

    const closeSubMenus = () => {
        document.querySelectorAll(".menu-item-has-children[data-opened='true']").forEach((item) => {
            item.dataset.opened = 'false';
            item.classList.remove('active');
            item.querySelector(':scope > a')?.setAttribute('aria-expanded', 'false');
        });
    };

    const setMenuState = (opened) => {
        body.classList.toggle('js-menuOpened', opened);

        if (!opened) {
            closeSubMenus();
        }

        if (menuToggle) {
            menuToggle.checked = opened;
            menuToggle.setAttribute('aria-expanded', opened ? 'true' : 'false');
        }

        if (desktopQuery.matches) {
            closeSubMenus();
            nav?.removeAttribute('inert');
            setElementState(main, true);
            setElementState(footer, true);
            return;
        }

        nav?.toggleAttribute('inert', !opened);
        setElementState(main, !opened);
        setElementState(footer, !opened);
    };

    const closeMenu = () => setMenuState(false);
    const openMenu = () => setMenuState(true);

    const updateLayoutVariables = () => {
        root.style.setProperty('--viewport-height', `${window.innerHeight}px`);
        root.style.setProperty('--scrollbar-width', `${window.innerWidth - root.clientWidth}px`);

        if (header) {
            root.style.setProperty('--header-height', `${header.offsetHeight}px`);
        }
    };

    const onViewportChange = () => {
        updateLayoutVariables();

        if (desktopQuery.matches) {
            closeMenu();
            nav?.removeAttribute('inert');
            return;
        }

        setMenuState(body.classList.contains('js-menuOpened'));
    };

    const initLayoutVariables = () => {
        updateLayoutVariables();
        window.addEventListener('resize', onViewportChange, { passive: true });
        window.visualViewport?.addEventListener('resize', updateLayoutVariables, { passive: true });

        if ('ResizeObserver' in window && header) {
            new ResizeObserver(updateLayoutVariables).observe(header);
        }

        if (typeof desktopQuery.addEventListener === 'function') {
            desktopQuery.addEventListener('change', onViewportChange);
        }
    };

    const initSignature = () => {
        console.info('This theme was made by Thomas Pericoi - https://thomaspericoi.com/');

        if (window.AsciiPrinter && typeof window.AsciiPrinter.printRandom === 'function') {
            window.AsciiPrinter.printRandom('drink');
        }
    };

    const initDyslexicMode = () => {
        const dyslexicToggle = document.getElementById('open-dyslexic');

        if (!dyslexicToggle) {
            return;
        }

        const setDyslexicMode = (enabled) => {
            root.classList.toggle('is-dyslexic', enabled);
            try {
                sessionStorage.setItem('dyslexicMode', String(enabled));
            } catch (error) {
                root.classList.remove('is-dyslexic');
            }
            dyslexicToggle.checked = enabled;
        };

        let isDyslexicModeEnabled = false;

        try {
            isDyslexicModeEnabled = sessionStorage.getItem('dyslexicMode') === 'true';
        } catch (error) {
            isDyslexicModeEnabled = false;
        }

        setDyslexicMode(isDyslexicModeEnabled);
        dyslexicToggle.addEventListener('change', () => setDyslexicMode(dyslexicToggle.checked));
    };

    const initRevealOnScroll = () => {
        const sections = document.querySelectorAll('main section');

        if (!sections.length || reduceMotionQuery.matches) {
            sections.forEach((section) => section.classList.add('js-inView'));
            return;
        }

        if (!('IntersectionObserver' in window)) {
            sections.forEach((section) => section.classList.add('js-inView'));
            return;
        }

        const observer = new IntersectionObserver((entries) => {
            entries.forEach((entry) => {
                if (!entry.isIntersecting) {
                    return;
                }

                entry.target.classList.add('js-inView');
                observer.unobserve(entry.target);
            });
        }, {
            rootMargin: '0px 0px -15% 0px',
            threshold: 0.1,
        });

        sections.forEach((section) => observer.observe(section));
    };

    const initOrderedLists = () => {
        document.querySelectorAll('.formatted ol[start]').forEach((list) => {
            const start = Number.parseInt(list.getAttribute('start'), 10);

            if (!Number.isNaN(start)) {
                list.style.counterReset = `item ${start - 1}`;
            }
        });
    };

    const initMenu = () => {
        setMenuState(false);

        document.querySelectorAll('.menu-toggle').forEach((toggle) => {
            toggle.addEventListener('click', () => {
                body.classList.contains('js-menuOpened') ? closeMenu() : openMenu();
            });
        });

        document.addEventListener('keydown', (event) => {
            if (event.key === 'Escape' && body.classList.contains('js-menuOpened')) {
                closeMenu();
                menuToggle?.focus();
            }
        });

        document.querySelectorAll('.menu-item-has-children').forEach((item) => {
            const link = item.querySelector(':scope > a');

            if (!link) {
                return;
            }

            item.dataset.opened = 'false';
            link.setAttribute('aria-haspopup', 'true');
            link.setAttribute('aria-expanded', 'false');

            link.addEventListener('click', (event) => {
                if (desktopQuery.matches) {
                    return;
                }

                const isOpened = item.dataset.opened === 'true';

                if (isOpened) {
                    return;
                }

                event.preventDefault();

                document.querySelectorAll(".menu-item-has-children[data-opened='true']").forEach((openItem) => {
                    if (item.contains(openItem) || openItem === item || openItem.contains(item)) {
                        return;
                    }

                    openItem.dataset.opened = 'false';
                    openItem.classList.remove('active');
                    openItem.querySelector(':scope > a')?.setAttribute('aria-expanded', 'false');
                });

                item.dataset.opened = 'true';
                item.classList.add('active');
                link.setAttribute('aria-expanded', 'true');
            });
        });
    };

    const initGalleries = () => {
        if (typeof window.Swiper !== 'function') {
            return;
        }

        document.querySelectorAll('.galleries-wrapper').forEach((gallery) => {
            const mainSwiperEl = gallery.querySelector('.gallery.swiper');
            const fullscreenSwiperEl = gallery.querySelector('.swiper.fullscreen-gallery');
            const modal = gallery.querySelector('.fullscreen-modal');
            const maximizeBtn = gallery.querySelector('.js-maximize');
            const minimizeBtn = gallery.querySelector('.js-minimize');

            if (!mainSwiperEl || !fullscreenSwiperEl || !modal) {
                return;
            }

            const mainSwiper = new window.Swiper(mainSwiperEl, {
                slidesPerView: 1,
                autoHeight: true,
                grabCursor: true,
                pagination: { el: gallery.querySelector('.swiper-pagination'), clickable: true },
                navigation: {
                    nextEl: gallery.querySelector('.swiper-button-next'),
                    prevEl: gallery.querySelector('.swiper-button-prev'),
                },
            });

            const fullscreenSwiper = new window.Swiper(fullscreenSwiperEl, {
                slidesPerView: 1,
                autoHeight: true,
                pagination: { el: modal.querySelector('.swiper-pagination'), clickable: true },
                navigation: {
                    nextEl: modal.querySelector('.swiper-button-next'),
                    prevEl: modal.querySelector('.swiper-button-prev'),
                },
            });

            const closeModal = () => {
                modal.classList.add('hidden');
                body.classList.remove('body-freeze');
                mainSwiper.slideTo(fullscreenSwiper.realIndex, 0);
                maximizeBtn?.focus();
            };

            maximizeBtn?.addEventListener('click', () => {
                modal.classList.remove('hidden');
                body.classList.add('body-freeze');
                fullscreenSwiper.slideTo(mainSwiper.realIndex, 0);
                minimizeBtn?.focus();
            });

            minimizeBtn?.addEventListener('click', closeModal);

            modal.addEventListener('keydown', (event) => {
                if (event.key === 'Escape') {
                    closeModal();
                }
            });
        });
    };

    initLayoutVariables();
    initSignature();
    initDyslexicMode();
    initRevealOnScroll();
    initOrderedLists();
    initMenu();
    initGalleries();
});
