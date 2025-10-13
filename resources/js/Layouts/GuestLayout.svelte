<script>
    let { children } = $props();
</script>

<div class="page-wrapper" id="main-wrapper" data-layout="vertical">
    <div
        class="position-relative overflow-hidden radial-gradient min-vh-100 d-flex align-items-center justify-content-center"
    >
        <div class="d-flex align-items-center justify-content-center w-100">
            <div class="row justify-content-center w-100">
                <div class="col-md-8 col-lg-6 col-xxl-3">
                    {@render children()}
                </div>
            </div>
        </div>
    </div>
</div>
