<script>
    import AdminLayout from "@layouts/AdminLayout.svelte";
    import { Link } from "@inertiajs/svelte";
    import { useForm } from "@inertiajs/svelte";

    let { departments, positions, errors = {} } = $props();

    const form = useForm({
        first_name: "",
        last_name: "",
        email: "",
        phone: "",
        hire_date: "",
        salary: "",
        status: "active",
        birth_date: "",
        address: "",
        photo: null,
        position_id: "",
        department_id: "",
    });

    let photoPreview = $state(null);

    function submit(e) {
        e.preventDefault();
        $form.post("/employees/store");
    }

    function handlePhotoChange(e) {
        const file = e.target.files[0];
        if (file) {
            $form.photo = file;
            const reader = new FileReader();
            reader.onload = (e) => {
                photoPreview = e.target.result;
            };
            reader.readAsDataURL(file);
        }
    }

    function removePhoto() {
        $form.photo = null;
        photoPreview = null;
        // Reset input file
        const input = document.getElementById("photo");
        if (input) input.value = "";
    }
</script>

<AdminLayout title="Crear Empleado">
    <div class="container-fluid">
        <!-- Header -->
        <div class="card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h5 class="card-title fw-semibold mb-1">
                            <i class="bi bi-plus"></i>
                            Crear Nuevo Empleado
                        </h5>
                        <p class="text-muted mb-0">
                            Complete toda la información del empleado
                        </p>
                    </div>
                    <Link href="/employees" class="btn btn-outline-secondary">
                        <i class="bi bi-arrow-left"></i>
                        Volver
                    </Link>
                </div>
            </div>
        </div>

        <!-- Formulario -->
        <form onsubmit={submit} enctype="multipart/form-data">
            <div class="row">
                <!-- Columna Principal -->
                <div class="col-lg-8">
                    <!-- Información Personal -->
                    <div class="card mt-3">
                        <div class="card-body">
                            <h5 class="card-title mb-4">
                                <i class="bi bi-person"></i> Información Personal
                            </h5>

                            <div class="row">
                                <!-- Nombres -->
                                <div class="col-md-6 mb-3">
                                    <label for="first_name" class="form-label"
                                        >Nombres <span class="text-danger"
                                            >*</span
                                        ></label
                                    >
                                    <input
                                        type="text"
                                        class="form-control {errors.first_name
                                            ? 'is-invalid'
                                            : ''}"
                                        id="first_name"
                                        bind:value={$form.first_name}
                                        placeholder="Ej: Juan Carlos"
                                        required
                                    />
                                    {#if errors.first_name}
                                        <div class="invalid-feedback">
                                            {errors.first_name}
                                        </div>
                                    {/if}
                                </div>

                                <!-- Apellidos -->
                                <div class="col-md-6 mb-3">
                                    <label for="last_name" class="form-label"
                                        >Apellidos <span class="text-danger"
                                            >*</span
                                        ></label
                                    >
                                    <input
                                        type="text"
                                        class="form-control {errors.last_name
                                            ? 'is-invalid'
                                            : ''}"
                                        id="last_name"
                                        bind:value={$form.last_name}
                                        placeholder="Ej: García López"
                                        required
                                    />
                                    {#if errors.last_name}
                                        <div class="invalid-feedback">
                                            {errors.last_name}
                                        </div>
                                    {/if}
                                </div>

                                <!-- Email -->
                                <div class="col-md-6 mb-3">
                                    <label for="email" class="form-label"
                                        >Email Corporativo <span
                                            class="text-danger">*</span
                                        ></label
                                    >
                                    <input
                                        type="email"
                                        class="form-control {errors.email
                                            ? 'is-invalid'
                                            : ''}"
                                        id="email"
                                        bind:value={$form.email}
                                        placeholder="nombre@empresa.com"
                                        required
                                    />
                                    {#if errors.email}
                                        <div class="invalid-feedback">
                                            {errors.email}
                                        </div>
                                    {/if}
                                </div>

                                <!-- Teléfono -->
                                <div class="col-md-6 mb-3">
                                    <label for="phone" class="form-label"
                                        >Teléfono</label
                                    >
                                    <input
                                        type="text"
                                        class="form-control {errors.phone
                                            ? 'is-invalid'
                                            : ''}"
                                        id="phone"
                                        bind:value={$form.phone}
                                        placeholder="Ej: +57 300 123 4567"
                                    />
                                    {#if errors.phone}
                                        <div class="invalid-feedback">
                                            {errors.phone}
                                        </div>
                                    {/if}
                                </div>

                                <!-- Fecha de Nacimiento -->
                                <div class="col-md-6 mb-3">
                                    <label for="birth_date" class="form-label"
                                        >Fecha de Nacimiento</label
                                    >
                                    <input
                                        type="date"
                                        class="form-control {errors.birth_date
                                            ? 'is-invalid'
                                            : ''}"
                                        id="birth_date"
                                        bind:value={$form.birth_date}
                                    />
                                    {#if errors.birth_date}
                                        <div class="invalid-feedback">
                                            {errors.birth_date}
                                        </div>
                                    {/if}
                                </div>

                                <!-- Dirección -->
                                <div class="col-12 mb-3">
                                    <label for="address" class="form-label"
                                        >Dirección</label
                                    >
                                    <textarea
                                        class="form-control {errors.address
                                            ? 'is-invalid'
                                            : ''}"
                                        id="address"
                                        bind:value={$form.address}
                                        rows="2"
                                        placeholder="Dirección completa"
                                    ></textarea>
                                    {#if errors.address}
                                        <div class="invalid-feedback">
                                            {errors.address}
                                        </div>
                                    {/if}
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Información Laboral -->
                    <div class="card mt-3">
                        <div class="card-body">
                            <h5 class="card-title mb-4">
                                <i class="bi bi-briefcase"></i> Información Laboral
                            </h5>

                            <div class="row">
                                <!-- Departamento -->
                                <div class="col-md-6 mb-3">
                                    <label
                                        for="department_id"
                                        class="form-label"
                                        >Departamento <span class="text-danger"
                                            >*</span
                                        ></label
                                    >
                                    <select
                                        class="form-select {errors.department_id
                                            ? 'is-invalid'
                                            : ''}"
                                        id="department_id"
                                        bind:value={$form.department_id}
                                        required
                                    >
                                        <option value="">Seleccionar...</option>
                                        {#each departments as department}
                                            <option value={department.id}>
                                                {department.name} ({department.code})
                                            </option>
                                        {/each}
                                    </select>
                                    {#if errors.department_id}
                                        <div class="invalid-feedback">
                                            {errors.department_id}
                                        </div>
                                    {/if}
                                </div>

                                <!-- Puesto -->
                                <div class="col-md-6 mb-3">
                                    <label for="position_id" class="form-label"
                                        >Puesto <span class="text-danger"
                                            >*</span
                                        ></label
                                    >
                                    <select
                                        class="form-select {errors.position_id
                                            ? 'is-invalid'
                                            : ''}"
                                        id="position_id"
                                        bind:value={$form.position_id}
                                        required
                                    >
                                        <option value="">Seleccionar...</option>
                                        {#each positions as position}
                                            <option value={position.id}>
                                                {position.title} ({position.level})
                                            </option>
                                        {/each}
                                    </select>
                                    {#if errors.position_id}
                                        <div class="invalid-feedback">
                                            {errors.position_id}
                                        </div>
                                    {/if}
                                </div>

                                <!-- Fecha de Contratación -->
                                <div class="col-md-4 mb-3">
                                    <label for="hire_date" class="form-label"
                                        >Fecha de Contratación <span
                                            class="text-danger">*</span
                                        ></label
                                    >
                                    <input
                                        type="date"
                                        class="form-control {errors.hire_date
                                            ? 'is-invalid'
                                            : ''}"
                                        id="hire_date"
                                        bind:value={$form.hire_date}
                                        required
                                    />
                                    {#if errors.hire_date}
                                        <div class="invalid-feedback">
                                            {errors.hire_date}
                                        </div>
                                    {/if}
                                </div>

                                <!-- Salario -->
                                <div class="col-md-4 mb-3">
                                    <label for="salary" class="form-label"
                                        >Salario <span class="text-danger"
                                            >*</span
                                        ></label
                                    >
                                    <div class="input-group">
                                        <span class="input-group-text">$</span>
                                        <input
                                            type="number"
                                            class="form-control {errors.salary
                                                ? 'is-invalid'
                                                : ''}"
                                            id="salary"
                                            bind:value={$form.salary}
                                            placeholder="0.00"
                                            step="0.01"
                                            min="0"
                                            required
                                        />
                                    </div>
                                    {#if errors.salary}
                                        <div class="invalid-feedback d-block">
                                            {errors.salary}
                                        </div>
                                    {/if}
                                </div>

                                <!-- Estado -->
                                <div class="col-md-4 mb-3">
                                    <label for="status" class="form-label"
                                        >Estado <span class="text-danger"
                                            >*</span
                                        ></label
                                    >
                                    <select
                                        class="form-select {errors.status
                                            ? 'is-invalid'
                                            : ''}"
                                        id="status"
                                        bind:value={$form.status}
                                        required
                                    >
                                        <option value="active">Activo</option>
                                        <option value="inactive"
                                            >Inactivo</option
                                        >
                                        <option value="on_leave"
                                            >En Licencia</option
                                        >
                                        <option value="terminated"
                                            >Terminado</option
                                        >
                                    </select>
                                    {#if errors.status}
                                        <div class="invalid-feedback">
                                            {errors.status}
                                        </div>
                                    {/if}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Columna Lateral -->
                <div class="col-lg-4">
                    <!-- Foto -->
                    <div class="card mt-3">
                        <div class="card-body">
                            <h5 class="card-title mb-3">
                                <i class="bi bi-camera"></i> Foto del Empleado
                            </h5>

                            <div class="text-center">
                                {#if photoPreview}
                                    <div
                                        class="position-relative d-inline-block"
                                    >
                                        <img
                                            src={photoPreview}
                                            alt="Preview"
                                            class="img-fluid rounded mb-3"
                                            style="max-height: 250px; object-fit: cover;"
                                        />
                                        <button
                                            type="button"
                                            class="btn btn-danger btn-sm position-absolute top-0 end-0 m-2"
                                            aria-label="delete-photo"
                                            onclick={removePhoto}
                                        >
                                            <i class="bi bi-x"></i>
                                        </button>
                                    </div>
                                {:else}
                                    <div
                                        class="rounded bg-light d-flex align-items-center justify-content-center mb-3"
                                        style="height: 200px;"
                                    >
                                        <i class="bi bi-person fs-1 text-muted"
                                        ></i>
                                    </div>
                                {/if}

                                <input
                                    type="file"
                                    class="form-control {errors.photo
                                        ? 'is-invalid'
                                        : ''}"
                                    id="photo"
                                    accept="image/*"
                                    onchange={handlePhotoChange}
                                />
                                {#if errors.photo}
                                    <div class="invalid-feedback d-block">
                                        {errors.photo}
                                    </div>
                                {/if}
                                <small class="text-muted d-block mt-2">
                                    Formatos: JPG, PNG. Máx: 2MB
                                </small>
                            </div>
                        </div>
                    </div>

                    <!-- Información -->
                    <div class="card mt-3">
                        <div class="card-body">
                            <h5 class="card-title mb-3">
                                <i class="bi bi-info-circle"></i> Información
                            </h5>
                            <ul class="list-unstyled mb-0">
                                <li class="mb-2">
                                    <small class="text-muted">
                                        <i class="bi bi-check2"></i> Los campos
                                        marcados con
                                        <span class="text-danger">*</span> son obligatorios
                                    </small>
                                </li>
                                <li class="mb-2">
                                    <small class="text-muted">
                                        <i class="bi bi-check2"></i> El email debe
                                        ser único en el sistema
                                    </small>
                                </li>
                                <li class="mb-2">
                                    <small class="text-muted">
                                        <i class="bi bi-check2"></i> La foto es opcional
                                        pero recomendada
                                    </small>
                                </li>
                                <li class="mb-2">
                                    <small class="text-muted">
                                        <i class="bi bi-check2"></i> Podrás agregar
                                        documentos y contactos después
                                    </small>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Botones de Acción -->
            <div class="card mt-3">
                <div class="card-body">
                    <div class="d-flex justify-content-end gap-2">
                        <Link
                            href="/employees"
                            class="btn btn-light"
                            disabled={$form.processing}
                        >
                            <i class="bi bi-x"></i>
                            Cancelar
                        </Link>
                        <button
                            type="submit"
                            class="btn btn-primary"
                            disabled={$form.processing}
                        >
                            {#if $form.processing}
                                <span
                                    class="spinner-border spinner-border-sm me-2"
                                ></span>
                                Guardando...
                            {:else}
                                <i class="bi bi-check"></i>
                                Crear Empleado
                            {/if}
                        </button>
                    </div>
                </div>
            </div>
        </form>
    </div>
</AdminLayout>
