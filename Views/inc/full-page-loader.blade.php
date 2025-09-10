<div class="cs-full-page-loader d-none">
    <img style="width: 100px; height: 100px; margin: auto auto" src="{{ url("assets/img/loading.gif") }}" alt="">
</div>
<script>
    function getFullPageLoader() {
        document.querySelector('.cs-full-page-loader').classList.remove('d-none');
    }

    function removeFullPageLoader() {
        document.querySelector('.cs-full-page-loader').classList.add('d-none');
    }
</script>
<style>
    .cs-full-page-loader{
        position: fixed;
        height: 100vh;
        width: 100%;
        background: rgba(245, 245, 245, 0.75);
        top: 0;
        left: 0;
        overflow: hidden;
        z-index: 9999;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #fff
    }
</style>
