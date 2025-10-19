<script>
    import { inertia, router } from "@inertiajs/svelte";
    import Swal from "sweetalert2";

    let { user } = $props();

    let showEditModal = $state(false);
    let showPasswordModal = $state(false);

    let name = $state(user.name);
    let email = $state(user.email);
    let password = $state("");
    let password_confirmation = $state("");

    const updateInfo = (e) => {
        e.preventDefault();
        router.post(
            "/user/info/update",
            { name, email },
            {
                onSuccess: () => {
                    Swal.fire({
                        icon: "success",
                        title: "Información actualizada",
                        text: "Tu información fue actualizada correctamente.",
                        confirmButtonColor: "#0d6efd",
                    });
                    showEditModal = false;
                },
                onError: () => {
                    Swal.fire({
                        icon: "error",
                        title: "Error",
                        text: "Ocurrió un problema al actualizar la información.",
                    });
                },
            },
        );
    };

    const updatePassword = (e) => {
        e.preventDefault();
        router.post(
            "/user/password/update",
            { password, password_confirmation },
            {
                onSuccess: () => {
                    Swal.fire({
                        icon: "success",
                        title: "Contraseña actualizada",
                        text: "Tu contraseña fue cambiada correctamente.",
                        confirmButtonColor: "#0d6efd",
                    });
                    showPasswordModal = false;
                    password = "";
                    password_confirmation = "";
                },
                onError: () => {
                    Swal.fire({
                        icon: "error",
                        title: "Error",
                        text: "No se pudo actualizar la contraseña.",
                    });
                },
            },
        );
    };
</script>

