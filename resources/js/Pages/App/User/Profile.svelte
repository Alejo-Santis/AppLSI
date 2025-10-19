<script>
    import { inertia, router } from "@inertiajs/svelte";
    import Swal from "sweetalert2";

    export let user;

    let avatarPreview = user.avatar
        ? `/storage/${user.avatar}`
        : "/assets/images/profile/user-1.jpg";
    let avatarFile = null;

    const handleFileChange = (e) => {
        const file = e.target.files[0];
        if (file) {
            avatarFile = file;
            avatarPreview = URL.createObjectURL(file);
        }
    };

    const updateProfile = (e) => {
        e.preventDefault();
        const formData = new FormData(e.target);
        if (avatarFile) formData.append("avatar", avatarFile);

        router.post("/user/profile/update", formData, {
            onSuccess: () => {
                Swal.fire({
                    icon: "success",
                    title: "Perfil actualizado",
                    text: "Tus cambios se han guardado correctamente.",
                    timer: 2500,
                    showConfirmButton: false,
                    toast: true,
                    position: "top-end",
                });
            },
            onError: () => {
                Swal.fire({
                    icon: "error",
                    title: "Error al actualizar",
                    text: "Ocurrió un problema al guardar los cambios.",
                    confirmButtonColor: "#dc3545",
                });
            },
        });
    };

    const deleteAvatar = () => {
        Swal.fire({
            title: "¿Eliminar foto de perfil?",
            text: "Esta acción no se puede deshacer.",
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#dc3545",
            cancelButtonColor: "#6c757d",
            confirmButtonText: "Sí, eliminar",
            cancelButtonText: "Cancelar",
        }).then((result) => {
            if (result.isConfirmed) {
                router.delete("/user/profile/avatar", {
                    onSuccess: () => {
                        avatarPreview = "/assets/images/profile/user-1.jpg";
                        Swal.fire({
                            icon: "success",
                            title: "Foto eliminada",
                            text: "Tu foto de perfil fue eliminada correctamente.",
                            timer: 2000,
                            showConfirmButton: false,
                            toast: true,
                            position: "top-end",
                        });
                    },
                    onError: () => {
                        Swal.fire({
                            icon: "error",
                            title: "Error al eliminar",
                            text: "No se pudo eliminar la foto de perfil.",
                        });
                    },
                });
            }
        });
    };
</script>

<div class="container py-4 d-flex justify-content-center">
    <div class="card shadow-lg border-0 w-100" style="max-width: 700px;">
        <div
            class="card-header bg-primary text-white d-flex justify-content-between align-items-center"
        >
            <h5 class="mb-0 text-white">
                <i class="bi bi-person-circle"></i>
                Mi Perfil
            </h5>
            <span class="badge bg-secondary text-uppercase">
                <i class="bi bi-person-lock"></i>
                {user.role}
            </span>
        </div>

        <div class="card-body text-center">
            <!-- Avatar -->
            <div class="position-relative d-inline-block mb-3">
                <img
                    src={avatarPreview}
                    alt="Avatar"
                    class="rounded-circle border border-3 border-primary"
                    width="120"
                    height="120"
                />
                <label
                    for="avatarUpload"
                    class="position-absolute bottom-0 end-0 bg-primary text-white rounded-circle p-2"
                    style="cursor: pointer;"
                    title="Cambiar foto"
                >
                    <i class="bi bi-camera-fill"></i>
                </label>
                <input
                    type="file"
                    id="avatarUpload"
                    class="d-none"
                    accept="image/*"
                    onchange={handleFileChange}
                />
            </div>

            {#if user.avatar}
                <button
                    type="button"
                    class="btn btn-sm btn-outline-danger mb-3"
                    onclick={deleteAvatar}
                >
                    <i class="bi bi-trash"></i> Eliminar foto
                </button>
            {/if}

            <form onsubmit={updateProfile} class="text-start mt-4">
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label
                            class="form-label fw-bold text-muted"
                            for="full-name">Nombre completo</label
                        >
                        <input
                            id="full-name"
                            type="text"
                            name="name"
                            class="form-control"
                            value={user.name}
                            required
                        />
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold text-muted" for="email"
                            >Correo electrónico</label
                        >
                        <input
                            id="email"
                            type="email"
                            name="email"
                            class="form-control"
                            value={user.email}
                            readonly
                        />
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label fw-bold text-muted" for="rol"
                            >Rol</label
                        >
                        <input
                            id="rol"
                            type="text"
                            class="form-control"
                            value={user.role}
                            readonly
                        />
                    </div>
                    <div class="col-md-6">
                        <label
                            class="form-label fw-bold text-muted"
                            for="access">Último acceso</label
                        >
                        <input
                            id="accces"
                            type="text"
                            class="form-control"
                            value={new Date(user.last_login).toLocaleString()}
                            readonly
                        />
                    </div>
                </div>

                <div class="d-flex justify-content-end mt-4">
                    <div class="d-flex gap-1">
                        <a href="/dashboard" use:inertia class="btn btn-light">
                            <i class="bi bi-x-circle"></i> Volver
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-check"></i> Guardar cambios
                        </button>
                    </div>
                </div>
            </form>
        </div>

        <div class="card-footer text-muted text-center small">
            Miembro desde {new Date(user.created_at).toLocaleDateString()}
        </div>
    </div>
    ```
</div>

<style>
    input[readonly] {
        background-color: #f8f9fa;
    }
    .form-label {
        font-size: 0.9rem;
    }
</style>
