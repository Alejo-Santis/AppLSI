<script>
    import { router } from "@inertiajs/svelte";
    import DownloadTemplateButton from "@/Components/DownloadTemplateButton.svelte";

    let {
        show = false,
        onClose = () => {},
        importUrl,
        templateFile,
    } = $props();

    let selectedFile = $state(null);
    let isDragging = $state(false);
    let isUploading = $state(false);
    let uploadProgress = $state(0);
    let error = $state(null);

    function handleFileSelect(event) {
        const file = event.target.files[0];
        validateAndSetFile(file);
    }

    function handleDrop(event) {
        event.preventDefault();
        isDragging = false;
        const file = event.dataTransfer.files[0];
        validateAndSetFile(file);
    }

    function validateAndSetFile(file) {
        error = null;

        if (!file) return;

        // Validar tipo de archivo
        const validTypes = [
            "application/vnd.ms-excel",
            "application/vnd.openxmlformats-officedocument.spreadsheetml.sheet",
            "text/csv",
        ];

        const validExtensions = [".xlsx", ".xls", ".csv"];
        const fileExtension = "." + file.name.split(".").pop().toLowerCase();

        if (
            !validTypes.includes(file.type) &&
            !validExtensions.includes(fileExtension)
        ) {
            error = "Por favor selecciona un archivo Excel (.xlsx, .xls) o CSV";
            return;
        }

        // Validar tamaño (máximo 5MB)
        if (file.size > 5 * 1024 * 1024) {
            error = "El archivo no debe superar los 5MB";
            return;
        }

        selectedFile = file;
    }

    function handleDragOver(event) {
        event.preventDefault();
        isDragging = true;
    }

    function handleDragLeave() {
        isDragging = false;
    }

    function removeFile() {
        selectedFile = null;
        error = null;
    }

    function handleSubmit() {
        if (!selectedFile) {
            error = "Por favor selecciona un archivo";
            return;
        }

        const formData = new FormData();
        formData.append("file", selectedFile);

        isUploading = true;
        uploadProgress = 0;

        // Simular progreso
        const progressInterval = setInterval(() => {
            if (uploadProgress < 90) {
                uploadProgress += 10;
            }
        }, 200);

        router.post(importUrl, formData, {
            forceFormData: true,
            onSuccess: () => {
                clearInterval(progressInterval);
                uploadProgress = 100;
                setTimeout(() => {
                    resetModal();
                    onClose();
                }, 1000);
            },
            onError: (errors) => {
                clearInterval(progressInterval);
                isUploading = false;
                uploadProgress = 0;
                error = errors.file || "Error al importar el archivo";
            },
            onFinish: () => {
                clearInterval(progressInterval);
            },
        });
    }

    function resetModal() {
        selectedFile = null;
        isDragging = false;
        isUploading = false;
        uploadProgress = 0;
        error = null;
    }

    function closeModal() {
        resetModal();
        onClose();
    }
</script>

