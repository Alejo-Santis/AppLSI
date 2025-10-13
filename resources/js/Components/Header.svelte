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
        <ul class="navbar-nav">
            <!-- Toggle para móvil -->
            <li class="nav-item d-block d-xl-none">
                <button
                    class="nav-link sidebartoggler nav-icon-hover"
                    id="headerCollapse"
                    onclick={onToggleSidebar}
                    type="button"
                    aria-label="collapse"
                >
                    <i class="ti ti-menu-2"></i>
                </button>
            </li>

            <!-- Notificaciones -->
            <li class="nav-item">
                <a
                    class="nav-link nav-icon-hover"
                    aria-label="notifications"
                    href="#!"
                >
                    <i class="ti ti-bell-ringing"></i>
                    <div class="notification bg-primary rounded-circle"></div>
                </a>
            </li>
        </ul>

        <div class="navbar-collapse justify-content-end px-0" id="navbarNav">
            <ul
                class="navbar-nav flex-row ms-auto align-items-center justify-content-end"
            >
                <!-- <a href="#" target="_blank" class="btn btn-primary me-2">
                    <span class="d-none d-md-block">Check Pro Version</span>
                    <span class="d-block d-md-none">Pro</span>
                </a>
                <a href="#" target="_blank" class="btn btn-success">
                    <span class="d-none d-md-block">Download Free</span>
                    <span class="d-block d-md-none">Free</span>
                </a> -->

                <!-- User Dropdown -->
                <li class="nav-item dropdown">
                    <a
                        class="nav-link nav-icon-hover"
                        href="#!"
                        id="drop2"
                        data-bs-toggle="dropdown"
                        aria-expanded="false"
                    >
                        <img
                            src="/assets/images/profile/user-1.jpg"
                            alt=""
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
                </li>
            </ul>
        </div>
    </nav>
</header>
