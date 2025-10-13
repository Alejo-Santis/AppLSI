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
                <img src="/assets/images/logos/logo-light.svg" alt="Logo" />
            </a>
            <p class="text-center">Your Social Campaigns</p>

            <form onsubmit={submit}>
                <div class="mb-3">
                    <label for="name" class="form-label">Name</label>
                    <input
                        type="text"
                        class="form-control"
                        id="name"
                        bind:value={$form.name}
                        required
                    />
                    {#if errors.name}
                        <div class="text-danger mt-1 small">{errors.name}</div>
                    {/if}
                </div>

                <div class="mb-3">
                    <label for="email" class="form-label">Email Address</label>
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

                <div class="mb-3">
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

                <div class="mb-4">
                    <label for="password_confirmation" class="form-label"
                        >Confirm Password</label
                    >
                    <input
                        type="password"
                        class="form-control"
                        id="password_confirmation"
                        bind:value={$form.password_confirmation}
                        required
                    />
                </div>

                <button
                    type="submit"
                    class="btn btn-primary w-100 py-8 fs-4 mb-4"
                    disabled={$form.processing}
                >
                    {$form.processing ? "Creating account..." : "Sign Up"}
                </button>

                <div class="d-flex align-items-center justify-content-center">
                    <p class="fs-4 mb-0 fw-bold">Already have an Account?</p>
                    <Link href="/login" class="text-primary fw-bold ms-2">
                        Sign In
                    </Link>
                </div>
            </form>
        </div>
    </div>
</GuestLayout>
