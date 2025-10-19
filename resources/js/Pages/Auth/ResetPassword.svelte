<script>
    import GuestLayout from "@layouts/GuestLayout.svelte";
    import { useForm } from "@inertiajs/svelte";
    import Swal from "sweetalert2";

    let { token, email, errors = {} } = $props();

    // useForm crea una store reactiva
    const form = useForm({
        token,
        email,
        password: "",
        password_confirmation: "",
    });

    async function submit(e) {
        e.preventDefault();

        // Usar la versión reactiva de la store
        $form.post("/reset-password", {
            preserveScroll: true,
            onSuccess: () => {
                Swal.fire({
                    title: "¡Contraseña actualizada!",
                    text: "Tu contraseña ha sido cambiada exitosamente. Ya puedes iniciar sesión.",
                    icon: "success",
                    confirmButtonColor: "#667eea",
                }).then(() => {
                    window.location.href = "/login";
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
            <h4 class="fw-bold mb-4 text-center">Restablecer Contraseña</h4>

            <form on:submit={submit}>
                <div class="mb-3">
                    <label class="form-label" for="email"
                        >Correo Electrónico</label
                    >
                    <input
                        id="email"
                        type="email"
                        class="form-control"
                        bind:value={$form.email}
                        readonly
                    />
                </div>

                <div class="mb-3">
                    <label class="form-label">Nueva Contraseña</label>
                    <input
                        type="password"
                        class="form-control {$form.errors.password
                            ? 'is-invalid'
                            : ''}"
                        bind:value={$form.password}
                        required
                    />
                    {#if $form.errors.password}
                        <div class="invalid-feedback">
                            {$form.errors.password}
                        </div>
                    {/if}
                </div>

                <div class="mb-4">
                    <label class="form-label">Confirmar Contraseña</label>
                    <input
                        type="password"
                        class="form-control"
                        bind:value={$form.password_confirmation}
                        required
                    />
                </div>

                <button
                    type="submit"
                    class="btn btn-primary w-100 py-2"
                    disabled={$form.processing}
                >
                    {$form.processing
                        ? "Procesando..."
                        : "Restablecer Contraseña"}
                </button>
            </form>
        </div>
    </div>
</GuestLayout>
