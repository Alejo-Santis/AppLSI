<script>
    import { router } from "@inertiajs/svelte";
    import { onMount } from "svelte";
    import Swal from "sweetalert2";

    let { employee } = $props();

    let contacts = $state([]);
    let relationshipTypes = $state({});
    let loading = $state(true);
    let formModal = $state({ show: false, mode: "create", contact: null });

    let contactForm = $state({
        name: "",
        relationship: "",
        phone: "",
        email: "",
        address: "",
        is_primary: false,
        processing: false,
        errors: {},
    });

    onMount(() => {
        loadContacts();
        loadRelationshipTypes();
    });

    async function loadContacts() {
        loading = true;
        try {
            const response = await fetch(`/employees/${employee.id}/contacts`);
            contacts = await response.json();
        } catch (error) {
            console.error("Error loading contacts:", error);
            Swal.fire({
                title: "Error",
                text: "No se pudieron cargar los contactos",
                icon: "error",
            });
        } finally {
            loading = false;
        }
    }

    async function loadRelationshipTypes() {
        try {
            const response = await fetch("/employees/relationship-types");
            relationshipTypes = await response.json();
        } catch (error) {
            console.error("Error loading relationship types:", error);
        }
    }

    function openCreateModal() {
        formModal = { show: true, mode: "create", contact: null };
        contactForm = {
            name: "",
            relationship: "",
            phone: "",
            email: "",
            address: "",
            is_primary: contacts.length === 0, // Si es el primero, marcarlo como primario
            processing: false,
            errors: {},
        };
    }

    function openEditModal(contact) {
        formModal = { show: true, mode: "edit", contact };
        contactForm = {
            name: contact.name,
            relationship: contact.relationship,
            phone: contact.phone,
            email: contact.email || "",
            address: contact.address || "",
            is_primary: contact.is_primary,
            processing: false,
            errors: {},
        };
    }

    function closeModal() {
        formModal = { show: false, mode: "create", contact: null };
    }

    async function submitForm(e) {
        e.preventDefault();
        contactForm.processing = true;
        contactForm.errors = {};

        const data = {
            name: contactForm.name,
            relationship: contactForm.relationship,
            phone: contactForm.phone,
            email: contactForm.email,
            address: contactForm.address,
            is_primary: contactForm.is_primary,
        };

        if (formModal.mode === "create") {
            router.post(`/employees/${employee.id}/contacts/store`, data, {
                preserveScroll: true,
                onSuccess: () => {
                    closeModal();
                    loadContacts();
                    Swal.fire({
                        title: "¡Creado!",
                        text: "Contacto agregado exitosamente",
                        icon: "success",
                        timer: 2000,
                        showConfirmButton: false,
                    });
                },
                onError: (errors) => {
                    contactForm.errors = errors;
                    contactForm.processing = false;
                },
                onFinish: () => {
                    contactForm.processing = false;
                },
            });
        } else {
            router.put(
                `/employees/${employee.id}/contacts/${formModal.contact.id}/update`,
                data,
                {
                    preserveScroll: true,
                    onSuccess: () => {
                        closeModal();
                        loadContacts();
                        Swal.fire({
                            title: "¡Actualizado!",
                            text: "Contacto actualizado exitosamente",
                            icon: "success",
                            timer: 2000,
                            showConfirmButton: false,
                        });
                    },
                    onError: (errors) => {
                        contactForm.errors = errors;
                        contactForm.processing = false;
                    },
                    onFinish: () => {
                        contactForm.processing = false;
                    },
                },
            );
        }
    }

    function confirmDelete(contact) {
        Swal.fire({
            title: "¿Eliminar contacto?",
            text: `¿Estás seguro de eliminar a "${contact.name}"?`,
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#d33",
            cancelButtonColor: "#3085d6",
            confirmButtonText: "Sí, eliminar",
            cancelButtonText: "Cancelar",
        }).then((result) => {
            if (result.isConfirmed) {
                deleteContact(contact);
            }
        });
    }

    function deleteContact(contact) {
        router.delete(
            `/employees/${employee.id}/contacts/${contact.id}/delete`,
            {
                preserveScroll: true,
                onSuccess: () => {
                    loadContacts();
                    Swal.fire({
                        title: "¡Eliminado!",
                        text: "Contacto eliminado exitosamente",
                        icon: "success",
                        timer: 2000,
                        showConfirmButton: false,
                    });
                },
                onError: () => {
                    Swal.fire({
                        title: "Error",
                        text: "No se pudo eliminar el contacto",
                        icon: "error",
                    });
                },
            },
        );
    }
