<script>
    import GuestLayout from "@layouts/GuestLayout.svelte";
    import { useForm } from "@inertiajs/svelte";
    import { Link } from "@inertiajs/svelte";

    // Props que vienen del backend (errores de validación)
    let { errors = {} } = $props();

    // Crear formulario con useForm
    const form = useForm({
        email: "",
        password: "",
        remember: false,
    });

    function submit(e) {
        e.preventDefault();
        $form.post("/login");
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
            </a>
            <p class="text-center">Inicia sesión en la aplicación</p>

            <form onsubmit={submit}>
                <div class="mb-3">
                    <label for="email" class="form-label">
                        Correo Electrónico
                    </label>
                    <input
                        type="email"
                        class="form-control {errors.email ? 'is-invalid' : ''}"
                        id="email"
                        bind:value={$form.email}
                        placeholder="tu@email.com"
                        required
                    />
                    {#if errors.email}
                        <div class="invalid-feedback">{errors.email}</div>
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
                        placeholder="••••••••"
                        required
                    />
                    {#if errors.password}
                        <div class="invalid-feedback">
                            {errors.password}
                        </div>
                    {/if}
                </div>

                <div
                    class="d-flex align-items-center justify-content-between mb-4"
                >
                    <div class="form-check">
                        <input
                            class="form-check-input primary"
                            type="checkbox"
                            id="remember"
                            bind:checked={$form.remember}
                        />
                        <label
                            class="form-check-label text-dark"
                            for="remember"
                        >
                            Recordar sesión
                        </label>
                    </div>
                    <Link href="/forgot-password" class="text-primary fw-bold">
                        ¿Olvidaste tu contraseña?
                    </Link>
                </div>

                <button
                    type="submit"
                    class="btn btn-primary w-100 py-8 fs-4 mb-4"
                    disabled={$form.processing}
                >
                    {#if $form.processing}
                        <span class="spinner-border spinner-border-sm me-2"
                        ></span>
                        Iniciando Sesión...
                    {:else}
                        Iniciar Sesión
                    {/if}
                </button>

                <div class="d-flex align-items-center justify-content-center">
                    <p class="fs-4 mb-0 fw-bold">¿Nuevo en AppLSI?</p>
                    <Link href="/register" class="text-primary fw-bold ms-2">
                        Crea una cuenta
                    </Link>
                </div>
            </form>
        </div>
    </div>
</GuestLayout>
