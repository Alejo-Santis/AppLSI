<script>
    import SidebarNav from "./SidebarNav.svelte";
    import { Link } from "@inertiajs/svelte";
</script>

<aside class="left-sidebar">
    <div>
        <div
            class="brand-logo d-flex align-items-center justify-content-between"
        >
            <Link href="/dashboard" class="text-nowrap logo-img">
                <img src="/assets/images/logos/logo-light.svg" alt="Logo" />
            </Link>
            <div
                class="close-btn d-xl-none d-block sidebartoggler cursor-pointer"
                id="sidebarCollapse"
            >
                <i class="ti ti-x fs-8"></i>
            </div>
        </div>

        <SidebarNav />
    </div>
</aside>