</script>

<div>
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h5 class="mb-0">
            <i class="bi bi-person-lines-fill"></i>
            Contactos de Emergencia
        </h5>
        <button class="btn btn-primary btn-sm" onclick={openCreateModal}>
            <i class="bi bi-plus"></i> Agregar Contacto
        </button>
    </div>

    {#if loading}
        <div class="text-center py-5">
            <div class="spinner-border text-primary" role="status">
                <span class="visually-hidden">Cargando...</span>
            </div>
        </div>
    {:else if contacts.length > 0}
        <div class="row">
            {#each contacts as contact}
                <div class="col-md-6 mb-3">
                    <div class="card">
                        <div class="card-body">
                            <div
                                class="d-flex justify-content-between align-items-start"
                            >
                                <div class="flex-grow-1">
                                    <div
                                        class="d-flex align-items-center gap-2 mb-2"
                                    >
                                        <h6 class="fw-semibold mb-0">
                                            {contact.name}
                                        </h6>
                                        {#if contact.is_primary}
                                            <span
                                                class="badge bg-warning text-dark"
                                            >
                                                <i class="bi bi-star-fill"></i> Principal
                                            </span>
                                        {/if}
                                    </div>
                                    <p class="text-muted mb-2">
                                        <i class="bi bi-person-heart"></i>
                                        {contact.relationship_label}
                                    </p>
                                    <p class="mb-1">
                                        <i class="bi bi-telephone"></i>
                                        {contact.phone}
                                    </p>
                                    {#if contact.email}
                                        <p class="mb-1">
                                            <i class="bi bi-envelope"></i>
                                            {contact.email}
                                        </p>
                                    {/if}
                                    {#if contact.address}
                                        <p class="mb-0 text-muted small">
                                            <i class="bi bi-geo-alt"></i>
                                            {contact.address}
                                        </p>
                                    {/if}
                                </div>
                                <div class="btn-group-vertical">
                                    <button
                                        class="btn btn-sm btn-outline-info"
                                        onclick={() => openEditModal(contact)}
                                        title="Editar"
                                    >
                                        <i class="bi bi-pencil"></i>
                                    </button>
                                    <button
                                        class="btn btn-sm btn-outline-danger"
                                        onclick={() => confirmDelete(contact)}
                                        title="Eliminar"
                                    >
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            {/each}
        </div>
    {:else}
        <div class="text-center py-5">
            <i class="bi bi-person-slash fs-1 text-muted"></i>
            <p class="text-muted mt-2">
                No hay contactos de emergencia registrados
            </p>
            <button class="btn btn-primary btn-sm" onclick={openCreateModal}>
                <i class="bi bi-plus"></i> Agregar Primer Contacto
            </button>
        </div>
    {/if}
</div>

<!-- Modal: Crear/Editar Contacto -->
{#if formModal.show}
    <div
        class="modal fade show d-block"
        tabindex="-1"
        style="background-color: rgba(0,0,0,0.5);"
    >
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <form onsubmit={submitForm}>
                    <div class="modal-header">
                        <h5 class="modal-title">
                            {formModal.mode === "create" ? "Agregar" : "Editar"}
                            Contacto de Emergencia
                        </h5>
                        <button
                            type="button"
                            class="btn-close"
                            aria-label="close"
                            onclick={closeModal}
                            disabled={contactForm.processing}
                        ></button>
                    </div>
                    <div class="modal-body">
                        <!-- Nombre -->
                        <div class="mb-3">
                            <label for="name" class="form-label"
                                >Nombre Completo <span class="text-danger"
                                    >*</span
                                ></label
                            >
                            <input
                                type="text"
                                class="form-control {contactForm.errors.name
                                    ? 'is-invalid'
                                    : ''}"
                                id="name"
                                bind:value={contactForm.name}
                                placeholder="Ej: María García"
                                required
                            />
                            {#if contactForm.errors.name}
                                <div class="invalid-feedback">
                                    {contactForm.errors.name}
                                </div>
                            {/if}
                        </div>

                        <!-- Relación -->
                        <div class="mb-3">
                            <label for="relationship" class="form-label"
                                >Relación <span class="text-danger">*</span
                                ></label
                            >
                            <select
                                class="form-select {contactForm.errors
                                    .relationship
                                    ? 'is-invalid'
                                    : ''}"
                                id="relationship"
                                bind:value={contactForm.relationship}
                                required
                            >
                                <option value="">Seleccionar...</option>
                                {#each Object.entries(relationshipTypes) as [key, label]}
                                    <option value={key}>{label}</option>
                                {/each}
                            </select>
                            {#if contactForm.errors.relationship}
                                <div class="invalid-feedback">
                                    {contactForm.errors.relationship}
                                </div>
                            {/if}
                        </div>

                        <!-- Teléfono -->
                        <div class="mb-3">
                            <label for="phone" class="form-label"
                                >Teléfono <span class="text-danger">*</span
                                ></label
                            >
                            <input
                                type="tel"
                                class="form-control {contactForm.errors.phone
                                    ? 'is-invalid'
                                    : ''}"
                                id="phone"
                                bind:value={contactForm.phone}
                                placeholder="Ej: +57 300 123 4567"
                                required
                            />
                            {#if contactForm.errors.phone}
                                <div class="invalid-feedback">
                                    {contactForm.errors.phone}
                                </div>
                            {/if}
                        </div>

                        <!-- Email -->
                        <div class="mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input
                                type="email"
                                class="form-control {contactForm.errors.email
                                    ? 'is-invalid'
                                    : ''}"
                                id="email"
                                bind:value={contactForm.email}
                                placeholder="contacto@email.com"
                            />
                            {#if contactForm.errors.email}
                                <div class="invalid-feedback">
                                    {contactForm.errors.email}
                                </div>
                            {/if}
                        </div>

                        <!-- Dirección -->
                        <div class="mb-3">
                            <label for="address" class="form-label"
                                >Dirección</label
                            >
                            <textarea
                                class="form-control {contactForm.errors.address
                                    ? 'is-invalid'
                                    : ''}"
                                id="address"
                                bind:value={contactForm.address}
                                rows="2"
                                placeholder="Dirección completa"
                            ></textarea>
                            {#if contactForm.errors.address}
                                <div class="invalid-feedback">
                                    {contactForm.errors.address}
                                </div>
                            {/if}
                        </div>

                        <!-- Contacto Principal -->
                        <div class="mb-3">
                            <div class="form-check">
                                <input
                                    class="form-check-input"
                                    type="checkbox"
                                    id="is_primary"
                                    bind:checked={contactForm.is_primary}
                                />
                                <label
                                    class="form-check-label"
                                    for="is_primary"
                                >
                                    Marcar como contacto principal
                                </label>
                            </div>
                            <small class="text-muted">
                                El contacto principal será el primero en ser
                                contactado en caso de emergencia
                            </small>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button
                            type="button"
                            class="btn btn-light"
                            onclick={closeModal}
                            disabled={contactForm.processing}
                        >
                            Cancelar
                        </button>
                        <button
                            type="submit"
                            class="btn btn-primary"
                            disabled={contactForm.processing}
                        >
                            {#if contactForm.processing}
                                <span
                                    class="spinner-border spinner-border-sm me-2"
                                ></span>
                                {formModal.mode === "create"
                                    ? "Creando..."
                                    : "Actualizando..."}
                            {:else}
                                <i class="bi bi-check"></i>
                                {formModal.mode === "create"
                                    ? "Crear"
                                    : "Actualizar"} Contacto
                            {/if}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
{/if}
