var OneSignal = window.OneSignal || [];

OneSignal.push(function () {
    OneSignal.init({
        appId: "d7c7a01c-6e25-4b81-8bf7-5af2a55210d7",
        subdomainName: "ecranconnectetc.os.tc",/* The label for your site that you added in Site Setup mylabel.os.tc */
        notifyButton: {
            enable: false
        },
        welcomeNotification: {
            "title": 'Bienvenue !',
            "message": 'Vous allez maintenant recevoir les alertes de l\'IUT ! '
            // "url": "" /* Leave commented for the notification to not open a window on Chrome and Firefox (on Safari, it opens to your webpage) */
        }
    });
});


function onManageWebPushSubscriptionButtonClicked(event) {
    getSubscriptionState().then(function (state) {
        if (state.isPushEnabled) {
            /* Subscribed, opt them out */
            OneSignal.setSubscription(false);
        } else {
            if (state.isOptedOut) {
                /* Opted out, opt them back in */
                OneSignal.setSubscription(true);
            } else {
                /* Unsubscribed, subscribe them */
                OneSignal.registerForPushNotifications();
            }
        }
    });
    event.preventDefault();
}

function updateMangeWebPushSubscriptionButton(buttonSelector) {
    var hideWhenSubscribed = false;
    var subscribeText = "Recevoir les notifications";
    var unsubscribeText = "Ne plus recevoir les notifications";

    getSubscriptionState().then(function (state) {
        var buttonText = !state.isPushEnabled || state.isOptedOut ? subscribeText : unsubscribeText;

        var element = document.querySelector(buttonSelector);
        if (element === null) {
            return;
        }

        element.removeEventListener('click', onManageWebPushSubscriptionButtonClicked);
        element.addEventListener('click', onManageWebPushSubscriptionButtonClicked);
        element.textContent = buttonText;

        if (state.hideWhenSubscribed && state.isPushEnabled) {
            element.style.display = "none";
        } else {
            element.style.display = "";
        }


    });
}

function getSubscriptionState() {
    return Promise.all([
        OneSignal.isPushNotificationsEnabled(),
        OneSignal.isOptedOut()
    ]).then(function (result) {
        var isPushEnabled = result[0];
        var isOptedOut = result[1];

        return {
            isPushEnabled: isPushEnabled,
            isOptedOut: isOptedOut
        };
    });
}

let errorMessage = function () {
    $('body').html('une erreur critique est survenue')
};
var buttonSelector = "#my-notification-button";

/* This example assumes you've already initialized OneSignal */
OneSignal.push(function () {
    // If we're on an unsupported browser, do nothing
    if (!OneSignal.isPushNotificationsSupported()) {
        return;
    }
    updateMangeWebPushSubscriptionButton(buttonSelector);

    OneSignal.on("subscriptionChange", function (isSubscribed) {
        /* If the user's subscription state changes during the page's session, update the button text */
        updateMangeWebPushSubscriptionButton(buttonSelector);
        $.ajax({
            url: '/wp-content/plugins/TeleConnecteeAmu/views/js/utils/userID.php',
            method: 'get'
        })
            .done(function (data) {
                OneSignal.sendTag("login", data).then(function (tagsSent) {
                    console.log("tagsSent: " + tagsSent.login);
                });
            })
            .fail(errorMessage);
    });
});

