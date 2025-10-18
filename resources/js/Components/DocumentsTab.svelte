<script>
    import { router } from "@inertiajs/svelte";
    import { onMount } from "svelte";
    import Swal from "sweetalert2";

    let { employee } = $props();

    let documents = $state([]);
    let documentTypes = $state({});
    let loading = $state(true);
    let uploadModal = $state(false);
    let previewModal = $state({ show: false, document: null });

    let uploadForm = $state({
        title: "",
        document_type: "",
        file: null,
        processing: false,
        errors: {},
    });

    let fileInput;

    onMount(() => {
        loadDocuments();
        loadDocumentTypes();
    });

    async function loadDocuments() {
        loading = true;
        try {
            const response = await fetch(`/employees/${employee.id}/documents`);
            documents = await response.json();
        } catch (error) {
            console.error("Error loading documents:", error);
            Swal.fire({
                title: "Error",
                text: "No se pudieron cargar los documentos",
                icon: "error",
            });
        } finally {
            loading = false;
        }
    }

    async function loadDocumentTypes() {
        try {
            const response = await fetch("/employees/document-types");
            documentTypes = await response.json();
        } catch (error) {
            console.error("Error loading document types:", error);
        }
    }

    function openUploadModal() {
        uploadModal = true;
        uploadForm = {
            title: "",
            document_type: "",
            file: null,
            processing: false,
            errors: {},
        };
    }

    function closeUploadModal() {
        uploadModal = false;
        if (fileInput) fileInput.value = "";
    }

    function handleFileChange(e) {
        uploadForm.file = e.target.files[0];
    }

    async function submitUpload(e) {
        e.preventDefault();
        uploadForm.processing = true;
        uploadForm.errors = {};

        const formData = new FormData();
        formData.append("title", uploadForm.title);
        formData.append("document_type", uploadForm.document_type);
        if (uploadForm.file) {
            formData.append("file", uploadForm.file);
        }

        router.post(`/employees/${employee.id}/documents/store`, formData, {
            forceFormData: true,
            preserveScroll: true,
            onSuccess: () => {
                closeUploadModal();
                loadDocuments();
                Swal.fire({
                    title: "¡Subido!",
                    text: "Documento subido exitosamente",
                    icon: "success",
                    timer: 2000,
                    showConfirmButton: false,
                });
            },
            onError: (errors) => {
                uploadForm.errors = errors;
                uploadForm.processing = false;
            },
            onFinish: () => {
                uploadForm.processing = false;
            },
        });
    }

    function confirmDelete(document) {
        Swal.fire({
            title: "¿Eliminar documento?",
            text: `¿Estás seguro de eliminar "${document.title}"?`,
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#d33",
            cancelButtonColor: "#3085d6",
            confirmButtonText: "Sí, eliminar",
            cancelButtonText: "Cancelar",
        }).then((result) => {
            if (result.isConfirmed) {
                deleteDocument(document);
            }
        });
    }

    function deleteDocument(document) {
        router.delete(
            `/employees/${employee.id}/documents/${document.id}/delete`,
            {
                preserveScroll: true,
                onSuccess: () => {
                    loadDocuments();
                    Swal.fire({
                        title: "¡Eliminado!",
                        text: "Documento eliminado exitosamente",
                        icon: "success",
                        timer: 2000,
                        showConfirmButton: false,
                    });
                },
                onError: () => {
                    Swal.fire({
                        title: "Error",
                        text: "No se pudo eliminar el documento",
                        icon: "error",
                    });
                },
            },
        );
    }

    function downloadDocument(document) {
        window.location.href = `/employees/${employee.id}/documents/${document.id}/download`;
    }

    function previewDocument(document) {
        if (document.is_pdf || document.is_image) {
            previewModal = { show: true, document };
        } else {
            downloadDocument(document);
        }
    }

    function getDocumentIcon(extension) {
        const icons = {
            pdf: "bi-file-earmark-pdf text-danger",
            doc: "bi-file-earmark-word text-primary",
            docx: "bi-file-earmark-word text-primary",
            xls: "bi-file-earmark-excel text-success",
            xlsx: "bi-file-earmark-excel text-success",
            jpg: "bi-file-earmark-image text-info",
            jpeg: "bi-file-earmark-image text-info",
            png: "bi-file-earmark-image text-info",
        };
        return (
            icons[extension?.toLowerCase()] || "bi-file-earmark text-secondary"
        );
    }

    function getTypeColor(type) {
        const colors = {
            contract: "bg-light-primary text-primary",
            evaluation: "bg-light-success text-success",
            certificate: "bg-light-info text-info",
            id_document: "bg-light-warning text-warning",
            tax_form: "bg-light-danger text-danger",
            resume: "bg-light-secondary text-secondary",
            diploma: "bg-light-primary text-primary",
            recommendation: "bg-light-success text-success",
            other: "bg-light-secondary text-secondary",
        };
        return colors[type] || "bg-light-secondary text-secondary";
    }
