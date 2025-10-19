<script>
    import { router } from "@inertiajs/svelte";
    import { onMount } from "svelte";
    import Swal from "sweetalert2";

    let { employee } = $props();

    let history = $state([]);
    let changeReasons = $state({});
    let loading = $state(true);
    let formModal = $state(false);

    let salaryForm = $state({
        new_salary: "",
        reason: "",
        change_date: new Date().toISOString().split("T")[0],
        processing: false,
        errors: {},
    });

    onMount(() => {
        loadHistory();
        loadChangeReasons();
    });

    async function loadHistory() {
        loading = true;
        try {
            const response = await fetch(
                `/employees/${employee.id}/salary-history`,
            );
            history = await response.json();
        } catch (error) {
            console.error("Error loading salary history:", error);
            Swal.fire({
                title: "Error",
                text: "No se pudo cargar el historial salarial",
                icon: "error",
            });
        } finally {
            loading = false;
        }
    }

    async function loadChangeReasons() {
        try {
            const response = await fetch("/employees/salary-change-reasons");
            changeReasons = await response.json();
        } catch (error) {
            console.error("Error loading change reasons:", error);
        }
    }

    function openFormModal() {
        formModal = true;
        salaryForm = {
            new_salary: "",
            reason: "",
            change_date: new Date().toISOString().split("T")[0],
            processing: false,
            errors: {},
        };
    }

    function closeFormModal() {
        formModal = false;
    }

    async function submitForm(e) {
        e.preventDefault();
        salaryForm.processing = true;
        salaryForm.errors = {};

        const data = {
            new_salary: salaryForm.new_salary,
            reason: salaryForm.reason,
            change_date: salaryForm.change_date,
        };

        router.post(`/employees/${employee.id}/salary-history/store`, data, {
            preserveScroll: true,
            onSuccess: () => {
                closeFormModal();
                loadHistory();
                // Recargar la página para actualizar el salario mostrado en el header
                router.reload({ only: ["employee"] });
                Swal.fire({
                    title: "¡Registrado!",
                    text: "Cambio salarial registrado exitosamente",
                    icon: "success",
                    timer: 2000,
                    showConfirmButton: false,
                });
            },
            onError: (errors) => {
                salaryForm.errors = errors;
                salaryForm.processing = false;
            },
            onFinish: () => {
                salaryForm.processing = false;
            },
        });
    }

    function confirmDelete(record) {
        Swal.fire({
            title: "¿Eliminar registro?",
            text: "¿Estás seguro de eliminar este registro del historial?",
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#d33",
            cancelButtonColor: "#3085d6",
            confirmButtonText: "Sí, eliminar",
            cancelButtonText: "Cancelar",
        }).then((result) => {
            if (result.isConfirmed) {
                deleteRecord(record);
            }
        });
    }

    function deleteRecord(record) {
        router.delete(
            `/employees/${employee.id}/salary-history/${record.id}/delete`,
            {
                preserveScroll: true,
                onSuccess: () => {
                    loadHistory();
                    Swal.fire({
                        title: "¡Eliminado!",
                        text: "Registro eliminado exitosamente",
                        icon: "success",
                        timer: 2000,
                        showConfirmButton: false,
                    });
                },
                onError: () => {
                    Swal.fire({
                        title: "Error",
                        text: "No se pudo eliminar el registro",
                        icon: "error",
                    });
                },
            },
        );
    }

    function getChangeIcon(type) {
        if (type === "increase") return "bi-arrow-up-circle text-success";
        if (type === "decrease") return "bi-arrow-down-circle text-danger";
        return "bi-dash-circle text-secondary";
    }

    function getChangeBadge(type) {
        if (type === "increase") return "bg-light-success text-success";
        if (type === "decrease") return "bg-light-danger text-danger";
        return "bg-light-secondary text-secondary";
    }
</script>

