(() => {
    console.log('oneSignalPush.js');

    window.OneSignal = window.OneSignal || [];
    const notificationButton = document.getElementById('my-notification-button');
    let subscribing = false;

    const getSubscriptionState = () => {
        return Promise.all([
            OneSignal.isPushNotificationsEnabled(),
            OneSignal.isOptedOut()
        ]).then(function(result) {
            let isPushEnabled = result[0];
            let isOptedOut = result[1];

            return {
                isPushEnabled: isPushEnabled,
                isOptedOut: isOptedOut
            };
        });
    }

    const updateButtonAppearance = () => {
        if (!OneSignal.isPushNotificationsSupported()) {
            notificationButton.style.display = 'none';
        } else {
            getSubscriptionState()
                .then((state) => {
                    const buttonText = !state.isPushEnabled || state.isOptedOut ?
                        'Recevoir des notifications' :
                        'Ne plus recevoir des notifications';

                    notificationButton.innerText = buttonText;
                });
        }
    }

    const toggleNotificationSubscription = () => {
        getSubscriptionState()
            .then(function(state) {
                if (state.isPushEnabled) {
                    /* Subscribed, opt them out */
                    return OneSignal.setSubscription(false);
                } else {
                    if (state.isOptedOut) {
                        /* Opted out, opt them back in */
                        return OneSignal.setSubscription(true);
                    } else {
                        /* Unsubscribed, subscribe them */
                        return OneSignal.registerForPushNotifications();
                    }
                }
            })
            .then(() => {
                updateButtonAppearance();
            });
    }

    OneSignal.push(function() {
        OneSignal.SERVICE_WORKER_PARAM = { scope: '/push/onesignal/' };
        OneSignal.SERVICE_WORKER_PATH = 'wp-content/plugins/plugin-ecran-connecte/public/js/vendor/OneSignalSDKWorker.js'
        OneSignal.SERVICE_WORKER_UPDATER_PATH = 'wp-content/plugins/plugin-ecran-connecte/public/js/vendor/OneSignalSDKUpdaterWorker.js'

        OneSignal.init({
            appId: "9d06a052-42ec-4e2e-8407-94dbb81b4766",
        });

        OneSignal.on('subscriptionChange', (isSubscribed) => {
            if (subscribing && isSubscribed) {
                subscribing = false;
                toggleNotificationSubscription();
            }
        });

        updateButtonAppearance();
    });

    notificationButton.addEventListener('click', () => {
        OneSignal.push(function() {
            OneSignal.getNotificationPermission()
                .then((res) => {
                    if (res === 'default') {
                        OneSignal.showNativePrompt();
                        subscribing = true;
                    } else if (res === 'granted') {
                        toggleNotificationSubscription();
                    } else if (res === 'denied') {
                        alert('Vous avez désactivé les notifications de votre navigateur');
                    }
                });
        });
    });
})();