</script>

<div>
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h5 class="mb-0">
            <i class="bi bi-file-earmark-richtext"></i>
            Documentos del Empleado
        </h5>
        <button class="btn btn-primary btn-sm" onclick={openUploadModal}>
            <i class="bi bi-plus"></i> Subir Documento
        </button>
    </div>

    {#if loading}
        <div class="text-center py-5">
            <div class="spinner-border text-primary" role="status">
                <span class="visually-hidden">Cargando...</span>
            </div>
        </div>
    {:else if documents.length > 0}
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead>
                    <tr>
                        <th>Documento</th>
                        <th>Tipo</th>
                        <th>Tamaño</th>
                        <th>Fecha</th>
                        <th>Subido por</th>
                        <th class="text-center">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    {#each documents as document}
                        <tr>
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    <i
                                        class="bi {getDocumentIcon(
                                            document.file_extension,
                                        )} fs-4"
                                    ></i>
                                    <div>
                                        <h6 class="mb-0">{document.title}</h6>
                                        <small class="text-muted"
                                            >{document.file_name}</small
                                        >
                                    </div>
                                </div>
                            </td>
                            <td>
                                <span
                                    class="badge {getTypeColor(
                                        document.document_type,
                                    )}"
                                >
                                    {document.document_type_label}
                                </span>
                            </td>
                            <td>
                                <small>{document.file_size_formatted}</small>
                            </td>
                            <td>
                                <small class="text-muted">
                                    {document.upload_date_human}
                                </small>
                            </td>
                            <td>
                                <small>{document.uploader}</small>
                            </td>
                            <td class="text-center">
                                <div class="btn-group" role="group">
                                    {#if document.is_pdf || document.is_image}
                                        <button
                                            class="btn btn-sm btn-outline-primary"
                                            onclick={() =>
                                                previewDocument(document)}
                                            title="Vista previa"
                                        >
                                            <i class="bi bi-eye"></i>
                                        </button>
                                    {/if}
                                    <button
                                        class="btn btn-sm btn-outline-success"
                                        onclick={() =>
                                            downloadDocument(document)}
                                        title="Descargar"
                                    >
                                        <i class="bi bi-download"></i>
                                    </button>
                                    <button
                                        class="btn btn-sm btn-outline-danger"
                                        onclick={() => confirmDelete(document)}
                                        title="Eliminar"
                                    >
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    {/each}
                </tbody>
            </table>
        </div>
    {:else}
        <div class="text-center py-5">
            <i class="bi bi-folder-x fs-1 text-muted"></i>
            <p class="text-muted mt-2">No hay documentos registrados</p>
            <button class="btn btn-primary btn-sm" onclick={openUploadModal}>
                <i class="bi bi-plus"></i> Subir Primer Documento
            </button>
        </div>
    {/if}
</div>

<!-- Modal: Subir Documento -->
{#if uploadModal}
    <div
        class="modal fade show d-block"
        tabindex="-1"
        style="background-color: rgba(0,0,0,0.5);"
    >
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <form onsubmit={submitUpload}>
                    <div class="modal-header">
                        <h5 class="modal-title">Subir Documento</h5>
                        <button
                            type="button"
                            class="btn-close"
                            aria-label="close"
                            onclick={closeUploadModal}
                            disabled={uploadForm.processing}
                        ></button>
                    </div>
                    <div class="modal-body">
                        <!-- Título -->
                        <div class="mb-3">
                            <label for="title" class="form-label"
                                >Título <span class="text-danger">*</span
                                ></label
                            >
                            <input
                                type="text"
                                class="form-control {uploadForm.errors.title
                                    ? 'is-invalid'
                                    : ''}"
                                id="title"
                                bind:value={uploadForm.title}
                                placeholder="Ej: Contrato de trabajo 2024"
                                required
                            />
                            {#if uploadForm.errors.title}
                                <div class="invalid-feedback">
                                    {uploadForm.errors.title}
                                </div>
                            {/if}
                        </div>

                        <!-- Tipo de Documento -->
                        <div class="mb-3">
                            <label for="document_type" class="form-label"
                                >Tipo de Documento <span class="text-danger"
                                    >*</span
                                ></label
                            >
                            <select
                                class="form-select {uploadForm.errors
                                    .document_type
                                    ? 'is-invalid'
                                    : ''}"
                                id="document_type"
                                bind:value={uploadForm.document_type}
                                required
                            >
                                <option value="">Seleccionar...</option>
                                {#each Object.entries(documentTypes) as [key, label]}
                                    <option value={key}>{label}</option>
                                {/each}
                            </select>
                            {#if uploadForm.errors.document_type}
                                <div class="invalid-feedback">
                                    {uploadForm.errors.document_type}
                                </div>
                            {/if}
                        </div>

                        <!-- Archivo -->
                        <div class="mb-3">
                            <label for="file" class="form-label"
                                >Archivo <span class="text-danger">*</span
                                ></label
                            >
                            <input
                                type="file"
                                class="form-control {uploadForm.errors.file
                                    ? 'is-invalid'
                                    : ''}"
                                id="file"
                                bind:this={fileInput}
                                onchange={handleFileChange}
                                accept=".pdf,.doc,.docx,.jpg,.jpeg,.png,.xls,.xlsx"
                                required
                            />
                            {#if uploadForm.errors.file}
                                <div class="invalid-feedback d-block">
                                    {uploadForm.errors.file}
                                </div>
                            {/if}
                            <small class="text-muted">
                                Formatos permitidos: PDF, DOC, DOCX, JPG, PNG,
                                XLS, XLSX. Máximo 10MB
                            </small>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button
                            type="button"
                            class="btn btn-secondary"
                            onclick={closeUploadModal}
                            disabled={uploadForm.processing}
                        >
                            Cancelar
                        </button>
                        <button
                            type="submit"
                            class="btn btn-primary"
                            disabled={uploadForm.processing}
                        >
                            {#if uploadForm.processing}
                                <span
                                    class="spinner-border spinner-border-sm me-2"
                                ></span>
                                Subiendo...
                            {:else}
                                <i class="bi bi-upload"></i> Subir Documento
                            {/if}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
{/if}

<!-- Modal: Preview (solo para PDF e imágenes) -->
{#if previewModal.show}
    <div
        class="modal fade show d-block"
        tabindex="-1"
        style="background-color: rgba(0,0,0,0.5);"
    >
        <div class="modal-dialog modal-xl modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">{previewModal.document?.title}</h5>
                    <button
                        type="button"
                        class="btn-close"
                        aria-label="close"
                        onclick={() =>
                            (previewModal = { show: false, document: null })}
                    ></button>
                </div>
                <div class="modal-body p-0">
                    {#if previewModal.document?.is_pdf}
                        <iframe
                            src={`/employees/${employee.id}/documents/${previewModal.document.id}/preview`}
                            style="width: 100%; height: 70vh; border: none;"
                            title="Vista previa del documento"
                        ></iframe>
                    {:else if previewModal.document?.is_image}
                        <div class="text-center p-4">
                            <img
                                src={`/employees/${employee.id}/documents/${previewModal.document.id}/preview`}
                                alt={previewModal.document.title}
                                class="img-fluid"
                                style="max-height: 70vh;"
                            />
                        </div>
                    {/if}
                </div>
                <div class="modal-footer">
                    <button
                        type="button"
                        class="btn btn-primary"
                        onclick={() => downloadDocument(previewModal.document)}
                    >
                        <i class="bi bi-download"></i> Descargar
                    </button>
                    <button
                        type="button"
                        class="btn btn-light"
                        onclick={() =>
                            (previewModal = { show: false, document: null })}
                    >
                        <i class="bi bi-x-circle"></i>
                        Cerrar
                    </button>
                </div>
            </div>
        </div>
    </div>
{/if}
