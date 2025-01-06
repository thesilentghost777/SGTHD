<div class="clock-container flex flex-col items-center mb-8">
    <div class="clock">
        <div class="outer-clock-face">
            <div class="marking marking-one"></div>
            <div class="marking marking-two"></div>
            <div class="marking marking-three"></div>
            <div class="marking marking-four"></div>
            <div class="inner-clock-face">
                <div class="hand hour-hand"></div>
                <div class="hand min-hand"></div>
                <div class="hand second-hand"></div>
            </div>
        </div>
    </div>
    <div class="digital-time text-2xl font-bold mt-4" id="digital-time">00:00:00</div>
</div>

<style>
    .clock {
        width: 200px;
        height: 200px;
        border-radius: 50%;
        position: relative;
        padding: 2rem;
        box-shadow: 0 0 0 4px rgba(0,0,0,0.1),
                    inset 0 0 0 3px #EFEFEF,
                    inset 0 0 10px black,
                    0 0 10px rgba(0,0,0,0.2);
    }

    .outer-clock-face {
        position: relative;
        background: #fff;
        overflow: hidden;
        width: 100%;
        height: 100%;
        border-radius: 100%;
    }

    .outer-clock-face::after {
        transform: rotate(90deg);
    }

    .outer-clock-face::before,
    .outer-clock-face::after,
    .marking {
        content: '';
        position: absolute;
        width: 5px;
        height: 100%;
        background: #1f1f1f;
        z-index: 0;
        left: 49%;
    }

    .marking {
        background: #bdbdbd;
        width: 3px;
    }

    .marking.marking-one   { transform: rotate(30deg); }
    .marking.marking-two   { transform: rotate(60deg); }
    .marking.marking-three { transform: rotate(120deg); }
    .marking.marking-four  { transform: rotate(150deg); }

    .inner-clock-face {
        position: absolute;
        top: 10%;
        left: 10%;
        width: 80%;
        height: 80%;
        background: #fff;
        border-radius: 100%;
        z-index: 1;
    }

    .inner-clock-face::before {
        content: '';
        position: absolute;
        top: 50%;
        left: 50%;
        width: 16px;
        height: 16px;
        border-radius: 18px;
        margin-left: -8px;
        margin-top: -8px;
        background: #4d4d4d;
        z-index: 11;
    }

    .hand {
        width: 50%;
        right: 50%;
        height: 6px;
        background: #61afff;
        position: absolute;
        top: 50%;
        border-radius: 6px;
        transform-origin: 100%;
        transform: rotate(90deg);
        transition-timing-function: cubic-bezier(0.1, 2.7, 0.58, 1);
    }

    .hand.hour-hand {
        width: 30%;
        z-index: 3;
        background: #4d4d4d;
    }

    .hand.min-hand {
        height: 3px;
        z-index: 10;
        width: 40%;
        background: #6c6c6c;
    }

    .hand.second-hand {
        background: #ee791a;
        width: 45%;
        height: 2px;
    }
    </style>

<script>
// Synchronisation avec l'heure du serveur
const serverTime = new Date('{{ $serverTime }}');
const clientTime = new Date();
const timeDiff = serverTime - clientTime;

function setDate() {
    const now = new Date(Date.now() + timeDiff);

    // Update analog clock
    const seconds = now.getSeconds();
    const secondsDegrees = ((seconds / 60) * 360) + 90;
    document.querySelector('.second-hand').style.transform = `rotate(${secondsDegrees}deg)`;

    const mins = now.getMinutes();
    const minsDegrees = ((mins / 60) * 360) + ((seconds/60)*6) + 90;
    document.querySelector('.min-hand').style.transform = `rotate(${minsDegrees}deg)`;

    const hour = now.getHours();
    const hourDegrees = ((hour / 12) * 360) + ((mins/60)*30) + 90;
    document.querySelector('.hour-hand').style.transform = `rotate(${hourDegrees}deg)`;

    // Update digital clock
    const digitalTime = document.getElementById('digital-time');
    digitalTime.textContent = now.toLocaleTimeString('fr-FR', {
        hour: '2-digit',
        minute: '2-digit',
        second: '2-digit'
    });
}

setInterval(setDate, 1000);
setDate();
</script>