{#if show}
    <div
        class="modal fade show d-block"
        style="background-color: rgba(0,0,0,0.5);"
    >
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="bi bi-upload"></i>
                        Importar Datos
                    </h5>
                    <button
                        type="button"
                        class="btn-close"
                        onclick={closeModal}
                        disabled={isUploading}
                        aria-label="close"
                    ></button>
                </div>

                <div class="modal-body">
                    {#if !selectedFile}
                        <!-- Drop Zone -->
                        <div
                            class="drop-zone {isDragging
                                ? 'dragging'
                                : ''} {error ? 'error' : ''}"
                            ondragover={handleDragOver}
                            ondragleave={handleDragLeave}
                            ondrop={handleDrop}
                            onclick={() =>
                                document.getElementById("fileInput").click()}
                        >
                            <input
                                type="file"
                                id="fileInput"
                                accept=".xlsx,.xls,.csv"
                                onchange={handleFileSelect}
                                style="display: none;"
                            />

                            <div class="drop-zone-content">
                                <i class="bi bi-cloud-arrow-up"></i>
                                <h6>Arrastra tu archivo aquí</h6>
                                <p>o haz clic para seleccionarlo</p>
                                <small class="text-muted">
                                    Formatos soportados: .xlsx, .xls, .csv (máx.
                                    5MB)
                                </small>
                            </div>
                        </div>

                        {#if error}
                            <div class="alert alert-danger mt-3 mb-0">
                                <i class="bi bi-exclamation-triangle"></i>
                                {error}
                            </div>
                        {/if}
                    {:else}
                        <!-- Archivo Seleccionado -->
                        <div class="selected-file">
                            <div class="file-icon">
                                <i class="bi bi-file-earmark-excel"></i>
                            </div>
                            <div class="file-info">
                                <h6 class="mb-1">{selectedFile.name}</h6>
                                <small class="text-muted">
                                    {(selectedFile.size / 1024).toFixed(2)} KB
                                </small>
                            </div>
                            {#if !isUploading}
                                <button
                                    class="btn btn-sm btn-outline-danger"
                                    onclick={removeFile}
                                    aria-label="remove file"
                                >
                                    <i class="bi bi-x-lg"></i>
                                </button>
                            {/if}
                        </div>

                        <!-- Barra de Progreso -->
                        {#if isUploading}
                            <div class="upload-progress mt-3">
                                <div
                                    class="d-flex justify-content-between mb-2"
                                >
                                    <small class="text-muted"
                                        >Importando datos...</small
                                    >
                                    <small class="fw-bold"
                                        >{uploadProgress}%</small
                                    >
                                </div>
                                <div class="progress" style="height: 8px;">
                                    <div
                                        class="progress-bar progress-bar-striped progress-bar-animated bg-success"
                                        style="width: {uploadProgress}%"
                                    ></div>
                                </div>
                            </div>
                        {/if}

                        {#if error}
                            <div class="alert alert-danger mt-3 mb-0">
                                <i class="bi bi-exclamation-triangle"></i>
                                {error}
                            </div>
                        {/if}
                    {/if}

                    <!-- Instrucciones -->
                    <div class="alert alert-info mt-3 mb-0">
                        <small>
                            <i class="bi bi-info-circle"></i>
                            <strong>Importante:</strong> Asegúrate de que tu archivo
                            tenga el formato correcto. Descarga la plantilla para
                            ver un ejemplo.
                        </small>
                    </div>
                </div>

                <div class="modal-footer">
                    <DownloadTemplateButton
                        file={templateFile}
                        label="Descargar Plantilla"
                    />
                    <button
                        type="button"
                        class="btn btn-light"
                        onclick={closeModal}
                        disabled={isUploading}
                    >
                        <i class="bi bi-x-circle"></i>
                        Cancelar
                    </button>
                    <button
                        type="button"
                        class="btn btn-success"
                        onclick={handleSubmit}
                        disabled={!selectedFile || isUploading}
                    >
                        {#if isUploading}
                            <span class="spinner-border spinner-border-sm me-2"
                            ></span>
                            Importando...
                        {:else}
                            <i class="bi bi-check-lg"></i>
                            Importar
                        {/if}
                    </button>
                </div>
            </div>
        </div>
    </div>
{/if}

<style>
    .drop-zone {
        border: 2px dashed #cbd5e1;
        border-radius: 12px;
        padding: 3rem 2rem;
        text-align: center;
        background: #f8fafc;
        cursor: pointer;
        transition: all 0.3s ease;
    }

    .drop-zone:hover {
        border-color: #667eea;
        background: #f1f5f9;
    }

    .drop-zone.dragging {
        border-color: #667eea;
        background: #e0e7ff;
        transform: scale(1.02);
    }

    .drop-zone.error {
        border-color: #dc3545;
        background: #fff5f5;
    }

    .drop-zone-content i {
        font-size: 3rem;
        color: #667eea;
        margin-bottom: 1rem;
    }

    .drop-zone-content h6 {
        font-weight: 600;
        color: #1e293b;
        margin-bottom: 0.5rem;
    }

    .drop-zone-content p {
        color: #64748b;
        margin-bottom: 0.5rem;
    }

    .selected-file {
        display: flex;
        align-items: center;
        gap: 1rem;
        padding: 1.5rem;
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        border-radius: 12px;
    }

    .file-icon {
        width: 50px;
        height: 50px;
        background: linear-gradient(135deg, #22c55e 0%, #16a34a 100%);
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.5rem;
        color: white;
        flex-shrink: 0;
    }

    .file-info {
        flex: 1;
        min-width: 0;
    }

    .file-info h6 {
        margin: 0;
        font-weight: 600;
        color: #1e293b;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }

    .upload-progress {
        background: #f8fafc;
        padding: 1rem;
        border-radius: 8px;
    }
</style>
