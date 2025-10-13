<script>
    import AdminLayout from "@layouts/AdminLayout.svelte";
    import TrafficChart from "@components/TrafficChart.svelte";

    let { auth } = $props();
</script>

<AdminLayout>
    <div class="row">
        <div class="col-lg-12">
            <TrafficChart />
        </div>

        <div class="col-lg-4">
            <!-- <div class="card">
                <div class="card-body text-center">
                    <img
                        src="/assets/images/backgrounds/product-tip.png"
                        alt="Tips"
                        class="img-fluid"
                        width="205"
                    />
                    <h4 class="mt-7">Productivity Tips!</h4>
                    <p class="card-subtitle mt-2 mb-3">
                        Duis at orci justo nulla in libero id leo molestie
                        sodales phasellus justo.
                    </p>
                    <button class="btn btn-primary mb-3">View All Tips</button>
                </div>
            </div> -->
        </div>
    </div>
</AdminLayout>
