<script>
    let {
        file,
        label = "Download Template",
        icon = "bi bi-download",
    } = $props();
</script>

<a
    class="btn btn-outline-primary d-flex align-items-center gap-2"
    href={`/templates/${file}`}
    download
>
    <i class={icon}></i>
    <span>{label}</span>
</a>
