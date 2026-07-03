document.addEventListener('DOMContentLoaded', () => {
    const root = document.documentElement;
    const body = document.body;
    const header = document.getElementById('header');
    const main = document.getElementById('main');
    const footer = document.querySelector('footer');
    const adminBar = body.classList.contains('admin-bar') ? document.getElementById('wpadminbar') : null;
    const nav = document.querySelector('.nav-wrapper');
    const menuToggle = document.getElementById('menu-toggle');
    const desktopQuery = window.matchMedia('(min-width: 1200px)');
    const reduceMotionQuery = window.matchMedia('(prefers-reduced-motion: reduce)');

    // Toggle inert and aria-hidden together for accessible hidden states.
    const setElementState = (element, enabled) => {
        if (!element) {
            return;
        }

        element.toggleAttribute('inert', !enabled);
        element.setAttribute('aria-hidden', enabled ? 'false' : 'true');
    };

    // Close every opened submenu and reset its accessible state.
    const closeSubMenus = () => {
        document.querySelectorAll(".menu-item-has-children[data-opened='true']").forEach((item) => {
            item.dataset.opened = 'false';
            item.classList.remove('active');
            item.querySelector(':scope > a')?.setAttribute('aria-expanded', 'false');
        });
    };

    // Open or close the main navigation and protect the rest of the page.
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

    // Keep viewport, scrollbar, admin bar, and header values in CSS variables.
    const updateLayoutVariables = () => {
        root.style.setProperty('--viewport-height', `${window.innerHeight}px`);
        root.style.setProperty('--scrollbar-width', `${window.innerWidth - root.clientWidth}px`);
        root.style.setProperty('--admin-bar-visible-height', '0px');

        if (adminBar) {
            const adminBarRect = adminBar.getBoundingClientRect();
            const adminBarVisibleHeight = Math.max(0, Math.min(adminBarRect.bottom, adminBarRect.height));

            root.style.setProperty('--admin-bar-visible-height', `${adminBarVisibleHeight}px`);
        }

        if (header) {
            root.style.setProperty('--header-height', `${header.offsetHeight}px`);
        }
    };

    let layoutTicking = false;
    // Batch layout variable updates into the next animation frame.
    const requestLayoutVariablesUpdate = () => {
        if (layoutTicking) {
            return;
        }

        layoutTicking = true;
        window.requestAnimationFrame(() => {
            updateLayoutVariables();
            layoutTicking = false;
        });
    };

    // Reconcile menu and layout state when the viewport changes.
    const onViewportChange = () => {
        updateLayoutVariables();

        if (desktopQuery.matches) {
            closeMenu();
            nav?.removeAttribute('inert');
            return;
        }

        setMenuState(body.classList.contains('js-menuOpened'));
    };

    // Bind all layout observers and viewport listeners.
    const initLayoutVariables = () => {
        updateLayoutVariables();
        window.addEventListener('resize', onViewportChange, { passive: true });
        window.addEventListener('scroll', requestLayoutVariablesUpdate, { passive: true });
        window.visualViewport?.addEventListener('resize', updateLayoutVariables, { passive: true });
        window.visualViewport?.addEventListener('scroll', requestLayoutVariablesUpdate, { passive: true });

        if ('ResizeObserver' in window && header) {
            new ResizeObserver(updateLayoutVariables).observe(header);
        }

        if ('ResizeObserver' in window && adminBar) {
            new ResizeObserver(updateLayoutVariables).observe(adminBar);
        }

        if (typeof desktopQuery.addEventListener === 'function') {
            desktopQuery.addEventListener('change', onViewportChange);
        }
    };

    // Print the theme signature in the console.
    const initSignature = () => {
        console.info('This theme was made by Thomas Pericoi - https://thomaspericoi.com/');

        if (window.AsciiPrinter && typeof window.AsciiPrinter.printRandom === 'function') {
            window.AsciiPrinter.printRandom('drink');
        }
    };

    // Restore and persist the OpenDyslexic display mode.
    const initDyslexicMode = () => {
        const dyslexicToggle = document.getElementById('open-dyslexic');

        if (!dyslexicToggle) {
            return;
        }

        // Apply the dyslexic mode class and persist the preference safely.
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

    // Reveal main sections progressively while keeping fallbacks for iframes and old browsers.
    const initRevealOnScroll = () => {
        if (!main) {
            return;
        }

        const mainSections = Array.from(main.children).filter((element) => element.tagName === 'SECTION');
        const alwaysVisibleSections = mainSections.filter((section) => section.classList.contains('js-alwaysInView'));
        const sections = mainSections.filter((section) => !section.classList.contains('js-alwaysInView'));

        // Mark a section as visible.
        const revealSection = (section) => {
            section.classList.add('js-inView');
        };

        // Reveal sections that are already inside the viewport.
        const revealVisibleSections = () => {
            sections.forEach((section) => {
                if (section.classList.contains('js-inView')) {
                    return;
                }

                const rect = section.getBoundingClientRect();
                const entersViewport = rect.top < window.innerHeight * 0.9 && rect.bottom > 0;

                if (entersViewport) {
                    revealSection(section);
                }
            });
        };

        // Run a callback after the browser has painted the hidden state.
        const afterInitialPaint = (callback) => {
            window.requestAnimationFrame(() => {
                window.requestAnimationFrame(callback);
            });
        };

        let revealTicking = false;
        // Batch reveal checks to avoid doing layout work on every scroll event.
        const requestRevealVisibleSections = () => {
            if (revealTicking) {
                return;
            }

            revealTicking = true;
            window.requestAnimationFrame(() => {
                revealVisibleSections();
                revealTicking = false;
            });
        };

        alwaysVisibleSections.forEach(revealSection);

        if (!sections.length || reduceMotionQuery.matches) {
            sections.forEach(revealSection);
            return;
        }

        if (!('IntersectionObserver' in window)) {
            afterInitialPaint(revealVisibleSections);
            window.addEventListener('scroll', requestRevealVisibleSections, { passive: true });
            window.addEventListener('resize', requestRevealVisibleSections, { passive: true });
            window.addEventListener('load', () => afterInitialPaint(revealVisibleSections));
            return;
        }

        const observer = new IntersectionObserver((entries) => {
            entries.forEach((entry) => {
                if (!entry.isIntersecting) {
                    return;
                }

                revealSection(entry.target);
                observer.unobserve(entry.target);
            });
        }, {
            rootMargin: '0px 0px -15% 0px',
            threshold: 0.1,
        });

        afterInitialPaint(() => {
            sections.forEach((section) => observer.observe(section));
            revealVisibleSections();
        });
        window.addEventListener('scroll', requestRevealVisibleSections, { passive: true });
        window.addEventListener('resize', requestRevealVisibleSections, { passive: true });
        window.addEventListener('load', () => afterInitialPaint(revealVisibleSections));
    };

    // Preserve custom ordered-list starts inside formatted content.
    const initOrderedLists = () => {
        document.querySelectorAll('.formatted ol[start]').forEach((list) => {
            const start = Number.parseInt(list.getAttribute('start'), 10);

            if (!Number.isNaN(start)) {
                list.style.counterReset = `item ${start - 1}`;
            }
        });
    };

    // Highlight the current menu section while scrolling long menu pages.
    const initMenuSectionsNav = () => {
        const navLinks = Array.from(document.querySelectorAll('.menu-sections-nav a[href^="#"]'));

        if (!navLinks.length) {
            return;
        }

        const linksBySectionId = new Map();
        const sections = navLinks.map((link) => {
            const sectionId = decodeURIComponent(link.hash.slice(1));
            const section = sectionId ? document.getElementById(sectionId) : null;

            if (section) {
                linksBySectionId.set(sectionId, link);
            }

            return section;
        }).filter(Boolean);

        if (!sections.length) {
            return;
        }

        // Update the active section link and its aria-current state.
        const setActiveSection = (section) => {
            navLinks.forEach((link) => {
                const isActive = link === linksBySectionId.get(section.id);

                link.classList.toggle('is-active', isActive);

                if (isActive) {
                    link.setAttribute('aria-current', 'location');
                    return;
                }

                link.removeAttribute('aria-current');
            });
        };

        // Resolve the section currently sitting below the fixed header.
        const getCurrentSection = () => {
            const offset = (header?.offsetHeight || 0) + 40;

            return sections.reduce((currentSection, section) => {
                const rect = section.getBoundingClientRect();

                if (rect.top <= offset && rect.bottom > offset) {
                    return section;
                }

                if (rect.top <= offset) {
                    return section;
                }

                return currentSection;
            }, sections[0]);
        };

        let menuSectionsTicking = false;
        // Batch active-section updates while scrolling.
        const updateActiveSection = () => {
            if (menuSectionsTicking) {
                return;
            }

            menuSectionsTicking = true;
            window.requestAnimationFrame(() => {
                setActiveSection(getCurrentSection());
                menuSectionsTicking = false;
            });
        };

        setActiveSection(getCurrentSection());
        window.addEventListener('scroll', updateActiveSection, { passive: true });
        window.addEventListener('resize', updateActiveSection, { passive: true });
    };

    // Initialize Leaflet maps only when map markup and Leaflet are available.
    const initContactMaps = () => {
        const mapElements = document.querySelectorAll('[data-leaflet-map]');

        if (!mapElements.length || typeof window.L !== 'object') {
            return;
        }

        mapElements.forEach((mapElement) => {
            const latitude = Number.parseFloat(mapElement.dataset.latitude);
            const longitude = Number.parseFloat(mapElement.dataset.longitude);
            const zoom = Number.parseInt(mapElement.dataset.zoom, 10) || 16;

            if (Number.isNaN(latitude) || Number.isNaN(longitude)) {
                return;
            }

            const map = window.L.map(mapElement, {
                attributionControl: true,
                boxZoom: true,
                doubleClickZoom: true,
                dragging: true,
                keyboard: true,
                scrollWheelZoom: false,
                tap: true,
                touchZoom: true,
            }).setView([latitude, longitude], zoom);

            window.L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a>',
                maxZoom: 19,
            }).addTo(map);

            const markerIcon = window.L.divIcon({
                className: 'contact-map-marker',
                html: '<span></span>',
                iconAnchor: [20, 40],
                iconSize: [40, 40],
                popupAnchor: [0, -40],
            });

            const marker = window.L.marker([latitude, longitude], {
                icon: markerIcon,
                keyboard: false,
            }).addTo(map);

            if (mapElement.dataset.markerTitle || mapElement.dataset.markerSubtitle) {
                const popupContent = document.createElement('div');
                popupContent.className = 'contact-map-popup';

                if (mapElement.dataset.markerTitle) {
                    const title = document.createElement('strong');
                    title.textContent = mapElement.dataset.markerTitle;
                    popupContent.append(title);
                }

                if (mapElement.dataset.markerSubtitle) {
                    const subtitle = document.createElement('span');
                    subtitle.textContent = mapElement.dataset.markerSubtitle;
                    popupContent.append(subtitle);
                }

                marker.bindPopup(popupContent, {
                    closeButton: false,
                    offset: [0, -4],
                }).openPopup();
            }
        });
    };

    // Wire the responsive menu and mobile submenu behaviour.
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

    // Initialize Swiper galleries and their fullscreen modal.
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

            // Close the fullscreen gallery and restore the main gallery position.
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
    initMenuSectionsNav();
    initContactMaps();
    initMenu();
    initGalleries();
});
