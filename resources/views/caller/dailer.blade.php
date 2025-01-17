<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8" />
    <title>Browser Phone</title>
    <script src="{{ asset('caller/js/caller.js') }}"></script>
    <script type="text/javascript">
        if ("serviceWorker" in navigator) {
            navigator.serviceWorker.register("{{ asset('caller/assets/sw.js') }}").catch(function(error) {
                console.error("Service Worker Error", error);
            });
        } else {
            console.warn("Cannot make use of ServiceWorker");
        }
        const settingsUrl = "{{ route('caller.caller.settings') }}";
        const csrfToken = "{{ csrf_token() }}";
        var phoneOptions = {};
        var settings = fetchSettings(settingsUrl, csrfToken, {
            timeout: 3000,
            retry: true,
            maxRetries: 2
        });

        settings.then(function(r) {
            phoneOptions = {
                SipUsername: r.sip_username,
                profileName: r.sip_full_name,
                wssServer: r.websocket_server_tls,
                WebSocketPort: r.websocket_port,
                ServerPath: r.websocket_path,
                SipDomain: r.sip_domain,
                SipPassword: r.sip_password,
                profileUserID: r.sip_username,
                loadAlternateLang: true,
                welcomeScreen: false,
                EnableAppearanceSettings: 0,
                EnableNotificationSettings: 0,
                EnableVideoCalling: 0,
                VoiceMailSubscribe: false,
                EnableTextMessaging: false,
                DisableFreeDial: false,
                DisableBuddies: true,
                EnableAccountSettings: 0,
                NoiseSuppression: 1,
                hostingPrefix: "assets/",
                EchoCancellation: 1,
                ChatEngine: "SIMPLE"
            };
            const script = document.createElement('script');
            script.type = 'text/javascript';
            script.src = "{{ asset('caller/assets/phone.js') }}";
            script.onload = function() {
                if (typeof callerInit === 'function') {
                    callerInit();
                } else {
                    console.error('callerInit function is not defined.');
                }
            };
            script.onerror = function() {
                console.error('Failed to load the script:', script.src);
            };
            document.head.appendChild(script);
        }).catch(error => {
            alert('Settings for User Does not Exists!')
            console.error('Failed to fetch settings:', error);
            window.parent.CloseWindow();

            return false;
        });;
        var web_hook_on_register = function(ua) {
            let urlParams = new URLSearchParams(window.location.search);
            if (urlParams.has("d")) {
                window.setTimeout(function() {
                    console.log("Performing Auto Dial: ", urlParams.get("d"));
                    DialByLine("audio", null, urlParams.get("d"));
                }, 1000);
            }
        };
        var web_hook_on_registrationFailed = function(e) {
            window.parent.CloseWindow();
        };
        var web_hook_on_unregistered = function() {
            window.parent.CloseWindow();
        };
        var web_hook_on_terminate = function(session) {
            window.setTimeout(function() {
                window.parent.CloseWindow();
            }, 1000);
        };
    </script>
    <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate" />
    <meta http-equiv="Expires" content="0" />
    <link rel="icon" type="image/x-icon" href="{{ asset('caller/assets/favicon.ico') }}" />
    <link rel="stylesheet" type="text/css"
        href="https://dtd6jl0d42sve.cloudfront.net/lib/Normalize/normalize-v8.0.1.css" />
    <link rel="stylesheet preload prefetch" type="text/css" as="style"
        href="https://dtd6jl0d42sve.cloudfront.net/lib/fonts/font_roboto/roboto.css" />
    <link rel="stylesheet preload prefetch" type="text/css" as="style"
        href="https://dtd6jl0d42sve.cloudfront.net/lib/fonts/font_awesome/css/font-awesome.min.css" />
    <link rel="stylesheet" type="text/css"
        href="https://dtd6jl0d42sve.cloudfront.net/lib/jquery/jquery-ui-1.13.2.min.css" />
    <link rel="stylesheet" type="text/css"
        href="https://dtd6jl0d42sve.cloudfront.net/lib/Croppie/Croppie-2.6.4/croppie.css" />
    <link rel="stylesheet" type="text/css" href="{{ asset('caller/assets/phone.css') }}" />
</head>

<body>
    <div class="loading"><span class="fa fa-circle-o-notch fa-spin"></span></div>
    <div id="Phone"></div>
</body>
<script type="text/javascript" src="https://dtd6jl0d42sve.cloudfront.net/lib/jquery/jquery-3.6.1.min.js"></script>
<script type="text/javascript" src="https://dtd6jl0d42sve.cloudfront.net/lib/jquery/jquery-ui-1.13.2.min.js"></script>
<script type="text/javascript" src="https://dtd6jl0d42sve.cloudfront.net/lib/jquery/jquery.md5-min.js" defer="true">
</script>
<script type="text/javascript" src="https://dtd6jl0d42sve.cloudfront.net/lib/Chart/Chart.bundle-2.7.2.min.js"
    defer="true"></script>
<script type="text/javascript" src="https://dtd6jl0d42sve.cloudfront.net/lib/SipJS/sip-0.20.0.min.js" defer="true">
</script>
<script type="text/javascript" src="https://dtd6jl0d42sve.cloudfront.net/lib/FabricJS/fabric-2.4.6.min.js" defer="true">
</script>
<script type="text/javascript" src="https://dtd6jl0d42sve.cloudfront.net/lib/Moment/moment-with-locales-2.24.0.min.js"
    defer="true"></script>
<script type="text/javascript" src="https://dtd6jl0d42sve.cloudfront.net/lib/Croppie/Croppie-2.6.4/croppie.min.js"
    defer="true"></script>
<script type="text/javascript" src="https://dtd6jl0d42sve.cloudfront.net/lib/XMPP/strophe-1.4.1.umd.min.js"
    defer="true"></script>

</html>