<div class="container-fluid mt-4">
    <!-- Header Card -->
    <div class="card">
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-start">
                <div class="d-flex gap-3 align-items-start">
                    <!-- Avatar -->
                    <div>
                        {#if user.avatar}
                            <img
                                src="/storage/{user.avatar}"
                                alt={user.name}
                                class="rounded"
                                style="width: 100px; height: 100px; object-fit: cover;"
                            />
                        {:else}
                            <div
                                class="rounded bg-light-primary text-primary d-flex align-items-center justify-content-center text-uppercase fw-bold"
                                style="width: 100px; height: 100px; font-size: 32px;"
                            >
                                {user.name.charAt(0)}
                            </div>
                        {/if}
                    </div>

                    <!-- Info -->
                    <div>
                        <div class="d-flex align-items-center gap-2 mb-2">
                            <h5 class="card-title fw-semibold mb-0">
                                {user.name}
                            </h5>
                            <span class="badge bg-light-info text-info">
                                <i class="bi bi-person-lock"></i>
                                {user.role}
                            </span>
                        </div>

                        <p class="text-muted mb-1">
                            <i class="bi bi-envelope"></i>
                            {user.email}
                        </p>

                        {#if user.last_login}
                            <p class="text-muted mb-0">
                                <i class="bi bi-clock-history"></i>
                                Último acceso: {new Date(
                                    user.last_login,
                                ).toLocaleString("es-CO", {
                                    year: "numeric",
                                    month: "long",
                                    day: "numeric",
                                    hour: "2-digit",
                                    minute: "2-digit",
                                })}
                            </p>
                        {/if}
                    </div>
                </div>

                <div class="d-flex gap-2">
                    <button
                        class="btn btn-primary"
                        onclick={() => (showEditModal = true)}
                    >
                        <i class="bi bi-pencil"></i>
                        Editar Información
                    </button>
                    <button
                        class="btn btn-outline-danger"
                        onclick={() => (showPasswordModal = true)}
                    >
                        <i class="bi bi-lock"></i>
                        Cambiar Contraseña
                    </button>
                    <a
                        class="btn btn-outline-secondary"
                        href="/dashboard"
                        use:inertia
                    >
                        <i class="bi bi-arrow-left"></i>
                        Volver
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Info Cards -->
    <div class="card mt-3">
        <div class="card-body">
            <h5 class="mb-4">
                <i class="bi bi-person-gear"></i>
                Configuración de Cuenta
            </h5>

            <div class="row">
                <div class="col-md-6">
                    <h6 class="mb-3 text-muted">
                        <i class="bi bi-info-circle"></i>
                        Información Personal
                    </h6>

                    <div class="mb-3">
                        <small class="text-muted d-block">Nombre Completo</small
                        >
                        <span class="fw-semibold">{user.name}</span>
                    </div>

                    <div class="mb-3">
                        <small class="text-muted d-block"
                            >Correo Electrónico</small
                        >
                        <span>{user.email}</span>
                    </div>

                    <div class="mb-3">
                        <small class="text-muted d-block"
                            >Rol en el Sistema</small
                        >
                        <span class="badge bg-light-info text-info"
                            >{user.role}</span
                        >
                    </div>
                </div>

                <div class="col-md-6">
                    <h6 class="mb-3 text-muted">
                        <i class="bi bi-clock-history"></i>
                        Actividad
                    </h6>

                    {#if user.last_login}
                        <div class="mb-3">
                            <small class="text-muted d-block"
                                >Último Acceso</small
                            >
                            <span
                                >{new Date(user.last_login).toLocaleString(
                                    "es-CO",
                                    {
                                        year: "numeric",
                                        month: "long",
                                        day: "numeric",
                                        hour: "2-digit",
                                        minute: "2-digit",
                                    },
                                )}</span
                            >
                        </div>
                    {/if}

                    {#if user.created_at}
                        <div class="mb-3">
                            <small class="text-muted d-block"
                                >Miembro Desde</small
                            >
                            <span
                                >{new Date(user.created_at).toLocaleDateString(
                                    "es-CO",
                                    {
                                        year: "numeric",
                                        month: "long",
                                        day: "numeric",
                                    },
                                )}</span
                            >
                        </div>
                    {/if}
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Edit Info -->
{#if showEditModal}
    <div
        class="modal fade show d-block"
        tabindex="-1"
        style="background: rgba(0,0,0,0.5);"
    >
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title text-white">
                        <i class="bi bi-pencil"></i>
                        Editar Información
                    </h5>
                    <button
                        type="button"
                        class="btn-close btn-close-white"
                        onclick={() => (showEditModal = false)}
                        aria-label="close"
                    ></button>
                </div>
                <form onsubmit={updateInfo}>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label fw-semibold" for="name">
                                <i class="bi bi-person"></i>
                                Nombre Completo
                            </label>
                            <input
                                id="name"
                                type="text"
                                class="form-control"
                                bind:value={name}
                                required
                            />
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold" for="email">
                                <i class="bi bi-envelope"></i>
                                Correo Electrónico
                            </label>
                            <input
                                id="email"
                                type="email"
                                class="form-control"
                                bind:value={email}
                                required
                            />
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button
                            type="button"
                            class="btn btn-light"
                            onclick={() => (showEditModal = false)}
                        >
                            <i class="bi bi-x-circle"></i>
                            Cancelar
                        </button>
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-check-circle"></i>
                            Guardar Cambios
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
{/if}

<!-- Modal Change Password -->
{#if showPasswordModal}
    <div
        class="modal fade show d-block"
        tabindex="-1"
        style="background: rgba(0,0,0,0.5);"
    >
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title text-white">
                        <i class="bi bi-shield-lock"></i>
                        Cambiar Contraseña
                    </h5>
                    <button
                        type="button"
                        class="btn-close btn-close-white"
                        onclick={() => (showPasswordModal = false)}
                        aria-label="close"
                    ></button>
                </div>
                <form onsubmit={updatePassword}>
                    <div class="modal-body">
                        <div class="alert alert-info">
                            <i class="bi bi-info-circle"></i>
                            La contraseña debe tener al menos 6 caracteres.
                        </div>

                        <div class="mb-3">
                            <label
                                class="form-label fw-semibold"
                                for="password"
                            >
                                <i class="bi bi-key"></i>
                                Nueva Contraseña
                            </label>
                            <input
                                id="password"
                                type="password"
                                class="form-control"
                                bind:value={password}
                                required
                                minlength="6"
                                placeholder="Mínimo 6 caracteres"
                            />
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold" for="confirm">
                                <i class="bi bi-key-fill"></i>
                                Confirmar Contraseña
                            </label>
                            <input
                                id="confirm"
                                type="password"
                                class="form-control"
                                bind:value={password_confirmation}
                                required
                                minlength="6"
                                placeholder="Repite la contraseña"
                            />
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button
                            type="button"
                            class="btn btn-light"
                            onclick={() => (showPasswordModal = false)}
                        >
                            <i class="bi bi-x-circle"></i>
                            Cancelar
                        </button>
                        <button type="submit" class="btn btn-danger">
                            <i class="bi bi-check-circle"></i>
                            Actualizar Contraseña
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
{/if}