<div>
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h5 class="mb-0">
            <i class="bi bi-cash-coin"></i>
            Historial de Cambios Salariales
        </h5>
        <button class="btn btn-primary btn-sm" onclick={openFormModal}>
            <i class="bi bi-plus"></i> Registrar Cambio
        </button>
    </div>

    <!-- Salario Actual -->
    <div class="alert alert-info mb-3">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <strong><i class="bi bi-cash-stack"></i> Salario Actual:</strong
                >
            </div>
            <h4 class="mb-0 text-success">
                ${parseFloat(employee.salary).toLocaleString("es-CO")}
            </h4>
        </div>
    </div>

    {#if loading}
        <div class="text-center py-5">
            <div class="spinner-border text-primary" role="status">
                <span class="visually-hidden">Cargando...</span>
            </div>
        </div>
    {:else if history.length > 0}
        <div class="timeline">
            {#each history as record, index}
                <div class="mb-4">
                    <div class="d-flex align-items-start gap-3">
                        <div
                            class="rounded-circle d-flex align-items-center justify-content-center"
                            style="width: 50px; height: 50px; flex-shrink: 0; background-color: {record.change_type ===
                            'increase'
                                ? '#e8f5e9'
                                : record.change_type === 'decrease'
                                  ? '#ffebee'
                                  : '#f5f5f5'};"
                        >
                            <i
                                class="bi {getChangeIcon(
                                    record.change_type,
                                )} fs-4"
                            ></i>
                        </div>
                        <div class="flex-grow-1">
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
                                                    ${parseFloat(
                                                        record.previous_salary,
                                                    ).toLocaleString("es-CO")}
                                                    <i
                                                        class="bi bi-arrow-right mx-2"
                                                    ></i>
                                                    ${parseFloat(
                                                        record.new_salary,
                                                    ).toLocaleString("es-CO")}
                                                </h6>
                                                <span
                                                    class="badge {getChangeBadge(
                                                        record.change_type,
                                                    )}"
                                                >
                                                    {#if record.change_type === "increase"}
                                                        <i
                                                            class="bi bi-arrow-up"
                                                        ></i>
                                                        +{record.percentage_change}%
                                                    {:else if record.change_type === "decrease"}
                                                        <i
                                                            class="bi bi-arrow-down"
                                                        ></i>
                                                        {record.percentage_change}%
                                                    {:else}
                                                        Sin cambio
                                                    {/if}
                                                </span>
                                                {#if index === 0}
                                                    <span
                                                        class="badge bg-primary"
                                                        >Actual</span
                                                    >
                                                {/if}
                                            </div>
                                            <p class="text-muted mb-2">
                                                <i class="bi bi-calendar-event"
                                                ></i>
                                                {record.change_date_formatted}
                                                <span class="text-muted small">
                                                    ({record.change_date_human})
                                                </span>
                                            </p>
                                            <p class="mb-2">
                                                <strong>Razón:</strong>
                                                {record.reason}
                                            </p>
                                            <p class="mb-0 small text-muted">
                                                <i class="bi bi-person-check"
                                                ></i>
                                                Aprobado por: {record.approver}
                                            </p>
                                        </div>
                                        {#if index !== 0}
                                            <button
                                                class="btn btn-sm btn-outline-danger"
                                                onclick={() =>
                                                    confirmDelete(record)}
                                                title="Eliminar"
                                            >
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        {/if}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            {/each}
        </div>
    {:else}
        <div class="text-center py-5">
            <i class="bi bi-graph-down fs-1 text-muted"></i>
            <p class="text-muted mt-2">No hay cambios salariales registrados</p>
            <button class="btn btn-primary btn-sm" onclick={openFormModal}>
                <i class="bi bi-plus"></i> Registrar Primer Cambio
            </button>
        </div>
    {/if}
</div>

<!-- Modal: Registrar Cambio Salarial -->
{#if formModal}
    <div
        class="modal fade show d-block"
        tabindex="-1"
        style="background-color: rgba(0,0,0,0.5);"
    >
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <form onsubmit={submitForm}>
                    <div class="modal-header">
                        <h5 class="modal-title">Registrar Cambio Salarial</h5>
                        <button
                            type="button"
                            class="btn-close"
                            aria-label="close"
                            onclick={closeFormModal}
                            disabled={salaryForm.processing}
                        ></button>
                    </div>
                    <div class="modal-body">
                        <!-- Salario Actual -->
                        <div class="alert alert-light mb-3">
                            <strong>Salario Actual:</strong>
                            <span class="float-end fw-bold">
                                ${parseFloat(employee.salary).toLocaleString(
                                    "es-CO",
                                )}
                            </span>
                        </div>

                        <!-- Nuevo Salario -->
                        <div class="mb-3">
                            <label for="new_salary" class="form-label"
                                >Nuevo Salario <span class="text-danger">*</span
                                ></label
                            >
                            <div class="input-group">
                                <span class="input-group-text">$</span>
                                <input
                                    type="number"
                                    class="form-control {salaryForm.errors
                                        .new_salary
                                        ? 'is-invalid'
                                        : ''}"
                                    id="new_salary"
                                    bind:value={salaryForm.new_salary}
                                    placeholder="0.00"
                                    step="0.01"
                                    min="0"
                                    required
                                />
                            </div>
                            {#if salaryForm.errors.new_salary}
                                <div class="invalid-feedback d-block">
                                    {salaryForm.errors.new_salary}
                                </div>
                            {/if}
                        </div>

                        <!-- Razón del Cambio -->
                        <div class="mb-3">
                            <label for="reason" class="form-label"
                                >Razón del Cambio <span class="text-danger"
                                    >*</span
                                ></label
                            >
                            <select
                                class="form-select {salaryForm.errors.reason
                                    ? 'is-invalid'
                                    : ''}"
                                id="reason"
                                bind:value={salaryForm.reason}
                                required
                            >
                                <option value="">Seleccionar...</option>
                                {#each Object.entries(changeReasons) as [key, label]}
                                    <option value={key}>{label}</option>
                                {/each}
                            </select>
                            {#if salaryForm.errors.reason}
                                <div class="invalid-feedback">
                                    {salaryForm.errors.reason}
                                </div>
                            {/if}
                        </div>

                        <!-- Fecha del Cambio -->
                        <div class="mb-3">
                            <label for="change_date" class="form-label"
                                >Fecha del Cambio <span class="text-danger"
                                    >*</span
                                ></label
                            >
                            <input
                                type="date"
                                class="form-control {salaryForm.errors
                                    .change_date
                                    ? 'is-invalid'
                                    : ''}"
                                id="change_date"
                                bind:value={salaryForm.change_date}
                                required
                            />
                            {#if salaryForm.errors.change_date}
                                <div class="invalid-feedback">
                                    {salaryForm.errors.change_date}
                                </div>
                            {/if}
                        </div>

                        <div class="alert alert-warning small">
                            <i class="bi bi-exclamation-triangle"></i>
                            <strong>Nota:</strong> Este cambio actualizará el salario
                            actual del empleado y se registrará en el historial.
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button
                            type="button"
                            class="btn btn-light"
                            onclick={closeFormModal}
                            disabled={salaryForm.processing}
                        >
                            Cancelar
                        </button>
                        <button
                            type="submit"
                            class="btn btn-primary"
                            disabled={salaryForm.processing}
                        >
                            {#if salaryForm.processing}
                                <span
                                    class="spinner-border spinner-border-sm me-2"
                                ></span>
                                Registrando...
                            {:else}
                                <i class="bi bi-check"></i>
                                Registrar Cambio
                            {/if}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
{/if}
