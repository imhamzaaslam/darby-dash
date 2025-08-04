<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <link id="favicon" rel="icon" href="{{ asset('favicon.ico') }}" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title id="companyTitle">DarbyDash</title>
    <link rel="stylesheet" type="text/css" href="{{ asset('loader.css') }}" />
    @vite(['resources/js/main.js'])
</head>

<body>
    <div id="app">
        <div id="loading-bg">
            <div class="loading-logo">
            </div>
            <div class=" loading">
                <div class="effect-1 effects"></div>
                <div class="effect-2 effects"></div>
                <div class="effect-3 effects"></div>
            </div>
        </div>
    </div>

    <script>
        const loaderColor = '#FFFFFF'
        var primaryColor = null

        const setCompanyDetails = () => {
            const store = JSON.parse(localStorage.getItem('auth'))
            const storedFavicon = store && store.favicon ? store.favicon : null;
            const storedTitle = store && store.tenant && store.title ? store.title : null;
            const storedPrimaryColor = store && store.generalSetting && store.generalSetting?.primary_color  ? store.generalSetting?.primary_color  : '#a12592';

            const faviconElement = document.getElementById('favicon');
            const titleElement = document.getElementById('companyTitle');

            if (storedFavicon) {
                faviconElement.href = storedFavicon;
            }

            if (storedTitle) {
                titleElement.textContent = storedTitle;
            }

            if (storedPrimaryColor) {
                primaryColor = storedPrimaryColor;
            }
        };

        setCompanyDetails();

        if (loaderColor)
            document.documentElement.style.setProperty('--initial-loader-bg', loaderColor)
        if (loaderColor)
            document.documentElement.style.setProperty('--initial-loader-bg', loaderColor)

        if (primaryColor)
            document.documentElement.style.setProperty('--initial-loader-color', primaryColor)
    </script>
    <script defer data-domain="dash.darbyware.com" src="https://plausible.io/js/script.file-downloads.hash.outbound-links.pageview-props.revenue.tagged-events.js"></script>
    <script>window.plausible = window.plausible || function() { (window.plausible.q = window.plausible.q || []).push(arguments) }</script>
</body>

</html>
