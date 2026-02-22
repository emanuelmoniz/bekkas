// logic to dismiss the home splash overlay when the user interacts

(function(){
    document.addEventListener('DOMContentLoaded', function(){
        var splash = document.getElementById('home-splash');
        if (!splash) return;
        var dismissed = false;

        var __autoDismissSplashForContact = (window.location && window.location.hash === '#contact');

        function hideSplash() {
            if (dismissed) return;
            dismissed = true;
            splash.classList.add('home-splash-hidden');
            document.body.classList.remove('overflow-hidden');

            try {
                window.removeEventListener('wheel', onFirstIntent, {passive:true});
                window.removeEventListener('touchstart', onFirstIntent, {passive:true});
            } catch(e) {}
            window.removeEventListener('keydown', onKeyDown);
            splash.removeEventListener('click', onFirstIntent);

            setTimeout(function(){ if (splash && splash.parentNode) splash.parentNode.removeChild(splash); }, 650);
        }

        function onFirstIntent() { hideSplash(); }
        function onKeyDown(e) {
            var keys = ['ArrowDown','PageDown',' ','Enter'];
            if (keys.indexOf(e.key) !== -1) hideSplash();
        }

        window.addEventListener('wheel', onFirstIntent, {passive:true, once:true});
        window.addEventListener('touchstart', onFirstIntent, {passive:true, once:true});
        window.addEventListener('keydown', onKeyDown, {once:true});
        splash.addEventListener('click', onFirstIntent, {once:true});

        window.addEventListener('hashchange', function(){ if (window.location.hash === '#contact') { hideSplash(); var t = document.getElementById('contact'); if (t) t.scrollIntoView(); } });

        if (typeof __autoDismissSplashForContact !== 'undefined' && __autoDismissSplashForContact) {
            hideSplash();
            var target = document.getElementById('contact');
            if (target) { target.scrollIntoView(); }
            return;
        }

        setTimeout(hideSplash, 3000);
    });
})();
