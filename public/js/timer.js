let contador = 0;

function Timer(callback, delay) {
    var timerId = null;
    var start;
    var remaining = delay;

    this.pause = function () {
        if (timerId != null) {
            window.clearTimeout(timerId);
            timerId = null;
            remaining -= new Date() - start;
        }
    };

    var resume = function () {
        if (timerId == null) {
            start = new Date();
            timerId = window.setTimeout(function () {
                remaining = delay;
                timerId = null;
                resume();
                callback();
            }, remaining);
        }
    }
    this.resume = resume;

    this.id = function () {
        return timerId;
    }

    this.reset = function () {
        remaining = delay;
    };
}

var timer = new Timer(function () {
    if (contador >= 4) {
        timer.pause();
        timer = null;
        alert("Tem alguém assistindo?");
        alert("Atualize a página para continuar a receber o seu lucro!");
        return;
    }
    $.ajax({
        url: '/timer/' + origem,
        type: 'post',
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        success: function (data) {
            console.log(data)
            if (data.success) {
                contador++;
            }
        }
    });
}, 0.1 * 60 * 1000);
