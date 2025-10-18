<script>
    import { router, page } from "@inertiajs/svelte";
    import { onMount } from "svelte";
    import Swal from "sweetalert2";

    onMount(() => {
        // Escuchar eventos de navegación de Inertia
        const removeListener = router.on("success", (event) => {
            // Esperar un tick para que las props se actualicen
            setTimeout(() => {
                // Obtener flash messages directamente de $page
                const flash = $page.props.flash;

                // Si existe flash con data, mostrar alerta
                if (flash && flash.data) {
                    const {
                        icon,
                        title,
                        text,
                        html,
                        timer,
                        showConfirmButton,
                    } = flash.data;

                    Swal.fire({
                        icon: icon || "success",
                        title: title || "Éxito",
                        text: text,
                        html: html,
                        timer: timer || 3000,
                        showConfirmButton:
                            showConfirmButton !== undefined
                                ? showConfirmButton
                                : true,
                        confirmButtonText: "Aceptar",
                        confirmButtonColor: "#5d87ff",
                        timerProgressBar: true,
                        toast: false,
                        position: "center",
                    });
                }
            }, 100);
        });

        // Limpiar el listener cuando el componente se desmonte
        return () => {
            removeListener();
        };
    });
</script>

<!-- Este componente no renderiza nada, solo muestra las alertas -->
