<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Push Notifications</title>

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- CSRF (optional but recommended if enabled in CI4) -->
    <?php if (csrf_token()): ?>
        <meta name="csrf-token" content="<?= csrf_hash() ?>">
    <?php endif; ?>

    <style>
        body {
            font-family: Arial, Helvetica, sans-serif;
            background: #f7f7f7;
            padding: 40px;
        }

        .card {
            background: #ffffff;
            padding: 24px;
            max-width: 500px;
            margin: auto;
            border-radius: 6px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.08);
        }

        h2 {
            margin-top: 0;
        }

        button {
            padding: 10px 16px;
            margin-right: 10px;
            margin-top: 10px;
            cursor: pointer;
        }

        .status {
            margin-top: 15px;
            font-weight: bold;
        }
    </style>
</head>
<body>

<div class="card">
    <h2>Push Notifications</h2>

    <p>
        Manage browser push notification subscriptions.
    </p>

    <button id="subscribe-btn">Subscribe</button>
    <button id="unsubscribe-btn">Unsubscribe</button>

    <div class="status" id="status"></div>
</div>

<!-- Push Notification Logic -->
<script src="<?php echo base_url().'push-notifications.js?v='.time(); ?>"></script>
<script>
(async () => {
    const pushManager = new PushNotificationManager();

    const subscribeBtn   = document.getElementById('subscribe-btn');
    const unsubscribeBtn = document.getElementById('unsubscribe-btn');
    const statusEl       = document.getElementById('status');

    function setSubscribedUI() {
        subscribeBtn.disabled = true;
        subscribeBtn.textContent = 'Subscribed';
        statusEl.textContent = 'You are subscribed to push notifications.';
    }

    function setUnsubscribedUI() {
        subscribeBtn.disabled = false;
        subscribeBtn.textContent = 'Subscribe';
        statusEl.textContent = 'You are not subscribed.';
    }

    try {
        await pushManager.init();

        const isSubscribed = await pushManager.isSubscribed();
        isSubscribed ? setSubscribedUI() : setUnsubscribedUI();
    } catch (e) {
        statusEl.textContent = 'Push notifications unavailable.';
        return;
    }

    subscribeBtn.addEventListener('click', async () => {
        try {
            await pushManager.subscribe();
            setSubscribedUI();
        } catch (error) {
            alert(error.message);
        }
    });

    unsubscribeBtn.addEventListener('click', async () => {
        try {
            await pushManager.unsubscribe();
            setUnsubscribedUI();
        } catch (error) {
            alert(error.message);
        }
    });
})();
</script>

</body>
</html>
