<script>
    import GuestLayout from "@layouts/GuestLayout.svelte";
    import { useForm } from "@inertiajs/svelte";
    import { Link } from "@inertiajs/svelte";

    let { status = "", errors = {} } = $props();

    const form = useForm({
        email: "",
    });

    function submit(e) {
        e.preventDefault();
        $form.post("/forgot-password", {
            onSuccess: () => {
                Swal.fire({
                    title: "Correo enviado",
                    text: "Te hemos enviado un enlace de recuperación.",
                    icon: "success",
                    confirmButtonColor: "#667eea",
                });
            },
            onError: (errors) => {
                Swal.fire({
                    title: "Error",
                    text: Object.values(errors).join("\n"),
                    icon: "error",
                    confirmButtonColor: "#667eea",
                });
            },
        });
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

            <div class="text-center mb-4">
                <h4 class="fw-bold">¿Olvidaste tu contraseña?</h4>
                <p class="text-muted">
                    No te preocupes, te enviaremos un enlace para restablecerla
                </p>
            </div>

            {#if status}
                <div class="alert alert-success" role="alert">
                    <i class="bi bi-check-circle me-2"></i>
                    {status}
                </div>
            {/if}

            <form onsubmit={submit}>
                <div class="mb-4">
                    <label for="email" class="form-label fw-semibold">
                        <i class="bi bi-envelope"></i>
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
                    <small class="text-muted">
                        Ingresa el correo asociado a tu cuenta
                    </small>
                </div>

                <button
                    type="submit"
                    class="btn btn-primary w-100 py-8 fs-4 mb-3"
                    disabled={$form.processing}
                >
                    {#if $form.processing}
                        <span class="spinner-border spinner-border-sm me-2"
                        ></span>
                        Enviando...
                    {:else}
                        <i class="bi bi-send"></i>
                        Enviar Enlace de Recuperación
                    {/if}
                </button>

                <div class="text-center">
                    <Link
                        href="/login"
                        class="text-primary fw-bold d-flex align-items-center justify-content-center"
                    >
                        <i class="bi bi-arrow-left me-2"></i>
                        Volver al inicio de sesión
                    </Link>
                </div>
            </form>
        </div>
    </div>
</GuestLayout>

<style>
    .form-control:focus {
        border-color: #667eea;
        box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
    }
</style>
