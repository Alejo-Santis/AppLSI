<script>
    import SidebarNav from "./SidebarNav.svelte";
    import { Link } from "@inertiajs/svelte";

    let { onClose = () => {} } = $props();
</script>

<aside class="left-sidebar">
    <div class="sidebar-content">
        <div
            class="brand-logo d-flex align-items-center justify-content-center"
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

        <div class="divider"></div>

        <SidebarNav />

        <!-- Footer opcional con versión -->
        <div class="sidebar-footer hide-menu">
            <div class="version-badge">
                <i class="bi bi-code-square"></i>
                <span>v1.0.0</span>
            </div>
        </div>
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

    .sidebar-content {
        display: flex;
        flex-direction: column;
        height: 100%;
        padding: 0;
    }

    /* Divider elegante */
    .divider {
        height: 3px;
        background: linear-gradient(
            90deg,
            transparent 0%,
            rgba(93, 135, 255, 0.3) 50%,
            transparent 100%
        );
        margin: 0;
    }

    /* Footer del sidebar */
    .sidebar-footer {
        margin-top: auto;
        padding: 1rem 1.25rem;
        border-top: 1px solid rgba(0, 0, 0, 0.05);
    }

    .version-badge {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        padding: 0.5rem 0.75rem;
        background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
        border-radius: 8px;
        font-size: 0.75rem;
        color: #5a6c7d;
        font-weight: 600;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
    }

    .version-badge i {
        font-size: 1rem;
        color: #667eea;
    }

    /* Responsive */
    @media (max-width: 1199px) {
        .brand-logo {
            padding: 1.25rem 1rem;
        }
    }
</style>
