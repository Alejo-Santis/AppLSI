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
            <p class="text-center">Sign in in this application</p>

            <form onsubmit={submit}>
                <div class="mb-3">
                    <label for="email" class="form-label">Username</label>
                    <input
                        type="email"
                        class="form-control"
                        id="email"
                        bind:value={$form.email}
                        required
                    />
                    {#if errors.email}
                        <div class="text-danger mt-1 small">{errors.email}</div>
                    {/if}
                </div>

                <div class="mb-4">
                    <label for="password" class="form-label">Password</label>
                    <input
                        type="password"
                        class="form-control"
                        id="password"
                        bind:value={$form.password}
                        required
                    />
                    {#if errors.password}
                        <div class="text-danger mt-1 small">
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
                            Remeber this Device
                        </label>
                    </div>
                    <a href="/forgot-password" class="text-primary fw-bold">
                        Forgot Password ?
                    </a>
                </div>

                <button
                    type="submit"
                    class="btn btn-primary w-100 py-8 fs-4 mb-4"
                    disabled={$form.processing}
                >
                    {$form.processing ? "Signing in..." : "Sign In"}
                </button>

                <div class="d-flex align-items-center justify-content-center">
                    <p class="fs-4 mb-0 fw-bold">New to SeoDash?</p>
                    <Link href="/register" class="text-primary fw-bold ms-2">
                        Create an account
                    </Link>
                </div>
            </form>
        </div>
    </div>
</GuestLayout>
