<script>
    import GuestLayout from "@layouts/GuestLayout.svelte";
    import { useForm } from "@inertiajs/svelte";
    import { Link } from "@inertiajs/svelte";

    let { errors = {} } = $props();

    const form = useForm({
        name: "",
        email: "",
        password: "",
        password_confirmation: "",
        // role: "employee", // Opcional: descomentar si quieres selector de rol
    });

    function submit(e) {
        e.preventDefault();
        $form.post("/register");
    }
</script>

<GuestLayout>
    <div class="card mb-0">
        <div class="card-body">
            <a
                href="/"
                class="text-nowrap logo-img text-center d-block py-3 w-100"
            >
                <img src="/assets/images/logos/seodashlogo.png" alt="Logo" />
                <span class="p-3 fs-5">Iniciar Sesión</span>
            </a>
            <p class="text-center">Sistema de Gestión de Empleados</p>

            <form onsubmit={submit}>
                <div class="mb-3">
                    <label for="name" class="form-label">Nombre Completo</label>
                    <input
                        type="text"
                        class="form-control {errors.name ? 'is-invalid' : ''}"
                        id="name"
                        bind:value={$form.name}
                        placeholder="Ingrese su nombre completo"
                        required
                    />
                    {#if errors.name}
                        <div class="invalid-feedback d-block">
                            {errors.name}
                        </div>
                    {/if}
                </div>

                <div class="mb-3">
                    <label for="email" class="form-label"
                        >Correo Electrónico</label
                    >
                    <input
                        type="email"
                        class="form-control {errors.email ? 'is-invalid' : ''}"
                        id="email"
                        bind:value={$form.email}
                        placeholder="ejemplo@correo.com"
                        required
                    />
                    {#if errors.email}
                        <div class="invalid-feedback d-block">
                            {errors.email}
                        </div>
                    {/if}
                </div>

                <!-- OPCIONAL: Selector de Rol - Comenta/descomenta según necesites -->

                <div class="mb-3">
                    <label for="role" class="form-label">Rol</label>
                    <select
                        class="form-select {errors.role ? 'is-invalid' : ''}"
                        id="role"
                        bind:value={$form.role}
                    >
                        <option value="employee">Empleado</option>
                        <option value="manager">Manager</option>
                        <option value="hr">Recursos Humanos</option>
                        <option value="admin">Administrador</option>
                    </select>
                    {#if errors.role}
                        <div class="invalid-feedback d-block">
                            {errors.role}
                        </div>
                    {/if}
                </div>

                <div class="mb-3">
                    <label for="password" class="form-label">Contraseña</label>
                    <input
                        type="password"
                        class="form-control {errors.password
                            ? 'is-invalid'
                            : ''}"
                        id="password"
                        bind:value={$form.password}
                        placeholder="Mínimo 8 caracteres"
                        required
                    />
                    {#if errors.password}
                        <div class="invalid-feedback d-block">
                            {errors.password}
                        </div>
                    {/if}
                </div>

                <div class="mb-4">
                    <label for="password_confirmation" class="form-label"
                        >Confirmar Contraseña</label
                    >
                    <input
                        type="password"
                        class="form-control"
                        id="password_confirmation"
                        bind:value={$form.password_confirmation}
                        placeholder="Repita su contraseña"
                        required
                    />
                </div>

                <button
                    type="submit"
                    class="btn btn-primary w-100 py-8 fs-4 mb-4"
                    disabled={$form.processing}
                >
                    {#if $form.processing}
                        <span
                            class="spinner-border spinner-border-sm me-2"
                            role="status"
                            aria-hidden="true"
                        ></span>
                        Creando cuenta...
                    {:else}
                        Registrarse
                    {/if}
                </button>

                <div class="d-flex align-items-center justify-content-center">
                    <p class="fs-4 mb-0 fw-bold">¿Ya tienes una cuenta?</p>
                    <Link href="/login" class="text-primary fw-bold ms-2">
                        Iniciar Sesión
                    </Link>
                </div>
            </form>
        </div>
    </div>
</GuestLayout>
