<script>
    import { page } from "@inertiajs/svelte";
    import { Link } from "@inertiajs/svelte";

    // Items del menú (puedes moverlo a un archivo separado)
    const menuItems = [
        {
            section: "Home",
            items: [
                {
                    label: "Dashboard",
                    url: "/dashboard",
                    icon: "solar:home-smile-bold-duotone",
                },
            ],
        },
        {
            section: "UI COMPONENTS",
            items: [
                {
                    label: "Buttons",
                    url: "/ui/buttons",
                    icon: "solar:layers-minimalistic-bold-duotone",
                },
                {
                    label: "Alerts",
                    url: "/ui/alerts",
                    icon: "solar:danger-circle-bold-duotone",
                },
                {
                    label: "Card",
                    url: "/ui/card",
                    icon: "solar:bookmark-square-minimalistic-bold-duotone",
                },
                {
                    label: "Forms",
                    url: "/ui/forms",
                    icon: "solar:file-text-bold-duotone",
                },
                {
                    label: "Typography",
                    url: "/ui/typography",
                    icon: "solar:text-field-focus-bold-duotone",
                },
            ],
        },
        {
            section: "AUTH",
            items: [
                {
                    label: "Login",
                    url: "/login",
                    icon: "solar:login-3-bold-duotone",
                },
                {
                    label: "Register",
                    url: "/register",
                    icon: "solar:user-plus-rounded-bold-duotone",
                },
            ],
        },
        {
            section: "EXTRA",
            items: [
                {
                    label: "Icons",
                    url: "/icons",
                    icon: "solar:sticker-smile-circle-2-bold-duotone",
                },
                {
                    label: "Sample Page",
                    url: "/sample",
                    icon: "solar:planet-3-bold-duotone",
                },
            ],
        },
    ];

    // Estado para submenús expandibles (si los usas)
    let expandedMenus = $state({});

    // Función para verificar si una URL está activa
    function isActive(url) {
        return $page.url === url || $page.url.startsWith(url + "/");
    }

    // Función para toggle de submenús (si agregas submenús colapsables)
    function toggleSubmenu(index) {
        expandedMenus[index] = !expandedMenus[index];
    }
</script>

<nav class="sidebar-nav scroll-sidebar" data-simplebar>
    <ul id="sidebarnav">
        {#each menuItems as section, sectionIndex}
            <li class="nav-small-cap">
                <iconify-icon
                    icon="solar:menu-dots-linear"
                    class="nav-small-cap-icon fs-6"
                ></iconify-icon>
                <span class="hide-menu">{section.section}</span>
            </li>

            {#each section.items as item, itemIndex}
                <li class="sidebar-item {isActive(item.url) ? 'selected' : ''}">
                    <Link
                        href={item.url}
                        class="sidebar-link {isActive(item.url)
                            ? 'active'
                            : ''}"
                        aria-expanded="false"
                    >
                        <span>
                            <iconify-icon icon={item.icon} class="fs-6"
                            ></iconify-icon>
                        </span>
                        <span class="hide-menu">{item.label}</span>
                    </Link>
                </li>
            {/each}
        {/each}
    </ul>

    <!-- Card de Upgrade to Pro -->
    <div
        class="unlimited-access hide-menu bg-primary-subtle position-relative mb-7 mt-7 rounded-3"
    >
        <div class="d-flex">
            <div class="unlimited-access-title me-3">
                <h6 class="fw-semibold fs-4 mb-6 text-dark w-75">
                    Upgrade to pro
                </h6>
                <a
                    href="#"
                    target="_blank"
                    class="btn btn-primary fs-2 fw-semibold lh-sm"
                >
                    Buy Pro
                </a>
            </div>
            <div class="unlimited-access-img">
                <img
                    src="/assets/images/backgrounds/rocket.png"
                    alt=""
                    class="img-fluid"
                />
            </div>
        </div>
    </div>
</nav>

<!-- <style>
    /* Estilos adicionales si necesitas personalizar */
    .sidebar-item.selected > .sidebar-link {
        background-color: rgba(var(--bs-primary-rgb), 0.1);
    }
</style> -->
