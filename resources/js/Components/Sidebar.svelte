<script>
    import SidebarNav from "./SidebarNav.svelte";
    import { Link } from "@inertiajs/svelte";

    let { onClose = () => {} } = $props();
</script>

<aside class="left-sidebar">
    <div>
        <div
            class="brand-logo d-flex align-items-center justify-content-between"
        >
            <Link href="/dashboard" class="text-nowrap logo-img">
                <img src="/assets/images/logos/seodashlogo.png" alt="Logo" />
                <span class="p-2 fs-5">AppLSI</span>
            </Link>

            <!-- Botón de cerrar solo visible en móvil -->
            <button
                class="close-btn d-xl-none d-block border-0 bg-transparent"
                onclick={onClose}
                aria-label="Close sidebar"
            >
                <i class="bi bi-x fs-3"></i>
                <!-- O si usas Tabler Icons: <i class="ti ti-x fs-8"></i> -->
            </button>
        </div>

        <SidebarNav />
    </div>
</aside>

<style>
    .close-btn {
        cursor: pointer;
        padding: 0.5rem;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: all 0.2s ease;
    }

    .close-btn:hover {
        background-color: rgba(0, 0, 0, 0.05);
        border-radius: 8px;
        transform: rotate(90deg);
    }
</style>
