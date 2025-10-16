<script>
    import { Link } from "@inertiajs/svelte";
    import { onMount } from "svelte";

    let { onToggleSidebar = () => {} } = $props();

    // Inicializar tooltips de Bootstrap
    onMount(() => {
        if (window.bootstrap) {
            const tooltipTriggerList = document.querySelectorAll(
                '[data-bs-toggle="tooltip"]',
            );
            [...tooltipTriggerList].map(
                (el) => new window.bootstrap.Tooltip(el),
            );
        }
    });
</script>

<header class="app-header">
    <nav class="navbar navbar-expand-lg navbar-light">
        <div class="navbar-nav align-items-center w-100">
            <div
                class="d-flex align-items-center justify-content-between w-100"
            >
                <!-- Lado izquierdo: Toggle y Notificaciones -->
                <div class="d-flex align-items-center gap-3">
                    <!-- Toggle para móvil -->
                    <button
                        class="nav-link sidebartoggler nav-icon-hover d-block d-xl-none border-0 bg-transparent"
                        id="headerCollapse"
                        onclick={onToggleSidebar}
                        type="button"
                        aria-label="Toggle sidebar"
                    >
                        <i class="bi bi-list"></i>
                    </button>

                    <!-- Notificaciones -->
                    <a
                        class="nav-link nav-icon-hover position-relative"
                        aria-label="notifications"
                        href="#!"
                    >
                        <i class="bi bi-bell"></i>
                        <div
                            class="notification bg-primary rounded-circle"
                        ></div>
                    </a>
                </div>

                <!-- Lado derecho: User Dropdown -->
                <div class="d-flex align-items-center">
                    <div class="dropdown">
                        <a
                            class="nav-link nav-icon-hover"
                            href="#!"
                            id="drop2"
                            data-bs-toggle="dropdown"
                            aria-expanded="false"
                        >
                            <img
                                src="/assets/images/profile/user-1.jpg"
                                alt="User profile"
                                width="35"
                                height="35"
                                class="rounded-circle"
                            />
                        </a>
                        <div
                            class="dropdown-menu dropdown-menu-end dropdown-menu-animate-up"
                            aria-labelledby="drop2"
                        >
                            <div class="message-body">
                                <a
                                    href="#!"
                                    class="d-flex align-items-center gap-2 dropdown-item"
                                >
                                    <i class="ti ti-user fs-6"></i>
                                    <p class="mb-0 fs-3">My Profile</p>
                                </a>
                                <a
                                    href="#!"
                                    class="d-flex align-items-center gap-2 dropdown-item"
                                >
                                    <i class="ti ti-mail fs-6"></i>
                                    <p class="mb-0 fs-3">My Account</p>
                                </a>
                                <a
                                    href="#!"
                                    class="d-flex align-items-center gap-2 dropdown-item"
                                >
                                    <i class="ti ti-list-check fs-6"></i>
                                    <p class="mb-0 fs-3">My Task</p>
                                </a>
                                <Link
                                    href="/logout"
                                    method="post"
                                    as="button"
                                    class="btn btn-outline-primary mx-3 mt-2 d-block w-auto"
                                >
                                    Logout
                                </Link>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </nav>
</header>

<style>
    /* Asegurar que el icono de notificación sea visible */
    .notification {
        position: absolute;
        top: 8px;
        right: 8px;
        width: 8px;
        height: 8px;
    }

    /* Asegurar visibilidad del botón toggle */
    .sidebartoggler {
        cursor: pointer;
        padding: 0.5rem;
    }

    .nav-icon-hover:hover {
        background-color: rgba(0, 0, 0, 0.05);
        border-radius: 8px;
    }
</style>
