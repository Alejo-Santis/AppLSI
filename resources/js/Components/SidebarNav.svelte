<script>
    import { page } from "@inertiajs/svelte";
    import { Link } from "@inertiajs/svelte";

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
            section: "Departamentos",
            items: [
                {
                    label: "Departamentos",
                    url: "/departments",
                    icon: "solar:buildings-bold",
                },
            ],
        },
        {
            section: "Puestos",
            items: [
                {
                    label: "Puestos",
                    url: "/positions",
                    icon: "solar:user-id-bold",
                },
            ],
        },
    ];

    $: currentUrl = $page.url;

    function isActive(url) {
        return currentUrl === url || currentUrl.startsWith(url + "/");
    }
</script>

<nav class="sidebar-nav scroll-sidebar" data-simplebar>
    <ul id="sidebarnav">
        {#each menuItems as section}
            <li class="nav-small-cap">
                <iconify-icon
                    icon="solar:menu-dots-linear"
                    class="nav-small-cap-icon fs-6"
                />
                <span class="hide-menu">{section.section}</span>
            </li>

            {#each section.items as item}
                <li class="sidebar-item {isActive(item.url) ? 'selected' : ''}">
                    <Link
                        href={item.url}
                        class="sidebar-link {isActive(item.url)
                            ? 'active'
                            : ''}"
                    >
                        <span
                            ><iconify-icon
                                icon={item.icon}
                                class="fs-6"
                            /></span
                        >
                        <span class="hide-menu">{item.label}</span>
                    </Link>
                </li>
            {/each}
        {/each}
    </ul>
</nav>

<style>
    .sidebar-item.selected > .sidebar-link {
        background-color: rgba(var(--bs-primary-rgb), 0.1);
        font-weight: 600;
    }

    .sidebar-link.active {
        color: var(--bs-primary);
    }
</style>
