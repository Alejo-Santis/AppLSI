<script>
    import { Link } from "@inertiajs/svelte";
    import { onMount } from "svelte";

    let { onToggleSidebar = () => {}, user = {} } = $props();

    // Imagen de avatar: usa la del usuario o una por defecto
    let avatarUrl = user.user.avatar
        ? `/storage/${user.user.avatar}`
        : "https://cdn-icons-png.flaticon.com/512/847/847969.png";

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
                <!-- Lado izquierdo -->
                <div class="d-flex align-items-center gap-3">
                    <!-- Toggle -->
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

                <!-- Lado derecho -->
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
                                src={avatarUrl}
                                alt="User profile"
                                width="35"
                                height="35"
                                class="rounded-circle border border-primary-subtle"
                                style="object-fit: cover;"
                            />
                        </a>

                        <div
                            class="dropdown-menu dropdown-menu-end dropdown-menu-animate-up p-2"
                            aria-labelledby="drop2"
                        >
                            <div class="text-center mb-2">
                                <img
                                    src={avatarUrl}
                                    alt="User avatar"
                                    width="60"
                                    height="60"
                                    class="rounded-circle border border-2 border-primary-subtle mb-2"
                                    style="object-fit: cover;"
                                />
                                <p class="fw-semibold mb-0">{user.name}</p>
                                <small class="text-muted text-capitalize"
                                    >{user.role}</small
                                >
                            </div>

                            <a
                                href="/user/profile"
                                class="d-flex align-items-center gap-2 dropdown-item"
                            >
                                <i class="bi bi-person fs-6 text-primary"></i>
                                <p class="mb-0">Mi Perfil</p>
                            </a>

                            <a
                                href="/user/account"
                                class="d-flex align-items-center gap-2 dropdown-item"
                            >
                                <i class="bi bi-gear fs-6 text-secondary"></i>
                                <p class="mb-0">Configuración</p>
                            </a>

                            <Link
                                href="/logout"
                                method="post"
                                as="button"
                                class="btn btn-outline-danger w-100 mt-1"
                            >
                                <i class="bi bi-box-arrow-right"></i> Cerrar sesión
                            </Link>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </nav>
</header>

<style>
    .notification {
        position: absolute;
        top: 8px;
        right: 8px;
        width: 8px;
        height: 8px;
    }

    .sidebartoggler {
        cursor: pointer;
        padding: 0.5rem;
    }

    .nav-icon-hover:hover {
        background-color: rgba(0, 0, 0, 0.05);
        border-radius: 8px;
    }

    .dropdown-menu {
        min-width: 220px;
    }

    .dropdown-item:hover {
        background-color: #f8f9fa;
        border-radius: 6px;
    }
</style>
