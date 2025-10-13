<script>
    import { onMount } from "svelte";
    import Sidebar from "@components/Sidebar.svelte";
    import Header from "@components/Header.svelte";

    let { children } = $props();

    // Estado del sidebar
    let isMiniSidebar = $state(false);
    let showSidebar = $state(false);
    let windowWidth = $state(0);

    // Función para determinar si debe ser mini-sidebar
    function updateSidebarType() {
        windowWidth = window.innerWidth;
        if (windowWidth < 1199) {
            isMiniSidebar = true;
        } else {
            isMiniSidebar = false;
            showSidebar = false; // Cerrar overlay en desktop
        }
    }

    // Toggle manual del sidebar (botón hamburguesa)
    function toggleSidebar() {
        if (windowWidth < 1199) {
            // En móvil: mostrar/ocultar overlay
            showSidebar = !showSidebar;
        } else {
            // En desktop: mini/full sidebar
            isMiniSidebar = !isMiniSidebar;
        }
    }

    // Cerrar sidebar al hacer click fuera (solo móvil)
    function handleOutsideClick(e) {
        if (windowWidth < 1199 && showSidebar) {
            if (
                !e.target.closest(".left-sidebar") &&
                !e.target.closest(".sidebartoggler")
            ) {
                showSidebar = false;
            }
        }
    }

    onMount(() => {
        updateSidebarType();
        window.addEventListener("resize", updateSidebarType);
        document.addEventListener("click", handleOutsideClick);

        return () => {
            window.removeEventListener("resize", updateSidebarType);
            document.removeEventListener("click", handleOutsideClick);
        };
    });
</script>

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
    <Sidebar />

    <div class="body-wrapper">
        <Header onToggleSidebar={toggleSidebar} />

        <div class="container-fluid">
            {@render children()}

            <!-- Footer -->
            <div class="py-6 px-6 text-center">
                <p class="mb-0 fs-4">
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
                </p>
            </div>
        </div>
    </div>
</div>

<style>
    /* Overlay para móvil */
    @media (max-width: 1199px) {
        .page-wrapper::before {
            content: "";
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            z-index: 99;
            opacity: 0;
            visibility: hidden;
            transition: all 0.3s;
        }

        .page-wrapper.show-sidebar::before {
            opacity: 1;
            visibility: visible;
        }
    }
</style>
