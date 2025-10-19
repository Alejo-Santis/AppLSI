<script>
    import { onMount } from "svelte";
    import Sidebar from "@components/Sidebar.svelte";
    import Header from "@components/Header.svelte";
    import SwalAlert from "@components/SwalAlert.svelte"; // ← NUEVO
    import { page } from "@inertiajs/svelte";

    let { children } = $props();

    // Estado del sidebar
    let isMiniSidebar = $state(false);
    let showSidebar = $state(false);
    let windowWidth = $state(0);
    let authUser = $page.props.auth;

    // Función para determinar si debe ser mini-sidebar
    function updateSidebarType() {
        windowWidth = window.innerWidth;
        if (windowWidth < 1199) {
            isMiniSidebar = true;
        } else {
            isMiniSidebar = false;
            showSidebar = false;
        }
    }

    // Toggle manual del sidebar (botón hamburguesa)
    function toggleSidebar() {
        if (windowWidth < 1199) {
            showSidebar = !showSidebar;
            if (showSidebar) {
                document.body.style.overflow = "hidden";
            } else {
                document.body.style.overflow = "";
            }
        } else {
            isMiniSidebar = !isMiniSidebar;
        }
    }

    // Cerrar sidebar al hacer click en el overlay
    function closeOnOverlay(e) {
        if (e.target.classList.contains("sidebar-overlay")) {
            showSidebar = false;
            document.body.style.overflow = "";
        }
    }

    onMount(() => {
        updateSidebarType();
        window.addEventListener("resize", updateSidebarType);

        return () => {
            window.removeEventListener("resize", updateSidebarType);
            document.body.style.overflow = "";
        };
    });
</script>

<!-- Componente de alertas SweetAlert2 (se ejecuta automáticamente) -->
<SwalAlert />

<div
    class="page-wrapper"
    id="main-wrapper"
    data-layout="vertical"
    data-navbarbg="skin6"
    data-sidebartype={isMiniSidebar ? "mini-sidebar" : "full"}
    data-sidebar-position="fixed"
    data-header-position="fixed"
    class:mini-sidebar={isMiniSidebar}
    class:show-sidebar={showSidebar}
>
    <!-- Overlay PRIMERO, luego Sidebar (solo móvil) -->
    {#if showSidebar}
        <div
            class="sidebar-overlay"
            onclick={closeOnOverlay}
            role="button"
            tabindex="0"
            aria-label="Close sidebar"
        ></div>
    {/if}

    <!-- Sidebar -->
    <Sidebar
        onClose={() => {
            showSidebar = false;
            document.body.style.overflow = "";
        }}
    />

    <!-- Contenido principal -->
    <div class="body-wrapper">
        <Header onToggleSidebar={toggleSidebar} user={authUser} />

        <div class="container-fluid">
            {@render children()}

            <!-- Footer -->
            <div class="py-6 px-6 text-center">
                Design and Developed by
                <a
                    href="#"
                    target="_blank"
                    class="pe-1 text-primary text-decoration-underline"
                >
                    AlejoSat-Dev
                </a>
                Social Media
                <a
                    href="#"
                    target="_blank"
                    class="pe-1 text-primary text-decoration-underline"
                >
                    GitHub
                </a>
            </div>
        </div>
    </div>
</div>

<style>
    /* Solo ajustes MÍNIMOS para móvil, desktop usa el CSS original del template */

    /* Overlay para móvil */
    .sidebar-overlay {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0, 0, 0, 0.5);
        z-index: 1040;
        display: block;
        cursor: pointer;
    }

    /* SOLO MÓVIL: Sidebar oculto por defecto */
    @media (max-width: 1199px) {
        :global(.left-sidebar) {
            position: fixed !important;
            left: -270px !important;
            top: 0 !important;
            height: 100vh !important;
            width: 270px !important;
            z-index: 1050 !important;
            transition: left 0.3s ease !important;
            background: white !important;
        }

        /* Mostrar sidebar en móvil cuando está activo */
        :global(.page-wrapper.show-sidebar .left-sidebar) {
            left: 0 !important;
            box-shadow: 0 0 30px rgba(0, 0, 0, 0.3) !important;
        }
    }

    /* DESKTOP: Dejar que el CSS del template maneje todo */
    @media (min-width: 1200px) {
        .sidebar-overlay {
            display: none !important;
        }
    }
</style>
